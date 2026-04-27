<?php

namespace App\Models\Api\Ecommerce;

use App\Models\Api\Admin\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class BundelDetails extends Model
{
    use HasFactory;
    protected $fillable = ['bundel_id' , 'product_id' , 'variant_ids' , 'quantity'];

    public function bundel()
    {
        return $this->belongsTo(Bundel::class);
    }


    // JSON cast for variant_ids
    protected $casts = [
        'variant_ids' => 'array',
    ];


    // get variants 
    public function variants()
    {
        return $this->hasMany(ProductVariant::class, 'id', 'variant_ids');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function selectedVariantIds(): array
    {
        $ids = $this->variant_ids;

        if (empty($ids)) {
            return [];
        }

        if (is_string($ids)) {
            $ids = json_decode($ids, true);
        }

        if (!is_array($ids)) {
            return [];
        }

        return array_values(array_filter($ids));
    }

    public function hasVariantSelection(): bool
    {
        return !empty($this->selectedVariantIds());
    }

    public function hasAtLeastOneActiveVariant(): bool
    {
        return $this->hasAtLeastOneVariantWithStatus('!=', 'draft');
    }

    public function hasAtLeastOneStrictlyActiveVariant(): bool
    {
        return $this->hasAtLeastOneVariantWithStatus('=', 'active');
    }

    protected function hasAtLeastOneVariantWithStatus(string $operator, string $status): bool
    {
        $selectedVariantIds = $this->selectedVariantIds();

        if (empty($selectedVariantIds)) {
            return true;
        }

        $product = $this->relationLoaded('product')
            ? $this->product
            : $this->product()->with('variants')->first();

        if (!$product) {
            return false;
        }

        $variants = $product->relationLoaded('variants')
            ? $product->variants
            : $product->variants()->get();

        return $variants
            ->whereIn('id', $selectedVariantIds)
            ->contains(fn ($variant) => $operator === '='
                ? $variant->status === $status
                : $variant->status !== $status);
    }



    // get all varaints
public function getVariants()
{
    $ids = $this->selectedVariantIds();

    // Normalize: handle null, empty, or bad string values
    if (empty($ids)) {
        return collect();
    }

    // Final safety check
    if (!is_array($ids) || empty($ids)) {
        return collect();
    }

    return ProductVariant::whereIn('id', $ids)->get();
}


    // get only first varaint
    public function getFirstVariant(): ?ProductVariant
    {
        $variantIds = $this->selectedVariantIds();

        if (empty($variantIds)) {
            return null;
        }

        return ProductVariant::find($variantIds[0]);
    }








}
