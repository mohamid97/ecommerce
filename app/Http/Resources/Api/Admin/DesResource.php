<?php

namespace App\Http\Resources\Api\Admin;

use App\Traits\HandlesImage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DesResource extends JsonResource
{
    use HandlesImage;
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return[
            'id'=>$this->id,
            'des_image'=> $this->getImageUrl($this->des_image),
            'breadcrumb'=> $this->getImageUrl($this->breadcrumb),
            'title'=>$this->getColumnLang('title'),
            'des'=>$this->getColumnLang('des'),
            'alt_image'=>$this->getColumnLang('alt_image'),
            'title_image'=>$this->getColumnLang('title_image'),
            'small_des'=>$this->getColumnLang('small_des'),
            'meta_title'=>$this->getColumnLang('meta_title'),
            'meta_des'=>$this->getColumnLang('meta_des'),
            'created_at'=>$this->created_at->format('Y-m-d H:i:s'),
            'updated_at'=>$this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}