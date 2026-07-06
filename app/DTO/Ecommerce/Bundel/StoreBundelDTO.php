<?php
namespace App\DTO\Ecommerce\Bundel;

class StoreBundelDTO{

        public function __construct(
            public ?float $price,
            public ?float $discount,
            public ?string $discount_type,
            public ?int $category_id,
            public ?int $brand_id,
            public $bundle_image,
            public string $status,
            public array $title,
            public ?array $des,
            public ?array $meta_title,
            public ?array $meta_des,
            public array $products,

        
    
    ) {}

    public static function fromRequest(array $data): self
    {

        return new self(
            $data["price"]??null,
            $data["discount"]??null,
            $data["discount_type"]??null,
            $data["category_id"]??null,
            $data["brand_id"]??null,
            $data["bundle_image"]??null,
            $data["status"]??'active',
            $data["title"]??null,
            $data["des"]??null,
            $data["meta_title"]??null,
            $data["meta_des"]??null,
            $data["products"],

            
        );
    }



    
}
