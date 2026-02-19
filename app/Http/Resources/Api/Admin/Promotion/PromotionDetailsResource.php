<?php

namespace App\Http\Resources\Api\Admin\Promotion;

use App\Traits\HandlesImage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PromotionDetailsResource extends JsonResource
{
    use HandlesImage;
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'title'=>$this->getColumnLang('title'),
            'des'=>$this->getColumnLang('des'),
            'meta_title'=>$this->getColumnLang('meta_title'),
            'meta_des'=>$this->getColumnLang('meta_des'),
            'start_date'=>$this->start_date,
            'end_date'=>$this->end_date,
            'status'=>$this->status,
            'is_coupon'=>$this->is_coupon,
            'coupon_code'=>$this->coupon_code,
            'coupon_limit'=>$this->coupon_limit,
            'type'=>$this->type,
            'target'=>$this->target,
            'location'=>$this->location,
            'image'=>$this->getImageUrl($this->image),
            'discount'=>$this->discount,
            'max_amount_discount'=>$this->max_amount_discount,
            // check if has product return product name and id else return null
            'product'=>$this->product ? [
                'id'=>$this->product->id,
                'name'=>$this->product->getColumnLang('title'),
            ] : null,
            'category'=>$this->category ? [
                'id'=>$this->category->id,
                'name'=>$this->category->getColumnLang('title'),
            ] : null,
            'brand'=>$this->brand ? [
                'id'=>$this->brand->id,
                'name'=>$this->brand->getColumnLang('title'),
            ] : null,
            'cta_link'=>$this->cta_link,
            
        ];
    }
}
