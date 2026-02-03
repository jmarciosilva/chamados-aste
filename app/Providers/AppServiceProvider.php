<?php

namespace App\Providers;

use Illuminate\Support\Carbon;
use Illuminate\Support\ServiceProvider;
use App\Console\Commands\CloseResolvedTickets;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Carbon::setLocale('pt_BR');
         /**
         * ----------------------------------------------------------
         * REGISTRO DE COMMANDS (Laravel 11)
         * ----------------------------------------------------------
         */
        if ($this->app->runningInConsole()) {
            $this->commands([
                CloseResolvedTickets::class,
            ]);
        }
    }

}
