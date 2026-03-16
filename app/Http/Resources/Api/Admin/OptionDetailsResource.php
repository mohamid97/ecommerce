<?php

namespace App\Http\Resources\Api\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OptionDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'option_image' => $this->getImageUrl($this->option_image),
            'code'=> $this->code,
            'value_type'=> $this->value_type,
            'title' => $this->getColumnLang('title'),
            'values' => $this->whenLoaded('values', function () {
               $values = $this->getColumnsLangWithArrayRelation(['title'], 'values', ['value']);
               if ($this->value_type === 'image') {
                    foreach ($values as &$value) {
                        if (!empty($value['value'])) {
                            $value['value'] = $this->getImageUrl($value['value']);
                        }
                    }
                }
               return $values;
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

    }


    
}
