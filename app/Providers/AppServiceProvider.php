<?php

namespace App\Providers;

use App\Contracts\CasAuthInterface;
use App\Services\ProductionCasAuthService;
use App\Services\TestCasAuthService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(CasAuthInterface::class, ProductionCasAuthService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
