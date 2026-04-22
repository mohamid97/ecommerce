<?php

namespace App\Models\Api\Ecommerce;

use App\Models\Api\Admin\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class BundelDetails extends Model
{
    use HasFactory;
    protected $fillable = ['bundel_id' , 'product_id' , 'variant_ids' , 'quantity'];

    public function bundel()
    {
        return $this->belongsTo(Bundel::class);
    }


    // JSON cast for variant_ids
    protected $casts = [
        'variant_ids' => 'array',
    ];


    // get variants 
    public function variants()
    {
        return $this->hasMany(ProductVariant::class, 'id', 'variant_ids');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }



    // get all varaints
public function getVariants()
{
    $ids = $this->variant_ids;

    // Normalize: handle null, empty, or bad string values
    if (empty($ids)) {
        return collect();
    }

    // If cast failed and it's still a string, decode manually
    if (is_string($ids)) {
        $ids = json_decode($ids, true);
    }

    // Final safety check
    if (!is_array($ids) || empty($ids)) {
        return collect();
    }

    return ProductVariant::whereIn('id', $ids)->get();
}


    // get only first varaint
    public function getFirstVariant(): ?ProductVariant
    {
        if (empty($this->variant_ids)) {
            return null;
        }

        return ProductVariant::find($this->variant_ids[0]);
    }








}
