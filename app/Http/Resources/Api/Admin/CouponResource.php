<?php

namespace App\Http\Resources\Api\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CouponResource extends JsonResource
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
            'code'=>$this->code,
            'name'=>$this->getColumnLang('name'),
            'des'=>$this->getColumnLang('des'),
            'type'=>$this->type,
            'value'=>$this->value,
            'min_order_amount'=>$this->min_order_amount,
            'usage_limit'=>$this->usage_limit,
            'is_active'=>$this->is_active,
            'code'=>$this->code,
            'count'=>$this->count,
            'start_date'=>$this->start_date->format('Y-m-d'),
            'end_date'=>$this->end_date->format('Y-m-d')
        ];
    }
}