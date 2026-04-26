<?php

namespace App\Http\Resources\Api\Admin\Ecommerce\Product\Varaint;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductVaraintsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [

            'id'=>$this->id,
            'sku'=>$this->sku,
            'sale_price'=>$this->sale_price,
            'price_after_discount' => $this->getDiscountPrice(),
            'stock'=>$this->stock,
            'status'=>$this->status,
            'is_default'=>(bool) $this->is_default,
            'option_ids'=>$this->variants->pluck('option_value_id'),
            'option' => $this->variants
                    ->map(function ($variant) {
                        return $variant->optionValue->option->title . ' ' . $variant->optionValue->title;
                    })->implode(' ، '),
       

            
        ];
    }
}
