<?php

namespace App\Models\Api\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Branch extends Model implements TranslatableContract
{


    use HasFactory , Translatable;
    protected $fillable = ['location','status','numbers' , 'images'];
    public $translatedAttributes = ['title', 'des'];
    public $translationForeignKey = 'branch_id';
    public $translationModel = 'App\Models\Api\Admin\BranchTranslation';
    protected $casts = [
        'numbers' => 'array',
    ];

     protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d');
    }



}
