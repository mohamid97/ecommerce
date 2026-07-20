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
        $bundel = Bundel::where('id', $data->id)->firstOrFail();

        $updateData = [
            'price' => null,
            'discount' => $data->discount,
            'discount_type' => $data->discount_type,
            'category_id' => $data->category_id,
            'brand_id' => $data->brand_id,
            'status' => $data->status,
        ];

        // Handle bundle_image update logic:
        // - If bundle_image is not set (not sent in request), don't update the image
        // - If bundle_image is set but null/empty, set image to null (remove it)
        // - If bundle_image is a valid file, upload and update the image
        if (isset($data->bundle_image)) {
            $updateData['bundle_image'] = $this->uploadImage($data->bundle_image, 'bundel', 'public');
        }

        $bundel->update($updateData);
  
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
