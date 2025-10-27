<?php

namespace App\Models\Api\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Faq extends Model implements TranslatableContract
{
    use HasFactory , Translatable;
    protected $fillable = ['icon' , 'topic'];
    public $translatedAttributes = ['question','answer'];
    public $translationForeignKey = 'faq_id';
    public $translationModel = 'App\Models\Api\Admin\FaqTranslation';

    protected function serializeDate(\DateTimeInterface $date)
    {
      return $date->format('Y-m-d'); 
    }

    
}
