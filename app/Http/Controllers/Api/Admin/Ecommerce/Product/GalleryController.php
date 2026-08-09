<?php

namespace App\Http\Controllers\Api\Admin\Ecommerce\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\Ecommerce\Product\Variant\GeneralGalleriesRequest;
use App\Http\Requests\Api\Admin\Ecommerce\Product\Variant\SpecialImagesRequest;
use App\Http\Requests\Api\Admin\Ecommerce\Product\Variant\StoreSpecialImagesRequest;
use App\Http\Resources\Api\Admin\Ecommerce\Product\Varaint\GeneralGalleriesResoure;
use App\Http\Resources\Api\Admin\Ecommerce\Product\Varaint\SpecialImageResource;
use App\Models\Api\Ecommerce\GerneralVariantGalleries;
use App\Models\Api\Ecommerce\ProductVaraintImages;
use App\Models\Api\Ecommerce\ProductVariant;
use App\Models\Api\Ecommerce\SpecialImageOptionSelection;
use App\Traits\HandlesImage;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GalleryController extends Controller
{
    use HandlesImage , ResponseTrait;

    // get all general images for varaints 
    public function generalImages(Request $request){
      $images = GerneralVariantGalleries::where('product_id' , $request->product_id)->get();
      return $this->success(GeneralGalleriesResoure::collection($images) , __('main.data_retrieved_successfully' , ['model'=>'Gernal Varaint Gallery']));
    }
    // store global image 
    public function storeGeneralImage(GeneralGalleriesRequest $request){
      $image = $this->uploadFile($request->image , 'products/general');
      $gallery = GerneralVariantGalleries::create([
        'image' => $image,
        'product_id' => $request->product_id,
        'alt_text' => $request->alt_text,
        'order' => $request->order,
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
    public function specialImages(SpecialImagesRequest $request){
      $images = GerneralVariantGalleries::where('product_id', $request->product_id)
        ->with(['specialImageOptions.option', 'specialImageOptions.optionValue'])
        ->orderBy('order')
        ->orderBy('id')
        ->get();

      return $this->success(SpecialImageResource::collection($images) , __('main.data_retrieved_successfully' , ['model'=>'Special Variant Gallery']));
    }
    // Assign selected general images to every variant that matches any selected option value.
    public function storeSpecialImage(StoreSpecialImagesRequest $request){
      $imageIds = $request->imageIds;
      $images = GerneralVariantGalleries::whereIn('id', $imageIds)->get();
      $productId = $images->first()->product_id;
      $optionSelections = collect($request->input('options', []));

      $variants = $optionSelections->isEmpty()
        ? collect()
        : ProductVariant::where('product_id', $productId)
        ->where('status', '!=', 'draft')
        ->where(function ($query) use ($optionSelections) {
          foreach ($optionSelections as $selection) {
            $query->orWhereHas('variants', function ($variantOptions) use ($selection) {
              $variantOptions->where('option_id', $selection['optionId'])
                ->whereIn('option_value_id', $selection['valueIds']);
            });
          }
        })
        ->get();

      DB::transaction(function () use ($images, $productId, $optionSelections, $variants) {
        foreach ($images as $image) {
          ProductVaraintImages::where('image_id', $image->id)
            ->whereHas('variant', fn ($query) => $query->where('product_id', $productId))
            ->delete();

          SpecialImageOptionSelection::where('image_id', $image->id)->delete();

          foreach ($optionSelections as $selection) {
            foreach ($selection['valueIds'] as $valueId) {
              SpecialImageOptionSelection::create([
                'image_id' => $image->id,
                'option_id' => $selection['optionId'],
                'option_value_id' => $valueId,
              ]);
            }
          }

          foreach ($variants as $variant) {
            ProductVaraintImages::firstOrCreate([
              'variant_id' => $variant->id,
              'image_id' => $image->id,
            ]);
          }
        }
      });

      $images->load(['specialImageOptions.option', 'specialImageOptions.optionValue']);
      return $this->success(SpecialImageResource::collection($images), __('main.stored_successfully' , ['model'=>'Special Variant Gallery']));
      
    }

    // delete image for special image varaint 
    public function deleteSpecialImage(Request $request){
      $gallery = GerneralVariantGalleries::find($request->id);
      if(!$gallery){
        return $this->error([] , __('main.not_found' , ['model'=>'Special Variant Gallery']));
      }
      DB::transaction(function () use ($gallery) {
        ProductVaraintImages::where('image_id', $gallery->id)->delete();
        SpecialImageOptionSelection::where('image_id', $gallery->id)->delete();
      });
      return $this->success([] , __('main.deleted_successfully' , ['model'=>'Special Variant Gallery']));
    }
      






}
