<?php

namespace App\Models\Api\Admin;

use App\Models\Api\Ecommerce\NoOptionStock;
use App\Models\Api\Ecommerce\ProductOption;
use App\Models\Api\Ecommerce\ProductShipement;
use App\Models\Api\Ecommerce\ProductVariant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Product extends Model implements TranslatableContract
{
    use HasFactory,Translatable;
    protected $fillable = ['product_image','sku','is_featured' , 'barcode','stock','sales_number','sale_price','discount','discount_type','has_options','on_demand' , 'status','breadcrumb' , 'order' , 'brand_id','category_id', 'moq'];
    public $translatedAttributes = ['title' , 'slug' , 'small_des' ,'des' , 'meta_title' , 'meta_des' , 'alt_image', 'title_image'];
    public $translationForeignKey = 'product_id';
    public $translationModel = 'App\Models\Api\Admin\ProductTranslation';

    protected static function booted()
    {
        static::deleting(function ($product) {
            $affectedBundleIds = \DB::table('bundel_details')
                ->where('product_id', $product->id)
                ->pluck('bundel_id')
                ->toArray();

            if (!empty($affectedBundleIds)) {
                \App\Models\Api\Ecommerce\Bundel::whereIn('id', $affectedBundleIds)
                    ->update(['status' => 'draft']);
            }
        });

        // Immediate update for products without variants/options
        static::updated(function ($product) {
            if (!empty($product->has_options)) {
                return;
            }

            if ($product->wasChanged(['sale_price', 'discount', 'discount_type'])) {
                $cartItems = \App\Models\Api\Ecommerce\CartItem::where('product_id', $product->id)
                    ->whereNull('variant_id')
                    ->get();

                foreach ($cartItems as $item) {
                    $qty = (int) $item->quantity;
                    $before = (float) ($product->sale_price ?? 0) * $qty;
                    $after = (float) ($product->getDiscountPrice() ?? 0) * $qty;
                    try {
                        $item->update([
                            'total_before_discount' => $before,
                            'total_after_discount' => $after,
                        ]);
                    } catch (\Throwable $e) {
                        // continue on failure
                    }
                }
            }
        });

        // Remove product-related cart items when product becomes not active
        static::updated(function ($product) {
            if ($product->wasChanged('status') && $product->status !== 'active') {
                try {
                    // mark related bundles as draft
                    $affectedBundleIds = \DB::table('bundel_details')
                        ->where('product_id', $product->id)
                        ->pluck('bundel_id')
                        ->toArray();

                    if (!empty($affectedBundleIds)) {
                        \App\Models\Api\Ecommerce\Bundel::whereIn('id', $affectedBundleIds)
                            ->update(['status' => 'draft']);
                    }

                    // remove cart items for this product
                    \App\Models\Api\Ecommerce\CartItem::where('product_id', $product->id)->delete();
                } catch (\Throwable $e) {
                    // ignore
                }
            }
        });

        
    }


    protected $casts = [
        
        'has_options' => 'boolean',
        'on_demand' => 'boolean',
        'sale_price' => 'decimal:2',
        'is_featured' => 'boolean',
        'sales_number' => 'integer',
        'moq' => 'integer',


    ];


    public function shipmentDetails(){
        return $this->hasOne(ProductShipement::class , 'product_id');
    }
    
    protected function serializeDate(\DateTimeInterface $date)
    {
      return $date->format('Y-m-d'); 
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function industries()
    {
        return $this->belongsToMany(Industry::class, 'industry_product');
    }

    public function options()
    {
        return $this->hasMany(ProductOption::class, 'product_id');
    }
    public function noOption(){
        return $this->hasOne(NoOptionStock::class , 'product_id');
    }

    public function getOption(){
        if($this->has_options){
            return $this->options();
        }

        return $this->noOption();
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class, 'product_id');
    }



    public function related()
    {
        return $this->belongsToMany(
            Product::class,
            'related_products',
            'product_id',
            'related_product_id'
        );
    }

    /**
     * Scope a query to only include active products.
     * Usage: Product::active()->get()
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }


    public function getDiscountPrice(){
        // return discount and calculate percenatge or value
        if($this->discount_type == 'percentage'){
            return $this->sale_price - ($this->sale_price * ($this->discount_value / 100));
        }
        return $this->sale_price - $this->discount;

    }










    


}
