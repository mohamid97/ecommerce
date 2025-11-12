<?php

namespace App\Http\Resources\Api\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OptionsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
      
        return [
            'product_option_id' => $this->id,
            'stock'=> $this->stock,
            'sku'=> $this->sku,
            'price'=> $this->price,
            'values' => $this->whenLoaded('options' , function(){
                return ProductoptionvalueResource::collection($this->options);
            }), 
        ];
    }
}