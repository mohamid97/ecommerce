<?php

namespace App\Http\Resources\Api\Admin;

use App\Models\Api\Admin\BasicContact;
use App\Models\Api\Admin\Location;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Traits\HandlesImage;

class ContactResource extends JsonResource
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
            'title' => $this->getColumnLang('title'),
            'des' => $this->getColumnLang('des'),
            'image' => $this->getImageUrl($this->image),
            'breadcrumb'=>$this->getImageUrl($this->breadcrumb),
            'title_image' => $this->getColumnLang('title_image'),
            'alt_image' => $this->getColumnLang('alt_image'),
            'meta_title' => $this->getColumnLang('meta_title'),
            'meta_des' => $this->getColumnLang('meta_des'),
            'locations' => LocationResource::collection(Location::with(['phones', 'emails'])->get()),
            'main_contact' => MaincontactResource::collection(BasicContact::all()),
            'created_at' => $this->created_at->format('Y-m-d'),
            'updated_at' => $this->updated_at->format('Y-m-d'),
        ];
                
    }

    
    
}