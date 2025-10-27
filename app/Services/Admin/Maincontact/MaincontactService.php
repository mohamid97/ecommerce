<?php
namespace App\Services\Admin\Maincontact;

use App\Models\Api\Admin\BasicContact;
use App\Services\BaseModelService;
class MaincontactService extends BaseModelService
{
    protected string $modelClass = BasicContact::class;

    public function store()
    {

        BasicContact::query()->delete();
        return parent::store($this->data);
        
    }


    
}