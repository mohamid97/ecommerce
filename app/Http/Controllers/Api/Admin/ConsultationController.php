<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Traits\ResponseTrait;
use App\Http\Requests\Api\Admin\Ecommerce\OrderAllRequest;
use App\Services\Admin\Consultation\ConsultationService;
use Illuminate\Support\Facades\Request;

class ConsultationController extends Controller
{
    use ResponseTrait;

    public function __construct(protected ConsultationService $service) {}

    public function all(Request $request)
    {
        try {
            $data = $request->toArray();
            $items = $this->service->all($data);
            return $this->success($items, __('main.retrieved_successfully', ['model' => 'Consultations']));
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function view(OrderAllRequest $request)
    {
        try {
            $id = $request->get('id');
            $item = $this->service->view($id);
            return $this->success($item, __('main.retrieved_successfully', ['model' => 'Consultation']));
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function delete(OrderAllRequest $request)
    {
        try {
            $id = $request->get('id');
            $item = $this->service->delete($id);
            return $this->success($item, __('main.deleted_successfully', ['model' => 'Consultation']));
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }
}
