<?php
namespace App\DTO\Ecommerce\Bundel;

class UpdateBundelDTO{

        public function __construct(
            public int $bundel_id,
            public float $price,
            public int $category_id,
            public int $brand_id,
            public ?string $bundle_image = null,
            public int $status,
            public array $title,
            public ?array $des = null,
            public ?array $meta_title = null,
            public ?array $meta_des = null,
            public array $product,

        
    
    ) {}

    public static function fromRequest(array $data): self
    {

        return new self(
            $data['bundel_id'],
            $data["price"],
            $data["category_id"]??null,
            $data["brand_id"]??null,
            $data["bundle_image"]??null,
            $data["status"]??null,
            $data["title"]??null,
            $data["des"]??null,
            $data["meta_title"],
            $data["meta_des"],
            $data["product"],

            
        );
    }



    
}
