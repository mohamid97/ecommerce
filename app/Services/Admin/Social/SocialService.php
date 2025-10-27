<?php
namespace App\Services\Admin\Social;

use App\Models\Api\Admin\Soical;
use App\Services\BaseModelService;
class SocialService extends BaseModelService{
    protected string $modelClass = Soical::class;

    public function store()
    {
        
        // Flatten the nested array into DB-friendly keys
        $flattenedData = [];

        foreach ($this->data as $platform => $fields) {
            if (is_array($fields)) {
                $flattenedData[$platform] = $fields['value'] ?? null;
                $flattenedData["{$platform}_cta"] = $fields['cta'] ?? false;
                $flattenedData["{$platform}_layout"] = $fields['layout'] ?? false;
            }
        }
        $this->setData($flattenedData);
        return parent::update(Soical::firstOrCreate([])->id);
    }
    
}