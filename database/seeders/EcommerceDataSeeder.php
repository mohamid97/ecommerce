<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Api\Admin\Category;
use App\Models\Api\Admin\Brand;
use App\Models\User;
use App\Models\Api\Ecommerce\Option;
use App\Models\Api\Ecommerce\OptionValue;
use App\Models\Api\Admin\Product;
use App\Models\Api\Ecommerce\ProductOption;
use App\Models\Api\Ecommerce\ProductOptionValue;
use App\Models\Api\Ecommerce\ProductVariant;
use App\Models\Api\Ecommerce\VariantOptionValue;
use App\Models\Api\Ecommerce\Bundel;
use App\Models\Api\Ecommerce\BundelDetails;
use App\Models\Api\Ecommerce\StockMovment;
use App\Models\Api\Ecommerce\ShipmentZone;
use App\Models\Api\Ecommerce\ShipmentCity;
use App\Models\Api\Ecommerce\Cart;
use App\Models\Api\Ecommerce\CartBundelItem;
use App\Models\Api\Ecommerce\Order;
use App\Models\Api\Ecommerce\LastPiece;
use App\Models\Api\Ecommerce\NewProduct;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EcommerceDataSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $categoryTech = $this->firstOrCreateCategory('electronics', 'Electronics', 'الإلكترونيات');
            $categoryFashion = $this->firstOrCreateCategory('fashion', 'Fashion', 'الأزياء');

            $brandCanGrow = $this->firstOrCreateBrand('cangrow-brand', 'CanGrow', 'كان جرو');
            $brandNova = $this->firstOrCreateBrand('nova-brand', 'Nova', 'نوفا');

            $optionColor = $this->firstOrCreateOption('color', 'Color', 'اللون');
            $optionSize = $this->firstOrCreateOption('size', 'Size', 'المقاس');

            $colorRed = $this->firstOrCreateOptionValue($optionColor, 'Red', 'أحمر');
            $colorBlue = $this->firstOrCreateOptionValue($optionColor, 'Blue', 'أزرق');
            $colorBlack = $this->firstOrCreateOptionValue($optionColor, 'Black', 'أسود');
            $sizeSmall = $this->firstOrCreateOptionValue($optionSize, 'Small', 'صغير');
            $sizeMedium = $this->firstOrCreateOptionValue($optionSize, 'Medium', 'متوسط');
            $sizeLarge = $this->firstOrCreateOptionValue($optionSize, 'Large', 'كبير');

            $simpleMouse = $this->firstOrCreateSimpleProduct(
                sku: 'SKU-MOUSE-001',
                enTitle: 'Wireless Mouse',
                arTitle: 'ماوس لاسلكي',
                salePrice: 20,
                discount: 0,
                discountType: 'percentage',
                categoryId: $categoryTech->id,
                brandId: $brandCanGrow->id,
                stock: 80
            );

            $simpleKeyboard = $this->firstOrCreateSimpleProduct(
                sku: 'SKU-KEYBOARD-001',
                enTitle: 'Mechanical Keyboard',
                arTitle: 'لوحة مفاتيح ميكانيكية',
                salePrice: 65,
                discount: 10,
                discountType: 'fixed',
                categoryId: $categoryTech->id,
                brandId: $brandNova->id,
                stock: 40
            );

            $simpleHeadset = $this->firstOrCreateSimpleProduct(
                sku: 'SKU-HEADSET-001',
                enTitle: 'Gaming Headset',
                arTitle: 'سماعة ألعاب',
                salePrice: 48,
                discount: 5,
                discountType: 'percentage',
                categoryId: $categoryTech->id,
                brandId: $brandCanGrow->id,
                stock: 55
            );

            $simpleHoodie = $this->firstOrCreateSimpleProduct(
                sku: 'SKU-HOODIE-001',
                enTitle: 'Basic Hoodie',
                arTitle: 'هودي أساسي',
                salePrice: 35,
                discount: 0,
                discountType: 'percentage',
                categoryId: $categoryFashion->id,
                brandId: $brandNova->id,
                stock: 60
            );

            $tshirtProduct = $this->firstOrCreateVariableProduct(
                sku: 'SKU-TSHIRT-001',
                enTitle: 'Cotton T-Shirt',
                arTitle: 'تيشيرت قطني',
                salePrice: 50,
                categoryId: $categoryFashion->id,
                brandId: $brandCanGrow->id
            );

            $poloProduct = $this->firstOrCreateVariableProduct(
                sku: 'SKU-POLO-001',
                enTitle: 'Polo Shirt',
                arTitle: 'قميص بولو',
                salePrice: 70,
                categoryId: $categoryFashion->id,
                brandId: $brandNova->id
            );

            $this->attachOptionsToProduct($tshirtProduct, $optionColor, [$colorRed, $colorBlue, $colorBlack]);
            $this->attachOptionsToProduct($tshirtProduct, $optionSize, [$sizeSmall, $sizeMedium, $sizeLarge]);

            $tshirtRedSmall = $this->firstOrCreateVariant(
                $tshirtProduct->id,
                'SKU-TSHIRT-R-S',
                'Cotton T-Shirt - Red Small',
                'تيشيرت قطني - أحمر صغير',
                50,
                0,
                'percentage',
                25,
                true
            );
            $this->attachVariantValues($tshirtRedSmall, [$colorRed, $sizeSmall]);

            $tshirtBlueMedium = $this->firstOrCreateVariant(
                $tshirtProduct->id,
                'SKU-TSHIRT-B-M',
                'Cotton T-Shirt - Blue Medium',
                'تيشيرت قطني - أزرق متوسط',
                52,
                3,
                'fixed',
                20,
                false
            );
            $this->attachVariantValues($tshirtBlueMedium, [$colorBlue, $sizeMedium]);

            $tshirtBlackLarge = $this->firstOrCreateVariant(
                $tshirtProduct->id,
                'SKU-TSHIRT-BLK-L',
                'Cotton T-Shirt - Black Large',
                'تيشيرت قطني - أسود كبير',
                55,
                10,
                'percentage',
                18,
                false
            );
            $this->attachVariantValues($tshirtBlackLarge, [$colorBlack, $sizeLarge]);

            $this->attachOptionsToProduct($poloProduct, $optionColor, [$colorBlue, $colorBlack]);
            $this->attachOptionsToProduct($poloProduct, $optionSize, [$sizeMedium, $sizeLarge]);

            $poloBlueMedium = $this->firstOrCreateVariant(
                $poloProduct->id,
                'SKU-POLO-B-M',
                'Polo Shirt - Blue Medium',
                'قميص بولو - أزرق متوسط',
                72,
                2,
                'fixed',
                15,
                true
            );
            $this->attachVariantValues($poloBlueMedium, [$colorBlue, $sizeMedium]);

            $poloBlackLarge = $this->firstOrCreateVariant(
                $poloProduct->id,
                'SKU-POLO-BLK-L',
                'Polo Shirt - Black Large',
                'قميص بولو - أسود كبير',
                75,
                5,
                'percentage',
                10,
                false
            );
            $this->attachVariantValues($poloBlackLarge, [$colorBlack, $sizeLarge]);

            $this->seedStocksAndShipments(
                [$simpleMouse, $simpleKeyboard, $simpleHeadset, $simpleHoodie],
                [$tshirtRedSmall, $tshirtBlueMedium, $tshirtBlackLarge, $poloBlueMedium, $poloBlackLarge]
            );

            $bundle = $this->firstOrCreateBundle(
                'summer-setup',
                'Summer Setup Bundle',
                'باقة الصيف المتكاملة',
                $categoryTech->id,
                $brandCanGrow->id,
                135
            );

            $this->firstOrCreateBundleDetail($bundle->id, $simpleMouse->id, null, 1);
            $this->firstOrCreateBundleDetail($bundle->id, $tshirtProduct->id, [$tshirtRedSmall->id, $tshirtBlueMedium->id], 2);

            DB::table('related_products')->updateOrInsert(
                ['product_id' => $simpleMouse->id, 'related_product_id' => $simpleKeyboard->id],
                ['created_at' => now(), 'updated_at' => now()]
            );
            DB::table('related_products')->updateOrInsert(
                ['product_id' => $simpleKeyboard->id, 'related_product_id' => $simpleHeadset->id],
                ['created_at' => now(), 'updated_at' => now()]
            );

            LastPiece::firstOrCreate(['product_id' => $simpleHoodie->id]);
            LastPiece::firstOrCreate(['product_id' => $tshirtProduct->id]);
            NewProduct::firstOrCreate(['product_id' => $simpleKeyboard->id]);
            NewProduct::firstOrCreate(['product_id' => $poloProduct->id]);

            [$zone, $city] = $this->seedShipmentZoneAndCity();
            $this->seedCartsAndOrders(
                $bundle,
                $simpleMouse,
                $simpleKeyboard,
                $tshirtProduct,
                $tshirtBlueMedium,
                $zone->id,
                $city->id
            );
        });
    }

    private function firstOrCreateCategory(string $slug, string $enTitle, string $arTitle): Category
    {
        $category = Category::whereHas('translations', function ($q) use ($slug) {
            $q->where('locale', 'en')->where('slug', $slug);
        })->first();

        if (!$category) {
            $category = Category::create();
        }

        $category->translateOrNew('en')->title = $enTitle;
        $category->translateOrNew('en')->slug = $slug;
        $category->translateOrNew('ar')->title = $arTitle;
        $category->translateOrNew('ar')->slug = $slug . '-ar';
        $category->save();

        return $category;
    }

    private function firstOrCreateBrand(string $slug, string $enTitle, string $arTitle): Brand
    {
        $brand = Brand::whereHas('translations', function ($q) use ($slug) {
            $q->where('slug', $slug);
        })->first();

        if (!$brand) {
            $brand = Brand::create();
        }

        $brand->translateOrNew('en')->title = $enTitle;
        $brand->translateOrNew('en')->slug = $slug;
        $brand->translateOrNew('ar')->title = $arTitle;
        $brand->translateOrNew('ar')->slug = $slug . '-ar';
        $brand->save();

        return $brand;
    }

    private function firstOrCreateOption(string $code, string $enTitle, string $arTitle): Option
    {
        $option = Option::firstOrCreate(
            ['code' => $code],
            ['value_type' => 'text']
        );
        $option->translateOrNew('en')->title = $enTitle;
        $option->translateOrNew('ar')->title = $arTitle;
        $option->save();

        return $option;
    }

    private function firstOrCreateOptionValue(Option $option, string $enTitle, string $arTitle): OptionValue
    {
        $value = OptionValue::where('option_id', $option->id)
            ->whereHas('translations', function ($q) use ($enTitle) {
                $q->where('locale', 'en')->where('title', $enTitle);
            })->first();

        if (!$value) {
            $value = OptionValue::create(['option_id' => $option->id]);
        }

        $value->translateOrNew('en')->title = $enTitle;
        $value->translateOrNew('ar')->title = $arTitle;
        $value->save();

        return $value;
    }

    private function firstOrCreateSimpleProduct(
        string $sku,
        string $enTitle,
        string $arTitle,
        float $salePrice,
        float $discount,
        string $discountType,
        int $categoryId,
        int $brandId,
        int $stock
    ): Product {
        $product = Product::firstOrCreate(
            ['sku' => $sku],
            [
                'stock' => $stock,
                'sale_price' => $salePrice,
                'discount' => $discount,
                'discount_type' => $discountType,
                'has_options' => false,
                'status' => 'active',
                'category_id' => $categoryId,
                'brand_id' => $brandId,
                'on_demand' => false,
            ]
        );

        $product->update([
            'stock' => $stock,
            'sale_price' => $salePrice,
            'discount' => $discount,
            'discount_type' => $discountType,
            'has_options' => false,
            'status' => 'active',
            'category_id' => $categoryId,
            'brand_id' => $brandId,
        ]);

        $baseSlug = Str::slug($enTitle);
        $product->translateOrNew('en')->title = $enTitle;
        $product->translateOrNew('en')->slug = $baseSlug;
        $product->translateOrNew('ar')->title = $arTitle;
        $product->translateOrNew('ar')->slug = $baseSlug . '-ar';
        $product->save();

        return $product;
    }

    private function firstOrCreateVariableProduct(
        string $sku,
        string $enTitle,
        string $arTitle,
        float $salePrice,
        int $categoryId,
        int $brandId
    ): Product {
        $product = Product::firstOrCreate(
            ['sku' => $sku],
            [
                'stock' => 0,
                'sale_price' => $salePrice,
                'discount' => 0,
                'discount_type' => 'percentage',
                'has_options' => true,
                'status' => 'active',
                'category_id' => $categoryId,
                'brand_id' => $brandId,
                'on_demand' => false,
            ]
        );

        $product->update([
            'sale_price' => $salePrice,
            'has_options' => true,
            'status' => 'active',
            'category_id' => $categoryId,
            'brand_id' => $brandId,
        ]);

        $baseSlug = Str::slug($enTitle);
        $product->translateOrNew('en')->title = $enTitle;
        $product->translateOrNew('en')->slug = $baseSlug;
        $product->translateOrNew('ar')->title = $arTitle;
        $product->translateOrNew('ar')->slug = $baseSlug . '-ar';
        $product->save();

        return $product;
    }

    private function attachOptionsToProduct(Product $product, Option $option, array $optionValues): void
    {
        $productOption = ProductOption::firstOrCreate([
            'product_id' => $product->id,
            'option_id' => $option->id,
        ]);

        foreach ($optionValues as $optionValue) {
            ProductOptionValue::firstOrCreate([
                'product_option_id' => $productOption->id,
                'option_value_id' => $optionValue->id,
            ]);
        }
    }

    private function firstOrCreateVariant(
        int $productId,
        string $sku,
        string $enTitle,
        string $arTitle,
        float $salePrice,
        float $discountValue,
        string $discountType,
        int $stock,
        bool $isDefault
    ): ProductVariant {
        $variant = ProductVariant::firstOrCreate(
            ['sku' => $sku],
            [
                'product_id' => $productId,
                'is_default' => $isDefault,
                'status' => 'active',
                'stock' => $stock,
                'sale_price' => $salePrice,
                'discount_value' => $discountValue,
                'discount_type' => $discountType,
            ]
        );

        $variant->update([
            'product_id' => $productId,
            'is_default' => $isDefault,
            'status' => 'active',
            'stock' => $stock,
            'sale_price' => $salePrice,
            'discount_value' => $discountValue,
            'discount_type' => $discountType,
        ]);

        $slug = Str::slug($enTitle);
        $variant->translateOrNew('en')->title = $enTitle;
        $variant->translateOrNew('en')->slug = $slug;
        $variant->translateOrNew('ar')->title = $arTitle;
        $variant->translateOrNew('ar')->slug = $slug . '-ar';
        $variant->save();

        return $variant;
    }

    private function attachVariantValues(ProductVariant $variant, array $optionValues): void
    {
        foreach ($optionValues as $optionValue) {
            VariantOptionValue::firstOrCreate([
                'product_variant_id' => $variant->id,
                'option_id' => $optionValue->option_id,
                'option_value_id' => $optionValue->id,
            ]);
        }
    }

    private function seedStocksAndShipments(array $simpleProducts, array $variants): void
    {
        foreach ($simpleProducts as $product) {
            DB::table('no_option_stocks')->updateOrInsert(
                ['product_id' => $product->id],
                [
                    'base_price' => $product->sale_price,
                    'sku' => $product->sku,
                    'stock' => $product->stock,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );

            DB::table('product_shipements')->updateOrInsert(
                ['product_id' => $product->id, 'variant_id' => null],
                [
                    'weight' => 1.5,
                    'length' => 20,
                    'width' => 15,
                    'height' => 5,
                    'min_estimated_delivery' => 2,
                    'max_estimated_delivery' => 5,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );

            StockMovment::updateOrCreate(
                ['product_id' => $product->id, 'variant_id' => null, 'note' => 'Initial stock for ' . $product->sku],
                [
                    'quantity' => $product->stock,
                    'cost_price' => round($product->sale_price * 0.6, 2),
                    'sale_price' => $product->sale_price,
                    'status' => 'active',
                ]
            );
        }

        foreach ($variants as $variant) {
            DB::table('product_shipements')->updateOrInsert(
                ['product_id' => $variant->product_id, 'variant_id' => $variant->id],
                [
                    'weight' => 0.7,
                    'length' => 30,
                    'width' => 20,
                    'height' => 3,
                    'min_estimated_delivery' => 1,
                    'max_estimated_delivery' => 4,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );

            StockMovment::updateOrCreate(
                ['product_id' => $variant->product_id, 'variant_id' => $variant->id, 'note' => 'Variant stock for ' . $variant->sku],
                [
                    'quantity' => $variant->stock,
                    'cost_price' => round($variant->sale_price * 0.55, 2),
                    'sale_price' => $variant->sale_price,
                    'status' => 'active',
                ]
            );
        }
    }

    private function firstOrCreateBundle(
        string $slug,
        string $enTitle,
        string $arTitle,
        int $categoryId,
        int $brandId,
        float $price
    ): Bundel {
        $bundle = Bundel::whereHas('translations', function ($q) use ($slug) {
            $q->where('locale', 'en')->where('slug', $slug);
        })->first();

        if (!$bundle) {
            $bundle = Bundel::create([
                'price' => $price,
                'category_id' => $categoryId,
                'brand_id' => $brandId,
                'status' => 'active',
            ]);
        } else {
            $bundle->update([
                'price' => $price,
                'category_id' => $categoryId,
                'brand_id' => $brandId,
                'status' => 'active',
            ]);
        }

        $bundle->translateOrNew('en')->title = $enTitle;
        $bundle->translateOrNew('en')->slug = $slug;
        $bundle->translateOrNew('ar')->title = $arTitle;
        $bundle->translateOrNew('ar')->slug = $slug . '-ar';
        $bundle->save();

        return $bundle;
    }

    private function firstOrCreateBundleDetail(int $bundleId, int $productId, ?array $variantIds, int $quantity): void
    {
        BundelDetails::updateOrCreate(
            ['bundel_id' => $bundleId, 'product_id' => $productId],
            ['variant_ids' => $variantIds, 'quantity' => $quantity]
        );
    }

    private function seedShipmentZoneAndCity(): array
    {
        $zone = ShipmentZone::firstOrCreate(
            ['price' => 25],
            ['status' => 'active']
        );
        $zone->translateOrNew('en')->title = 'Cairo Zone';
        $zone->translateOrNew('en')->des = 'Main Cairo shipping zone';
        $zone->translateOrNew('ar')->title = 'منطقة القاهرة';
        $zone->translateOrNew('ar')->des = 'منطقة شحن القاهرة';
        $zone->save();

        $city = ShipmentCity::firstOrCreate(
            ['zone_id' => $zone->id],
            ['status' => 'active']
        );
        $city->translateOrNew('en')->title = 'Nasr City';
        $city->translateOrNew('en')->des = 'Fast shipping city';
        $city->translateOrNew('ar')->title = 'مدينة نصر';
        $city->translateOrNew('ar')->des = 'مدينة شحن سريع';
        $city->save();

        return [$zone, $city];
    }

    private function seedCartsAndOrders(
        Bundel $bundle,
        Product $simpleMouse,
        Product $simpleKeyboard,
        Product $tshirtProduct,
        ProductVariant $tshirtBlueMedium,
        int $zoneId,
        int $cityId
    ): void {
        $user = User::where('type', 'user')->first() ?? User::first();

        $cartOpen = Cart::firstOrCreate(
            ['user_id' => $user?->id, 'status' => 'open']
        );

        $productItemId = $this->upsertCartItem(
            $cartOpen->id,
            ['type' => 'product', 'product_id' => $simpleMouse->id, 'variant_id' => null, 'bundel_id' => null],
            ['quantity' => 2, 'total_before_discount' => 40, 'total_after_discount' => 40]
        );

        $variantItemId = $this->upsertCartItem(
            $cartOpen->id,
            ['type' => 'variant', 'product_id' => $tshirtProduct->id, 'variant_id' => $tshirtBlueMedium->id, 'bundel_id' => null],
            [
                'quantity' => 1,
                'total_before_discount' => $tshirtBlueMedium->sale_price,
                'total_after_discount' => $tshirtBlueMedium->getDiscountPrice(),
            ]
        );

        $bundleItemId = $this->upsertCartItem(
            $cartOpen->id,
            ['type' => 'bundel', 'product_id' => null, 'variant_id' => null, 'bundel_id' => $bundle->id],
            ['quantity' => 1, 'total_before_discount' => $bundle->price, 'total_after_discount' => $bundle->price]
        );

        CartBundelItem::firstOrCreate([
            'cart_item_id' => $bundleItemId,
            'product_id' => $simpleMouse->id,
            'variant_id' => null,
        ]);
        CartBundelItem::firstOrCreate([
            'cart_item_id' => $bundleItemId,
            'product_id' => $tshirtProduct->id,
            'variant_id' => $tshirtBlueMedium->id,
        ]);

        $stockSimple = StockMovment::where('product_id', $simpleMouse->id)->whereNull('variant_id')->first();
        $stockVariant = StockMovment::where('variant_id', $tshirtBlueMedium->id)->first();

        $subtotal = 40 + (float) $tshirtBlueMedium->getDiscountPrice() + (float) $bundle->price;
        $shipping = 25;
        $tax = round($subtotal * 0.14, 2);
        $total = $subtotal + $shipping + $tax;

        $order = Order::firstOrCreate(
            ['user_id' => $user?->id, 'status' => 'completed', 'shipment_address' => 'Test Street 123'],
            [
                'subtotal' => $subtotal,
                'shipping_cost' => $shipping,
                'tax' => $tax,
                'total' => $total,
                'points_used' => 20,
                'points_amount' => 10,
                'shipment_zone_id' => $zoneId,
                'shipment_city_id' => $cityId,
                'payment_method' => 'cash',
            ]
        );

        $this->upsertOrderItem(
            $order->id,
            ['product_id' => $simpleMouse->id, 'variant_id' => null, 'bundel_id' => null],
            [
                'quantity' => 2,
                'sale_price' => 20,
                'price_after_discount' => 20,
                'total_price' => 40,
                'total_price_after_discount' => 40,
                'unit_cost_price' => 12,
                'bundel_snapshot' => null,
            ],
            $stockSimple?->id
        );

        $this->upsertOrderItem(
            $order->id,
            ['product_id' => $tshirtProduct->id, 'variant_id' => $tshirtBlueMedium->id, 'bundel_id' => null],
            [
                'quantity' => 1,
                'sale_price' => $tshirtBlueMedium->sale_price,
                'price_after_discount' => $tshirtBlueMedium->getDiscountPrice(),
                'total_price' => $tshirtBlueMedium->sale_price,
                'total_price_after_discount' => $tshirtBlueMedium->getDiscountPrice(),
                'unit_cost_price' => 28,
                'bundel_snapshot' => null,
            ],
            $stockVariant?->id
        );

        $this->upsertOrderItem(
            $order->id,
            ['product_id' => null, 'variant_id' => null, 'bundel_id' => $bundle->id],
            [
                'quantity' => 1,
                'sale_price' => $bundle->price,
                'price_after_discount' => $bundle->price,
                'total_price' => $bundle->price,
                'total_price_after_discount' => $bundle->price,
                'unit_cost_price' => 75,
                'bundel_snapshot' => [
                    'bundel_id' => $bundle->id,
                    'title' => $bundle->title,
                    'items' => [
                        ['product_id' => $simpleMouse->id, 'quantity' => 1],
                        ['product_id' => $tshirtProduct->id, 'variant_id' => $tshirtBlueMedium->id, 'quantity' => 2],
                    ],
                ],
            ],
            null
        );

        Order::firstOrCreate(
            ['status' => 'pending', 'shipment_address' => 'Guest Address 77'],
            [
                'user_id' => null,
                'subtotal' => $simpleKeyboard->sale_price,
                'shipping_cost' => 15,
                'tax' => round($simpleKeyboard->sale_price * 0.14, 2),
                'total' => round($simpleKeyboard->sale_price * 1.14 + 15, 2),
                'shipment_zone_id' => $zoneId,
                'shipment_city_id' => $cityId,
                'payment_method' => 'card',
            ]
        );

        $this->upsertCartItem(
            $cartOpen->id,
            ['type' => 'product', 'product_id' => $simpleMouse->id, 'variant_id' => null, 'bundel_id' => null],
            ['quantity' => 2, 'total_before_discount' => 40, 'total_after_discount' => 40]
        );
        $this->upsertCartItem(
            $cartOpen->id,
            ['type' => 'variant', 'product_id' => $tshirtProduct->id, 'variant_id' => $tshirtBlueMedium->id, 'bundel_id' => null],
            [
                'quantity' => 1,
                'total_before_discount' => $tshirtBlueMedium->sale_price,
                'total_after_discount' => $tshirtBlueMedium->getDiscountPrice(),
            ]
        );
        $this->upsertCartItem(
            $cartOpen->id,
            ['type' => 'bundel', 'product_id' => null, 'variant_id' => null, 'bundel_id' => $bundle->id],
            ['quantity' => 1, 'total_before_discount' => $bundle->price, 'total_after_discount' => $bundle->price]
        );
    }

    private function upsertCartItem(int $cartId, array $where, array $data): int
    {
        $query = DB::table('cart_items')->where('cart_id', $cartId)->where('type', $where['type']);
        foreach (['product_id', 'variant_id', 'bundel_id'] as $key) {
            if (is_null($where[$key])) {
                $query->whereNull($key);
            } else {
                $query->where($key, $where[$key]);
            }
        }

        $existing = $query->first();
        if ($existing) {
            DB::table('cart_items')->where('id', $existing->id)->update(array_merge($data, ['updated_at' => now()]));
            return (int) $existing->id;
        }

        return (int) DB::table('cart_items')->insertGetId(array_merge(
            ['cart_id' => $cartId, 'type' => $where['type']],
            $where,
            $data,
            ['created_at' => now(), 'updated_at' => now()]
        ));
    }

    private function upsertOrderItem(int $orderId, array $where, array $data, ?int $stockMovementId): void
    {
        $query = DB::table('order_items')->where('order_id', $orderId);
        foreach (['product_id', 'variant_id', 'bundel_id'] as $key) {
            if (is_null($where[$key])) {
                $query->whereNull($key);
            } else {
                $query->where($key, $where[$key]);
            }
        }

        $existing = $query->first();
        if ($existing) {
            DB::table('order_items')->where('id', $existing->id)->update(array_merge($data, ['updated_at' => now()]));
            $orderItemId = $existing->id;
        } else {
            $orderItemId = DB::table('order_items')->insertGetId(array_merge(
                $where,
                $data,
                ['order_id' => $orderId, 'created_at' => now(), 'updated_at' => now()]
            ));
        }

        $batchQuery = DB::table('order_item_batches')->where('order_item_id', $orderItemId);
        if (is_null($stockMovementId)) {
            $batchQuery->whereNull('stock_movment_id');
        } else {
            $batchQuery->where('stock_movment_id', $stockMovementId);
        }

        $existingBatch = $batchQuery->first();
        if ($existingBatch) {
            DB::table('order_item_batches')->where('id', $existingBatch->id)->update([
                'quantity' => $data['quantity'],
                'sale_price' => $data['price_after_discount'],
                'cost_price' => $data['unit_cost_price'],
                'updated_at' => now(),
            ]);
        } else {
            DB::table('order_item_batches')->insert([
                'order_item_id' => $orderItemId,
                'stock_movment_id' => $stockMovementId,
                'quantity' => $data['quantity'],
                'sale_price' => $data['price_after_discount'],
                'cost_price' => $data['unit_cost_price'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
