<?php

namespace App\Http\Requests\Api\Ecommerce\Cart;

use App\Traits\ResponseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class GuestCartViewRequest extends FormRequest
{
    use ResponseTrait;

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $products = collect($this->input('products', []))
            ->map(function ($product) {
                if (! is_array($product)) {
                    return $product;
                }

                if (array_key_exists('quantity:', $product) && ! array_key_exists('quantity', $product)) {
                    $product['quantity'] = $product['quantity:'];
                    unset($product['quantity:']);
                }

                return $product;
            })
            ->values()
            ->all();

        $bundles = $this->input('bundles', []);

        if (is_array($bundles) && $bundles !== [] && array_keys($bundles) !== range(0, count($bundles) - 1)) {
            $bundles = [$bundles];
        }

        $bundles = collect($bundles)
            ->map(function ($bundle) {
                if (! is_array($bundle)) {
                    return $bundle;
                }

                if (array_key_exists('quantity:', $bundle) && ! array_key_exists('quantity', $bundle)) {
                    $bundle['quantity'] = $bundle['quantity:'];
                    unset($bundle['quantity:']);
                }

                return $bundle;
            })
            ->values()
            ->all();

        $this->merge([
            'products' => $products,
            'bundles' => $bundles,
        ]);
    }

    public function rules(): array
    {
        return [
            'products' => 'nullable|array',
            'products.*.product_id' => 'required|integer|exists:products,id',
            'products.*.variant_id' => 'nullable|integer|exists:product_variants,id',
            'products.*.quantity' => 'required|integer|min:1|max:50',
            'bundles' => 'nullable|array',
            'bundles.*.bundel_id' => 'required|integer|exists:bundels,id',
            'bundles.*.quantity' => 'required|integer|min:1|max:50',
            'bundles.*.bundle_items' => 'required|array|min:1',
            'bundles.*.bundle_items.*.product_id' => 'required|integer|exists:products,id',
            'bundles.*.bundle_items.*.variant_id' => 'nullable|integer|exists:product_variants,id',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (empty($this->input('products', [])) && empty($this->input('bundles', []))) {
                $validator->errors()->add('cart', 'At least one product or bundle is required.');

                return;
            }

            $bundles = $this->input('bundles', []);
            if (is_array($bundles)) {
                foreach ($bundles as $bIndex => $bundle) {
                    $bundleId = $bundle['bundel_id'] ?? null;
                    if (! $bundleId) {
                        continue;
                    }

                    $bundleDetails = \DB::table('bundel_details')
                        ->where('bundel_id', $bundleId)
                        ->get();

                    // A product can occupy multiple rows/slots within the
                    // same bundle, so group all rows per product_id instead
                    // of assuming a single row per product.
                    $detailsByProduct = $bundleDetails->groupBy('product_id');

                    // Rows already "claimed" within THIS bundle entry; reset
                    // for each bundle in the bundles array.
                    $consumedRowIds = [];

                    $bundleItems = $bundle['bundle_items'] ?? [];
                    if (is_array($bundleItems)) {

                        foreach ($bundleItems as $iIndex => $item) {
                            $productId = $item['product_id'] ?? null;
                            $variantId = $item['variant_id'] ?? null;

                            if (! $productId) {
                                continue;
                            }

                            $candidateRows = $detailsByProduct->get($productId, collect());

                            if ($candidateRows->isEmpty()) {
                                $validator->errors()->add(
                                    "bundles.{$bIndex}.bundle_items.{$iIndex}.product_id",
                                    "The product with ID {$productId} does not belong to bundle ID {$bundleId}."
                                );

                                continue;
                            }

                            // Only rows for this product not yet consumed by
                            // an earlier entry in this bundle's bundle_items.
                            $availableRows = $candidateRows->reject(
                                fn ($row) => in_array($row->id, $consumedRowIds)
                            );

                            if ($availableRows->isEmpty()) {
                                $validator->errors()->add(
                                    "bundles.{$bIndex}.bundle_items.{$iIndex}.product_id",
                                    "The product with ID {$productId} was requested more times than available in bundle ID {$bundleId}."
                                );

                                continue;
                            }

                            if ($variantId !== null) {
                                // Pick the first available row for this
                                // product whose allowed variants include the
                                // requested one.
                                $matchingRow = $availableRows->first(function ($row) use ($variantId) {
                                    $allowedVariantIds = json_decode($row->variant_ids, true) ?: [];

                                    return in_array((int) $variantId, array_map('intval', $allowedVariantIds));
                                });

                                if (! $matchingRow) {
                                    $validator->errors()->add(
                                        "bundles.{$bIndex}.bundle_items.{$iIndex}.variant_id",
                                        "The variant with ID {$variantId} does not belong to the selected product inside bundle ID {$bundleId}."
                                    );

                                    continue;
                                }

                                $consumedRowIds[] = $matchingRow->id;
                            } else {
                                // No variant requested: claim the first
                                // available row for this product.
                                $consumedRowIds[] = $availableRows->first()->id;
                            }
                        }
                    }
                }
            }
        });
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            $this->error($validator->errors()->first(), 422)
        );
    }
}
