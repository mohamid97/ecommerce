<?php
namespace App\Services\Admin\Contact;

use App\Models\Api\Admin\ContactUs;
use App\Services\BaseModelService;
use App\Traits\StoreMultiLang;
use Illuminate\Database\Eloquent\Builder;

class ContactService extends BaseModelService
{
    use StoreMultiLang;
    protected string $modelClass = ContactUs::class;
   
    public function all($request){
        $allDetails = ContactUs::first();
        return $allDetails;
    }


    public function store()
    {

        $this->uploadSingleImage(['image' , 'breadcrumb'] , 'uploads/contact');
        $contact = ContactUs::updateOrCreate(['id' => 1] , $this->data);
        $this->processTranslations($contact, $this->data, ['title', 'des' , 'alt_image' , 'title_image'  , 'meta_title' , 'meta_des']);  
       
        return $contact;
      
        
    }














    
    

    
}