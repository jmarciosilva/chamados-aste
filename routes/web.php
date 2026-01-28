<?php

use App\Http\Controllers\Admin\DepartmentController;
/*
|--------------------------------------------------------------------------
| CONTROLLERS
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Admin\ProblemCategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\SlaController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\UserImportController;
/*
|--------------------------------------------------------------------------
| ADMIN CONTROLLERS
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\AgentSupportGroupController;
use App\Http\Controllers\AgentTicketController;
use App\Http\Controllers\SwitchModeController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketImageController;
use App\Http\Controllers\UserDashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| ROTA PRINCIPAL
|--------------------------------------------------------------------------
| - Usuário autenticado → dashboard global
| - Visitante → login
*/
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : view('auth.login');
});

/*
|--------------------------------------------------------------------------
| DASHBOARD GLOBAL (POR ROLE)
|--------------------------------------------------------------------------
| IMPORTANTE:
| - Baseado SOMENTE no role do usuário
| - Não depende do mode (contexto)
*/
Route::get('/dashboard', function () {

    if (! auth()->check()) {
        return redirect()->route('login');
    }

    return match (auth()->user()->role) {
        'admin' => redirect()->route('admin.dashboard'),
        'agent' => redirect()->route('agent.queue'),
        default => redirect()->route('user.home'),
    };

})->middleware('auth')->name('dashboard');

/*
|--------------------------------------------------------------------------
| ROTA GLOBAL DE UPLOAD DE IMAGEM (CTRL+V)
|--------------------------------------------------------------------------
| ✔ Usada por usuário e operador
| ✔ Evita duplicação de código
| ✔ Protegida por autenticação
*/
Route::post('/tickets/upload-image',
    [TicketImageController::class, 'upload']
)->middleware('auth')
    ->name('tickets.upload-image');

/*
|--------------------------------------------------------------------------
| TROCA DE MODO (CONTEXT SWITCH)
|--------------------------------------------------------------------------
| Controla o CONTEXTO ATIVO do usuário
| Ex:
| - Admin → Admin / Agent / User
| - Agent → Agent
| - User → User
*/
Route::middleware('auth')->group(function () {
    Route::post('/switch-mode/{mode}', [SwitchModeController::class, 'switch'])
        ->name('switch.mode');
});

/*
|--------------------------------------------------------------------------
| ROTAS PROTEGIDAS (AUTH)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | PORTAL DO USUÁRIO (SOLICITANTE)
    |--------------------------------------------------------------------------
    | Regras:
    | - Qualquer usuário autenticado
    | - Somente quando mode:user
    */
    Route::prefix('user')
        ->name('user.')
         ->middleware(['role:user,admin', 'mode:user'])
        ->group(function () {

            Route::get('/', [UserDashboardController::class, 'index'])
                ->name('home');

            Route::get('/tickets', [TicketController::class, 'index'])
                ->name('tickets.index');

            Route::get('/tickets/create', [TicketController::class, 'create'])
                ->name('tickets.create');

            Route::post('/tickets', [TicketController::class, 'store'])
                ->name('tickets.store');

            Route::get('/tickets/{ticket}', [TicketController::class, 'show'])
                ->name('tickets.show');

            // Resposta do usuário ao operador
            Route::post('/tickets/{ticket}/reply',
                [TicketController::class, 'reply']
            )->name('tickets.reply');

            // Upload de imagens (CKEditor / Tiptap)
            Route::post('/tickets/upload-image', [TicketController::class, 'uploadImage'])
                ->name('tickets.upload-image');

            Route::get('/knowledge-base', fn () => view('user.knowledge-base.index'))
                ->name('knowledge-base');
        });

    /*
    |--------------------------------------------------------------------------
    | PORTAL DO AGENTE / SUPORTE
    |--------------------------------------------------------------------------
    | Regras:
    | - Agent sempre pode
    | - Admin SOMENTE se estiver em mode:agent
    */
    Route::prefix('agent')
        ->name('agent.')
        ->middleware(['role:agent,admin', 'mode:agent'])
        ->group(function () {

            Route::get('/queue', [AgentTicketController::class, 'queue'])
                ->name('queue');

            Route::get('/queue/closed', [AgentTicketController::class, 'closedQueue'])
                ->name('queue.closed');

            Route::get('/ticket/{ticket}', [AgentTicketController::class, 'show'])
                ->name('tickets.show');

            Route::post('/ticket/{ticket}/take', [AgentTicketController::class, 'take'])
                ->name('tickets.take');

            Route::patch('/ticket/{ticket}', [AgentTicketController::class, 'update'])
                ->name('tickets.update');

            // Upload de imagens (Editor do operador)
            Route::post('/tickets/upload-image', [AgentTicketController::class, 'uploadImage'])
                ->name('tickets.upload-image');

            /*
            |--------------------------------------------------------------------------
            | GRUPOS DE SUPORTE
            |--------------------------------------------------------------------------
            */
            Route::resource('support-groups', AgentSupportGroupController::class)
                ->except(['show', 'destroy']);

            // Encaminhar chamado para grupo de suporte ou especialista
            Route::post('/ticket/{ticket}/forward',
                [AgentTicketController::class, 'forward']
            )->name('tickets.forward');

        });

    /*
    |--------------------------------------------------------------------------
    | PORTAL ADMINISTRATIVO
    |--------------------------------------------------------------------------
    | Regras:
    | - APENAS role:admin
    | - SOMENTE quando mode:admin
    */
    Route::prefix('admin')
        ->name('admin.')
        ->middleware(['role:admin', 'mode:admin'])
        ->group(function () {

            /*
            |--------------------------------------------------------------------------
            | DASHBOARD ADMIN
            |--------------------------------------------------------------------------
            */
            Route::get('/', fn () => view('admin.dashboard'))
                ->name('dashboard');

            /*
            |--------------------------------------------------------------------------
            | USUÁRIOS
            |--------------------------------------------------------------------------
            */
            Route::resource('users', UserController::class)
                ->except(['show']);

            Route::patch('/users/{user}/toggle-status',
                [UserController::class, 'toggleStatus']
            )->name('users.toggle-status');

            /*
            |--------------------------------------------------------------------------
            | IMPORTAÇÃO DE USUÁRIOS
            |--------------------------------------------------------------------------
            */
            Route::get('/users/import/template',
                [UserImportController::class, 'downloadTemplate']
            )->name('users.import.template');

            Route::post('/users/import/preview',
                [UserImportController::class, 'preview']
            )->name('users.import.preview');

            Route::post('/users/import/confirm',
                [UserImportController::class, 'confirm']
            )->name('users.import.confirm');

            /*
            |--------------------------------------------------------------------------
            | DEPARTAMENTOS
            |--------------------------------------------------------------------------
            */
            Route::resource('departments', DepartmentController::class)
                ->except(['show']);

            Route::patch('/departments/{department}/toggle-status',
                [DepartmentController::class, 'toggleStatus']
            )->name('departments.toggle-status');

            /*
            |--------------------------------------------------------------------------
            | SLA (SERVICE LEVEL AGREEMENT)
            |--------------------------------------------------------------------------
            */
            Route::resource('slas', SlaController::class)
                ->except(['show']);

            Route::patch('/slas/{sla}/toggle-status',
                [SlaController::class, 'toggleStatus']
            )->name('slas.toggle-status');

            /*
            |--------------------------------------------------------------------------
            | CATEGORIAS DE PROBLEMAS (NOVO CRUD)
            |--------------------------------------------------------------------------
            | - Base para abertura de chamados
            | - Define prioridade padrão
            | - Relacionável a produto e SLA
            */
            Route::resource('problem-categories', ProblemCategoryController::class)
                ->except(['show']);

            Route::resource('products', ProductController::class)
                ->except(['show']);

        });
});

/*
|--------------------------------------------------------------------------
| ROTAS DE AUTENTICAÇÃO (BREEZE)
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';
