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


    // get price of bundel sum of product or varaint after thes discount
    public function getBundlePrice(): float
    {
        $total = 0.0;

        // Eager load products to avoid N+1 queries
        $details = $this->bundelDetails()->with('product')->get();

        foreach ($details as $detail) {
            if (!empty($detail->variant_ids)) {
                // Fetch only the first variant from the JSON array
                $variant = ProductVariant::find($detail->variant_ids[0]);
                $price   = $variant?->getDiscountPrice() ?? 0;
            } else {
                $price = $detail->product?->getDiscountPrice() ?? 0;
            }

            $total += $price * $detail->quantity;
        }

        return $total;
    }










}
