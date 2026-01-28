<?php

namespace App\Http\Controllers;

use App\Models\Ticket;

class UserDashboardController extends Controller
{
    /**
     * Dashboard do usuário solicitante
     */
    public function index()
    {
        $userId = auth()->id();

        /*
        |--------------------------------------------------------------------------
        | KPIs DO USUÁRIO
        |--------------------------------------------------------------------------
        */
        $openTickets = Ticket::where('requester_id', $userId)
            ->whereIn('status', ['open', 'in_progress'])
            ->count();

        $waitingTickets = Ticket::where('requester_id', $userId)
            ->where('status', 'in_progress')
            ->count();

        $resolvedThisMonth = Ticket::where('requester_id', $userId)
            ->where('status', 'resolved')
            ->whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', now()->year)
            ->count();

        /*
        |--------------------------------------------------------------------------
        | CHAMADOS RECENTES (últimos 5)
        |--------------------------------------------------------------------------
        */
        $recentTickets = Ticket::where('requester_id', $userId)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('user.dashboard', compact(
            'openTickets',
            'waitingTickets',
            'resolvedThisMonth',
            'recentTickets'
        ));
    }
}
