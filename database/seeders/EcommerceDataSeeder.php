<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
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
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 0. Ensure we have Category / Brand to satisfy any Foreign Keys. 
        $category = Category::first() ?? Category::create([
            
            'en' => ['title' => 'Default Category'],
            'ar' => ['title' => 'الفئة الافتراضية']
        ]);
        
        // $brand = Brand::first() ?? Brand::create([
        //     'en' => ['title' => 'Default Brand'],
        //     'ar' => ['title' => 'العلامة الافتراضية']
        // ]);

        // // 1. Create Options (Dimensions)
        // $optionColor = Option::create([
        //     'value_type' => 'text',
        //     'code' => 'color',
        //     'en' => ['title' => 'Color'],
        //     'ar' => ['title' => 'اللون']
        // ]);

        // $optionSize = Option::create([
        //     'value_type' => 'text', // e.g. text or dropdown
        //     'code' => 'size',
        //     'en' => ['title' => 'Size'],
        //     'ar' => ['title' => 'المقاس']
        // ]);

        // // Color Values
        // $colorRed = OptionValue::create([
        //     'option_id' => $optionColor->id,
        //     'en' => ['title' => 'Red'],
        //     'ar' => ['title' => 'أحمر']
        // ]);

        // $colorBlue = OptionValue::create([
        //     'option_id' => $optionColor->id,
        //     'en' => ['title' => 'Blue'],
        //     'ar' => ['title' => 'أزرق']
        // ]);

        // // Size Values
        // $sizeSmall = OptionValue::create([
        //     'option_id' => $optionSize->id,
        //     'en' => ['title' => 'Small'],
        //     'ar' => ['title' => 'صغير']
        // ]);

        // $sizeLarge = OptionValue::create([
        //     'option_id' => $optionSize->id,
        //     'en' => ['title' => 'Large'],
        //     'ar' => ['title' => 'كبير']
        // ]);

        // 2. Create Simple Product
        // $simpleProduct = Product::create([
        //     'sku' => 'SKU-MOUSE-001',
        //     'stock' => 50,
        //     'sale_price' => 20,
        //     'discount' => 0,
        //     'discount_type' => 'percentage',
        //     'has_options' => false,
        //     'status' => 'active',
        //     'category_id' => $category->id,
        //     'brand_id' => $brand->id,
        //     'en' => ['title' => 'Wireless Mouse', 'slug' => 'wireless-mouse'],
        //     'ar' => ['title' => 'ماوس لاسلكي', 'slug' => 'wireless-mouse-ar']
        // ]);

        // // 3. Create Variable Product
        // $variableProduct = Product::create([
        //     'sku' => 'SKU-TSHIRT-001',
        //     'stock' => 100,
        //     'sale_price' => 50,
        //     'discount' => 0,
        //     'discount_type' => 'percentage',
        //     'has_options' => true,
        //     'status' => 'active',
        //     'category_id' => $category->id,
        //     'brand_id' => $brand->id,
        //     'en' => ['title' => 'Cotton T-Shirt', 'slug' => 'cotton-tshirt'],
        //     'ar' => ['title' => 'تيشيرت قطني', 'slug' => 'cotton-tshirt-ar']
        // ]);

        // // 4. Attach Options to Product
        // $productOptionColor = ProductOption::create([
        //     'product_id' => $variableProduct->id,
        //     'option_id' => $optionColor->id
        // ]);
        
        // $productOptionSize = ProductOption::create([
        //     'product_id' => $variableProduct->id,
        //     'option_id' => $optionSize->id
        // ]);

        // // Attach Available Option Values to Product
        // ProductOptionValue::create(['product_option_id' => $productOptionColor->id, 'option_value_id' => $colorRed->id]);
        // ProductOptionValue::create(['product_option_id' => $productOptionColor->id, 'option_value_id' => $colorBlue->id]);
        // ProductOptionValue::create(['product_option_id' => $productOptionSize->id, 'option_value_id' => $sizeSmall->id]);
        // ProductOptionValue::create(['product_option_id' => $productOptionSize->id, 'option_value_id' => $sizeLarge->id]);

        // // 5. Create Product Variants
        // // -> Variant 1: Red & Small
        // $variantRedSmall = ProductVariant::create([
        //     'product_id' => $variableProduct->id,
        //     'is_default' => 1,
        //     'sku' => 'SKU-TSHIRT-R-S',
        //     'status' => 'active',
        //     'stock' => 25,
        //     'sale_price' => 50,
        //     'discount_value' => 0,
        //     'discount_type' => 'percentage',
        //     'en' => ['title' => 'Cotton T-Shirt - Red Small', 'slug' => 'cotton-tshirt-red-small'],
        //     'ar' => ['title' => 'تيشيرت قطني - أحمر صغير', 'slug' => 'cotton-tshirt-red-small-ar']
        // ]);
        // VariantOptionValue::create(['product_variant_id' => $variantRedSmall->id, 'option_value_id' => $colorRed->id]);
        // VariantOptionValue::create(['product_variant_id' => $variantRedSmall->id, 'option_value_id' => $sizeSmall->id]);

        // // -> Variant 2: Blue & Large
        // $variantBlueLarge = ProductVariant::create([
        //     'product_id' => $variableProduct->id,
        //     'is_default' => 0,
        //     'sku' => 'SKU-TSHIRT-B-L',
        //     'status' => 'active',
        //     'stock' => 25,
        //     'sale_price' => 55, // Premium size
        //     'discount_value' => 5, // Has 5.00 fixed discount
        //     'discount_type' => 'fixed',
        //     'en' => ['title' => 'Cotton T-Shirt - Blue Large', 'slug' => 'cotton-tshirt-blue-large'],
        //     'ar' => ['title' => 'تيشيرت قطني - أزرق كبير', 'slug' => 'cotton-tshirt-blue-large-ar']
        // ]);
        // VariantOptionValue::create(['product_variant_id' => $variantBlueLarge->id, 'option_value_id' => $colorBlue->id]);
        // VariantOptionValue::create(['product_variant_id' => $variantBlueLarge->id, 'option_value_id' => $sizeLarge->id]);

        // // 6. Create Bundle
        // $bundle = Bundel::create([
        //     'price' => 100, // Bundle promotional fixed price
        //     'category_id' => $category->id,
        //     'brand_id' => $brand->id,
        //     'status' => 'active',
        //     'en' => ['title' => 'Summer Setup Bundle', 'slug' => 'summer-setup'],
        //     'ar' => ['title' => 'باقة التجهيز الصيفي', 'slug' => 'summer-setup-ar']
        // ]);

        // // 7. Bundle Details Linking
        // // Link the simple product
        // BundelDetails::create([
        //     'bundel_id' => $bundle->id,
        //     'product_id' => $simpleProduct->id,
        //     'variant_ids' => null, 
        //     'quantity' => 1
        // ]);

        // // Link the variable product (T-shirt), mapping the bundle specifically to allow BOTH Red-Small and Blue-Large
        // // When checking out, user can pick which variant they want for this part of the bundle.
        // BundelDetails::create([
        //     'bundel_id' => $bundle->id,
        //     'product_id' => $variableProduct->id,
        //     // JSON encode bypassing array-casting grammar logic directly
        //     'variant_ids' => json_encode([$variantRedSmall->id, $variantBlueLarge->id]), 
        //     'quantity' => 2 // The bundle includes 2 T-shirts of the allowed variations
        // ]);
    }
}
