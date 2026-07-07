<?php

namespace App\Http\Requests\Api\Ecommerce\Cart;

use App\Traits\ResponseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CartStoreRequest extends FormRequest
{
    use ResponseTrait;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * product_id  → always required (plain product, product+variant, and bundle all need it)
     * variant_id  → nullable; required at the strategy layer ONLY if the product actually has options
     * bundel_id   → nullable; its presence switches to BundleStrategy
     * quantity    → always required
     */
    public function rules(): array
    {
        return [
            'product_id' => 'nullable|integer|exists:products,id',
            'variant_id' => 'nullable|integer|exists:product_variants,id',
            'bundel_id' => 'nullable|required_without:product_id|integer|exists:bundels,id',
            'quantity' => 'required|integer|min:1|max:50',
            'bundle_items' => 'nullable|array',
            'bundle_items.*.product_id' => 'nullable|integer|exists:products,id',
            'bundle_items.*.variant_id' => 'nullable|integer|exists:product_variants,id',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $bundleId = $this->input('bundel_id');
            $bundleItems = $this->input('bundle_items', []);

            if ($bundleId && ! empty($bundleItems) && is_array($bundleItems)) {
                $bundleDetails = \DB::table('bundel_details')
                    ->where('bundel_id', $bundleId)
                    ->get();

                // A product can now occupy multiple rows/slots in the same
                // bundle, so group all rows per product_id instead of
                // assuming a single row per product.
                $detailsByProduct = $bundleDetails->groupBy('product_id');

                // Track which specific rows (by id) have already been
                // "claimed" by an earlier bundle_items entry, so that
                // repeated product_ids get checked against distinct rows
                // rather than all passing against the same one.
                $consumedRowIds = [];

                foreach ($bundleItems as $index => $item) {

                    $productId = $item['product_id'] ?? null;
                    $variantId = $item['variant_id'] ?? null;
                    if (! $productId) {
                        continue;
                    }

                    $candidateRows = $detailsByProduct->get($productId, collect());

                    if ($candidateRows->isEmpty()) {
                        $validator->errors()->add(
                            "bundle_items.{$index}.product_id",
                            "The product with ID {$productId} does not belong to bundle ID {$bundleId}."
                        );

                        continue;
                    }

                    // Only rows for this product not yet consumed by a
                    // previous entry in bundle_items.
                    $availableRows = $candidateRows->reject(
                        fn ($row) => in_array($row->id, $consumedRowIds)
                    );

                    if ($availableRows->isEmpty()) {
                        $validator->errors()->add(
                            "bundle_items.{$index}.product_id",
                            "The product with ID {$productId} was requested more times than available in bundle ID {$bundleId}."
                        );

                        continue;
                    }

                    if ($variantId !== null) {
                        // Pick the first available row for this product
                        // whose allowed variants include the requested one.
                        $matchingRow = $availableRows->first(function ($row) use ($variantId) {
                            $allowedVariantIds = json_decode($row->variant_ids, true) ?: [];

                            return in_array((int) $variantId, array_map('intval', $allowedVariantIds));
                        });

                        if (! $matchingRow) {
                            $validator->errors()->add(
                                "bundle_items.{$index}.variant_id",
                                "The variant with ID {$variantId} does not belong to the selected product inside bundle ID {$bundleId}."
                            );

                            continue;
                        }

                        $consumedRowIds[] = $matchingRow->id;
                    } else {
                        // No variant requested: claim the first available
                        // row for this product.
                        $consumedRowIds[] = $availableRows->first()->id;
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
