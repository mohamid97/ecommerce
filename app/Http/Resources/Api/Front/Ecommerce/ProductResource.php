<?php

namespace App\Http\Resources\Api\Front\Ecommerce;

use App\Traits\HandlesUpload;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    use HandlesUpload;
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $defaultVaraint = $this->getDefaultVaraint();

        // detect requested price range (frontend uses `from` and `to`)
        $min = null;
        $max = null;
        if ($request->filled('from') && is_numeric($request->input('from'))) {
            $min = (float) $request->input('from');
        }
        if ($request->filled('to') && is_numeric($request->input('to'))) {
            $max = (float) $request->input('to');
        }

        // if product has options and a range was requested, try to find a variant matching the range
        $selectedVariant = null;
        if ($this->has_options && ($min !== null || $max !== null)) {
            $variants = $this->relationLoaded('variants')
                ? $this->variants->where('status', '!=', 'draft')
                : $this->variants()->where('status', '!=', 'draft')->get();

            $matched = $variants->filter(function ($v) use ($min, $max) {
                if ($min !== null && $v->sale_price < $min) {
                    return false;
                }
                if ($max !== null && $v->sale_price > $max) {
                    return false;
                }
                return true;
            });

            if ($matched->isNotEmpty()) {
                $selectedVariant = $matched->firstWhere('is_default', true) ?? $matched->first();
            }
        }

        $priceSource = $selectedVariant ?? ($this->has_options && $defaultVaraint ? $defaultVaraint : $this);

        // compute overall min/max prices (variants or product)
        $minPrice = null;
        $maxPrice = null;
        if ($this->has_options) {
            $variants = $this->relationLoaded('variants')
                ? $this->variants->where('status', '!=', 'draft')
                : $this->variants()->where('status', '!=', 'draft')->get();

            if ($variants->isNotEmpty()) {
                $minPrice = (float) $variants->min('sale_price');
                $maxPrice = (float) $variants->max('sale_price');
            } else {
                $minPrice = $maxPrice = (float) $this->sale_price;
            }
        } else {
            $minPrice = $maxPrice = (float) $this->sale_price;
        }

        $displayVariant = $selectedVariant ?? $defaultVaraint;

        return[
            'id' => $this->id,
            'title' => $this->title,
            'slug'=>$this->getColumnLang('slug'),
            // 'des' => $this->des,
            'sale_price' => (float) $priceSource->sale_price,
            'moq' => $priceSource->moq ?? $this->moq ?? 1,
            'discount_price' => (float) ($priceSource->discount_value ?? $priceSource->discount ?? 0),
            'discount_type' => $priceSource->discount_type,
            'price_after_discount' => (float) $priceSource->getDiscountPrice(),
            'price_min' => $minPrice,
            'price_max' => $maxPrice,
            'on_demand' => $this->on_demand,
            'sku' => $priceSource->sku,
            'has_options' => $this->has_options,
            'product_image' => $this->getImageUrl($this->product_image),
            // 'breadcrumb' => $this->getImageUrl($this->breadcrumb),
            'status' => $this->has_options ? ($priceSource->status ?? $this->status) : $this->status,
            'stock' => $this->has_options ? ($priceSource->stock ?? $this->stock) : $this->stock,
            'default_varaint' => $this->has_options && $displayVariant ? [
                'id' => $displayVariant->id,
                'title' => $displayVariant->title,
                'sku' => $displayVariant->sku,
                'sale_price' => (float) $displayVariant->sale_price,
                'discount' => (float) ($displayVariant->discount_value ?? $displayVariant->discount ?? 0),
                'discount_type' => $displayVariant->discount_type,
                'price_after_discount' => (float) $displayVariant->getDiscountPrice(),
                'stock' => $displayVariant->stock,
                'status' => $displayVariant->status,
                'is_default' => (bool) $displayVariant->is_default,
                'moq' => $displayVariant->moq ?? 1,
            ] : null,
            // return slug also and id
            // 'category' => $this->whenLoaded('category', function () {
            //     return [
            //         'id' => $this->category->id,
            //         'title' => $this->category->title,
            //         'slug' => $this->category->slug,
            //     ];
            // }),
            // 'brand'    => $this->whenLoaded('brand', function () {
            //     return [
            //         'id' => $this->brand->id,
            //         'title' => $this->brand->title,
            //         'slug' => $this->brand->slug,
            //     ];
            // }),
            // 'industries' => $this->whenLoaded('industries', function () {
            //     return $this->industries->map(function ($industry) {
            //         return [
            //             'id' => $industry->id,
            //             'title' => $industry->title,
            //             'slug' => $industry->slug,
            //         ];
            //     });
            // }),
            'created_at' => $this->created_at->format('Y-m-d'),
            'updated_at' => $this->updated_at->format('Y-m-d'),
        ];
    }

    private function getDefaultVaraint()
    {
        if (!$this->has_options) {
            return null;
        }

        $variants = $this->relationLoaded('variants')
            ? $this->variants
            : $this->variants()->where('status', '!=', 'draft')->orderByDesc('is_default')->orderBy('id')->get();

        return $variants->firstWhere('is_default', true)
            ?? $variants->firstWhere('is_default', 1)
            ?? $variants->first();
    }


    
}
