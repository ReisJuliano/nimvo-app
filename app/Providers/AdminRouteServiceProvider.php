<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class AdminRouteServiceProvider extends ServiceProvider
{
    /**
     * Dominio central do painel admin.
     * Altere aqui se seu domínio mudar.
     */
    protected string $adminDomain = 'admin.nimvo.com.br';

    public function boot(): void
    {
        $this->routes(function () {
            Route::domain($this->adminDomain)
                ->middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/admin.php'));
        });
    }
}
