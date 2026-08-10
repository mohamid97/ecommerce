<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // OTP endpoints: tight limit per IP + email to prevent email bombing & brute-force
        RateLimiter::for('otp', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip() . '|' . $request->input('email'));
        });

        // Login: limit per IP + email to prevent password brute-force
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip() . '|' . $request->input('email'));
        });

        // Register: limit per IP to prevent mass account creation / spam
        RateLimiter::for('register', function (Request $request) {
            return Limit::perMinute(15)->by($request->ip());
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api/admin')
                ->namespace('App\Http\Controllers\Api\Admin')
                ->group(base_path('routes/api/admin.php'));

            Route::middleware('api')
                ->prefix('api/front')
                ->namespace('App\Http\Controllers\Api\Front')
                ->group(base_path('routes/api/front.php'));

            Route::middleware('web')
                ->namespace('App\Http\Controllers\Api\Front')
                ->group(base_path('routes/web.php'));

            Route::middleware('api')
                ->prefix('api/general')
                ->namespace('App\Http\Controllers\Api\General')
                ->group(base_path('routes/api/general.php'));

        });

    }
}
