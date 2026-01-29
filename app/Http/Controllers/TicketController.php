<?php

namespace App\Http\Controllers;

use App\Enums\Criticality;
use App\Enums\ServiceType;
use App\Enums\TicketStatus;
use App\Models\ProblemCategory;
use App\Models\Product;
use App\Models\Sla;
use App\Models\SupportGroup;
use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Services\AttachmentService;
use Illuminate\Http\Request;

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
         */
        $slas = Sla::active()
            ->with('product')
            ->orderBy('priority')
            ->get()
            ->groupBy(fn (Sla $sla) => $sla->product->name)
            ->map(function ($productSlas) {
                return $productSlas
                    ->groupBy(fn (Sla $sla) => $sla->priority->value)
                    ->map(fn ($prioritySlas) => $prioritySlas->sortByDesc('is_default')->first()
                    )
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
    public function store(
        Request $request,
        AttachmentService $attachmentService
    ) {
        /**
         * ============================================================
         * CAPTURA BRUTA (ANTES DO VALIDATE)
         * ============================================================
         */
        $rawDescription = trim($request->input('description', ''));

        /**
         * ============================================================
         * VALIDAÇÃO
         * ============================================================
         */
        $validated = $request->validate([
            'subject' => 'required|string|max:255',

            'product_id' => 'required|exists:products,id',
            'service_type' => 'required|in:'.implode(',', array_column(ServiceType::cases(), 'value')),

            'description' => 'nullable|string',
            'attachments.*' => 'nullable|file|max:51200',

            'criticality' => 'required|in:'.implode(',', array_column(Criticality::cases(), 'value')),
        ]);

        /**
         * ============================================================
         * NORMALIZA DESCRIÇÃO
         * ============================================================
         */
        $description = trim($validated['description'] ?? '');

        /**
         * ============================================================
         * REGRA DE NEGÓCIO — DESCRIÇÃO OU ANEXO
         * ============================================================
         */
        $hasText = trim(strip_tags($rawDescription)) !== '';
        $hasInlineImage = str_contains($rawDescription, '<img');
        $hasAttachment = $request->hasFile('attachments');

        if (! $hasText && ! $hasInlineImage && ! $hasAttachment) {
            return back()
                ->withErrors([
                    'description' => 'Informe a descrição do problema ou anexe um print da tela.',
                ])
                ->withInput();
        }

        /**
         * ============================================================
         * GRUPO DE ENTRADA
         * ============================================================
         */
        $serviceDesk = SupportGroup::where('is_entry_point', true)->firstOrFail();

        /**
         * ============================================================
         * CÓDIGO DO CHAMADO
         * ============================================================
         */
        $monthYear = now()->format('my');

        $lastTicket = Ticket::where('code', 'like', "CH{$monthYear}-%")
            ->orderByDesc('code')
            ->first();

        $sequence = $lastTicket
            ? intval(substr($lastTicket->code, -6)) + 1
            : 1;

        $code = sprintf('CH%s-%06d', $monthYear, $sequence);

        /**
         * ============================================================
         * CRITICIDADE → PRIORIDADE
         * ============================================================
         */
        $criticality = Criticality::from($validated['criticality']);
        $priority = $criticality->toPriority();

        /**
         * ============================================================
         * SLA
         * ============================================================
         */
        $sla = Sla::matchRule(
            $validated['product_id'],
            ServiceType::from($validated['service_type']),
            $priority
        )->first()
            ?? Sla::defaultForProduct($validated['product_id'])->first();

        /**
         * ============================================================
         * CRIAÇÃO DO CHAMADO
         * ============================================================
         */
        $ticket = Ticket::create([
            'code' => $code,
            'subject' => $validated['subject'],
            'description' => $description,

            'product_id' => $validated['product_id'],
            'service_type' => ServiceType::from($validated['service_type']),

            // 🔥 categoria removida do fluxo
            // 'problem_category_id' => null,

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
         * ============================================================
         * PRIMEIRA MENSAGEM
         * ============================================================
         */
        $initialMessage = null;

        if ($hasText || $hasInlineImage) {
            $initialMessage = $ticket->messages()->create([
                'user_id' => auth()->id(),
                'message' => $rawDescription,
                'is_internal_note' => false,
            ]);
        }

        /**
         * ============================================================
         * ANEXOS
         * ============================================================
         */
        if ($hasAttachment) {
            foreach ($request->file('attachments') as $file) {
                $path = $attachmentService->uploadTicketAttachment($file, $ticket->id);

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
         * ============================================================
         * HISTÓRICO ITIL
         * ============================================================
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
     * UPLOAD DE IMAGEM (EDITOR)
     * ============================================================
     */
    public function uploadImage(
        Request $request,
        AttachmentService $attachmentService
    ) {
        $request->validate([
            'image' => 'required|image|max:5120',
        ]);

        $url = $attachmentService->uploadEditorImage(
            $request->file('image')
        );

        return response()->json([
            'url' => $url,
        ]);
    }

    /**
     * ============================================================
     * RESPOSTA DO USUÁRIO
     * ============================================================
     */
    public function reply(Request $request, Ticket $ticket)
    {
        abort_if($ticket->requester_id !== auth()->id(), 403);

        $validated = $request->validate([
            'message' => 'required|string',
        ]);

        $ticket->messages()->create([
            'user_id' => auth()->id(),
            'message' => $validated['message'],
            'is_internal_note' => false,
        ]);

        if ($ticket->sla_status === 'paused') {
            $ticket->resumeSla();
        }

        if ($ticket->status === TicketStatus::WAITING_USER) {
            $ticket->update([
                'status' => TicketStatus::IN_PROGRESS,
            ]);
        }

        $ticket->addSystemMessage(
            'O solicitante respondeu e o atendimento foi retomado.'
        );

        return back()->with('success', 'Mensagem enviada com sucesso.');
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
            'messages.attachments',
            'groupHistories.fromGroup',
            'groupHistories.toGroup',
            'groupHistories.user',
        ]);

        /**
         * ------------------------------------------------------------
         * TIMELINE UNIFICADA
         * ------------------------------------------------------------
         */
        $timeline = collect();
        // $attachments = $ticket->attachments->sortBy('created_at');

        /**
         * Mensagens (usuário / operador / sistema)
         */
        // foreach ($ticket->messages as $message) {

        //     $relatedAttachments = $attachments->filter(
        //         fn ($attachment) => $attachment->created_at->between(
        //             $message->created_at,
        //             $message->created_at->copy()->addSeconds(10)
        //         )
        //     );

        //     $timeline->push([
        //         'type' => 'message',

        //         // 🔑 CAMPOS ESTRUTURAIS
        //         'user_id' => $message->user_id,           // <<< ESSENCIAL
        //         'user' => $message->user->name ?? 'Sistema',
        //         'is_internal' => (bool) $message->is_internal_note,

        //         // 📄 CONTEÚDO
        //         'content' => $message->message,
        //         'created_at' => $message->created_at,
        //         'attachments' => $relatedAttachments,
        //     ]);
        // }
        foreach ($ticket->messages as $message) {

            $timeline->push([
                'type' => 'message',

                // Identidade
                'user_id' => $message->user_id,
                'user' => $message->user->name ?? 'Sistema',
                'is_internal' => (bool) $message->is_internal_note,

                // Conteúdo
                'content' => $message->message,
                'created_at' => $message->created_at,

                // ✅ ANEXOS CORRETOS
                'attachments' => $message->attachments,
            ]);
        }

        /**
         * Transferências de grupo (ITIL)
         */
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
}
