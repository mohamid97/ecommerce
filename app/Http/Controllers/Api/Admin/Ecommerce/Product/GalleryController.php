<?php

namespace App\Http\Controllers\Api\Admin\Ecommerce\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\Ecommerce\Product\Variant\GeneralGalleriesRequest;
use App\Http\Requests\Api\Admin\Ecommerce\Product\Variant\VariantGalleriesRequest;
use App\Http\Resources\Api\Admin\Ecommerce\Product\Varaint\GeneralGalleriesResoure;
use App\Http\Resources\Api\Admin\Ecommerce\Product\Varaint\SpecialGalleriesResoure;
use App\Models\Api\Ecommerce\GerneralVariantGalleries;
use App\Models\Api\Ecommerce\VariantGalleries;
use App\Traits\HandlesImage;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    use HandlesImage;

    // get all general images for varaints 
    public function generalImages($product_id){
      $images = GerneralVariantGalleries::where('product_id' , $product_id)->get();
      return $this->success(GeneralGalleriesResoure::collection($images) , __('main.data_retrieved_successfully' , ['model'=>'Gernal Varaint Gallery']));
    }
    // store global image 
    public function storeGeneralImage(GeneralGalleriesRequest $request){
      $image = $this->uploadFile($request->image , 'products/general');
      $gallery = GerneralVariantGalleries::create([
        'image' => $image,
        'product_id' => $request->product_id,
      ]);
      return $this->success(new GeneralGalleriesResoure($gallery) , __('main.stored_successfully' , ['model'=>'Gernal Varaint Gallery']));
      
    }
    // delete image fron general image 
    public function deleteGeneralImage(Request $request){
      $gallery = GerneralVariantGalleries::find($request->id);
      if(!$gallery){
        return $this->error([] , __('main.not_found' , ['model'=>'Gernal Varaint Gallery']));
      }
      $this->deleteFile($gallery->image);
      $gallery->delete();
      return $this->success([] , __('main.deleted_successfully' , ['model'=>'Gernal Varaint Gallery']));
    }


    // get all special gallery for varaint 
    public function specialImages(Request $request){
      $images = VariantGalleries::where('variant_id' , $request->variant_id)->get();
      return $this->success(SpecialGalleriesResoure::collection($images) , __('main.data_retrieved_successfully' , ['model'=>'Special Variant Gallery']));
    }
    // store variant image 
    public function storeVariantImage(VariantGalleriesRequest $request){
      $gallery = VariantGalleries::create([
        'image_id' => $request->image_id,
        'variant_id' => $request->variant_id,
      ]);
      $gallery->load('image');
      return $this->success(new SpecialGalleriesResoure($gallery), __('main.stored_successfully' , ['model'=>'Special Variant Gallery']));
      
    }

    // delete image for special image varaint 
    public function deleteSpecialImage(Request $request){
      $gallery = VariantGalleries::find($request->id);
      if(!$gallery){
        return $this->error([] , __('main.not_found' , ['model'=>'Special Variant Gallery']));
      }
      $gallery->delete();
      return $this->success([] , __('main.deleted_successfully' , ['model'=>'Special Variant Gallery']));
    }
      






}
