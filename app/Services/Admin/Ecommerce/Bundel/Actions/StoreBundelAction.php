<?php
namespace App\Services\Admin\Ecommerce\Bundel\Actions;

use App\Models\Api\Admin\Product;
use App\Models\Api\Ecommerce\Bundel;
use App\Models\Api\Ecommerce\BundelDetails;
use App\Models\Api\Ecommerce\ProductVariant;
use App\Services\Admin\Common\TranslationService;
use App\Traits\HandlesImage;

class StoreBundelAction{
    use HandlesImage;

    public function __construct(
        public ValidateBundel $validateBundel
    ) {}
    public function storeBundel($data){
        $translation = app(TranslationService::class);
        $bundel = Bundel::create([
            'price' => $data->price,
            'category_id' => $data->category_id,
            'brand_id' => $data->brand_id,
            'bundle_image' => $this->uploadImage($data->bundle_image ,'bundel' , 'public'),
            'status' => $data->status,
        ]);

        $translation->storeTranslations($bundel, $data , ['title' , 'des' , 'meta_title' , 'meta_des']);

        $this->validateBundel->validateBundelDetails($bundel->id, $data);
        $this->StoreBundelDetails($bundel->id , $data);
        return $bundel;


    }





    private function StoreBundelDetails($bundel_id , $data){
            foreach($data->products as $item){

                BundelDetails::create([
                    'bundle_id'   => $bundel_id,
                    'product_id'  => $item['product_id'],
                    'quantity'    => $item['quantity'],
                    'variant_ids' => $item['variant_ids']??null,
                ]);
            }



    }

        
 


    
}