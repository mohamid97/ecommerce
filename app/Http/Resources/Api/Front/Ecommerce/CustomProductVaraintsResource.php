<?php

namespace App\Http\Resources\Api\Front\Ecommerce;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomProductVaraintsResource extends JsonResource
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
            'is_default'=>(bool) $this->is_default,
            'option_values'=>$this->variants->map(function($variant){
                return [
                    'option_id'=>$variant->optionValue->option?->id,
                    'value_id'=>$variant->optionValue->id,
                ];
            }),
            'option_ids'=>$this->variants->pluck('option_value_id'),
            'option' => $this->variants
                    ->map(function ($variant) {
                        return $variant->optionValue->option->title . ' ' . $variant->optionValue->title;
                    })->implode(' ، '),
       

            
        ];
    }
}
