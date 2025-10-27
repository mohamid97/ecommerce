<?php

namespace App\Exceptions;

use App\Traits\ResponseTrait;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;

class Handler extends ExceptionHandler
{
    use ResponseTrait;
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }



    public function render($request, Throwable $exception)
    {
        App::setLocale('en');
        if ($exception instanceof AuthenticationException) {
        
            return $this->error( __('main.unauthenticated') , JsonResponse::HTTP_UNAUTHORIZED);
        }
        if ($exception instanceof \Illuminate\Http\Exceptions\ThrottleRequestsException) { 
            return $this->error( __('main.many_request') , 429);
        }

        return parent::render($request, $exception);
        
    }


    

    
}