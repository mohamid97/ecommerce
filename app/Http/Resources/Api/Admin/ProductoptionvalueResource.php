<?php

namespace App\Http\Resources\Api\Admin;

use App\Traits\HandlesImage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductoptionvalueResource extends JsonResource
{
    use HandlesImage;
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            'option_id' => $this->option_id,
            'option_value_id'=> $this->option_value_id,
            'option_name'=>$this->option->title,
            'option_value'=> $this->optionValue->title,
            // 'option_image'=>$this->getImageUrl($this->option ? $this->option->option_image : null),
            // 'option_name'=> $this->option ? $this->getColumnLang('title' , 'option') : null,
            // 'value'=> $this->getColumnLang('value'),
 
        ];
    }
}