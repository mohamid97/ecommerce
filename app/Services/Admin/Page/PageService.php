<?php

namespace App\Services\Admin\Page;

use App\Models\Api\Admin\Page;
use App\Services\BaseModelService;
use App\Traits\HandlesImage;
use App\Traits\StoreMultiLang;

class PageService extends BaseModelService
{
    use StoreMultiLang , HandlesImage;
    protected string $modelClass = Page::class;


    public function all($request){
        $allDetails = parent::all($request);
        return $allDetails;
    }

    public function view($id){
        $pageDetails = parent::view($id);
        return $pageDetails;
    }

    public function store()
    {
        $this->uploadSingleImage(['page_image', 'breadcrumb'], 'uploads/pages');
        $page = parent::store($this->getBasicColumn(['page_image','position',  'breadcrumb']));
        $this->data['slug']  = $this->createSlug($this->data);
        $this->processTranslations($page, $this->data, ['title', 'slug', 'des','small_des' , 'alt_image' , 'title_image' , 'meta_title' , 'meta_des']);
        return $page;

    }


    public function update($id){
        $this->uploadSingleImage(['page_image', 'breadcrumb'], 'uploads/pages');
        $page = parent::update($id , $this->getBasicColumn( ['page_image','position', 'breadcrumb']));
        $this->processTranslations($page, $this->data, ['title', 'slug', 'des','small_des' , 'alt_image' , 'title_image' , 'meta_title' , 'meta_des']);
        return $page;
    }

    public function delete($id){
        $page = parent::delete($id);
        return $page;
    }



}
