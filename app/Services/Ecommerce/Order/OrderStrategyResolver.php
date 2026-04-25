<?php

namespace App\Services\Ecommerce\Order;

use App\Models\Api\Ecommerce\CartItem;
use App\Services\Ecommerce\Order\Strategies\CartItemStrategyInterface;
use App\Services\Ecommerce\Order\Strategies\VariantItemStrategy;
use App\Services\Ecommerce\Order\Strategies\BundleItemStrategy;
use App\Services\Ecommerce\Order\Strategies\SimpleProductItemStrategy;

class OrderStrategyResolver
{
    public function resolve(CartItem $cartItem): CartItemStrategyInterface
    {
        // Simple resolver: prefer bundle, then variant, then simple product
        if (!empty($cartItem->bundel_id)) {
            return new BundleItemStrategy();
        }

        if (!empty($cartItem->variant_id)) {
            return new VariantItemStrategy();
        }

        return new SimpleProductItemStrategy();
    }
}
