<?php

namespace App\Models\Api\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Certificate extends Model
{
    use HasFactory , Translatable;
    protected $fillable = ['image' , 'date'];
    public $translatedAttributes = ['title','des'];
    public $translationForeignKey = 'certificate_id';
    public $translationModel = 'App\Models\Api\Admin\CertificateTranslation';


}
