<?php

namespace App\Http\Controllers\Api\Admin\Ecommerce\Product;

use App\DTO\Ecommerce\Product\BulkStoreVariantsDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\Ecommerce\Product\Variant\BulkStoreVariantsRequest;
use App\Http\Resources\Api\Admin\Ecommerce\Product\Varaint\VarinatDetailsResource;
use App\Services\Admin\Ecommerce\Product\Actions\Variant\BulkStoreVariantsAction;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\DB;

class BulkVariantController extends Controller
{
    use ResponseTrait;

    public function storeBulkVariants(BulkStoreVariantsRequest $request, BulkStoreVariantsAction $action)
    {
        try {
            DB::beginTransaction();
            $dto = BulkStoreVariantsDTO::fromRequest($request->validated());
            $variants = $action->store($dto);
            DB::commit();

            return $this->success(
                VarinatDetailsResource::collection($variants),
                __('main.stored_successfully', ['model' => 'Variants'])
            );
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->error($e->getMessage(), 500);
        }
    }
}
