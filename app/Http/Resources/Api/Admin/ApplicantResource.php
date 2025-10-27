<?php

namespace App\Http\Resources\Api\Admin;

use App\Traits\HandlesUpload;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicantResource extends JsonResource
{
    use HandlesUpload;
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=>$this->id,
            'first_name'=>$this->first_name,
            'last_name'=>$this->last_name,
            'email'=>$this->email,
            'phone'=>$this->phone,
            'job_title'=>$this->job_title,
            'msg'=>$this->msg,
            'cv'=>$this->getFileUrl($this->cv)
        ];
    }
}
