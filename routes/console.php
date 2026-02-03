<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

use App\Console\Commands\CloseResolvedTickets;

/*
|--------------------------------------------------------------------------
| ROTAS DE CONSOLE / COMMANDS (Laravel 11+)
|--------------------------------------------------------------------------
| Este arquivo é responsável por:
| - Registrar comandos artisan
| - Definir tarefas agendadas (Scheduler)
|
| IMPORTANTE:
| - Laravel 11 NÃO utiliza Console/Kernel.php
| - O agendamento agora vive aqui
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| COMANDO DE EXEMPLO (PADRÃO LARAVEL)
|--------------------------------------------------------------------------
| Apenas um comando ilustrativo, útil para testar:
| php artisan inspire
|--------------------------------------------------------------------------
*/
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| AGENDAMENTO: FECHAMENTO AUTOMÁTICO DE CHAMADOS
|--------------------------------------------------------------------------
| Regra de negócio:
| - Chamados com status RESOLVED
| - Sem interação do usuário por X dias
| - São fechados automaticamente (CLOSED)
|
| Comando responsável:
| App\Console\Commands\CloseResolvedTickets
|
| Execução:
| - Diária (uma vez por dia é suficiente)
|--------------------------------------------------------------------------
*/
Schedule::command(CloseResolvedTickets::class)
    ->daily()
    ->description('Fecha automaticamente chamados resolvidos após período sem retorno do usuário');
