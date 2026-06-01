<?php

namespace App\Models\Api\Ecommerce;

use App\Models\Api\Admin\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class ProductVariant extends Model implements TranslatableContract
{
    use HasFactory , Translatable;
    public $translatedAttributes = ['title' , 'slug' , 'des' , 'meta_title' , 'meta_des'];
    public $translationForeignKey = 'product_variant_id';
    public $translationModel = 'App\Models\Api\Ecommerce\ProductVariantTranslation';

    protected static function booted()
    {
        static::deleting(function ($variant) {
            $affectedDetails = \DB::table('bundel_details')
                ->whereJsonContains('variant_ids', $variant->id)
                ->get();

            if ($affectedDetails->isNotEmpty()) {
                $bundleIds = $affectedDetails->pluck('bundel_id')->unique()->toArray();

                // 1. Make all affected bundles draft
                \App\Models\Api\Ecommerce\Bundel::whereIn('id', $bundleIds)
                    ->update(['status' => 'draft']);

                // 2. Remove the deleted variant ID from the JSON arrays in bundel_details
                foreach ($affectedDetails as $detail) {
                    $variantIds = json_decode($detail->variant_ids, true) ?: [];
                    
                    $filteredIds = array_values(array_filter($variantIds, function ($id) use ($variant) {
                        return (int)$id !== (int)$variant->id;
                    }));

                    \DB::table('bundel_details')
                        ->where('id', $detail->id)
                        ->update([
                            'variant_ids' => !empty($filteredIds) ? json_encode($filteredIds) : null
                        ]);
                }
            }
        });
    }

    protected $fillable = ['product_id','is_default', 'sku' , 'barcode'  , 'status' , 'stock' , 'sales_number' , 'sale_price' , 'discount_value' , 'discount_type' , 'length' , 'width' , 'height' , 'weight' , 'delivery_time' , 'max_time' , 'images'];
    protected $casts = [
        'sale_price' => 'float',
        'discount_value' => 'float',
        'length' => 'float',
        'width' => 'float',
        'height' => 'float',
        'weight' => 'float',
        'sales_number' => 'integer',
    ];

     protected $appends = ['variant_full_name'];

     protected function serializeDate(\DateTimeInterface $date)
     {
       return $date->format('Y-m-d'); 
     }

    public function getVariantFullNameAttribute(): ?string
    {
        return $this->variants
            ->map(function ($variantOptionValue) {
                $optionTitle = optional($variantOptionValue->optionValue?->option)->title;
                $valueTitle = $variantOptionValue->optionValue?->title;

                if (!$optionTitle || !$valueTitle) {
                    return null;
                }

                return $optionTitle . ' ' . $valueTitle;
            })
            ->filter()
            ->implode(' ');
    }
        
    
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
   
    public function variants()
    {
        return $this->hasMany(VariantOptionValue::class);
    }


    public function varaintImages()
    {
        return $this->hasMany(ProductVaraintImages::class, 'variant_id');
    }


    public function getDiscountPrice(){
        // return discount and calculate percenatge or value
        if($this->discount_type == 'percentage'){
            return $this->sale_price - ($this->sale_price * ($this->discount_value / 100));
        }
        return $this->sale_price - $this->discount_value;
    }



    
    
}
