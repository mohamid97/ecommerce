<?php

namespace App\Http\Middleware\Api\Admin;

use App\Traits\ResponseTrait;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    use ResponseTrait;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next , $action): Response
    {
            $model = $request->model;
          
            $permission = $action . ' ' . strtolower($model);
            
        
            if ($request->user()->type = 'admin' && $request->user()?->can($permission)) {
                
                return $next($request);
            }
            return $this->error("You do not have permission to $action $model", 403);

    }   
}