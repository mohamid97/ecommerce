<?php

namespace App\Models\Api\Ecommerce;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class ProductOptionValues extends Model implements TranslatableContract
{
    use HasFactory , Translatable;
    protected $fillable = ['product_option_id' , 'option_name_id'];
    public $translatedAttributes = ['value'];
    public $translationForeignKey = 'p_o_value_id';
    public $translationModel = 'App\Models\Api\Ecommerce\ProductOptionValuesTranslation';
    public function option()
    {
        return $this->belongsTo(Option::class, 'option_name_id');
    }
}