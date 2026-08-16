<?php

namespace App\Services\Ecommerce\Shipment;

use App\Models\Api\Admin\Product;
use App\Models\Api\Ecommerce\BundelDetails;
use App\Models\Api\Ecommerce\CartItem;
use App\Models\Api\Ecommerce\ProductVariant;
use App\Models\Api\Ecommerce\ShipmentWay;
use App\Models\Api\Ecommerce\ShipmentWayZone;

class ShipmentCalculationService
{
    public function calculate(iterable $cartItems, ?int $shipmentZoneId): array
    {
        $packingList = [];

        foreach ($cartItems as $cartItem) {
            $unitsList = $this->resolveUnitsForCartItem($cartItem);
            for ($i = 0; $i < (int) ($cartItem->quantity ?? 1); $i++) {
                foreach ($unitsList as $units) {
                    $packingList[] = $units;
                }
            }
        }

        if (empty($packingList) || empty($shipmentZoneId)) {
            return ['shipping_cost' => 0.0, 'used_cars' => []];
        }

        $largestCapacity = (int) (ShipmentWay::where('status', 'active')->max('capacity') ?? 0);
        foreach ($packingList as $units) {
            if ($units > $largestCapacity) {
                throw new \Exception("Item requires {$units} units but the largest available car capacity is {$largestCapacity}.");
            }
        }

        $ways = ShipmentWay::where('status', 'active')->orderByDesc('capacity')->get();
        if ($ways->isEmpty()) {
            return ['shipping_cost' => 0.0, 'used_cars' => []];
        }

        $usedWayIds = [];
        $remaining = $packingList;

        foreach ($ways as $way) {
            if (empty($remaining)) {
                break;
            }

            $capacity = (int) $way->capacity;
            $currentLoad = 0;
            foreach ($remaining as $index => $units) {
                if ($currentLoad + $units <= $capacity) {
                    $currentLoad += $units;
                    unset($remaining[$index]);
                }
            }

            if ($currentLoad > 0) {
                $usedWayIds[] = $way->id;
            }
        }

        if (!empty($remaining)) {
            throw new \Exception('Unable to pack all items into available shipment ways.');
        }

        $prices = ShipmentWayZone::whereIn('way_id', $usedWayIds)
            ->where('zone_id', $shipmentZoneId)
            ->where('status', 'active')
            ->get()
            ->keyBy('way_id');

        $totalShipping = 0.0;
        $usedCars = [];
        foreach ($usedWayIds as $wayId) {
            $way = $ways->firstWhere('id', $wayId);
            $price = (float) ($prices->get($wayId)?->price ?? 0);
            $totalShipping += $price;
            $usedCars[] = [
                'way_id' => $wayId,
                'title' => $way?->title,
                'capacity' => $way?->capacity ?? 0,
                'price' => $price,
            ];
        }

        return ['shipping_cost' => round($totalShipping, 2), 'used_cars' => $usedCars];
    }

    private function resolveUnitsForCartItem(CartItem $cartItem): array
    {
        if ($cartItem->bundel_id) {
            return $this->resolveBundleUnits($cartItem);
        }

        $variant = $cartItem->variant_id
            ? ($cartItem->variant ?: ProductVariant::find($cartItem->variant_id))
            : null;
        $product = $cartItem->product_id
            ? ($cartItem->product ?: Product::find($cartItem->product_id))
            : null;

        return [$this->requireUnits(
            $variant?->resolveUnits() ?? $product?->resolveUnits(),
            $product?->id ?? $variant?->product_id,
        )];
    }

    private function resolveBundleUnits(CartItem $cartItem): array
    {
        $unitsList = [];

        foreach ($cartItem->cartBundelItems as $bundleItem) {
            $variant = $bundleItem->variant_id
                ? ($bundleItem->variant ?: ProductVariant::find($bundleItem->variant_id))
                : null;
            $product = $bundleItem->product_id
                ? ($bundleItem->product ?: Product::find($bundleItem->product_id))
                : null;
            $units = $this->requireUnits(
                $variant?->resolveUnits() ?? $product?->resolveUnits(),
                $product?->id ?? $variant?->product_id,
            );
            $detail = $bundleItem->bundleDetail ?: BundelDetails::find($bundleItem->bundle_item_id);

            for ($i = 0; $i < (int) ($detail?->quantity ?? 1); $i++) {
                $unitsList[] = $units;
            }
        }

        return $unitsList;
    }

    private function requireUnits(?int $units, ?int $productId): int
    {
        if ($units === null) {
            throw new \Exception(__('main.units_are_required_for_product', [
                'product' => $productId ?? 'unknown',
            ]));
        }

        return $units;
    }
}
