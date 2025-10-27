<?php

namespace App\Http\Middleware\Api;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckLang
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->hasHeader('lang')) {
        $lang = $request->header('lang');
        app()->setLocale($lang);
        app('config')->set('app.column_langs', $lang);
        
        }else{
          app()->setLocale('ar');
          app('config')->set('app.column_langs', '');
        }
        return $next($request);
    }
    
}