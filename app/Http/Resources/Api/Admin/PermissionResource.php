<?php

namespace App\Http\Resources\Api\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PermissionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }

    /**
     * Override the collection() method to return grouped permissions
     */
    public static function collection($resource): ResourceCollection
    {
        $grouped = [];

        foreach ($resource as $permission) {
            // Split permission name, assume format: "action model"
            $parts = explode(' ', $permission->name);
            $model = end($parts);

            $grouped[$model][] = new static($permission);
        }

        // Return as a custom anonymous ResourceCollection
        return new class(collect($grouped)) extends ResourceCollection {
            public function toArray($request)
            {
                return $this->collection->toArray();
            }
        };
    }
}