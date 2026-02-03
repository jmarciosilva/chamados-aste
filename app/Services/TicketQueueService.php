<?php

namespace App\Services;

use App\Models\Ticket;
use App\Enums\TicketStatus;

class TicketQueueService
{
    public function getQueue()
    {
        return Ticket::with([
                'requester',
                'department',
            ])
            ->whereIn('status', [
                TicketStatus::OPEN,
                TicketStatus::IN_PROGRESS,
                TicketStatus::WAITING_USER,
            ])
            ->orderByRaw("
                CASE status
                    WHEN 'open' THEN 1
                    WHEN 'in_progress' THEN 2
                    WHEN 'waiting_user' THEN 3
                END
            ")
            ->orderBy('created_at')
            ->paginate(10);
    }
}
