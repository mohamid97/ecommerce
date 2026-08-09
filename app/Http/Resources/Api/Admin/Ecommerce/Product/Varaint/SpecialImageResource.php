<?php

namespace App\Http\Resources\Api\Admin\Ecommerce\Product\Varaint;

use App\Traits\HandlesImage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SpecialImageResource extends JsonResource
{
    use HandlesImage;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'image' => $this->getImageUrl($this->image),
            'alt_text' => $this->alt_text ?? (object) [],
            'order' => $this->order,
            'options' => $this->specialImageOptions
                ->groupBy('option_id')
                ->map(function ($selections) {
                    $option = $selections->first()->option;

                    return [
                        'option' => [
                            'id' => $option?->id,
                            'title' => $option?->title,
                        ],
                        'values' => $selections->map(fn ($selection) => [
                            'id' => $selection->optionValue?->id,
                            'title' => $selection->optionValue?->title,
                        ])->values(),
                    ];
                })
                ->values(),
        ];
    }
}
