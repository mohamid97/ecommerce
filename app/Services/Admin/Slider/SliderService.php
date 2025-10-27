<?php
namespace App\Services\Admin\Slider;

use App\Models\Api\Admin\Slider;
use App\Services\BaseModelService;
use App\Traits\StoreMultiLang;

class SliderService extends BaseModelService
{
    use StoreMultiLang;
    protected string $modelClass = Slider::class;

    public function all($request){
        $allDetails = parent::all($request);
        return $allDetails;
    }

    public function view($id){
        $sliderDetails = parent::view($id);
        return $sliderDetails;
    }

    public function store()
    {          
        $this->uploadSingleImage(['image'] , 'uploads/sliders');
        $slider = parent::store($this->getBasicColumn(['image', 'link', 'video', 'order']));
        $this->processTranslations($slider, $this->data, ['title', 'des' , 'alt_image' , 'title_image' , 'small_des']);  
        return $slider;        
    }


    public function update($id){
     
        if(isset($this->data['image']) && $this->data['image'] != ''){
         $this->uploadSingleImage(['image'] , 'uploads/sliders');
        }
        $slider = parent::update($id , $this->getBasicColumn( ['image', 'link', 'video', 'order']));
        $this->processTranslations($slider, $this->data, ['title', 'des' , 'alt_image' , 'title_image' , 'small_des']);
        return $slider;
        
    }

    public function delete($id){
        $slider = parent::delete($id);
        return $slider;
    }








    
    

    
}