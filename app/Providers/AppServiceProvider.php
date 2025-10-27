<?php

namespace App\Providers;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;


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
    public function boot(Request $request): void
    {

        if ($request->header('X-Authorization') && !$request->header('Authorization')) {
            $request->headers->set('Authorization', $request->header('X-Authorization'));
        }

    }

    


    
}