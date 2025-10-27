<?php

namespace App\Http\Resources\Api\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Traits\HandlesImage;
class SettingResource extends JsonResource
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
            'id'=>$this->id,
            'work_hours'=>$this->work_hours,
            'title'=>$this->getColumnLang('title'),
            'breif'=>$this->getColumnLang('breif'),
            'meta_des'=>$this->getColumnLang('meta_des'),
            'meta_title'=>$this->getColumnLang('meta_title'),
            'logo'=>$this->getImageUrl($this->icon),
            'favicon'=>$this->getImageUrl($this->favicon)
        ];
    }
}