<?php

namespace App\Http\Controllers;

class SwitchModeController extends Controller
{
    /**
     * ============================================================
     * ALTERA O MODO DE OPERAÇÃO DO USUÁRIO
     * ============================================================
     * - Admin → admin | agent | user
     * - Agent → agent
     * - User  → user
     */
    public function switch(string $mode)
    {
        $user = auth()->user();

        /**
         * ------------------------------------------------------------
         * MAPA DE MODOS PERMITIDOS POR ROLE
         * ------------------------------------------------------------
         */
        $allowedModes = match ($user->role) {
            'admin' => ['admin', 'agent', 'user'],
            'agent' => ['agent'],
            default => ['user'],
        };

        if (! in_array($mode, $allowedModes)) {
            abort(403, 'Modo não permitido.');
        }

        /**
         * ------------------------------------------------------------
         * SALVA O MODE NA SESSÃO
         * ------------------------------------------------------------
         */
        session()->put('mode', $mode);

        /**
         * ------------------------------------------------------------
         * REDIRECIONAMENTO CONFORME CONTEXTO
         * ------------------------------------------------------------
         */
        return match ($mode) {
            'admin' => redirect()->route('admin.dashboard'),
            'agent' => redirect()->route('agent.queue'),
            default => redirect()->route('user.home'),
        };
    }
}
