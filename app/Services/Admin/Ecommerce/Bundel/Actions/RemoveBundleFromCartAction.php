<?php

namespace App\Services\Admin\Ecommerce\Bundel\Actions;

use App\Models\Api\Ecommerce\Bundel;
use App\Models\Api\Ecommerce\CartItem;
use Exception;

class RemoveBundleFromCartAction
{
    public function execute(Bundel $bundel): void
    {
        // cart items 
        $cartItems = CartItem::where('bundel_id', $bundel->id)->get();
        foreach ($cartItems as $cartItem) {
            $cartItem->delete();
        }
        
    }
}