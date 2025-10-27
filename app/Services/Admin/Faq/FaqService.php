<?php 
namespace App\Services\Admin\Faq;

use App\Models\Api\Admin\Faq;
use App\Services\BaseModelService;
use App\Traits\StoreMultiLang;

class FaqService extends BaseModelService{
    use StoreMultiLang;

    protected string $modelClass = Faq::class;
    
    public function store()
    {
        $this->uploadSingleImage(['icon'] , 'uploads/faq');
        $faq = parent::store($this->getBasicColumn(['icon' , 'topic']));
        $this->processTranslations($faq, $this->data, ['question' , 'answer']);  
        return $faq;     
    }



}