<?php

namespace App\Services\Admin\Dynamicfeature;

use App\Models\Api\Admin\Dynamicfeature;
use App\Services\BaseModelService;
use App\Traits\StoreMultiLang;

class DynamicfeatureService extends BaseModelService
{
    use StoreMultiLang;

    protected string $modelClass = Dynamicfeature::class;

    public function store()
    {
        $this->uploadSingleImage(['icon'], 'uploads/dynamicfeature');
        $dynamicFeature = parent::store($this->getBasicColumn(['icon', 'type']));
        $this->processTranslations($dynamicFeature, $this->data, ['title', 'small_des', 'des']);
        return $dynamicFeature;
    }

    public function update(int $id)
    {
        $this->uploadSingleImage(['icon'], 'uploads/dynamicfeature');
        $dynamicFeature = parent::update($id, $this->getBasicColumn(['icon', 'type']));
        $this->processTranslations($dynamicFeature, $this->data, ['title', 'small_des', 'des']);
        return $dynamicFeature;
    }

    public function delete(int $id)
    {
        $dynamicFeature = Dynamicfeature::findOrFail($id);
        if ($dynamicFeature->icon) {
            $this->deleteImage($dynamicFeature->icon);
        }
        return parent::delete($id);
    }
}
