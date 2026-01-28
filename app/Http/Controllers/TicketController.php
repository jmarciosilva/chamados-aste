<?php

namespace App\Http\Controllers;

use App\Enums\Priority;
use App\Enums\ServiceType;
use App\Enums\TicketStatus;
use App\Models\ProblemCategory;
use App\Models\Product;
use App\Models\Sla;
use App\Models\SupportGroup;
use App\Models\Ticket;
use App\Models\TicketAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class TicketController extends Controller
{
    /**
     * ============================================================
     * LISTAGEM DE CHAMADOS DO USUÁRIO
     * ============================================================
     */
    public function index()
    {
        $tickets = Ticket::where('requester_id', auth()->id())
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('user.tickets.index', compact('tickets'));
    }

    /**
     * ============================================================
     * FORMULÁRIO DE ABERTURA DE CHAMADO
     * ============================================================
     */
    public function create()
    {
        /**
         * ------------------------------------------------------------
         * PRODUTOS ATIVOS
         * ------------------------------------------------------------
         */
        $products = Product::active()
            ->orderBy('name')
            ->get();

        /**
         * ------------------------------------------------------------
         * CATEGORIAS ATIVAS
         * ------------------------------------------------------------
         */
        $categories = ProblemCategory::active()
            ->ordered()
            ->get();

        /**
         * ------------------------------------------------------------
         * SLAs ATIVOS AGRUPADOS POR PRODUTO
         * ------------------------------------------------------------
         * Estrutura final:
         * [
         *   'PDV' => Collection<Sla>,
         *   'E-commerce' => Collection<Sla>,
         * ]
         */
        $slas = Sla::active()
            ->with('product')
            ->orderBy('priority')
            ->get()
            ->groupBy(fn (Sla $sla) => $sla->product->name)
            ->map(function ($productSlas) {
                /**
                 * Mantém apenas 1 SLA por prioridade:
                 * - Prioriza SLA padrão do produto
                 */
                return $productSlas
                    ->groupBy(fn (Sla $sla) => $sla->priority->value)
                    ->map(function ($prioritySlas) {
                        return $prioritySlas
                            ->sortByDesc('is_default')
                            ->first();
                    })
                    ->values();
            });

        return view('user.tickets.create', compact(
            'products',
            'categories',
            'slas'
        ));
    }

    /**
     * ============================================================
     * ARMAZENAR NOVO CHAMADO
     * ============================================================
     */
    public function store(Request $request)
    {
        /**
         * ------------------------------------------------------------
         * VALIDAÇÃO
         * ------------------------------------------------------------
         */
        $validated = $request->validate([
            'subject' => 'required|string|max:255',

            'product_id' => 'required|exists:products,id',
            'problem_category_id' => 'required|exists:problem_categories,id',
            'service_type' => 'required|in:'.implode(',', array_column(ServiceType::cases(), 'value')),

            'description' => 'nullable|string',
            'attachments.*' => 'nullable|file|max:51200',
        ]);

        /**
         * ------------------------------------------------------------
         * REGRA DE NEGÓCIO
         * ------------------------------------------------------------
         */
        if (
            empty(trim($validated['description'] ?? '')) &&
            ! $request->hasFile('attachments')
        ) {
            return back()
                ->withErrors([
                    'description' => 'Informe a descrição do problema ou anexe um print da tela.',
                ])
                ->withInput();
        }

        /**
         * ------------------------------------------------------------
         * CATEGORIA (VALIDAÇÃO CRUZADA)
         * ------------------------------------------------------------
         */
        $category = ProblemCategory::where('id', $validated['problem_category_id'])
            ->where('product_id', $validated['product_id'])
            ->active()
            ->firstOrFail();

        /**
         * ------------------------------------------------------------
         * GRUPO DE ENTRADA (SERVICE DESK)
         * ------------------------------------------------------------
         */
        $serviceDesk = SupportGroup::where('is_entry_point', true)->firstOrFail();

        /**
         * ------------------------------------------------------------
         * CÓDIGO DO CHAMADO
         * ------------------------------------------------------------
         */
        $monthYear = now()->format('my');

        $sequence = optional(
            Ticket::where('code', 'like', "CH{$monthYear}-%")
                ->orderByDesc('code')
                ->first()
        )->code
            ? intval(substr(Ticket::max('code'), -6)) + 1
            : 1;

        $code = sprintf('CH%s-%06d', $monthYear, $sequence);

        /**
         * ------------------------------------------------------------
         * PRIORIDADE INICIAL (CATEGORIA)
         * ------------------------------------------------------------
         */
        $priority = Priority::from($category->default_priority);

        /**
         * ------------------------------------------------------------
         * SLA APLICÁVEL (PRODUTO + TIPO + PRIORIDADE)
         * ------------------------------------------------------------
         */
        $sla = Sla::active()
            ->where('product_id', $category->product_id)
            ->where('service_type', $category->service_type)
            ->where('priority', $priority)
            ->first();

        /**
         * Fallback → SLA padrão do produto
         */
        if (! $sla) {
            $sla = Sla::active()
                ->where('product_id', $category->product_id)
                ->where('is_default', true)
                ->where('priority', $priority)
                ->first();
        }

        /**
         * ------------------------------------------------------------
         * CRIAÇÃO DO CHAMADO (COM SNAPSHOT DO SLA)
         * ------------------------------------------------------------
         */
        $ticket = Ticket::create([
            'code' => $code,
            'subject' => $validated['subject'],
            'description' => $validated['description'],

            'product_id' => $category->product_id,
            'problem_category_id' => $category->id,
            'service_type' => ServiceType::from($validated['service_type']),

            'priority' => $priority,
            'status' => TicketStatus::OPEN,

            'sla_id' => $sla?->id,
            'sla_response_hours' => $sla?->response_time_hours,
            'sla_resolution_hours' => $sla?->resolution_time_hours,
            'sla_started_at' => now(),
            'sla_status' => 'running',

            'requester_id' => auth()->id(),
            'department_id' => auth()->user()->department_id,
            'current_group_id' => $serviceDesk->id,
        ]);

        /**
         * ------------------------------------------------------------
         * PRIMEIRA MENSAGEM
         * ------------------------------------------------------------
         */
        $initialMessage = null;

        if (! empty(trim($validated['description'] ?? ''))) {
            $initialMessage = $ticket->messages()->create([
                'user_id' => auth()->id(),
                'message' => $validated['description'],
                'is_internal_note' => false,
            ]);
        }

        /**
         * ------------------------------------------------------------
         * ANEXOS
         * ------------------------------------------------------------
         */
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {

                $path = $file->store("tickets/{$ticket->id}", 'public');

                TicketAttachment::create([
                    'ticket_id' => $ticket->id,
                    'ticket_message_id' => optional($initialMessage)->id,
                    'original_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'mime_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                    'uploaded_by' => auth()->id(),
                ]);
            }
        }

        /**
         * ------------------------------------------------------------
         * HISTÓRICO ITIL
         * ------------------------------------------------------------
         */
        $ticket->groupHistories()->create([
            'from_group_id' => null,
            'to_group_id' => $serviceDesk->id,
            'changed_by' => auth()->id(),
            'note' => 'Ticket opened by requester',
        ]);

        return redirect()
            ->route('user.tickets.index')
            ->with('success', "Ticket {$code} aberto com sucesso.");
    }

    /**
     * ============================================================
     * EXIBIR CHAMADO (PORTAL DO USUÁRIO)
     * ============================================================
     */
    public function show(Ticket $ticket)
    {
        /**
         * ------------------------------------------------------------
         * AUTORIZAÇÃO
         * ------------------------------------------------------------
         * Usuário só pode ver seus próprios chamados
         */
        abort_if(
            $ticket->requester_id !== auth()->id(),
            403,
            'Acesso não autorizado.'
        );

        /**
         * ------------------------------------------------------------
         * CARREGAMENTO DE RELACIONAMENTOS
         * ------------------------------------------------------------
         */
        $ticket->load([
            'product',
            'problemCategory',
            'requester',
            'department',
            'currentGroup',
            'assignedAgent',
            'messages.user',
            'attachments',
            'groupHistories.fromGroup',
            'groupHistories.toGroup',
            'groupHistories.user',
        ]);

        /**
         * ------------------------------------------------------------
         * TIMELINE UNIFICADA (IGUAL AO AGENTE)
         * ------------------------------------------------------------
         */
        $timeline = collect();
        $attachments = $ticket->attachments->sortBy('created_at');

        // Mensagens (usuário, operador e sistema)
        foreach ($ticket->messages as $message) {

            $relatedAttachments = $attachments->filter(
                fn ($attachment) => $attachment->created_at->between(
                    $message->created_at,
                    $message->created_at->copy()->addSeconds(10)
                )
            );

            $timeline->push([
                'type' => 'message',
                'user' => $message->user->name ?? 'Sistema',
                'content' => $message->message,
                'created_at' => $message->created_at,
                'attachments' => $relatedAttachments,
            ]);
        }

        // Transferências de grupo (ITIL)
        foreach ($ticket->groupHistories as $history) {

            if (! $history->from_group_id) {
                continue;
            }

            $timeline->push([
                'type' => 'group_transfer',
                'user' => $history->user->name ?? 'Sistema',
                'from_group' => $history->fromGroup?->name ?? '—',
                'to_group' => $history->toGroup?->name ?? '—',
                'note' => $history->note,
                'created_at' => $history->created_at,
                'attachments' => collect(),
            ]);
        }

        $timeline = $timeline->sortBy('created_at');

        return view('user.tickets.show', compact(
            'ticket',
            'timeline'
        ));
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:5120', // 5MB
        ]);

        $path = $request->file('image')
            ->store('tickets/temp', 'public');

        return response()->json([
            'url' => Storage::url($path),
        ]);
    }

    public function reply(Request $request, Ticket $ticket)
{
    // Segurança: só o dono do chamado pode responder
    abort_if($ticket->requester_id !== auth()->id(), 403);

    $validated = $request->validate([
        'message' => 'required|string',
    ]);

    /**
     * ======================================================
     * 1️⃣ Mensagem do usuário
     * ======================================================
     */
    $ticket->messages()->create([
        'user_id' => auth()->id(),
        'message' => $validated['message'],
        'is_internal_note' => false,
    ]);

    /**
     * ======================================================
     * 2️⃣ RETOMADA AUTOMÁTICA DO SLA
     * ======================================================
     */
    if ($ticket->sla_status === 'paused') {
        $ticket->resumeSla();
    }

    /**
     * ======================================================
     * 3️⃣ SAIR DO STATUS "AGUARDANDO USUÁRIO"
     * ======================================================
     */
    if ($ticket->status === \App\Enums\TicketStatus::WAITING_USER) {
        $ticket->update([
            'status' => \App\Enums\TicketStatus::IN_PROGRESS,
        ]);
    }

    /**
     * ======================================================
     * 4️⃣ Mensagem de sistema (transparência)
     * ======================================================
     */
    $ticket->addSystemMessage(
        'O solicitante respondeu e o atendimento foi retomado.'
    );

    return back()->with('success', 'Mensagem enviada com sucesso.');
}

}
