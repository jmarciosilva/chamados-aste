<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

/*
|--------------------------------------------------------------------------
| Middlewares customizados da aplicação
|--------------------------------------------------------------------------
*/
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\CheckMode;

/*
|--------------------------------------------------------------------------
| BOOTSTRAP DA APLICAÇÃO (Laravel 11+)
|--------------------------------------------------------------------------
| Este arquivo é responsável por:
| - Inicializar o container da aplicação
| - Registrar rotas (web / console / health)
| - Registrar middlewares globais e aliases
| - Configurar tratamento de exceções
|
| IMPORTANTE:
| - Laravel 11 NÃO utiliza mais Kernel.php
| - Tudo que antes ficava no Kernel agora é
|   configurado fluentemente aqui
|
| OBS:
| - Agendamentos (Scheduler) ficam em:
|   routes/console.php
|--------------------------------------------------------------------------
*/
return Application::configure(
    basePath: dirname(__DIR__)
)

    /*
    |--------------------------------------------------------------------------
    | ROTAS DA APLICAÇÃO
    |--------------------------------------------------------------------------
    | web      → rotas HTTP (views, formulários, painéis)
    | commands → comandos artisan + scheduler
    | health   → endpoint de healthcheck (/up)
    |--------------------------------------------------------------------------
    */
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )

    /*
    |--------------------------------------------------------------------------
    | REGISTRO DE MIDDLEWARES
    |--------------------------------------------------------------------------
    | Aqui registramos:
    | - Middlewares globais (se necessário futuramente)
    | - Middlewares de rota (aliases)
    |--------------------------------------------------------------------------
    */
    ->withMiddleware(function (Middleware $middleware): void {

        /*
        |--------------------------------------------------------------------------
        | MIDDLEWARES DE ROTA (ALIASES)
        |--------------------------------------------------------------------------
        | Podem ser utilizados diretamente nas rotas:
        |
        | ->middleware('role:admin')
        | ->middleware('mode:agent')
        |--------------------------------------------------------------------------
        */
        $middleware->alias([

            /*
            |--------------------------------------------------------------------------
            | Controle de PAPEL do usuário
            |--------------------------------------------------------------------------
            | Exemplos:
            | - admin
            | - agent
            | - user
            |--------------------------------------------------------------------------
            */
            'role' => RoleMiddleware::class,

            /*
            |--------------------------------------------------------------------------
            | Controle de MODO de operação
            |--------------------------------------------------------------------------
            | Exemplos:
            | - admin → painel administrativo
            | - agent → atendimento / suporte
            | - user  → solicitante
            |
            | Permite:
            | - Admin atuar como agent ou user
            | - Agent não acessar admin
            |--------------------------------------------------------------------------
            */
            'mode' => CheckMode::class,
        ]);
    })

    /*
    |--------------------------------------------------------------------------
    | TRATAMENTO DE EXCEÇÕES
    |--------------------------------------------------------------------------
    | Ponto central para:
    | - Páginas 403 / 404 customizadas
    | - Logs específicos
    | - Integração com ferramentas externas
    |--------------------------------------------------------------------------
    */
    ->withExceptions(function (Exceptions $exceptions): void {
        // Customizações futuras
    })

    /*
    |--------------------------------------------------------------------------
    | FINALIZA A CRIAÇÃO DA APLICAÇÃO
    |--------------------------------------------------------------------------
    */
    ->create();
