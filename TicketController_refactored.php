<?php

namespace App\Http\Controllers;

use App\Enums\Criticality;
use App\Enums\ServiceType;
use App\Enums\TicketStatus;
use App\Models\ProblemCategory;
use App\Models\Product;
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

        return view('user.tickets.create', compact(
            'products',
            'categories'
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
         * REGRA DE NEGÓCIO – DESCRIÇÃO OU ANEXO
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
         * ✅ NOVO: SLA DIRETO DO PRODUTO
         * ============================================================
         */
        $product = Product::findOrFail($validated['product_id']);
        $slaConfig = $product->getSlaForPriority($priority->value);

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

            'priority' => $priority,
            'status' => TicketStatus::OPEN,

            // ✅ NOVO: SLA vem do produto
            'sla_id' => null, // Não usa mais tabela slas
            'sla_response_hours' => $slaConfig['response_hours'],
            'sla_resolution_hours' => $slaConfig['resolution_hours'],
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

    // ... demais métodos permanecem iguais
}
