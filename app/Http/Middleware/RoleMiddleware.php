<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Middleware de controle de acesso por papel (role)
     *
     * Permite:
     * - agent acessar rotas de agent
     * - admin acessar rotas de agent E admin
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!auth()->check()) {
            abort(401, 'Não autenticado.');
        }

        $user = auth()->user();

        // Usuário inativo não acessa nada
        if (!$user->is_active) {
            abort(403, 'Usuário inativo.');
        }

        /*
        |--------------------------------------------------------------
        | REGRA DE HERANÇA DE PAPEL
        |--------------------------------------------------------------
        | Admin pode acessar tudo
        | Agent acessa apenas rotas de agent
        */
        if ($user->role === 'admin') {
            return $next($request);
        }

        if ($user->role !== $role) {
            abort(403, 'Acesso não autorizado.');
        }

        return $next($request);
    }
}
