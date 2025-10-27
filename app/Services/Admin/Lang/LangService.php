<?php

namespace App\Services\Admin\Lang;

use App\Models\Api\Admin\Lang;
use App\Services\BaseModelService;    

class LangService extends BaseModelService
{
    protected string $modelClass = Lang::class;

    public function store()
    {
        
        $this->data['code'] = strtolower($this->data['code']);
        $lang = parent::store($this->data);
        return $lang;
    }
    

    public function update(int $id)
    {
        $this->data['code'] = strtolower($this->data['code']);
        return parent::update($id, $this->data);
    }

    public function all($request){
        $langs = parent::all($request);
        return $langs;
    }

    

    

    
}