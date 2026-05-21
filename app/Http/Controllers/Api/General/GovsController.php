<?php

namespace App\Http\Controllers\Api\General;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Front\Ecommerce\GovResource;
use App\Models\Api\Ecommerce\Gov;
use App\Traits\ResponseTrait;

class GovsController extends Controller
{
    use ResponseTrait;
    public function get()
    {
        $govs = Gov::query()
            ->orderBy('id')
            ->get();

        return $this->success(
            GovResource::collection($govs),
            __('main.retrieved_successfully', ['model' => 'Governments'])
        );
    }
}
