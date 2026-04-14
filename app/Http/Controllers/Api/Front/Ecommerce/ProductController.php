<?php

namespace App\Http\Controllers\Api\Front\Ecommerce;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Front\Ecommerce\ProductDetailsResource;
use App\Http\Resources\Api\Front\Ecommerce\ProductResource;
use App\Http\Resources\Api\Front\Ecommerce\VaraintDetailsResource;
use App\Models\Api\Admin\Product;
use App\Models\Api\Ecommerce\ProductVariant;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class ProductController extends Controller
{

    use ResponseTrait;
    // get
    public function get(Request $request){
      

        $products = Product::query();
        if($request->has('search')){
            $products->whereHas('translations', function($query) use ($request){
                $query->where('title', 'like', '%' . $request->search . '%');
            });
        }

        if($request->has('category_id')){
            $products->where('category_id', $request->category_id);
        }

        if($request->has('sort') && in_array($request->sort , ['asc' , 'desc'])){
            $products->orderBy('created_at', $request->sort);
        }


        // status 
        $products->where('status' , 'active');
        if($request->has('paginate') && ($request->paginate >= 1 && $request->paginate <= 100)){
            $products = $products->paginate($request->paginate);
        }else{
            $products = $products->paginate(10);
        }

        

        return $this->success(ProductResource::collection($products) , __('main.list_successfully' , ['products']));



    } // end get products



    public function productDetails(Request $request){
        // need to get all options and varaints

        $product = Product::with(['options.option','options.values.optionValue' , 'variants'])->findOrFail($request->id);
        if(!$product){
            return $this->error(__('main.not_found' , ['product']));
        }


      

        return $this->success(new ProductDetailsResource($product) , __('main.show_successfully' , ['product']));


    }

    public function varaintDetails(Request $request){
        if($request->has('variant_id')){
            $variant = ProductVariant::with(['optionValues.optionValue'])->findOrFail($request->variant_id);
            if(!$variant){
                return $this->error(__('main.not_found' , ['variant']));
            }
            return $this->success(new VaraintDetailsResource($variant) , __('main.show_successfully' , ['variant']));
        }

    }




}
