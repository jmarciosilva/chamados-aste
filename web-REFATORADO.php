<?php

use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\ProblemCategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\SlaController;
use App\Http\Controllers\Admin\SupportGroupController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\UserImportController;
use App\Http\Controllers\Agent\AgentTicketController;
use App\Http\Controllers\AgentSupportGroupController;
use App\Http\Controllers\SwitchModeController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketImageController;
use App\Http\Controllers\UserDashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| ROTA RAIZ
|--------------------------------------------------------------------------
| Redireciona usuários autenticados para o dashboard apropriado
| Visitantes são direcionados para a tela de login
*/
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| DASHBOARD GLOBAL
|--------------------------------------------------------------------------
| Redireciona automaticamente para o dashboard correto baseado no role
*/
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return match (auth()->user()->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'agent' => redirect()->route('agent.queue'),
            default => redirect()->route('user.home'),
        };
    })->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| TROCA DE CONTEXTO (MODE SWITCHING)
|--------------------------------------------------------------------------
| Permite que usuários alternem entre diferentes modos de operação
| - Admin: pode acessar admin, agent e user
| - Agent: pode acessar apenas agent
| - User: pode acessar apenas user
*/
Route::middleware('auth')->group(function () {
    // POST (recomendado para produção)
    Route::post('/switch-mode/{mode}', [SwitchModeController::class, 'switch'])
        ->name('switch.mode');

    // GET (temporário para desenvolvimento - remover em produção)
    Route::get('/switch-mode/{mode}', function (string $mode) {
        $user = auth()->user();

        $allowedModes = match ($user->role) {
            'admin' => ['admin', 'agent', 'user'],
            'agent' => ['agent'],
            default => ['user'],
        };

        if (! in_array($mode, $allowedModes)) {
            abort(403, 'Modo não permitido para este usuário.');
        }

        session()->put('mode', $mode);

        return match ($mode) {
            'admin' => redirect()->route('admin.dashboard'),
            'agent' => redirect()->route('agent.queue'),
            default => redirect()->route('user.home'),
        };
    })->name('switch.mode.get');
});

/*
|--------------------------------------------------------------------------
| UPLOAD GLOBAL DE IMAGENS
|--------------------------------------------------------------------------
| Endpoint compartilhado para upload de imagens via Ctrl+V
*/
Route::post('/tickets/upload-image', [TicketImageController::class, 'upload'])
    ->middleware('auth')
    ->name('tickets.upload-image');

/*
|--------------------------------------------------------------------------
| PORTAL DO USUÁRIO (SOLICITANTE)
|--------------------------------------------------------------------------
| Área para solicitantes abrirem e acompanharem chamados
*/
Route::prefix('user')
    ->name('user.')
    ->middleware(['auth', 'role:user,admin', 'mode:user'])
    ->group(function () {
        // Dashboard do usuário
        Route::get('/', [UserDashboardController::class, 'index'])
            ->name('home');

        // Gestão de chamados
        Route::get('/tickets', [TicketController::class, 'index'])
            ->name('tickets.index');

        Route::get('/tickets/create', [TicketController::class, 'create'])
            ->name('tickets.create');

        Route::post('/tickets', [TicketController::class, 'store'])
            ->name('tickets.store');

        Route::get('/tickets/{ticket}', [TicketController::class, 'show'])
            ->name('tickets.show');

        Route::post('/tickets/{ticket}/reply', [TicketController::class, 'reply'])
            ->name('tickets.reply');

        // Base de conhecimento
        Route::get('/knowledge-base', fn() => view('user.knowledge-base.index'))
            ->name('knowledge-base');
    });

/*
|--------------------------------------------------------------------------
| PORTAL DO AGENTE (SUPORTE)
|--------------------------------------------------------------------------
| Área de atendimento e gestão de chamados pelos operadores
*/
Route::prefix('agent')
    ->name('agent.')
    ->middleware(['auth', 'role:agent,admin', 'mode:agent'])
    ->group(function () {
        // Filas de atendimento
        Route::get('/queue', [AgentTicketController::class, 'queue'])
            ->name('queue');

        Route::get('/queue/closed', [AgentTicketController::class, 'closedQueue'])
            ->name('queue.closed');

        // Gestão de chamados
        Route::get('/ticket/{ticket}', [AgentTicketController::class, 'show'])
            ->name('tickets.show');

        Route::post('/ticket/{ticket}/take', [AgentTicketController::class, 'take'])
            ->name('tickets.take');

        Route::patch('/ticket/{ticket}', [AgentTicketController::class, 'update'])
            ->name('tickets.update');

        Route::post('/ticket/{ticket}/forward', [AgentTicketController::class, 'forward'])
            ->name('tickets.forward');

        // Grupos de suporte (visão do agente)
        Route::resource('support-groups', AgentSupportGroupController::class)
            ->except(['show', 'destroy']);
    });

/*
|--------------------------------------------------------------------------
| PORTAL ADMINISTRATIVO
|--------------------------------------------------------------------------
| Área de configuração e gestão do sistema
*/
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:admin', 'mode:admin'])
    ->group(function () {
        // Dashboard administrativo
        Route::get('/', fn() => view('admin.dashboard'))
            ->name('dashboard');

        /*
        |----------------------------------------------------------------------
        | USUÁRIOS
        |----------------------------------------------------------------------
        */
        Route::resource('users', UserController::class)->except(['show']);

        Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])
            ->name('users.toggle-status');

        // Importação de usuários
        Route::prefix('users/import')->name('users.import.')->group(function () {
            Route::get('/template', [UserImportController::class, 'downloadTemplate'])
                ->name('template');

            Route::post('/preview', [UserImportController::class, 'preview'])
                ->name('preview');

            Route::post('/confirm', [UserImportController::class, 'confirm'])
                ->name('confirm');
        });

        /*
        |----------------------------------------------------------------------
        | DEPARTAMENTOS
        |----------------------------------------------------------------------
        */
        Route::resource('departments', DepartmentController::class)->except(['show']);

        Route::patch('/departments/{department}/toggle-status', [DepartmentController::class, 'toggleStatus'])
            ->name('departments.toggle-status');

        /*
        |----------------------------------------------------------------------
        | GRUPOS DE ATENDIMENTO
        |----------------------------------------------------------------------
        */
        Route::resource('support-groups', SupportGroupController::class)->except(['show']);

        /*
        |----------------------------------------------------------------------
        | SLA (SERVICE LEVEL AGREEMENT)
        |----------------------------------------------------------------------
        */
        Route::resource('slas', SlaController::class)->except(['show']);

        Route::patch('/slas/{sla}/toggle-status', [SlaController::class, 'toggleStatus'])
            ->name('slas.toggle-status');

        /*
        |----------------------------------------------------------------------
        | CATEGORIAS DE PROBLEMAS
        |----------------------------------------------------------------------
        */
        Route::resource('problem-categories', ProblemCategoryController::class)->except(['show']);

        /*
        |----------------------------------------------------------------------
        | PRODUTOS
        |----------------------------------------------------------------------
        */
        Route::resource('products', ProductController::class)->except(['show']);
    });

/*
|--------------------------------------------------------------------------
| ROTAS DE AUTENTICAÇÃO
|--------------------------------------------------------------------------
| Gerenciadas pelo Laravel Breeze
*/
require __DIR__.'/auth.php';
