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
            'stock'=>$this->stock,
            'status'=>$this->status,
            'option' => $this->variants
                    ->map(function ($variant) {
                        return $variant->optionValue->option->title . ' ' . $variant->optionValue->title;
                    })->implode(' ، '),
       

            
        ];
    }
}
