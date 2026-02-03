<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\TicketQueueService;

class AdminQueueController extends Controller
{
    public function index(TicketQueueService $queueService)
    {
        return view('admin.queue', [
            'tickets' => $queueService->getQueue(),
        ]);
    }
}
