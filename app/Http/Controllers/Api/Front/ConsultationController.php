<?php

namespace App\Http\Controllers\Api\Front;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Front\ConsultationStoreRequest;
use App\Services\Front\Consultation\ConsultationService;
use App\Traits\ResponseTrait;

class ConsultationController extends Controller
{
    use ResponseTrait;

    public function __construct(protected ConsultationService $service) {}

    public function store(ConsultationStoreRequest $request)
    {
        try {
            $data = $request->validated();
            $item = $this->service->store($data);
            return $this->success($item, __('main.created_successfully', ['model' => 'Consultation']));
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 400);
        }
    }
}
