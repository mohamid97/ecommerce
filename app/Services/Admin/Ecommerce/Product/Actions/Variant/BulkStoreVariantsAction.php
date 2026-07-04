<?php

namespace App\Services\Admin\Ecommerce\Product\Actions\Variant;

use App\DTO\Ecommerce\Product\BulkStoreVariantsDTO;
use App\Models\Api\Admin\Lang;
use App\Models\Api\Admin\Product;
use App\Models\Api\Ecommerce\OptionValue;
use App\Models\Api\Ecommerce\ProductOption;
use App\Models\Api\Ecommerce\ProductVariant;
use App\Models\Api\Ecommerce\ProductVaraintImages;
use App\Models\Api\Ecommerce\VariantOptionValue;

class BulkStoreVariantsAction
{
    public function store(BulkStoreVariantsDTO $dto): array
    {
        $product = Product::findOrFail($dto->productId);

        $createdVariants = [];

        foreach ($dto->variants as $variantPayload) {
            $this->validateVariantPayload($variantPayload);
            $this->checkIfVariantExists($dto->productId, $variantPayload['option_value_ids']);

            $variantData = $dto->sharedData;

            $productVariant = ProductVariant::create([
                'product_id' => $dto->productId,
                'sale_price' => $variantData['sale_price'] ?? $product->sale_price ?? 0,
                'discount_value' => $variantData['discount'] ?? null,
                'discount_type' => $variantData['discount_type'] ?? null,
                'sku' => $variantData['sku'] ?? null,
                'barcode' => $variantData['barcode'] ?? null,
                'moq' => $variantData['moq'] ?? null,
                'length' => $variantData['length'] ?? null,
                'weight' => $variantData['weight'] ?? null,
                'width' => $variantData['width'] ?? null,
                'height' => $variantData['height'] ?? null,
                'delivery_time' => $variantData['delivery_time'] ?? 0,
                'max_time' => $variantData['max_time'] ?? 0,
            ]);

            $this->storeVariantTranslations($variantData, $productVariant);

            foreach ($variantPayload['option_value_ids'] as $optionValueId) {
                VariantOptionValue::create([
                    'product_variant_id' => $productVariant->id,
                    'option_id' => OptionValue::findOrFail($optionValueId)->option_id,
                    'option_value_id' => $optionValueId,
                ]);
            }


            $createdVariants[] = $productVariant;
        }

        return $createdVariants;
    }

    protected function validateVariantPayload(array $variantPayload): void
    {
        if (empty($variantPayload['option_value_ids']) || ! is_array($variantPayload['option_value_ids'])) {
            throw new \Exception('Option values are required for each variant');
        }

    }

    protected function storeVariantTranslations(array $variantPayload, ProductVariant $productVariant): void
    {
        foreach (Lang::all() as $locale) {
            if (isset($variantPayload['title'][$locale->code])) {
                $productVariant->translateOrNew($locale->code)->title = $variantPayload['title'][$locale->code];
            }
            if (isset($variantPayload['slug'][$locale->code])) {
                $productVariant->translateOrNew($locale->code)->slug = $variantPayload['slug'][$locale->code];
            }
            if (isset($variantPayload['des'][$locale->code])) {
                $productVariant->translateOrNew($locale->code)->des = $variantPayload['des'][$locale->code];
            }
            if (isset($variantPayload['meta_title'][$locale->code])) {
                $productVariant->translateOrNew($locale->code)->meta_title = $variantPayload['meta_title'][$locale->code];
            }
            if (isset($variantPayload['meta_des'][$locale->code])) {
                $productVariant->translateOrNew($locale->code)->meta_des = $variantPayload['meta_des'][$locale->code];
            }
        }

        $productVariant->save();
    }

    protected function checkIfVariantExists(int $productId, array $optionValueIds): void
    {
        $optionValueIds = array_values(array_unique($optionValueIds));

        $values = OptionValue::whereIn('id', $optionValueIds)->get();
        if ($values->count() !== count($optionValueIds)) {
            throw new \Exception('One or more option values do not exist');
        }

        $optionIds = $values->pluck('option_id')->toArray();

        if (count(array_unique($optionIds)) !== count($optionIds)) {
            throw new \Exception('Multiple option values belong to the same option');
        }

        $productOptionIds = ProductOption::where('product_id', $productId)->pluck('option_id')->toArray();
        foreach ($optionIds as $optId) {
            if (! in_array($optId, $productOptionIds, true)) {
                throw new \Exception('One or more option values do not belong to this product');
            }
        }

        $existingVariant = ProductVariant::where('product_id', $productId)
            ->whereHas('variants', function ($query) use ($optionValueIds) {
                $query->whereIn('option_value_id', $optionValueIds)
                    ->groupBy('product_variant_id')
                    ->havingRaw('COUNT(DISTINCT option_value_id) = ?', [count($optionValueIds)]);
            })
            ->first();

        if ($existingVariant) {
            throw new \Exception('Variant already exists');
        }
    }
}
