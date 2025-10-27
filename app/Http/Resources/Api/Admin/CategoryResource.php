<?php

namespace App\Http\Resources\Api\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Traits\HandlesImage;
class CategoryResource extends JsonResource
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
            'slug' => $this->getColumnLang('slug'),
            'title' => $this->getColumnLang('title'),
            'small_des' => $this->getColumnLang('small_des'),
            'des' => $this->getColumnLang('des'),
            'meta_title' => $this->getColumnLang('meta_title'),
            'type' => $this->type,
            'parent' => $this->whenLoaded('parent', function () {
                return $this->parent ? [
                    'id' => $this->parent->id,
                    'title' => $this->getColumnLang('title' , 'parent'),
                    'slug' =>  $this->getColumnLang('slug' , 'parent')
                ] : null;
            }),
            'childrens' => $this->whenLoaded('childrens', function () {
                  return $this->getColumnsLangWithArrayRelation(['title' , 'slug'] , 'childrens');
            }),
            'meta_des' => $this->getColumnLang('meta_des'),
            'category_image' => $this->getImageUrl($this->category_image),
            'breadcrumb' => $this->getImageUrl($this->breadcrumb),
            'title_image' => $this->getColumnLang('title_image'),
            'alt_image' => $this->getColumnLang('alt_image'),
            'thumbnail' => $this->getImageUrl($this->thumbnail),
            'order' => $this->order,
            'created_at' => $this->created_at->format('Y-m-d'),
            'updated_at' => $this->updated_at->format('Y-m-d'),
            'brands' => $this->whenLoaded('brands', function () {
               return $this->getColumnsLangWithArrayRelation(['title' , 'slug'] , 'brands' , ['image']);
            }),

           'blogs' => $this->whenLoaded('blogs', function () {
               return $this->getColumnsLangWithArrayRelation(['title' , 'slug'] , 'blogs' , ['blog_image']);
            }),
            'services' => $this->whenLoaded('services', function () {
               return $this->getColumnsLangWithArrayRelation(['title' , 'slug'] , 'services' , ['service_image']);
            })
        ];

    }


}