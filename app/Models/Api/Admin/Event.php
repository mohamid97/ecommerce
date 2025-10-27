<?php

namespace App\Models\Api\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
class Event extends Model implements TranslatableContract
{
    use HasFactory , Translatable;
    protected $fillable = ['event_image','breadcrumb', 'date'];
    public $translatedAttributes = ['title','slug' ,'alt_image', 'title_image', 'des', 'meta_title', 'meta_des'];
    public $translationForeignKey = 'event_id';
    public $translationModel = 'App\Models\Api\Admin\EventTranslation';

    protected $casts = [
        'date' => 'date',
    ];

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d');
    }
}
