<?php

namespace App\Http\Controllers\Api\General;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\DynamicApi\DaynamicRequest;
use App\Services\dynamic\DynamicService;
use App\Traits\ResponseTrait;
use Illuminate\Support\Str;

class DynamicEndPoint extends Controller
{
    use ResponseTrait;


    public $dynamicService;
    public function __construct(DynamicService $dynamicService)
    {
        $this->dynamicService = $dynamicService;
    }
    public function dynamicEndpoint(DaynamicRequest $request)
    {
    
        try {

            $modelClass = $this->dynamicService->getModelClass($request->model);
            $this->dynamicService->validateModel($modelClass);            
            $data = $this->dynamicService->getQuery($request, $modelClass);

            if ($request->filled('pagination')) {
                return $this->successPaginated(
                    $data['paginator'],
                    $data['items'],
                    'items',
                    __('main.list_successfully', ['model' => $request->model])
                );
            }

            return $this->success($data, __('main.list_successfully', ['model' => $request->model]));

            
        } catch (\Exception $e) {
            return $this->error($e->getMessage() , 500);
        }
    }
}
