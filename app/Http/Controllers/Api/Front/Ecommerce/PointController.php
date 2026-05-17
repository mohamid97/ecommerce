<?php
namespace App\Http\Controllers\Api\Front\Ecommerce;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Front\Points\CalculatePoinstRequest;
use App\Models\Api\Ecommerce\PointsSetting;
use App\Traits\ResponseTrait;

class PointController extends Controller
{
    use ResponseTrait;


    public function calculate(CalculatePoinstRequest $request)
    {
        $points = PointsSetting::first();

        if(!$points){
            return $this->error('Points settings not found', 404);
        }
        return $this->success([
            'points' => $request->points,
            'equivalent_amount' => $request->points * $points->pound_per_point,
        ] , __('main.points_calculated_successfully'));

    }


    


}