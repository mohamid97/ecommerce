<?php

namespace App\Models\Api\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class ProductSpecification extends Model implements TranslatableContract
{
    use HasFactory , Translatable;
    protected $fillable = ['product_id'];
    public $translatedAttributes = ['prop','value'];
    public $translationForeignKey = 'product_spec_id';
    public $translationModel = 'App\Models\Api\Admin\ProductSpecificationTranslation';
}



