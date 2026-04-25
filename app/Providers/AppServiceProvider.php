<?php

namespace App\Providers;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Services\Ecommerce\Order\OrderStrategyResolver;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // OrderStrategyResolver is simple and auto-resolvable — no manual binding required
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(Request $request): void
    {
         ini_set('serialize_precision', 14);

        if ($request->header('X-Authorization') && !$request->header('Authorization')) {
            $request->headers->set('Authorization', $request->header('X-Authorization'));
        }

    }

    


    
}