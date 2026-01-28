<?php

namespace App\Http\Controllers;

use App\Enums\Priority;
use App\Enums\TicketStatus;
use App\Models\SupportGroup;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;

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
    public function queue()
    {
        $user = auth()->user();

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

            $resolvedTodayCount = Ticket::where('status', TicketStatus::CLOSED)
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
     * VISUALIZAÇÃO DO CHAMADO (AGENT)
     * - Timeline unificada (ITIL v4)
     * ==============================================================
     */
    public function show(Ticket $ticket)
    {
        /**
         * --------------------------------------------------------------
         * Carregamento completo do contexto do chamado
         * --------------------------------------------------------------
         */
        $ticket->load([
            'messages.user',
            'attachments',
            'requester',
            'department',
            'groupHistories.fromGroup',
            'groupHistories.toGroup',
            'groupHistories.user',
        ]);

        /**
         * --------------------------------------------------------------
         * TIMELINE UNIFICADA
         * - Mensagens
         * - Transferências de grupo
         * --------------------------------------------------------------
         */
        $timeline = collect();
        $attachments = $ticket->attachments->sortBy('created_at');

        // Mensagens
        foreach ($ticket->messages as $message) {

            // Anexos próximos à mensagem (janela de 10s)
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

        // Transferências de grupo (Escalonamento ITIL)
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

        /**
         * --------------------------------------------------------------
         * Grupos disponíveis para encaminhamento
         * --------------------------------------------------------------
         */
        $groups = SupportGroup::where('is_active', true)
            ->where('id', '!=', $ticket->current_group_id)
            ->orderBy('name')
            ->get();

        /**
         * --------------------------------------------------------------
         * Especialistas do grupo atual
         * --------------------------------------------------------------
         */
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

        /**
         * --------------------------------------------------------------
         * SLA (badge pronto para UI)
         * --------------------------------------------------------------
         */
        $sla = $ticket->slaBadge();

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
     * ATENDIMENTO DO CHAMADO
     * ==============================================================
     */
    public function update(Request $request, Ticket $ticket)
    {
        if ($ticket->status === TicketStatus::CLOSED) {
            return back()->withErrors(
                'Chamados fechados não podem ser alterados.'
            );
        }

        /**
         * --------------------------------------------------------------
         * Validação
         * --------------------------------------------------------------
         */
        $validated = $request->validate([
            'message' => 'nullable|string',
            'message_type' => 'required|in:user,internal,closing',
            'status' => 'required|in:in_progress,resolved',
            'priority' => 'required|in:low,medium,high,critical',
            'attachments.*' => 'nullable|file|max:51200',
        ]);

        /**
         * --------------------------------------------------------------
         * 1️⃣ Registro da mensagem
         * --------------------------------------------------------------
         */
        if (! empty(trim($validated['message'] ?? ''))) {

            $isInternal = $validated['message_type'] === 'internal';

            $ticket->messages()->create([
                'user_id' => auth()->id(),
                'message' => $validated['message'],
                'is_internal_note' => $isInternal,
            ]);

            /**
             * Pausa SLA SOMENTE quando operador fala com o usuário
             */
            if ($validated['message_type'] === 'user') {
                $ticket->pauseSla();
            }
        }

        /**
         * --------------------------------------------------------------
         * 2️⃣ Upload de anexos
         * --------------------------------------------------------------
         */
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {

                $path = $file->store("tickets/{$ticket->id}", 'public');

                $ticket->attachments()->create([
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
         * 3️⃣ Status / Prioridade
         * --------------------------------------------------------------
         */
        if ($validated['status'] === 'resolved') {

            // Mensagem automática de encerramento
            if ($validated['message_type'] !== 'internal') {
                $ticket->addSystemMessage(
                    'Seu chamado foi finalizado e marcado como resolvido.'
                );
            }

            $ticket->update([
                'status' => TicketStatus::CLOSED,
                'priority' => Priority::from($validated['priority']),
                'resolved_at' => now(),
            ]);

        } else {

            $ticket->update([
                'status' => TicketStatus::IN_PROGRESS,
                'priority' => Priority::from($validated['priority']),
                'assigned_to' => $ticket->assigned_to ?? auth()->id(),
            ]);
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
        if ($ticket->assigned_to) {
            return back()->with(
                'error',
                'Este chamado já está em atendimento.'
            );
        }

        $ticket->update([
            'assigned_to' => auth()->id(),
            'status' => TicketStatus::IN_PROGRESS,
        ]);

        $ticket->addSystemMessage(
            'Seu chamado foi assumido por um operador e o atendimento foi iniciado.'
        );

        return redirect()->route('agent.tickets.show', $ticket);
    }

    /**
     * ==============================================================
     * FILA DE CHAMADOS FECHADOS
     * ==============================================================
     */
    public function closedQueue()
    {
        $tickets = Ticket::with([
            'requester',
            'department',
            'assignedAgent',
        ])
            ->where('status', TicketStatus::CLOSED)
            ->orderByDesc('resolved_at')
            ->paginate(20);

        return view('agent.queue-closed', compact('tickets'));
    }

    /**
     * ==============================================================
     * UPLOAD DE IMAGEM (EDITOR DO OPERADOR)
     * ==============================================================
     */
    public function uploadImage(Request $request)
    {
        $request->validate([
            'upload' => 'required|image|max:51200',
        ]);

        $path = $request->file('upload')
            ->store('tickets/temp', 'public');

        return response()->json([
            'url' => asset('storage/'.$path),
        ]);
    }

    /**
     * ==============================================================
     * ENCAMINHAR CHAMADO (GRUPO E/OU ESPECIALISTA)
     * ==============================================================
     */
    public function forward(Request $request, Ticket $ticket)
    {
        if ($ticket->status === TicketStatus::CLOSED) {
            return back()->withErrors(
                'Chamado fechado não pode ser encaminhado.'
            );
        }

        $validated = $request->validate([
            'to_group_id' => 'nullable|exists:support_groups,id',
            'assigned_to' => 'nullable|exists:users,id',
            'note' => 'nullable|string|max:1000',
        ]);

        /**
         * ----------------------------------------------------------
         * Resolver grupo destino
         * ----------------------------------------------------------
         */
        $toGroupId = $validated['to_group_id'] ?? $ticket->current_group_id;
        $toGroup = $toGroupId ? SupportGroup::find($toGroupId) : null;

        /**
         * ----------------------------------------------------------
         * Resolver especialista (se houver)
         * ----------------------------------------------------------
         */
        $specialist = null;

        if (! empty($validated['assigned_to'])) {

            $specialist = User::where('id', $validated['assigned_to'])
                ->where('role', 'agent')
                ->where('agent_type', 'specialist')
                ->firstOrFail();

            // Se grupo não foi informado, usar grupo do especialista
            if (! $toGroup) {
                $toGroup = $specialist->supportGroups()->first();
                $toGroupId = $toGroup?->id;
            }

            // Validação ITIL: especialista precisa pertencer ao grupo
            if ($toGroup && ! $specialist->supportGroups()
                ->where('support_groups.id', $toGroup->id)
                ->exists()) {

                return back()->withErrors(
                    'O especialista selecionado não pertence ao grupo informado.'
                );
            }
        }

        /**
         * ----------------------------------------------------------
         * Histórico de grupo (se mudou)
         * ----------------------------------------------------------
         */
        if ($toGroupId && $toGroupId !== $ticket->current_group_id) {

            $ticket->groupHistories()->create([
                'from_group_id' => $ticket->current_group_id,
                'to_group_id' => $toGroupId,
                'changed_by' => auth()->id(),
                'note' => $validated['note'],
            ]);

            $ticket->update([
                'current_group_id' => $toGroupId,
            ]);

            $ticket->addSystemMessage(
                "Seu chamado foi encaminhado para o grupo '{$toGroup->name}'."
            );
        }

        /**
         * ----------------------------------------------------------
         * Atribuição ao especialista
         * ----------------------------------------------------------
         */
        if ($specialist) {

            $ticket->update([
                'assigned_to' => $specialist->id,
                'status' => TicketStatus::IN_PROGRESS,
            ]);

            $ticket->addSystemMessage(
                "Seu chamado foi atribuído ao especialista {$specialist->name}."
            );
        }

        return back()->with('success', 'Chamado encaminhado com sucesso.');
    }
}
