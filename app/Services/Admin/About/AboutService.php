<?php
namespace App\Services\Admin\About;

use App\Models\Api\Admin\AboutUs;
use App\Services\BaseModelService;
use App\Traits\HandlesImage;
use App\Traits\StoreMultiLang;
use Illuminate\Database\Eloquent\Builder;

class AboutService extends BaseModelService
{
    use StoreMultiLang , HandlesImage;
    protected string $modelClass = AboutUs::class;
   


    public function all($request){
        $allDetails = AboutUs::first();
        return $allDetails;
    }

    public function store()
    {
        $this->uploadSingleImage(['image' , 'breadcrumb'] , 'uploads/about');    
        $about = AboutUs::updateOrCreate(['id' => 1] , $this->data);
        $this->processTranslations($about, $this->data, ['title','small_des','mission','vission','brief','services' ,'des' , 'alt_image' , 'title_image'  , 'meta_title' , 'meta_des']);  
        return $about;
        
    }














    
    

    
}