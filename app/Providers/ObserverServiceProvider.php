<?php

namespace App\Providers;

use App\Services\Observer\ObserverService;
use Illuminate\Support\ServiceProvider;

class ObserverServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(ObserverService::class, function ($app) {
            return new ObserverService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        
        $observerService = $this->app->make(ObserverService::class);
        $observerService->registerObservers('admin');
    }
}