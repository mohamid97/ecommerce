<?php

namespace App\Http\Controllers\Api\Front\Ecommerce;

use App\DTO\Ecommerce\Cart\AddToCartDTO;
use App\DTO\Ecommerce\Cart\RemoveFromCartDTO;
use App\DTO\Ecommerce\Cart\UpdateCartItemQuantityDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Ecommerce\Cart\CartStoreRequest;
use App\Http\Requests\Api\Ecommerce\Cart\DeleteFromCartRequest;
use App\Http\Requests\Api\Ecommerce\Cart\GuestCartViewRequest;
use App\Http\Requests\Api\Ecommerce\Cart\UpdateCartItemQuantityRequest;
use App\Http\Resources\Api\Front\Ecommerce\CartResource;
use App\Http\Resources\Api\Front\Ecommerce\GuestCartResource;
use App\Models\Api\Ecommerce\Cart;
use App\Services\Ecommerce\Cart\CartService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    use ResponseTrait;

    public function __construct(
        private CartService $cartService
    ) {}

    public function viewCart(Request $request)
    {
        $userId = $request->user()->id;

        $cart = Cart::with($this->cartRelations())
            ->where('user_id', $userId)
            ->first();

        if (!$cart) {
            return $this->success(null, 200);
        }

        return $this->success(new CartResource($cart), 200);
    }

    public function viewGuestCart(GuestCartViewRequest $request)
    {
        try {
            $cart = $this->cartService->mapGuestCartData($request->validated());

            return $this->success(
                new GuestCartResource($cart),
                __('main.retrieved_successfully', ['model' => 'cart'])
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 422);
        }
    }

    public function addToCart(CartStoreRequest $request)
    {
        try {
            DB::beginTransaction();
            $dto = AddToCartDTO::fromRequest($request->validated());
            $userId = $request->user()->id;
            $this->cartService->StoreToCart($userId, $dto);
            DB::commit();

            return $this->success(null, __('main.stored_successfully', ['model' => 'Item']), 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->error($e->getMessage(), 422);
        }
    }

    public function deleteFromCart(DeleteFromCartRequest $request)
    {
        try {
            DB::beginTransaction();
            $userId = $request->user()->id;
            $dto = RemoveFromCartDTO::fromRequest($request->validated());
            $this->cartService->RemoveFromCart($userId, $dto);
            DB::commit();

            return $this->success(null, __('main.deleted_successfully', ['model' => 'Item']), 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->error($e->getMessage(), 422);
        }
    }

    public function updateQuantity(UpdateCartItemQuantityRequest $request)
    {
        try {
            DB::beginTransaction();
            $userId = $request->user()->id;
            $dto = UpdateCartItemQuantityDTO::fromRequest($request->validated());
            $cart = $this->cartService->updateCartItemQuantity($userId, $dto);
            DB::commit();

            return $this->success(
                new CartResource($cart),
                __('main.updated_successfully', ['model' => 'cart item'])
            );
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->error($e->getMessage(), 422);
        }
    }

    public function deleteAllFromCart(Request $request)
    {
        $userId = $request->user()->id;
        Cart::where('user_id', $userId)->delete();

        return $this->success(null, 200);
    }

    private function cartRelations(): array
    {
        return [

            'items.product',
            'items.variant.variants.optionValue.option',
            'items.bundel',
            'items.cartBundelItems.product',
            'items.cartBundelItems.variant.variants.optionValue.option',
            'items.product',
            'items.variant.variants.optionValue.option',
            'items.bundel',
            'items.cartBundelItems.product',
            'items.cartBundelItems.variant.variants.optionValue.option',
        ];
    }
}
