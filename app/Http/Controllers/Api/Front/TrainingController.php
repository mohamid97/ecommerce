<?php

namespace App\Http\Controllers\Api\Front;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Front\TrainingStoreRequest;
use App\Services\Front\Training\TrainingService;
use App\Traits\ResponseTrait;

class TrainingController extends Controller
{
    use ResponseTrait;

    public function __construct(protected TrainingService $service) {}

    public function store(TrainingStoreRequest $request)
    {
        try {
            $data = $request->validated();
            $item = $this->service->store($data);
            return $this->success($item, __('main.created_successfully', ['model' => 'Training']));
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 400);
        }
    }
}
