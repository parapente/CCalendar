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
        $this->app->bind(CasAuthInterface::class, function ($app) {
            // Check if we are running tests
            if ($app->runningUnitTests()) {
                return new TestCasAuthService();
            } else {
                // In production, use the production authentication service
                return new ProductionCasAuthService();
            }
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
