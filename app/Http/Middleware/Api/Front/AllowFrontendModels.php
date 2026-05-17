<?php

namespace App\Http\Middleware\Api\Front;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Traits\ResponseTrait;


class AllowFrontendModels
{
    use ResponseTrait;
    /**
     * Allow only specific model keys for frontend dynamic routes.
     *
     * Usage:
     * ->middleware('allowFrontendModels:about,contactus,category')
     */
    public function handle(Request $request, Closure $next): Response
    {
        $model = strtolower((string) $request->input('model', ''));

        if ($model === '') {
            return $this->error( __('main.no_model'), 404);
        }

        $allowedFromConfig = (array) config('frontend.allowed_data_models', []);


        $normalized = array_map(
            static fn ($item) => strtolower(trim((string) $item)),
            $allowedFromConfig
        );

        if (!in_array($model, $normalized, true)) {
            return $this->error(__('main.model_not_allowed' , ['model' => $model]),403);
        }

        return $next($request);
    }
}
