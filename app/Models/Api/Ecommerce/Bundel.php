<?php

namespace App\Models\Api\Ecommerce;

use App\Models\Api\Admin\Brand;
use App\Models\Api\Admin\Category;
use App\Models\Api\Admin\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Bundel extends Model implements TranslatableContract
{
    use HasFactory , Translatable;
    protected $fillable = ['price' , 'category_id' , 'brand_id' , 'bundle_image' , 'status'];
    public $translatedAttributes = ['title' , 'slug' , 'des' , 'meta_title' , 'meta_des'];
    public $translationForeignKey = 'bundel_id';
    public $translationModel = 'App\Models\Api\Ecommerce\BundelTranslation';

    public function bundelDetails()
    {
       return $this->hasMany(BundelDetails::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function hasOnlyAvailableItems(): bool
    {
        return $this->detailsMatchAvailabilityRule(false);
    }

    public function hasOnlyActiveItems(): bool
    {
        return $this->detailsMatchAvailabilityRule(true);
    }

    protected function detailsMatchAvailabilityRule(bool $strictActive): bool
    {
        $details = $this->relationLoaded('bundelDetails')
            ? $this->bundelDetails
            : $this->bundelDetails()->with('product.variants')->get();

        if ($details->isEmpty()) {
            return false;
        }

        return $details->every(function ($detail) use ($strictActive) {
            $product = $detail->relationLoaded('product')
                ? $detail->product
                : $detail->product()->with('variants')->first();

            if (!$product) {
                return false;
            }

            if ($strictActive) {
                if ($product->status !== 'active') {
                    return false;
                }

                return $detail->hasAtLeastOneStrictlyActiveVariant();
            }

            if ($product->status === 'draft') {
                return false;
            }

            return $detail->hasAtLeastOneActiveVariant();
        });
    }


    // get price of bundel sum of product or varaint after thes discount
    public function getBundlePrice(): array
    {
        $totalPrice = 0.0;
        $totalDiscountPrice = 0.0;

        // Eager load products to avoid N+1 queries
        $details = $this->bundelDetails()->with('product')->get();

        foreach ($details as $detail) {
            if (!empty($detail->variant_ids)) {
               if(!is_array($detail->variant_ids)){
                $varaintsIds = json_decode($detail->variant_ids, true);
               }else{
                $varaintsIds = $detail->variant_ids;
               }
                $variant = ProductVariant::find($varaintsIds[0]);

                $discountPrice   = $variant?->getDiscountPrice() ?? 0;
                $price           = $variant?->sale_price ?? 0;
            } else {
                $discountPrice = $detail->product?->getDiscountPrice() ?? 0;
                $price = $detail->product?->sale_price ?? 0;
            }

            $totalDiscountPrice += $discountPrice * $detail->quantity;
            $totalPrice += $price * $detail->quantity;
        }

        return ['total_price' => $totalPrice, 'price_after_discount' => $totalDiscountPrice];
    }










}
