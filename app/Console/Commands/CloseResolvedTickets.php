<?php

namespace App\Console\Commands;

use App\Enums\TicketStatus;
use App\Models\Ticket;
use Illuminate\Console\Command;

class CloseResolvedTickets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:close-resolved-tickets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = config('tickets.auto_close_days', 5);

        $tickets = Ticket::where('status', TicketStatus::RESOLVED)
            ->where('resolved_at', '<=', now()->subDays($days))
            ->get();

        foreach ($tickets as $ticket) {
            $ticket->update([
                'status' => TicketStatus::CLOSED,
                'closed_at' => now(),
            ]);

            $ticket->addSystemMessage(
                'Chamado fechado automaticamente após período sem retorno do usuário.'
            );
        }

        $this->info("{$tickets->count()} chamados fechados automaticamente.");
    }
}
