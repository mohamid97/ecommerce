<?php

namespace App\Http\Controllers\Api\Admin\Ecommerce;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Admin\Ecommerce\WishlistResource;
use App\Services\Ecommerce\Wishlist\WishlistService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    use ResponseTrait;

    public function __construct(
        private WishlistService $wishlistService
    ) {}

    public function index(Request $request)
    {
        $paginator = $this->wishlistService->paginateAdminWishlist($request);
        $resourceCollection = WishlistResource::collection($paginator->getCollection());

        return $this->successPaginated(
            $paginator,
            $resourceCollection,
            'wishlists',
            __('main.retrieved_successfully', ['model' => 'wishlists'])
        );
    }

    public function show(Request $request)
    {
        $wishlist = $this->wishlistService->findAdminWishlistItem($request->id);

        if (! $wishlist) {
            return $this->success(null, 'Wishlist item not found');
        }

        return $this->success(
            new WishlistResource($wishlist),
            __('main.retrieved_successfully', ['model' => 'wishlist'])
        );
    }

    public function deleteItem(Request $request)
    {
        $deleted = $this->wishlistService->deleteAdminWishlistItem($request->id);

        if (! $deleted) {
            return $this->error('Wishlist item not found', 404);
        }

        return $this->success(null, __('main.deleted_successfully', ['model' => 'wishlist item']));
    }

    public function clearByUser(Request $request)
    {
        $this->wishlistService->clearByUser($request->user_id);

        return $this->success(null, __('main.cleared_successfully', ['model' => 'user wishlist']));
    }
}
