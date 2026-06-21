<?php

namespace App\Models\Api\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Feedback extends Model implements TranslatableContract
{
    use HasFactory , Translatable;


    protected $table = 'feedbacks';
    public const TYPE_ABOUT = 'about';
    public const TYPE_TRAIN = 'train';
    public const TYPE_CONSULT = 'consult';

    public static array $types = [
      self::TYPE_ABOUT,
      self::TYPE_TRAIN,
      self::TYPE_CONSULT,
    ];

    protected $fillable = ['feedback_image' , 'breadcrumb', 'type'];
    public $translatedAttributes = ['title' , 'small_des' , 'des' , 'meta_title' , 'meta_des'];
    public $translationForeignKey = 'feedback_id';
    public $translationModel = 'App\Models\Api\Admin\FeedbackTranslation';

    protected function serializeDate(\DateTimeInterface $date)
    {
      return $date->format('Y-m-d'); 
      
    }

    
}