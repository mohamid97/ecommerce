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
        $title = $this->getColumnLang('title');
        $slug = $this->getColumnLang('slug');

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
            'title' => $title,
            // Create a fallback slug when the translation does not provide one.
            'slug' => filled($slug) ? $slug : $this->createSlugFromTitle($title),
            'created_at'=>$this->created_at->format('Y-m-d'),
            'updated_at'=>$this->updated_at->format('Y-m-d'),
        ];
    }

    /**
     * Build a slug from the English title, falling back to Arabic.
     *
     * @param array<string, mixed>|string|null $title
     */
    protected function createSlugFromTitle(array|string|null $title): ?string
    {
        if (is_array($title)) {
            $title = filled($title['en'] ?? null)
                ? $title['en']
                : ($title['ar'] ?? null);
        }

        if (! is_string($title) || trim($title) === '') {
            return null;
        }

        return strtolower((string) preg_replace('/\s+/u', '-', trim($title)));
    }
}
