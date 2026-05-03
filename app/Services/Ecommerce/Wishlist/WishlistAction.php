<?php

namespace App\Services\Ecommerce\Wishlist;

use App\Models\Api\Admin\Product;
use App\Models\Api\Ecommerce\ProductVariant;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class WishlistAction
{
    public ?Product $product = null;
    public ?ProductVariant $variant = null;

    public function checkProductExists(int $productId): Product
    {
        $this->product = Product::where('status', '!=', 'draft')->find($productId);

        if (! $this->product) {
            throw new ModelNotFoundException(
                __('main.model_not_founded', ['model' => 'Product'])
            );
        }

        return $this->product;
    }

    public function checkVariantBelongsToProduct(?int $variantId): void
    {
        if (! $variantId) {
            return;
        }

        $this->variant = ProductVariant::where('id', $variantId)
            ->where('product_id', $this->product->id)
            ->where('status', '!=', 'draft')
            ->first();

        if (! $this->variant) {
            throw new ModelNotFoundException(
                __('main.model_not_founded', ['model' => 'Product Variant'])
            );
        }
    }
}
