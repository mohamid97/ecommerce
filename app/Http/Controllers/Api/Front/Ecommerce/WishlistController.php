<?php

namespace App\Http\Controllers\Api\Front\Ecommerce;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Ecommerce\Wishlist\WishlistDeleteRequest;
use App\Http\Requests\Api\Ecommerce\Wishlist\WishlistStoreRequest;
use App\Http\Resources\Api\Front\Ecommerce\WishlistResource;
use App\Services\Ecommerce\Wishlist\WishlistService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    use ResponseTrait;

    public function __construct(
        private WishlistService $wishlistService
    ) {}

    public function view(Request $request)
    {
        $wishlist = $this->wishlistService->getUserWishlist($request->user()->id);

        return $this->success(
            WishlistResource::collection($wishlist),
            __('main.retrieved_successfully', ['model' => 'wishlist'])
        );
    }

    public function add(WishlistStoreRequest $request)
    {
        try {
            $wishlist = $this->wishlistService->add($request->user()->id, $request->validated());

            return $this->success(
                new WishlistResource($wishlist),
                __('main.stored_successfully', ['model' => 'wishlist'])
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 422);
        }
    }

    public function remove(WishlistDeleteRequest $request)
    {
        try {
            $deleted = $this->wishlistService->remove($request->user()->id, $request->validated());

            if (! $deleted) {
                return $this->error('Wishlist item not found', 404);
            }

            return $this->success(null, __('main.deleted_successfully', ['model' => 'wishlist item']));
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 422);
        }
    }

    public function toggle(WishlistStoreRequest $request)
    {
        try {
            $result = $this->wishlistService->toggle($request->user()->id, $request->validated());

            if (! $result['in_wishlist']) {
                return $this->success([
                    'in_wishlist' => false,
                ], __('main.deleted_successfully', ['model' => 'wishlist item']));
            }

            return $this->success([
                'in_wishlist' => true,
                'item' => new WishlistResource($result['item']),
            ], __('main.stored_successfully', ['model' => 'wishlist']));
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 422);
        }
    }
}
