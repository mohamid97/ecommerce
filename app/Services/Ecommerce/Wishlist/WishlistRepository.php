<?php

namespace App\Services\Ecommerce\Wishlist;

use App\Models\Api\Ecommerce\Wishlist;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class WishlistRepository
{
    public function getUserWishlist(int $userId): Collection
    {
        return Wishlist::with($this->frontRelations())
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function add(int $userId, array $data): Wishlist
    {
        $wishlist = Wishlist::firstOrCreate([
            'user_id' => $userId,
            'product_id' => $data['product_id'],
            'variant_id' => $data['variant_id'] ?? null,
        ]);

        return $wishlist->load($this->frontRelations());
    }

    public function findUserWishlistItem(int $userId, array $data): ?Wishlist
    {
        $query = Wishlist::where('user_id', $userId)
            ->where('product_id', $data['product_id']);

        if (array_key_exists('variant_id', $data)) {
            $query->where('variant_id', $data['variant_id']);
        } else {
            $query->whereNull('variant_id');
        }

        return $query->first();
    }

    public function deleteForUser(int $userId, array $data): bool
    {
        if (!empty($data['id'])) {
            return (bool) Wishlist::where('user_id', $userId)
                ->where('id', $data['id'])
                ->delete();
        }

        $query = Wishlist::where('user_id', $userId)
            ->where('product_id', $data['product_id']);

        if (!empty($data['variant_id'])) {
            $query->where('variant_id', $data['variant_id']);
        } else {
            $query->whereNull('variant_id');
        }

        return (bool) $query->delete();
    }

    public function paginateAdminWishlist(Request $request): LengthAwarePaginator
    {
        $query = Wishlist::with($this->adminRelations());

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        return $query->orderBy('created_at', 'desc')->paginate(15);
    }

    public function findAdminWishlistItem(int $id): ?Wishlist
    {
        return Wishlist::with($this->adminRelations())->find($id);
    }

    public function deleteAdminWishlistItem(int $id): bool
    {
        return (bool) Wishlist::where('id', $id)->delete();
    }

    public function clearByUser(int $userId): void
    {
        Wishlist::where('user_id', $userId)->delete();
    }

    private function frontRelations(): array
    {
        return [
            'product',
            'variant.variants.optionValue.option',
        ];
    }

    private function adminRelations(): array
    {
        return [
            'user',
            'product',
            'variant.variants.optionValue.option',
        ];
    }
}
