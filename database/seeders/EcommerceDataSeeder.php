<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Api\Admin\Category;
use App\Models\Api\Admin\Brand;
use App\Models\Api\Ecommerce\Option;
use App\Models\Api\Ecommerce\OptionValue;
use App\Models\Api\Admin\Product;
use App\Models\Api\Ecommerce\ProductOption;
use App\Models\Api\Ecommerce\ProductOptionValue;
use App\Models\Api\Ecommerce\ProductVariant;
use App\Models\Api\Ecommerce\VariantOptionValue;
use App\Models\Api\Ecommerce\Bundel;
use App\Models\Api\Ecommerce\BundelDetails;

class EcommerceDataSeeder extends Seeder
{
    public function run(): void
    {
        // 0. Ensure we have Category / Brand using explicitly declared saves 
        // to bypass ANY array indexing failures during Laravel insert statements!
        $category = Category::first();
        if (!$category) {
            $category = Category::create();
            $category->translateOrNew('en')->title = 'Default Category';
            $category->translateOrNew('en')->slug  = 'default-category';
            $category->translateOrNew('ar')->title = 'الفئة الافتراضية';
            $category->translateOrNew('ar')->slug  = 'default-category-ar';
            $category->save();
        }

        $brand = Brand::first();
        if (!$brand) {
            $brand = Brand::create();
            $brand->translateOrNew('en')->title = 'Default Brand';
            $brand->translateOrNew('en')->slug  = 'default-brand';
            $brand->translateOrNew('ar')->title = 'العلامة الافتراضية';
            $brand->translateOrNew('ar')->slug  = 'default-brand-ar';
            $brand->save();
        }

        // 1. Create Options (Dimensions)
        $optionColor = Option::create(['value_type' => 'text', 'code' => 'color']);
        $optionColor->translateOrNew('en')->title = 'Color';
        $optionColor->translateOrNew('ar')->title = 'اللون';
        $optionColor->save();

        $optionSize = Option::create(['value_type' => 'text', 'code' => 'size']);
        $optionSize->translateOrNew('en')->title = 'Size';
        $optionSize->translateOrNew('ar')->title = 'المقاس';
        $optionSize->save();

        // Color Values
        $colorRed = OptionValue::create(['option_id' => $optionColor->id]);
        $colorRed->translateOrNew('en')->title = 'Red';
        $colorRed->translateOrNew('ar')->title = 'أحمر';
        $colorRed->save();

        $colorBlue = OptionValue::create(['option_id' => $optionColor->id]);
        $colorBlue->translateOrNew('en')->title = 'Blue';
        $colorBlue->translateOrNew('ar')->title = 'أزرق';
        $colorBlue->save();

        // Size Values
        $sizeSmall = OptionValue::create(['option_id' => $optionSize->id]);
        $sizeSmall->translateOrNew('en')->title = 'Small';
        $sizeSmall->translateOrNew('ar')->title = 'صغير';
        $sizeSmall->save();

        $sizeLarge = OptionValue::create(['option_id' => $optionSize->id]);
        $sizeLarge->translateOrNew('en')->title = 'Large';
        $sizeLarge->translateOrNew('ar')->title = 'كبير';
        $sizeLarge->save();

        // 2. Create Simple Product
        $simpleProduct = Product::create([
            'sku' => 'SKU-MOUSE-001',
            'stock' => 50,
            'sale_price' => 20,
            'discount' => 0,
            'discount_type' => 'percentage',
            'has_options' => false,
            'status' => 'active',
            'category_id' => $category->id,
            'brand_id' => $brand->id
        ]);
        $simpleProduct->translateOrNew('en')->title = 'Wireless Mouse';
        $simpleProduct->translateOrNew('en')->slug = 'wireless-mouse';
        $simpleProduct->translateOrNew('ar')->title = 'ماوس لاسلكي';
        $simpleProduct->translateOrNew('ar')->slug = 'wireless-mouse-ar';
        $simpleProduct->save();

        // 3. Create Variable Product
        $variableProduct = Product::create([
            'sku' => 'SKU-TSHIRT-001',
            'stock' => 100,
            'sale_price' => 50,
            'discount' => 0,
            'discount_type' => 'percentage',
            'has_options' => true,
            'status' => 'active',
            'category_id' => $category->id,
            'brand_id' => $brand->id
        ]);
        $variableProduct->translateOrNew('en')->title = 'Cotton T-Shirt';
        $variableProduct->translateOrNew('en')->slug = 'cotton-tshirt';
        $variableProduct->translateOrNew('ar')->title = 'تيشيرت قطني';
        $variableProduct->translateOrNew('ar')->slug = 'cotton-tshirt-ar';
        $variableProduct->save();

        // 4. Attach Options to Product
        $productOptionColor = ProductOption::create(['product_id' => $variableProduct->id, 'option_id' => $optionColor->id]);
        $productOptionSize = ProductOption::create(['product_id' => $variableProduct->id, 'option_id' => $optionSize->id]);

        ProductOptionValue::create(['product_option_id' => $productOptionColor->id, 'option_value_id' => $colorRed->id]);
        ProductOptionValue::create(['product_option_id' => $productOptionColor->id, 'option_value_id' => $colorBlue->id]);
        ProductOptionValue::create(['product_option_id' => $productOptionSize->id, 'option_value_id' => $sizeSmall->id]);
        ProductOptionValue::create(['product_option_id' => $productOptionSize->id, 'option_value_id' => $sizeLarge->id]);

        // 5. Create Product Variants
        $variantRedSmall = ProductVariant::create([
            'product_id' => $variableProduct->id,
            'is_default' => 1,
            'sku' => 'SKU-TSHIRT-R-S',
            'status' => 'active',
            'stock' => 25,
            'sale_price' => 50,
            'discount_value' => 0,
            'discount_type' => 'percentage'
        ]);
        $variantRedSmall->translateOrNew('en')->title = 'Cotton T-Shirt - Red Small';
        $variantRedSmall->translateOrNew('en')->slug = 'cotton-tshirt-red-small';
        $variantRedSmall->translateOrNew('ar')->title = 'تيشيرت قطني - أحمر صغير';
        $variantRedSmall->translateOrNew('ar')->slug = 'cotton-tshirt-red-small-ar';
        $variantRedSmall->save();

        VariantOptionValue::create(['product_variant_id' => $variantRedSmall->id, 'option_value_id' => $colorRed->id]);
        VariantOptionValue::create(['product_variant_id' => $variantRedSmall->id, 'option_value_id' => $sizeSmall->id]);

        $variantBlueLarge = ProductVariant::create([
            'product_id' => $variableProduct->id,
            'is_default' => 0,
            'sku' => 'SKU-TSHIRT-B-L',
            'status' => 'active',
            'stock' => 25,
            'sale_price' => 55,
            'discount_value' => 5,
            'discount_type' => 'fixed'
        ]);
        $variantBlueLarge->translateOrNew('en')->title = 'Cotton T-Shirt - Blue Large';
        $variantBlueLarge->translateOrNew('en')->slug = 'cotton-tshirt-blue-large';
        $variantBlueLarge->translateOrNew('ar')->title = 'تيشيرت قطني - أزرق كبير';
        $variantBlueLarge->translateOrNew('ar')->slug = 'cotton-tshirt-blue-large-ar';
        $variantBlueLarge->save();

        VariantOptionValue::create(['product_variant_id' => $variantBlueLarge->id, 'option_value_id' => $colorBlue->id]);
        VariantOptionValue::create(['product_variant_id' => $variantBlueLarge->id, 'option_value_id' => $sizeLarge->id]);

        // 6. Create Bundle
        $bundle = Bundel::create([
            'price' => 100,
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'status' => 'active'
        ]);
        $bundle->translateOrNew('en')->title = 'Summer Setup Bundle';
        $bundle->translateOrNew('en')->slug = 'summer-setup';
        $bundle->translateOrNew('ar')->title = 'باقة التجهيز الصيفي';
        $bundle->translateOrNew('ar')->slug = 'summer-setup-ar';
        $bundle->save();

        // 7. Bundle Details Linking
        BundelDetails::create([
            'bundel_id' => $bundle->id,
            'product_id' => $simpleProduct->id,
            'variant_ids' => null, 
            'quantity' => 1
        ]);

        BundelDetails::create([
            'bundel_id' => $bundle->id,
            'product_id' => $variableProduct->id,
            'variant_ids' => json_encode([$variantRedSmall->id, $variantBlueLarge->id]), 
            'quantity' => 2
        ]);
    }
}
