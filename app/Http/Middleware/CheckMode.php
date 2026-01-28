<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * ============================================================================
 * CHECK MODE MIDDLEWARE
 * ============================================================================
 * Controla o CONTEXTO ATUAL do usuário (admin | agent | user)
 *
 * - Trabalha em conjunto com RoleMiddleware
 * - Usa session('mode') como fonte de verdade
 * - Inicializa o mode automaticamente se não existir
 */
class CheckMode
{
    public function handle(Request $request, Closure $next, ...$modes)
    {
        /**
         * ------------------------------------------------------------
         * INICIALIZA O MODE SE NÃO EXISTIR NA SESSÃO
         * ------------------------------------------------------------
         * Evita erro 403 no primeiro login
         */
        if (! session()->has('mode')) {
            session()->put('mode', match (auth()->user()->role) {
                'admin' => 'admin',
                'agent' => 'agent',
                default => 'user',
            });
        }

        $currentMode = session('mode');

        /**
         * ------------------------------------------------------------
         * VERIFICA SE O MODO ATUAL É PERMITIDO NA ROTA
         * ------------------------------------------------------------
         */
        if (! in_array($currentMode, $modes)) {
            abort(403, 'Acesso não autorizado para este modo.');
        }

        return $next($request);
    }
}
