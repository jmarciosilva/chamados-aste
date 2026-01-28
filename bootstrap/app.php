<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

/**
 * Middlewares customizados da aplicação
 */
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\CheckMode;

/**
 * ============================================================================
 * BOOTSTRAP DA APLICAÇÃO
 * ============================================================================
 * Este arquivo é responsável por:
 * - Inicializar o Laravel
 * - Registrar rotas
 * - Registrar middlewares globais e de rota
 * - Configurar tratamento de exceções
 *
 * IMPORTANTE:
 * - NÃO é o Kernel (Laravel 11+ não usa Kernel.php)
 * - Tudo que antes ficava no Kernel agora fica aqui
 */
return Application::configure(
    basePath: dirname(__DIR__)
)

    /**
     * ------------------------------------------------------------------------
     * ROTAS DA APLICAÇÃO
     * ------------------------------------------------------------------------
     * web      → rotas HTTP (views, formulários, painéis)
     * console  → comandos artisan
     * health   → endpoint de healthcheck (/up)
     */
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )

    /**
     * ------------------------------------------------------------------------
     * REGISTRO DE MIDDLEWARES
     * ------------------------------------------------------------------------
     * Aqui registramos:
     * - Middlewares de rota (alias)
     * - Middlewares globais (se necessário no futuro)
     */
    ->withMiddleware(function (Middleware $middleware): void {

        /**
         * ============================================================
         * MIDDLEWARES DE ROTA (ALIASES)
         * ============================================================
         *
         * Esses middlewares podem ser usados nas rotas via:
         *
         * ->middleware('role:admin')
         * ->middleware('mode:admin')
         */

        $middleware->alias([

            /**
             * --------------------------------------------------------
             * Controle de PAPEL do usuário
             * --------------------------------------------------------
             * Exemplo:
             * - admin
             * - agent
             * - user
             */
            'role' => RoleMiddleware::class,

            /**
             * --------------------------------------------------------
             * Controle de MODO de operação
             * --------------------------------------------------------
             * Exemplo:
             * - admin (painel administrativo)
             * - agent (operador / atendimento)
             * - user  (solicitante)
             *
             * Usado para permitir que:
             * - Admin atue como agent ou user
             * - Agent NÃO acesse admin
             */
            'mode' => CheckMode::class,
        ]);
    })

    /**
     * ------------------------------------------------------------------------
     * TRATAMENTO DE EXCEÇÕES
     * ------------------------------------------------------------------------
     * Pode ser customizado futuramente para:
     * - Páginas 403 personalizadas
     * - Logs específicos
     * - Integração com Sentry, Bugsnag, etc.
     */
    ->withExceptions(function (Exceptions $exceptions): void {
        // Customizações futuras
    })

    /**
     * ------------------------------------------------------------------------
     * FINALIZA A CRIAÇÃO DA APLICAÇÃO
     * ------------------------------------------------------------------------
     */
    ->create();
