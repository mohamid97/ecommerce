<?php
namespace App\Jobs;

use App\Models\Api\Ecommerce\CartItem;
use App\Models\Api\Admin\Product;
use App\Services\Ecommerce\Cart\CartRepository;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateCartsForProduct implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected int $productId)
    {
    }

    public function handle(CartRepository $cartRepository): void
    {
        $product = Product::with('variants')->find($this->productId);
        if (! $product) {
            return;
        }

        $variantIds = $product->variants->pluck('id')->toArray();

        $query = CartItem::where('product_id', $product->id);
        if (! empty($variantIds)) {
            $query->orWhereIn('variant_id', $variantIds);
        }

        $cartItems = $query->get();

        foreach ($cartItems as $cartItem) {
            try {
                $cartRepository->updateCartItemQuantity($cartItem, (int) $cartItem->quantity);
            } catch (\Throwable $e) {
                // swallow individual failures to allow other items to continue
            }
        }

        // Also handle bundle cart items that reference this product/variants
        $bundleQuery = \App\Models\Api\Ecommerce\CartBundelItem::where('product_id', $product->id);
        if (! empty($variantIds)) {
            $bundleQuery->orWhereIn('variant_id', $variantIds);
        }

        $bundleCartItemIds = $bundleQuery->pluck('cart_item_id')->unique()->toArray();

        if (! empty($bundleCartItemIds)) {
            $bundleCartItems = CartItem::whereIn('id', $bundleCartItemIds)->get();
            foreach ($bundleCartItems as $cartItem) {
                try {
                    $cartRepository->updateCartItemQuantity($cartItem, (int) $cartItem->quantity);
                } catch (\Throwable $e) {
                    // continue
                }
            }
        }
    }
}
