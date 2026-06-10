<?php

namespace App\Imports;

use App\Models\Api\Admin\Product;
use App\Models\Api\Ecommerce\Option;
use App\Models\Api\Ecommerce\OptionValue;
use App\Models\Api\Ecommerce\ProductOption;
use App\Models\Api\Ecommerce\ProductOptionValue;
use App\Models\Api\Ecommerce\ProductVariant;
use App\Models\Api\Ecommerce\VariantOptionValue;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use App\Services\Admin\Ecommerce\Product\Actions\Variant\StoreVaraintAction;
use App\DTO\Ecommerce\Product\StoreVaraintDTO;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class VariantsImport implements ToCollection, WithHeadingRow
{
    protected $defaultProductId;

    public function __construct($defaultProductId = null)
    {
        $this->defaultProductId = $defaultProductId;
    }
    public function collection(Collection $rows)
    {
        
        foreach ($rows as $row) {
            $row = $row->map(function ($v) {
                return is_null($v) ? null : trim((string)$v);
            })->toArray();
            dd($row);

            // identify product: prefer product_id; fallback to constructor default
            $product = null;
            if (!empty($row['product_id'])) {
                $product = Product::find($row['product_id']);
            }
            if (!$product && !empty($this->defaultProductId)) {
                $product = Product::find($this->defaultProductId);
            }
            if (!$product) {
                // skip row if product cannot be found
                continue;
            }

            // ensure product marked as having options
            if (!$product->has_options) {
                $product->update(['has_options' => true]);
            }

            // create variant
            $variantData = [];
            $mapping = [
                'sku' => 'sku',
                'barcode' => 'barcode',
                'stock' => 'stock',
                'sale_price' => 'sale_price',
                'discount_value' => 'discount_value',
                'discount_type' => 'discount_type',
                'length' => 'length',
                'width' => 'width',
                'height' => 'height',
                'weight' => 'weight',
                'delivery_time' => 'delivery_time',
                'max_time' => 'max_time',
                'status' => 'status',
                'is_default' => 'is_default',
                'sales_number' => 'sales_number',
            ];

            foreach ($mapping as $col => $field) {
                if (array_key_exists($col, $row) && $row[$col] !== null && $row[$col] !== '') {
                    $variantData[$field] = $row[$col];
                }
            }

            $variantData['product_id'] = $product->id;

            // default stock to zero when not provided
            if (!array_key_exists('stock', $variantData) || $variantData['stock'] === null || $variantData['stock'] === '') {
                $variantData['stock'] = 0;
            }else{
                $variantData['stock'] = 0;
            }

            $optionValueIds = [];

            // handle option columns: option_1_name, option_1_value, option_2_name, option_2_value, ... up to 5
            for ($i = 1; $i <= 5; $i++) {
                $nameKey1 = "option_{$i}_name";
                $nameKey2 = "option+{$i}_name";
                $valueKey1 = "option_{$i}_value";
                $valueKey2 = "option+{$i}_value";
                $optIdKey1 = "option_{$i}_id";
                $optIdKey2 = "option+{$i}+id";
                $optValueIdKey1 = "option_{$i}_value_id";
                $optValueIdKey2 = "option+{$i}_value_id";

                $hasName = isset($row[$nameKey1]) && $row[$nameKey1] !== '' ? $row[$nameKey1] : (isset($row[$nameKey2]) ? $row[$nameKey2] : null);
                $hasValue = isset($row[$valueKey1]) && $row[$valueKey1] !== '' ? $row[$valueKey1] : (isset($row[$valueKey2]) ? $row[$valueKey2] : null);
                $optId = isset($row[$optIdKey1]) && $row[$optIdKey1] !== '' ? $row[$optIdKey1] : (isset($row[$optIdKey2]) ? $row[$optIdKey2] : null);
                $optValueId = isset($row[$optValueIdKey1]) && $row[$optValueIdKey1] !== '' ? $row[$optValueIdKey1] : (isset($row[$optValueIdKey2]) ? $row[$optValueIdKey2] : null);

                // if neither name/id nor value/id provided, skip
                if (empty($hasName) && empty($optId)) {
                    continue;
                }
                if (empty($hasValue) && empty($optValueId)) {
                    continue;
                }

                $option = null;
                // find by id if provided
                if (!empty($optId) && is_numeric($optId)) {
                    $option = Option::find($optId);
                }

                // otherwise find/create by name
                if (!$option && !empty($hasName)) {
                    $optionName = $hasName;
                    $code = Str::slug($optionName);
                    $option = Option::where('code', $code)->first();
                    if (!$option) {
                        $option = Option::create(['code' => $code]);
                        if (method_exists($option, 'setTranslation')) {
                            $option->setTranslation('title', 'en', $optionName);
                            $option->save();
                        }
                    }
                }

                if (!$option) {
                    // cannot resolve option, skip
                    continue;
                }

                // resolve option value either by id or by title/value
                $optionValue = null;
                if (!empty($optValueId) && is_numeric($optValueId)) {
                    $optionValue = OptionValue::find($optValueId);
                }

                if (!$optionValue && !empty($hasValue)) {
                    $optionValueTitle = $hasValue;
                    $optionValue = OptionValue::where('option_id', $option->id)
                        ->where('value', $optionValueTitle)
                        ->first();
                    if (!$optionValue) {
                        $optionValue = OptionValue::create([
                            'option_id' => $option->id,
                            'value' => $optionValueTitle,
                        ]);
                        if (method_exists($optionValue, 'setTranslation')) {
                            $optionValue->setTranslation('title', 'en', $optionValueTitle);
                            $optionValue->save();
                        }
                    }
                }

                if (!$optionValue) {
                    // cannot resolve value, skip
                    continue;
                }

                // attach product_option and product_option_value
                $productOption = ProductOption::firstOrCreate([
                    'product_id' => $product->id,
                    'option_id' => $option->id,
                ]);

                ProductOptionValue::firstOrCreate([
                    'product_option_id' => $productOption->id,
                    'option_value_id' => $optionValue->id,
                ]);

                // collect option value id for DTO
                $optionValueIds[] = $optionValue->id;
            }

            // if no option values collected, skip
            if (empty($optionValueIds)) {
                continue;
            }

            // prepare data for DTO and use existing StoreVaraintAction to create variant
            $dtoData = [
                'product_id' => $product->id,
                'option_value_ids' => $optionValueIds,
                'sale_price' => $variantData['sale_price'] ?? null,
                'stock' => $variantData['stock'] ?? null,
                'barcode' => $variantData['barcode'] ?? null,
                'title' => isset($row['title']) ? ['en' => $row['title']] : null,
                'des' => isset($row['des']) ? ['en' => $row['des']] : null,
                'discount' => $variantData['discount_value'] ?? null,
                'discount_type' => $variantData['discount_type'] ?? null,
                'length' => $variantData['length'] ?? null,
                'weight' => $variantData['weight'] ?? null,
                'width' => $variantData['width'] ?? null,
                'height' => $variantData['height'] ?? null,
                'delivery_time' => $variantData['delivery_time'] ?? 0,
                'max_time' => $variantData['max_time'] ?? 0,
                'images' => null,
                'sku' => $variantData['sku'] ?? null,
                'meta_title' => isset($row['meta_title']) ? ['en' => $row['meta_title']] : null,
                'meta_des' => isset($row['meta_des']) ? ['en' => $row['meta_des']] : null,
                'image_ids' => null,
            ];

            try {
                $dto = StoreVaraintDTO::fromRequest($dtoData);
                $action = new StoreVaraintAction();
                $action->storeVariant($dto);
            } catch (\Throwable $e) {
                // skip row on error to continue processing others
                continue;
            }
        }
    }
}
