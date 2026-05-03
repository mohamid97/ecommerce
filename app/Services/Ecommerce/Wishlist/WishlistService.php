<?php

namespace App\Services\Ecommerce\Wishlist;

use App\Models\Api\Ecommerce\Wishlist;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WishlistService
{
    public function __construct(
        protected WishlistRepository $repo,
        protected WishlistAction $action
    ) {}

    public function getUserWishlist(int $userId): Collection
    {
        return $this->repo->getUserWishlist($userId);
    }

    public function add(int $userId, array $data): Wishlist
    {
        $this->validateWishlistData($data);

        return $this->repo->add($userId, $data);
    }

    public function remove(int $userId, array $data): bool
    {
        return $this->repo->deleteForUser($userId, $data);
    }

    public function toggle(int $userId, array $data): array
    {
        return DB::transaction(function () use ($userId, $data) {
            $this->validateWishlistData($data);

            $wishlist = $this->repo->findUserWishlistItem($userId, $data);

            if ($wishlist) {
                $wishlist->delete();

                return [
                    'in_wishlist' => false,
                    'item' => null,
                ];
            }

            return [
                'in_wishlist' => true,
                'item' => $this->repo->add($userId, $data),
            ];
        });
    }

    public function paginateAdminWishlist(Request $request): LengthAwarePaginator
    {
        return $this->repo->paginateAdminWishlist($request);
    }

    public function findAdminWishlistItem(int $id): ?Wishlist
    {
        return $this->repo->findAdminWishlistItem($id);
    }

    public function deleteAdminWishlistItem(int $id): bool
    {
        return $this->repo->deleteAdminWishlistItem($id);
    }

    public function clearByUser(int $userId): void
    {
        $this->repo->clearByUser($userId);
    }

    private function validateWishlistData(array $data): void
    {
        $this->action->checkProductExists($data['product_id']);
        $this->action->checkVariantBelongsToProduct($data['variant_id'] ?? null);
    }
}
