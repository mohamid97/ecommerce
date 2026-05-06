<?php

namespace App\Http\Resources\Api\Front\Ecommerce;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WishlistResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if ($this->variant_id) {
            return (new WishlistVariantProductResource($this->resource))->toArray($request);
        }

        return (new WishlistSimpleProductResource($this->resource))->toArray($request);
    }
}
