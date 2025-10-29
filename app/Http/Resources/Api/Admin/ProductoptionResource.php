<?php

namespace App\Http\Resources\Api\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductoptionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        
        return [
            
            'product_id' => $this->id,
            'title'=> $this->getColumnLang('title'),
            'base_price'=> $this->base_price,
            'has_options'=> $this->has_options,
            'status'=> $this->status,
            'options' => $this->whenLoaded('options' , function(){
                return OptionsResource::collection($this->options);
            }),
            'created_at' => $this->created_at?->format('Y-m-d'),
            'updated_at' => $this->updated_at?->format('Y-m-d'),
        ];
    }
}