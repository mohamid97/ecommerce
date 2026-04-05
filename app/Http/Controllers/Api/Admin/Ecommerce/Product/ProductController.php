<?php

namespace App\Http\Controllers\Api\Admin\Ecommerce\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\Ecommerce\Product\StoreRelatedProductsRequest;
use App\Http\Requests\Api\Admin\Ecommerce\Product\UpdateProductVaraintStatusRequest;
use App\Models\Api\Admin\Product;
use App\Models\Api\Ecommerce\ProductVariant;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    use ResponseTrait;
    public function makeFeatured(Request $request){
        if ($request->id) {
            $product = Product::find($request->id);

            if (!$product) {
                return $this->error(__('main.model_not_found_id', ['model'=>'Product' , 'id'=>$request->id ]));
            }

               $product->update(['is_featured' => !$product->is_featured]);

               return $this->success(__('main.updated_successfully'));
        }

        return $this->error(__('main.no_id'));

    } // end add to feateured


    public function updateStatusProductOrVaraint(UpdateProductVaraintStatusRequest $request){

        if($request->product_id ||$request->varaint_id){

            if(isset($request->product_id)){
                $model = Product::findOrFail($request->product_id);
            }else{
                $model = ProductVariant::findOrFail($request->varaint_id);

            }
            $model->update(['status'=>$request->status]);

            return $this->success(__('main.updated_successfully'));

        }

        return $this->error(__('main.no_id'));

    }



    public function storeRelatedProduct(StoreRelatedProductsRequest $request){
        try{
            $product = Product::findOrFail($request->id);
            $product->update(['related_products' , $request->related_products]);
            return $this->success(__('main.updated_successfully'));

        }catch(\Exception $e){
          return $this->error($e->getMessage(), 500);
        }


    }



public function filterProduct(Request $request)
{
    if ($request->search) {
        $products = Product::whereHas('translation', function ($q) use ($request) {
            $q->where('title', 'like', '%' . $request->search . '%');
        })
        ->with('translation')
        ->get()
        ->map(function ($product) {
            return [
                'id'    => $product->id,
                'title' => $product->title,
            ];
        });

        if ($products->isEmpty()) {
            return $this->error(__('main.not_founded') , 404);
        }

        return $this->success(__('main.data_retrieved'), $products);
    }

    return $this->error(__('main.not_founded') ,404);
}





    



}
