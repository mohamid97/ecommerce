<?php

namespace App\Http\Resources\Api\Admin\Ecommerce\Promotion;

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
            'id' => $this->id,
            'title' => $this->getColumnLang('title'),
            'des' => $this->getColumnLang('des'),
            'meta_title' => $this->getColumnLang('meta_title'),
            'meta_des' => $this->getColumnLang('meta_des'),
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'status' => $this->status,
            'is_coupon' => $this->is_coupon,
            'coupon_code' => $this->coupon_code,
            'coupon_limit' => $this->coupon_limit,
            'type' => $this->type,
            'target' => $this->target,
            'location' => $this->location,
            'image' => $this->getImageUrl($this->image),
            'discount' => (float) $this->discount,
            'max_amount_discount' => (float) $this->max_amount_discount,
            'product' => $this->product ? [
                'id' => $this->product->id,
                'title' => $this->product->title,
            ] : null,
            'brands' => $this->whenLoaded('brands', function () {
                return $this->brands->map(function ($brand) {
                    return [
                        'id' => $brand->id,
                        'title' => $brand->title,
                    ];
                });
            }),
            'categories' => $this->whenLoaded('categories', function () {
                return $this->categories->map(function ($category) {
                    return [
                        'id' => $category->id,
                        'title' => $category->title,
                    ];
                });
            }),
            'customer_group' => $this->customer_group,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
