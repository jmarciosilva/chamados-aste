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
    // ... [métodos anteriores mantidos] ...

    public function store(
        Request $request,
        AttachmentService $attachmentService
    ) {
        $rawDescription = trim($request->input('description', ''));

        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'product_id' => 'required|exists:products,id',
            'service_type' => 'required|in:'.implode(',', array_column(ServiceType::cases(), 'value')),
            'description' => 'nullable|string',
            'attachments.*' => 'nullable|file|max:51200',
            'criticality' => 'required|in:'.implode(',', array_column(Criticality::cases(), 'value')),
        ]);

        $description = trim($validated['description'] ?? '');

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

        $serviceDesk = SupportGroup::where('is_entry_point', true)->firstOrFail();

        $monthYear = now()->format('my');
        $lastTicket = Ticket::where('code', 'like', "CH{$monthYear}-%")
            ->orderByDesc('code')
            ->first();

        $sequence = $lastTicket
            ? intval(substr($lastTicket->code, -6)) + 1
            : 1;

        $code = sprintf('CH%s-%06d', $monthYear, $sequence);

        $criticality = Criticality::from($validated['criticality']);
        $priority = $criticality->toPriority();

        /**
         * ============================================================
         * SLA (DO PRODUTO) - ATUALIZADO
         * ============================================================
         */
        $product = Product::findOrFail($validated['product_id']);
        $slaConfig = $product->getSlaForPriority($priority->value);

        $ticket = Ticket::create([
            'code' => $code,
            'subject' => $validated['subject'],
            'description' => $description,
            'product_id' => $validated['product_id'],
            'service_type' => ServiceType::from($validated['service_type']),
            'priority' => $priority,
            'status' => TicketStatus::OPEN,
            'sla_id' => null, // Não usamos mais a tabela slas
            'sla_response_hours' => $slaConfig['response_hours'] ?? null,
            'sla_resolution_hours' => $slaConfig['resolution_hours'] ?? null,
            'sla_started_at' => now(),
            'sla_status' => 'running',
            'requester_id' => auth()->id(),
            'department_id' => auth()->user()->department_id,
            'current_group_id' => $serviceDesk->id,
        ]);

        $initialMessage = null;

        if ($hasText || $hasInlineImage) {
            $initialMessage = $ticket->messages()->create([
                'user_id' => auth()->id(),
                'message' => $rawDescription,
                'is_internal_note' => false,
            ]);
        }

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

    // ... [outros métodos mantidos] ...
}
