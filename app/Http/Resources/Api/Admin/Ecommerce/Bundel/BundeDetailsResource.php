<?php

namespace App\Http\Resources\Api\Admin\Ecommerce\Bundel;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BundeDetailsResource extends JsonResource
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
            'bundel_price'=>$this->price,
            'category'=>$this->whenLoaded('category', function () {
                $this->getColumnsLangWithArrayRelation(['slug' , 'title'] , 'category' , ['id']);

            }),
            'brand'=>$this->whenLoaded('brand', function () {
                $this->getColumnsLangWithArrayRelation(['slug' , 'title'] , 'brand' , ['id']);
            }),
            'bundle_price'=>$this->getImageUrl($this->bundle_image),
            'status'=>$this->status,
            'title'=>$this->getColumnLang('title'),
            'des'=>$this->getColumnLang('des'),
            'meta_title'=>$this->getColumnLang('meta_title'),
            'meta_des'=>$this->getColumnLang('meta_des'),
            'products'=>$this->whenLoaded('bundelDetails', function () {
                return $this->bundelDetails->map(function ($detail) {
                    return [
                        'id' => $detail->id,
                        'product' => ['id'=>$detail->product->id  , 'title'=>$detail->product->title],
                        'quantity' => $detail->quantity,
                        'varaints' => $detail->variants,
                    ];
                });
            }),
            'created_at'=>$this->created_at->format('Y-m-d'),
            'updated_at'=>$this->updated_at->format('Y-m-d'),
            
        ];
    }
}
