<?php

namespace App\Http\Resources\Api\Admin\Ecommerce\Promotion;

use App\Traits\HandlesImage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PromotionsResource extends JsonResource
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
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'status' => $this->status,
            'target' => $this->target,
            'image' => $this->getImageUrl($this->image),
            'type' => $this->type,
            'discount' => (float) $this->discount,
            'is_coupon' => $this->is_coupon,
            'brands_count' => $this->brands_count ?? $this->brands()->count(),
            'categories_count' => $this->categories_count ?? $this->categories()->count(),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
