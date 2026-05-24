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
        $faq = parent::store($this->getBasicColumn(['icon' , 'topic', 'type', 'blog_id']));
        $this->processTranslations($faq, $this->data, ['question' , 'answer']);  
        return $faq;     
    }

    public function update(int $id)
    {
        $this->uploadSingleImage(['icon'] , 'uploads/faq');
        $faq = parent::update($id, $this->getBasicColumn(['icon' , 'topic', 'type', 'blog_id']));
        $this->processTranslations($faq, $this->data, ['question' , 'answer']);  
        return $faq;     
    }
}