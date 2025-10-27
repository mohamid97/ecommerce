<?php

namespace App\Http\Resources\Api\Admin;
use App\Traits\HandlesImage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
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
            'id' => $this->id,
            'title' => $this->getColumnLang('title'),
            'slug' => $this->getColumnLang('slug'),
            'des' => $this->getColumnLang('des'),
            'breadcrumb' => $this->getImageUrl($this->breadcrumb),
            'event_image' => $this->getImageUrl($this->event_image),
            'date' => $this->date ? $this->date->format('Y-m-d') : null,
            'meta_title' => $this->getColumnLang('meta_title'),
            'meta_des' => $this->getColumnLang('meta_des'),
            'alt_image' => $this->getColumnLang('alt_image'),
            'title_image' => $this->getColumnLang('title_image'),
            'created_at' => $this->created_at->format('Y-m-d'),
            'updated_at' => $this->updated_at->format('Y-m-d'),
        ];


    }



}
