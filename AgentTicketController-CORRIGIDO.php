<?php

namespace App\Http\Controllers\Agent;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Group;
use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\TicketGroupHistory;
use App\Models\TicketMessage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;

class AgentTicketController extends Controller
{
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
            'messages' => function ($query) {
                $query->orderBy('created_at', 'asc');
            },
            'messages.user',
            'messages.attachments',
            'requester',
            'department',
            'assignedAgent',
            'groupHistories.fromGroup',
            'groupHistories.toGroup',
            'groupHistories.user',
        ]);

        /**
         * --------------------------------------------------------------
         * DEBUG: Verificar se mensagens estão sendo carregadas
         * --------------------------------------------------------------
         */
        Log::info('AgentTicketController@show - Ticket ID: ' . $ticket->id);
        Log::info('AgentTicketController@show - Total de mensagens: ' . $ticket->messages->count());
        
        // Se não tem mensagens, vamos verificar direto no banco
        if ($ticket->messages->count() === 0) {
            $directMessages = DB::table('ticket_messages')
                ->where('ticket_id', $ticket->id)
                ->get();
            Log::warning('Mensagens direto do banco: ' . $directMessages->count());
        }

        /**
         * --------------------------------------------------------------
         * TIMELINE UNIFICADA
         * - Mensagens
         * - Transferências de grupo
         * --------------------------------------------------------------
         */
        $timeline = collect();

        // ✅ MENSAGENS - Garantindo que funcionará
        foreach ($ticket->messages as $message) {
            $timeline->push([
                'type'        => 'message',
                
                // Identidade
                'user_id'     => $message->user_id,
                'user'        => $message->user->name ?? 'Sistema',
                'is_internal' => (bool) $message->is_internal_note,
                
                // Conteúdo
                'content'     => $message->message,
                'created_at'  => $message->created_at,
                
                // Anexos
                'attachments' => $message->attachments ?? collect(),
            ]);
        }

        Log::info('Timeline - Total de mensagens adicionadas: ' . $timeline->where('type', 'message')->count());

        // Transferências de grupo (Escalonamento ITIL)
        foreach ($ticket->groupHistories as $history) {
            if (! $history->from_group_id) {
                continue;
            }

            $timeline->push([
                'type'       => 'group_transfer',
                'user'       => $history->user->name ?? 'Sistema',
                'from_group' => $history->fromGroup?->name ?? '—',
                'to_group'   => $history->toGroup?->name ?? '—',
                'note'       => $history->note,
                'created_at' => $history->created_at,
                'attachments' => collect(),
            ]);
        }

        $timeline = $timeline->sortBy('created_at');

        Log::info('Timeline FINAL - Total de itens: ' . $timeline->count());

        /**
         * --------------------------------------------------------------
         * SLA (Service Level Agreement)
         * --------------------------------------------------------------
         */
        $slaHours = match ($ticket->priority) {
            TicketPriority::LOW    => 72,
            TicketPriority::MEDIUM => 48,
            TicketPriority::HIGH   => 24,
            TicketPriority::URGENT => 4,
        };

        $now = now();
        $slaDeadline = $ticket->created_at->addHours($slaHours);
        $remaining = $now->diffInHours($slaDeadline, false);

        if ($remaining < 0) {
            $sla = [
                'label' => 'SLA EXPIRADO há ' . abs($remaining) . 'h',
                'color' => 'bg-red-100 text-red-800 border border-red-300',
            ];
        } elseif ($remaining <= 4) {
            $sla = [
                'label' => 'SLA crítico: ' . $remaining . 'h restantes',
                'color' => 'bg-orange-100 text-orange-800 border border-orange-300',
            ];
        } else {
            $sla = [
                'label' => 'SLA OK: ' . $remaining . 'h restantes',
                'color' => 'bg-green-100 text-green-800 border border-green-300',
            ];
        }

        /**
         * --------------------------------------------------------------
         * GRUPOS
         * --------------------------------------------------------------
         */
        $groups = Group::orderBy('name')->get();

        return view('agent.tickets.show', compact('ticket', 'timeline', 'sla', 'groups'));
    }

    // ... resto dos métodos do controller ...
}
