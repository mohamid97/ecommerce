<?php
namespace App\Services\Admin\Ecommerce\Bundel\Actions;

use App\Models\Api\Ecommerce\Bundel;
use App\Models\Api\Ecommerce\BundelDetails;
use App\Services\Admin\Common\TranslationService;
use App\Traits\HandlesUpload;

class UpdateBundelAction{
 use HandlesUpload;

     public function __construct(
        public ValidateBundel $validateBundel
    ) {}
    public function updateBundel($data){
        $translation = app(TranslationService::class);
        $bundel = Bundel::where('id' , $data->bundel_id)->update([
            'price' => $data->price,
            'category_id' => $data->category_id,
            'brand_id' => $data->brand_id,
            'bundle_image' => $this->uploadImage($data->bundle_image ,'bundel' , 'public'),
            'status' => $data->status,
        ]);
        $translation->storeTranslations($bundel, $data , ['title' , 'des' , 'meta_title' , 'meta_des']);
        $this->validateBundel->validateBundelDetails($bundel->id, $data);
        $this->updateBundelDetails($bundel->id , $data);
        return $bundel;


    }

    private function updateBundelDetails($bundel_id , $data){
        BundelDetails::where('bundel_id' , $bundel_id)->delete();
        foreach($data->products as $item){
            BundelDetails::create([
                'bundel_id' => $bundel_id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'variant_ids' => $item['variant_ids']??null,
            ]);
        }
        
    }
}