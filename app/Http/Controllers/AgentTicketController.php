<?php

namespace App\Http\Controllers;

use App\Enums\Priority;
use App\Enums\TicketStatus;
use App\Models\SupportGroup;
use App\Models\Ticket;
use Illuminate\Http\Request;
use App\Models\User;
use App\Services\TicketQueueService;

class AgentTicketController extends Controller
{
    /**
     * ==============================================================
     * DASHBOARD OPERACIONAL (AGENT / ADMIN)
     * ==============================================================
     */
    public function dashboard()
    {
        return view('agent.dashboard');
    }

    /**
     * ==============================================================
     * FILA DE CHAMADOS (OPERADOR / ESPECIALISTA)
     * ==============================================================
     */
    public function queue(TicketQueueService $queueService)
    {
        $user = auth()->user();

        $tickets = $queueService->getQueue();

        /**
         * --------------------------------------------------------------
         * PERFIL: OPERADOR (Service Desk)
         * --------------------------------------------------------------
         */
        if ($user->agent_type === 'operator') {

            // KPIs operacionais
            $waitingCount = Ticket::where('status', TicketStatus::OPEN)
                ->whereNull('assigned_to')
                ->count();

            $inProgressCount = Ticket::where('status', TicketStatus::IN_PROGRESS)
                ->whereNotNull('assigned_to')
                ->count();

            $resolvedTodayCount = Ticket::where('status', TicketStatus::RESOLVED)
                ->whereDate('resolved_at', now())
                ->count();

            /**
             * ----------------------------------------------------------
             * FILA GERAL
             * - Chamados abertos ou em atendimento
             * - Ordenação ITIL por prioridade
             * ----------------------------------------------------------
             */
            $tickets = Ticket::with(['requester', 'department'])
                ->whereIn('status', [
                    TicketStatus::OPEN,
                    TicketStatus::IN_PROGRESS,
                ])
                ->orderByRaw("
                    CASE priority
                        WHEN 'critical' THEN 1
                        WHEN 'high' THEN 2
                        WHEN 'medium' THEN 3
                        WHEN 'low' THEN 4
                    END
                ")
                ->orderBy('created_at')
                ->paginate(20);

            return view('agent.queue', compact(
                'tickets',
                'waitingCount',
                'inProgressCount',
                'resolvedTodayCount'
            ));
        }

        /**
         * --------------------------------------------------------------
         * PERFIL: ESPECIALISTA
         * - Vê apenas chamados do(s) seu(s) grupo(s)
         * --------------------------------------------------------------
         */
        $groupIds = $user->supportGroups->pluck('id');

        $tickets = Ticket::with(['requester', 'department'])
            ->where('status', TicketStatus::IN_PROGRESS)
            ->whereIn('current_group_id', $groupIds)
            ->orderBy('created_at')
            ->paginate(20);

        return view('agent.queue', compact('tickets'));
    }

    /**
     * ==============================================================
     * VISUALIZAR CHAMADO (OPERADOR / ESPECIALISTA)
     * ==============================================================
     */
    public function show(Ticket $ticket)
    {
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

        // Montagem da timeline (igual à atual)
        $timeline = collect();

        foreach ($ticket->messages as $message) {
            $timeline->push([
                'type' => 'message',
                'user_id' => $message->user_id,
                'user' => $message->user->name ?? 'Sistema',
                'is_internal' => (bool) $message->is_internal_note,
                'content' => $message->message,
                'created_at' => $message->created_at,
                'attachments' => $message->attachments ?? collect(),
            ]);
        }

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

        $sla = $ticket->slaBadge();

        /**
         * ----------------------------------------------------------
         * DESVIO DE VIEW CONFORME STATUS
         * ----------------------------------------------------------
         */
        if (in_array($ticket->status, [
            \App\Enums\TicketStatus::RESOLVED,
            \App\Enums\TicketStatus::CLOSED,
        ])) {
            return view('agent.tickets.show-closed', compact(
                'ticket',
                'timeline',
                'sla'
            ));
        }

        // Grupos disponíveis para encaminhamento
        $groups = SupportGroup::where('is_active', true)
            ->where('id', '!=', $ticket->current_group_id)
            ->orderBy('name')
            ->get();

        // Especialistas do grupo atual
        $specialists = collect();

        if ($ticket->current_group_id) {
            $specialists = User::where('role', 'agent')
                ->where('agent_type', 'specialist')
                ->where('is_active', true)
                ->whereHas(
                    'supportGroups',
                    fn ($q) => $q->where('support_groups.id', $ticket->current_group_id)
                )
                ->orderBy('name')
                ->get();
        }

        return view('agent.tickets.show', compact(
            'ticket',
            'timeline',
            'sla',
            'groups',
            'specialists'
        ));

    }

    /**
     * ==============================================================
     * ATENDIMENTO DO CHAMADO (OPERADOR / ESPECIALISTA)
     * ==============================================================
     */
    public function update(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'message' => 'required|string',
            'message_type' => 'required|in:user,internal,closing',
            'priority' => 'nullable|in:low,medium,high,critical',
            'status' => 'nullable|in:in_progress,resolved',
            'attachments.*' => 'nullable|file|max:51200',
        ]);

        /**
         * --------------------------------------------------------------
         * CRIAR MENSAGEM
         * --------------------------------------------------------------
         */
        $isInternal = $validated['message_type'] === 'internal';
        $isClosing = $validated['message_type'] === 'closing';

        $ticketMessage = $ticket->messages()->create([
            'user_id' => auth()->id(),
            'message' => $validated['message'],
            'is_internal_note' => $isInternal,
        ]);

        /**
         * --------------------------------------------------------------
         * ANEXOS
         * --------------------------------------------------------------
         */
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('tickets/attachments', 'public');

                $ticket->attachments()->create([
                    'ticket_message_id' => $ticketMessage->id,
                    'original_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'mime_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                    'uploaded_by' => auth()->id(),
                ]);
            }
        }

        /**
         * --------------------------------------------------------------
         * LÓGICA DE STATUS E SLA
         * --------------------------------------------------------------
         */
        if ($validated['message_type'] === 'user') {
            // Mensagem para usuário → PAUSA SLA
            $ticket->pauseSla();

            $ticket->update([
                'status' => TicketStatus::WAITING_USER,
            ]);

            $ticket->addSystemMessage(
                'O operador solicitou informações adicionais. Aguardando resposta do usuário.'
            );
        }

        if ($isClosing) {
            // Finalização do chamado
            $ticket->update([
                'status' => TicketStatus::RESOLVED,
                'resolved_at' => now(),
            ]);

            $ticket->addSystemMessage(
                'Chamado finalizado por '.auth()->user()->name
            );
        }

        /**
         * --------------------------------------------------------------
         * ATUALIZAR PRIORIDADE E STATUS (SE INFORMADOS)
         * --------------------------------------------------------------
         */
        $updates = [];

        if (isset($validated['priority'])) {
            $updates['priority'] = Priority::from($validated['priority']);
        }

        if (isset($validated['status'])) {
            $updates['status'] = TicketStatus::from($validated['status']);
        }

        if (! empty($updates)) {
            $ticket->update($updates);
        }

        return redirect()
            ->route('agent.tickets.show', $ticket)
            ->with('success', 'Atendimento registrado com sucesso.');
    }

    /**
     * ==============================================================
     * ASSUMIR CHAMADO (OPERADOR)
     * ==============================================================
     */
    public function take(Ticket $ticket)
    {
        abort_if(
            $ticket->assigned_to !== null,
            403,
            'Chamado já está atribuído.'
        );

        $ticket->update([
            'assigned_to' => auth()->id(),
            'status' => TicketStatus::IN_PROGRESS,
        ]);

        $ticket->addSystemMessage(
            auth()->user()->name.' assumiu o atendimento deste chamado.'
        );

        return back()->with('success', 'Chamado assumido com sucesso.');
    }

    /**
     * ==============================================================
     * ENCAMINHAR CHAMADO (ITIL)
     * ==============================================================
     */
    public function forward(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'to_group_id' => 'required|exists:support_groups,id',
            'assigned_to' => 'nullable|exists:users,id',
            'note' => 'nullable|string|max:500',
        ]);

        /**
         * --------------------------------------------------------------
         * HISTÓRICO DE TRANSFERÊNCIA (ITIL)
         * --------------------------------------------------------------
         */
        $ticket->groupHistories()->create([
            'from_group_id' => $ticket->current_group_id,
            'to_group_id' => $validated['to_group_id'],
            'changed_by' => auth()->id(),
            'note' => $validated['note'] ?? 'Encaminhamento de grupo',
        ]);

        /**
         * --------------------------------------------------------------
         * ATUALIZAR TICKET
         * --------------------------------------------------------------
         */
        $ticket->update([
            'current_group_id' => $validated['to_group_id'],
            'assigned_to' => $validated['assigned_to'] ?? null,
        ]);

        $toGroup = SupportGroup::find($validated['to_group_id']);

        $ticket->addSystemMessage(
            "Chamado encaminhado para o grupo: {$toGroup->name}"
        );

        return back()->with('success', 'Chamado encaminhado com sucesso.');
    }

    /**
     * ==============================================================
     * FILA DE CHAMADOS FECHADOS
     * ==============================================================
     */
    public function closedQueue()
    {
        $tickets = Ticket::with(['requester', 'department', 'assignedAgent'])
            ->whereIn('status', [
                TicketStatus::RESOLVED,
                TicketStatus::CLOSED,
            ])
            ->orderByDesc('resolved_at')
            ->paginate(20);

        return view('agent.queue-closed', compact('tickets'));
    }

    /**
     * ==============================================================
     * REABRIR CHAMADO (AGENT / ADMIN)
     * ==============================================================
     */
    public function reopen(Ticket $ticket)
    {
        /**
         * ----------------------------------------------------------
         * REGRA DE SEGURANÇA
         * ----------------------------------------------------------
         * Somente chamados resolvidos ou fechados podem ser reabertos
         */
        if (! in_array($ticket->status, [
            TicketStatus::RESOLVED,
            TicketStatus::CLOSED,
        ])) {
            abort(403, 'Este chamado não pode ser reaberto.');
        }

        /**
         * ----------------------------------------------------------
         * REABERTURA DO CHAMADO
         * ----------------------------------------------------------
         * - Volta para atendimento
         * - Retoma SLA
         */
        $ticket->update([
            'status' => TicketStatus::IN_PROGRESS,
            'resolved_at' => null,
            'closed_at' => null,
        ]);

        $ticket->resumeSla();

        /**
         * ----------------------------------------------------------
         * MENSAGEM DE SISTEMA
         * ----------------------------------------------------------
         */
        $ticket->addSystemMessage(
            'Chamado reaberto por '.auth()->user()->name.'.'
        );

        return redirect()
            ->route('agent.tickets.show', $ticket)
            ->with('success', 'Chamado reaberto com sucesso.');
    }
}
