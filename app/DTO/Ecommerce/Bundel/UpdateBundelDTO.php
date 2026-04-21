<?php
namespace App\DTO\Ecommerce\Bundel;

class UpdateBundelDTO{

        public function __construct(
            public int $id,
            public ?float $price,
            public ?int $category_id,
            public ?int $brand_id,
            public ?string $bundle_image = null,
            public ?string $status,
            public array $title,
            public ?array $des = null,
            public ?array $meta_title = null,
            public ?array $meta_des = null,
            public array $products,

        
    
    ) {}

    public static function fromRequest(array $data): self
    {

        return new self(
            $data['id'],
            $data["price"]??null,
            $data["category_id"]??null,
            $data["brand_id"]??null,
            $data["bundle_image"]??null,
            $data["status"]??'active',
            $data["title"],
            $data["des"]??null,
            $data["meta_title"]??null,
            $data["meta_des"]??null,
            $data["products"],

            
        );
    }



    
}
