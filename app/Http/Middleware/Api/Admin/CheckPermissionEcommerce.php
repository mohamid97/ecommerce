<?php

namespace App\Http\Middleware\Api\Admin;

use App\Traits\ResponseTrait;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermissionEcommerce
{
    use ResponseTrait;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next , $permission , $action): Response
    {
                   
            $permission = $action . ' ' . strtolower($permission);
        
            if ($request->user()->type = 'admin' && $request->user()?->can($permission)) {
                
                return $next($request);
            }
            return $this->error("You do not have permission to $permission", 403);
    }
}
