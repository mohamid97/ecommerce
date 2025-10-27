<?php
namespace App\Services\Admin\Certificate;

use App\Models\Api\Admin\Certificate;
use App\Services\BaseModelService;
use App\Traits\StoreMultiLang;

class CertificateService extends BaseModelService{

    use StoreMultiLang;

    protected string $modelClass = Certificate::class;

    public function store()
    {
        $this->uploadSingleImage(['image'] , 'uploads/certificate');
        $certificate = parent::store($this->getBasicColumn(['date' , 'image']));
        $this->processTranslations($certificate, $this->data, ['title', 'des']);  
        return $certificate;
        
    }

    public function update($id)
    {
        $this->uploadSingleImage(['image'] , 'uploads/certificate');
        $certificate = parent::update($id,$this->getBasicColumn(['date' , 'image']));
        $this->processTranslations($certificate, $this->data, ['title', 'des']);  
        return $certificate;
        
    }





}