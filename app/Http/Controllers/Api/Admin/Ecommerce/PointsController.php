<?php

namespace App\Http\Controllers\Api\Admin\Ecommerce;

use App\Http\Controllers\Controller;
use App\Models\Api\Ecommerce\PointsSetting;
use App\Traits\ResponseTrait;
use App\Http\Resources\Api\Admin\Ecommerce\PointsSettingResource;
use App\Http\Requests\Api\Admin\Ecommerce\Points\StorePointsSettingRequest;
use Illuminate\Http\Request;

class PointsController extends Controller
{
    use ResponseTrait;

    /**
     * Return the single points settings record.
     */
    public function getTiers()
    {
        $settings = PointsSetting::first();

        return $this->success(new PointsSettingResource($settings), __('main.retrieved_successfully', ['model' => 'points settings']));
    }

    /**
     * Create or update the single points settings record.
     * Accepts `min_order_amount`, `points`, and `pound_per_point`.
     */
    public function storeTier(StorePointsSettingRequest $request)
    {
        $data = $request->validated();

        $settings = PointsSetting::first();

        if (! $settings) {
            $settings = PointsSetting::create($data);
        } else {
            $settings->update($data);
        }

        return $this->success(new PointsSettingResource($settings), __('main.stored_successfully', ['model' => 'points settings']));
    }


    /**
     * Delete the single settings record.
     */
    public function deleteTier(Request $request)
    {
        $settings = PointsSetting::first();

        if ($settings) {
            $settings->delete();
        }

        return $this->success(null, __('main.deleted_successfully', ['model' => 'points settings']));
    }




}
