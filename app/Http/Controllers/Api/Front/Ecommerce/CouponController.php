<?php
namespace App\Http\Controllers\Api\Front\Ecommerce;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Front\Coupon\ValidateCouponRequest;
use App\Models\Api\Ecommerce\Promotion;
use App\Traits\ResponseTrait;

class CouponController extends Controller
{
    use ResponseTrait;

    public function validateCoupon(ValidateCouponRequest $request){

        $coupon = Promotion::where('coupon_code', $request->coupon_code)
            ->where('is_coupon', true)
            ->where('status', 'active')
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->first();

        if(!$coupon){
            return $this->error('Invalid coupon code', 404);
        }


        if (!is_null($coupon->coupon_limit) && $coupon->coupon_limit <= 0) {
            return $this->error(__('main.coupon_limit_reached'), 422);
        }



        return $this->success([
            'id' => $coupon->id,
            'coupon_code' => $coupon->coupon_code,
            'type' => $coupon->type,
            'discount' => $coupon->discount,
            'max_amount_discount' => $coupon->max_amount_discount,
            'is_valid' => true,
            'end_date' => $coupon->end_date,
            'status' => $coupon->status,
            'image'=> $coupon->image ? asset('storage/' . $coupon->image) : null,
            'created_at' => $coupon->created_at->format('Y-m-d'),
            'updated_at' => $coupon->updated_at->format('Y-m-d'),
        ] , __('main.valid_coupon'));
    }
}