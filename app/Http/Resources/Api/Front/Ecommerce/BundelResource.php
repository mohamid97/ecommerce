<?php

namespace App\Http\Resources\Api\Front\Ecommerce;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BundelResource extends JsonResource
{
    
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {


        if(isset($this->slug) && is_array($this->slug)){
            $slug = $this->getColumnLang('slug');
        }else{
         $slug = $this->createSlugFromTitle();

        }

        return [
            'id'=>$this->id,
            'status'=>$this->status,
            'bundle_image'=>$this->getImageUrl($this->bundle_image),
            'price'=>(float) $this->getBundlePrice()['total_price'],
            'price_after_discount'=>(float) $this->getBundlePrice()['price_after_discount'],
            'discount' => (float) ($this->discount ?? 0),
            'discount_type' => $this->discount_type,
            'category'=>$this->whenLoaded('category', function () {
                return [
                    'title'=>$this->category->title,
                    'slug'=>$this->category->slug,
                    'id'=>$this->category->id,
                ];
            }),
            'brand'=>$this->whenLoaded('brand', function () {
                return [
                    'title'=>$this->brand->title,
                    'slug'=>$this->brand->slug,
                    'id'=>$this->brand->id,
                ];
            }),
            'title' => $this->getColumnLang('title'),
            // Create a fallback slug when the translation does not provide one.
            'slug' => $slug,
            'created_at'=>$this->created_at->format('Y-m-d'),
            'updated_at'=>$this->updated_at->format('Y-m-d'),
        ];
    }

    /**
     * Build a slug from the English title, falling back to Arabic.
     *
     * @param array<string, mixed>|string|null $title
     */
    protected function createSlugFromTitle(): array
    {
        $data = [];

        $data['ar'] = strtolower((string) preg_replace('/\s+/u', '-', trim($this->translate('ar')->title)));
        $data['en'] = strtolower((string) preg_replace('/\s+/u', '-', trim($this->translate('en')->title)));
        return $data;
    }


    
}
