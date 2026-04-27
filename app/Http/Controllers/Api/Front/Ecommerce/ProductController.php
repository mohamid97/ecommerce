<?php

namespace App\Http\Controllers\Api\Front\Ecommerce;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Front\Ecommerce\ProductDetailsResource;
use App\Http\Resources\Api\Front\Ecommerce\ProductNoOptionResource;
use App\Http\Resources\Api\Front\Ecommerce\ProductResource;
use App\Http\Resources\Api\Front\Ecommerce\VaraintDetailsResource;
use App\Models\Api\Admin\Product;
use App\Models\Api\Ecommerce\LastPiece;
use App\Models\Api\Ecommerce\NewProduct;
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
        $products->where('status', '!=', 'draft');
        if($request->has('paginate') && ($request->paginate >= 1 && $request->paginate <= 100)){
            $products = $products->paginate($request->paginate);
        }else{
            $products = $products->paginate(10);
        }

        return $this->successPaginated(
            $products,
            ProductResource::collection($products),
            'products',
            __('main.list_successfully', ['products' => 'Products'])
        );

    } // end get products



    public function productDetails(Request $request){
        $product = Product::with(['category', 'brand'])->where('status', '!=', 'draft')->where('id', $request->id)->first();
        if(!$product){
            return $this->error(__('main.not_found' , ['product']));
        }
        if (!$product->has_options) {
            return $this->success(new ProductNoOptionResource($product), __('main.show_successfully', ['product']));
        }

        $product->load([
            'options.option',
            'options.values.optionValue',
            'variants.variants.optionValue.option',
            'variants.varaintImages.image',
        ]);

        return $this->success(new ProductDetailsResource($product), __('main.show_successfully', ['product']));


    }

    public function lastPiece(Request $request)
    {
        return $this->sectionProducts($request, LastPiece::query()->select('product_id'), 'last_piece_products');
    }

    public function newest(Request $request)
    {
        return $this->sectionProducts($request, NewProduct::query()->select('product_id'), 'newest_products');
    }

    public function varaintDetails(Request $request){
        if($request->has('variant_id')){
            $variant = ProductVariant::with(['varaintImages' , 'variants'])->where('status', '!=', 'draft')->where('id', $request->variant_id)->first();
            if(!$variant){
                return $this->error(__('main.not_found' , ['variant']));
            }
            return $this->success(new VaraintDetailsResource($variant) , __('main.show_successfully' , ['variant']));
        }

    }




    private function sectionProducts(Request $request, $productsSubQuery, string $responseKey)
    {
        $products = Product::query()
            ->where('status', '!=', 'draft')
            ->whereIn('id', $productsSubQuery);

        if($request->has('paginate') && ($request->paginate >= 1 && $request->paginate <= 100)){
            $products = $products->paginate($request->paginate);
        }else{
            $products = $products->paginate(10);
        }

        return $this->successPaginated(
            $products,
            ProductResource::collection($products),
            $responseKey,
            __('main.list_successfully', ['model' => 'Products'])
        );
    }

}
