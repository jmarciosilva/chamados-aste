<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * ------------------------------------------------------------------
     * EXIBE A TELA DE LOGIN
     * ------------------------------------------------------------------
     * Apenas retorna a view de autenticação.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * ------------------------------------------------------------------
     * PROCESSA O LOGIN DO USUÁRIO
     * ------------------------------------------------------------------
     * Responsável por:
     *  - Validar credenciais
     *  - Autenticar usuário
     *  - Bloquear acesso de usuários inativos
     *  - Redirecionar conforme perfil (role)
     *  - Tratar exceções de forma segura
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        /**
         * ----------------------------------------------------------
         * DEFINE O MODE INICIAL NO LOGIN
         * ----------------------------------------------------------
         */
        $request->session()->put('mode', match (auth()->user()->role) {
            'admin' => 'admin',
            'agent' => 'agent',
            default => 'user',
        });

        $user = auth()->user();

        // Bloqueia usuário inativo
        if (! $user || ! $user->is_active) {
            Auth::logout();

            return redirect()->route('login')->withErrors([
                'email' => 'Seu usuário está inativo.',
            ]);
        }

        // Redirecionamento por perfil
        return match ($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'agent' => redirect()->route('agent.queue'),
            default => redirect()->route('user.home'),
        };
    }

    /**
     * ------------------------------------------------------------------
     * FINALIZA A SESSÃO DO USUÁRIO (LOGOUT)
     * ------------------------------------------------------------------
     * Responsável por:
     *  - Encerrar autenticação
     *  - Invalidar sessão
     *  - Regenerar token CSRF
     */
    public function destroy(Request $request): RedirectResponse
    {
        /*
        |--------------------------------------------------------------------------
        | 1. LOGOUT
        |--------------------------------------------------------------------------
        | Encerra a sessão do usuário no guard "web".
        */
        Auth::guard('web')->logout();

        /*
        |--------------------------------------------------------------------------
        | 2. INVALIDAÇÃO DE SESSÃO
        |--------------------------------------------------------------------------
        | Remove todos os dados da sessão atual.
        */
        $request->session()->invalidate();

        /*
        |--------------------------------------------------------------------------
        | 3. NOVO TOKEN CSRF
        |--------------------------------------------------------------------------
        | Gera um novo token para evitar reutilização maliciosa.
        */
        $request->session()->regenerateToken();

        /*
        |--------------------------------------------------------------------------
        | 4. REDIRECIONAMENTO FINAL
        |--------------------------------------------------------------------------
        | Envia o usuário para a tela inicial (login).
        */
        return redirect('/');
    }
}
