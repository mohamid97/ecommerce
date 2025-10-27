<?php

namespace App\Http\Resources\Api\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Schema;

class SocialResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        
        return $this->fetchData();
      
      
    }

    private function getColumn(){
        $columns = Schema::getColumnListing('soicals');
        $platforms = collect($columns)
            ->filter(fn ($col) => !in_array($col, ['id', 'created_at', 'updated_at'])) 
            ->map(function ($col) {
                return str_replace(['_cta', '_layout'], '', $col);
            })
            ->unique()
            ->values();

            return $platforms;
    }

    protected function fetchData(){
        $data = [];
        foreach ($this->getColumn() as $platform) {
            $data[$platform] = [
                'value'  => $this->getAttribute($platform),
                'cta'    => (bool) $this->getAttribute("{$platform}_cta"),
                'layout' => (bool) $this->getAttribute("{$platform}_layout"),
            ];
        }

        return $data;
        
    }

    
}