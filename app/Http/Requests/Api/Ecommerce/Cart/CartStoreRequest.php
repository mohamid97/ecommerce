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
            'product_id'   => 'nullable|integer|exists:products,id',
            'variant_id'   => 'nullable|integer|exists:product_variants,id',
            'bundel_id'    => 'nullable|required_without:product_id|integer|exists:bundels,id',
            'quantity'     => 'required|integer|min:1|max:50',
            'bundle_items' => 'nullable|array',
            'bundle_items.*.product_id' => 'nullable|integer|exists:products,id',
            'bundle_items.*.bundle_item_id'=>'required|integer|exists:bundel_details,id',
            'bundle_items.*.variant_id' => 'nullable|integer|exists:product_variants,id',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $bundleId = $this->input('bundel_id');
            $bundleItems = $this->input('bundle_items', []);

            if ($bundleId && !empty($bundleItems) && is_array($bundleItems)) {
                $bundleDetails = \DB::table('bundel_details')
                    ->where('bundel_id', $bundleId)
                    ->get();

                foreach ($bundleItems as $index => $item) {
                    $productId = $item['product_id'] ?? null;
                    $variantId = $item['variant_id'] ?? null;
                    $bundleItemId = $item['bundle_item_id'] ?? null;

                    if (!$productId || !$bundleItemId) {
                        continue;
                    }

                    $matchingDetail = $bundleDetails->firstWhere('product_id', $productId)->where('id', $bundleItemId);

                    if (!$matchingDetail) {
                        $validator->errors()->add(
                            "bundle_items.{$index}.product_id",
                            "The product with ID {$productId} does not belong to bundle ID {$bundleId}."
                        );
                        continue;
                    }

                    if ($variantId !== null) {
                        $allowedVariantIds = json_decode($matchingDetail->variant_ids, true) ?: [];
                        if (!in_array((int)$variantId, array_map('intval', $allowedVariantIds))) {
                            $validator->errors()->add(
                                "bundle_items.{$index}.variant_id",
                                "The variant with ID {$variantId} does not belong to the selected product inside bundle ID {$bundleId}."
                            );
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