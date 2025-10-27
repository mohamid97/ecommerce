<?php

namespace App\Http\Resources\Api\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Traits\HandlesImage;

class BranchResource extends JsonResource
{
    use HandlesImage;
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $images = [];
        if($this->images){
            foreach ($this->images as $image) {
                
                $images[] = $this->getImageUrl($image);
            }  
        }
        return [
            'id'=>$this->id,
            'location'=>$this->location,
            'numbers'=>$this->numbers,
            'images'=>$images,
            'status'=>$this->status,
            'title' => $this->getColumnLang('title'),
            'des' => $this->getColumnLang('des'),
            'created_at' => $this->created_at->format('Y-m-d'),
            'updated_at' => $this->updated_at->format('Y-m-d'),
        ];
    }
}
