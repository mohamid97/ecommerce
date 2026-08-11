<?php

namespace App\Services\Ecommerce\Shipment;

use App\Models\Api\Admin\Category;
use App\Models\Api\Admin\Product;
use App\Models\Api\Ecommerce\Bundel;
use App\Models\Api\Ecommerce\BundelDetails;
use App\Models\Api\Ecommerce\CartItem;
use App\Models\Api\Ecommerce\ProductShipement;
use App\Models\Api\Ecommerce\ProductVariant;
use App\Models\Api\Ecommerce\ShipmentWay;
use App\Models\Api\Ecommerce\ShipmentWayZone;
use Illuminate\Support\Facades\DB;

class ShipmentCalculationService
{
    /**
     * Calculate shipping cost for a list of cart items at a given zone.
     *
     * @param iterable<CartItem> $cartItems
     * @param int|null $shipmentZoneId
     * @return array{shipping_cost: float, used_cars: array}
     */
    public function calculate(iterable $cartItems, ?int $shipmentZoneId): array
    {
        if (empty($cartItems) || empty($shipmentZoneId)) {
            return ['shipping_cost' => 0.0, 'used_cars' => []];
        }

        // Step 1: build packing list of units
        $packingList = [];
        foreach ($cartItems as $cartItem) {
            $units = $this->resolveUnitsForCartItem($cartItem);
            $qty = (int) ($cartItem->quantity ?? 1);
            for ($i = 0; $i < $qty; $i++) {
                $packingList[] = $units;
            }
        }

        if (empty($packingList)) {
            return ['shipping_cost' => 0.0, 'used_cars' => []];
        }

        // Step 2: validate no single item exceeds largest car capacity
        $largestCapacity = ShipmentWay::where('status', 'active')->max('capacity');
        if ($largestCapacity === null) {
            $largestCapacity = 0;
        }

        foreach ($packingList as $units) {
            if ($units > $largestCapacity) {
                throw new \Exception("Item requires {$units} units but the largest available car capacity is {$largestCapacity}.");
            }
        }

        // Step 3: get active ways sorted by capacity DESC
        $ways = ShipmentWay::where('status', 'active')
            ->orderByDesc('capacity')
            ->get();

        if ($ways->isEmpty()) {
            return ['shipping_cost' => 0.0, 'used_cars' => []];
        }

        // Step 4: greedy packing (largest car first)
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

        // Step 5: sum prices for used ways at the given zone
        $prices = ShipmentWayZone::whereIn('way_id', $usedWayIds)
            ->where('zone_id', $shipmentZoneId)
            ->where('status', 'active')
            ->get()
            ->keyBy('way_id');

        $totalShipping = 0.0;
        $usedCars = [];

        foreach ($usedWayIds as $wayId) {
            $price = $prices->get($wayId)?->price ?? 0;
            $way = $ways->firstWhere('id', $wayId);
            $totalShipping += (float) $price;
            $usedCars[] = [
                'way_id' => $wayId,
                'title' => $way?->title ?? null,
                'capacity' => $way?->capacity ?? 0,
                'price' => (float) $price,
            ];
        }

        return [
            'shipping_cost' => round($totalShipping, 2),
            'used_cars' => $usedCars,
        ];
    }

    /**
     * Resolve units for a single cart item (variant → product → category default = 1).
     * For bundles, sum units of all contained products/variants.
     */
    private function resolveUnitsForCartItem(CartItem $cartItem): int
    {
        // If this cart item is a bundle, sum units of all bundle contents
        if ($cartItem->bundel_id) {
            return $this->resolveBundleUnits($cartItem);
        }

        // 1. Variant units
        if ($cartItem->variant_id) {
            $variant = ProductVariant::find($cartItem->variant_id);
            if ($variant && !is_null($variant->units)) {
                return (int) $variant->units;
            }
        }

        // 2. Product shipment units
        if ($cartItem->product_id) {
            $productShipment = ProductShipement::where('product_id', $cartItem->product_id)
                ->whereNull('variant_id')
                ->first();

            if ($productShipement && !is_null($productShipement->units)) {
                return (int) $productShipement->units;
            }
        }

        // 3. Default is 1 unit if nothing is set
        return 1;
    }

    /**
     * Sum units for all products/variants inside a bundle.
     */
    private function resolveBundleUnits(CartItem $cartItem): int
    {
        $bundleDetails = BundelDetails::where('bundel_id', $cartItem->bundel_id)->get();
        $totalUnits = 0;

        foreach ($bundleDetails as $detail) {
            $variantIds = $detail->selectedVariantIds();
            $units = 1;

            if (!empty($variantIds)) {
                $variant = ProductVariant::find($variantIds[0]);
                if ($variant && !is_null($variant->units)) {
                    $units = (int) $variant->units;
                }
            } else {
                $productShipment = ProductShipement::where('product_id', $detail->product_id)
                    ->whereNull('variant_id')
                    ->first();

                if ($productShipement && !is_null($productShipement->units)) {
                    $units = (int) $productShipement->units;
                }
            }

            $totalUnits += $units * ($detail->quantity ?? 1);
        }

        return max($totalUnits, 1);
    }
}
