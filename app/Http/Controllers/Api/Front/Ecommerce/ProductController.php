<?php

namespace App\Http\Controllers\Api\Front\Ecommerce;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Admin\IndustryResource;
use App\Http\Resources\Api\Front\Ecommerce\ProductDetailsResource;
use App\Http\Resources\Api\Front\Ecommerce\ProductNoOptionResource;
use App\Http\Resources\Api\Front\Ecommerce\ProductResource;
use App\Http\Resources\Api\Front\Ecommerce\VaraintDetailsResource;
use App\Models\Api\Admin\Industry;
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
      

        $products = Product::with(['category', 'brand', 'industries']);
        if($request->has('search')){
            $products->whereHas('translations', function($query) use ($request){
                $query->where('title', 'like', '%' . $request->search . '%');
            });
        }

        if($request->has('category_id')){
            $products->where('category_id', $request->category_id);
        }

        if($request->has('industry_id')){
            $products->whereHas('industries', function ($query) use ($request) {
                $query->where('industries.id', $request->industry_id);
            });
        }

        if($request->has('sort') && in_array($request->sort , ['asc' , 'desc'])){
            if($request->has('sort_by') && in_array($request->sort_by, ['created_at', 'sale_price' , 'id' , 'discount' , 'order'])){
                $products->orderBy($request->sort_by, $request->sort);
            }else{
              $products->orderBy('created_at', $request->sort);
            }
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
        $product = Product::with(['category', 'brand', 'industries'])->where('status', '!=', 'draft')->where('id', $request->id)->first();
        if(!$product){
            return $this->error(__('main.not_found' , ['model' => 'Product']) , 404);
        }
        if (!$product->has_options) {
            return $this->success(new ProductNoOptionResource($product), __('main.show_successfully', ['model' => 'Product']));
        }

        $product->load([
            'options.option',
            'options.values.optionValue',
            'variants.variants.optionValue.option',
            'variants.varaintImages.image',
        ]);

        return $this->success(new ProductDetailsResource($product), __('main.show_successfully', ['model' => 'Product']));


    }

    public function lastPiece(Request $request)
    {
        return $this->sectionProducts($request, LastPiece::query()->select('product_id'), 'last_piece_products');
    }

    public function newest(Request $request)
    {
        return $this->sectionProducts($request, NewProduct::query()->select('product_id'), 'newest_products');
    }

    public function industries(Request $request)
    {
        $industries = Industry::query();

        if ($request->has('search')) {
            $industries->whereHas('translations', function ($query) use ($request) {
                $query->where('title', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('sort') && in_array($request->sort, ['asc', 'desc'])) {
            $industries->orderBy('created_at', $request->sort);
        }

        if ($request->has('paginate') && ($request->paginate >= 1 && $request->paginate <= 100)) {
            $industries = $industries->paginate($request->paginate);
        } else {
            $industries = $industries->paginate(10);
        }

        return $this->successPaginated(
            $industries,
            IndustryResource::collection($industries),
            'industries',
            __('main.list_successfully', ['model' => 'Industries'])
        );
    }

    public function productsByIndustry(Request $request)
    {
        if (!$request->has('id')) {
            return $this->error(__('main.no_id'), 404);
        }

        $industry = Industry::find($request->id);
        if (!$industry) {
            return $this->error(__('main.not_found', ['model' => 'Industry']), 404);
        }

        $products = Product::with(['category', 'brand', 'industries'])
            ->where('status', '!=', 'draft')
            ->whereHas('industries', function ($query) use ($request) {
                $query->where('industries.id', $request->id);
            });

        if ($request->has('sort') && in_array($request->sort, ['asc', 'desc'])) {
            $products->orderBy('created_at', $request->sort);
        }

        if ($request->has('paginate') && ($request->paginate >= 1 && $request->paginate <= 100)) {
            $products = $products->paginate($request->paginate);
        } else {
            $products = $products->paginate(10);
        }

        return $this->successPaginated(
            $products,
            ProductResource::collection($products),
            'products',
            __('main.list_successfully', ['model' => 'Products'])
        );
    }

    public function varaintDetails(Request $request){
        if($request->has('variant_id')){
            $variant = ProductVariant::with(['varaintImages' , 'variants'])->where('status', '!=', 'draft')->where('id', $request->variant_id)->first();
            if(!$variant){
                return $this->error(__('main.not_found' , ['model' => 'Variant']), 404);
            }
            return $this->success(new VaraintDetailsResource($variant) , __('main.show_successfully' , ['model' => 'Variant']));
        }

    }




    private function sectionProducts(Request $request, $productsSubQuery, string $responseKey)
    {
        $products = Product::query()
            ->with(['category', 'brand', 'industries'])
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




    // get realted products based on product id
    public function relatedProducts(Request $request){
        if(!$request->has('id')){
            return $this->error(__('main.no_id'), 404);
        }
        $product = Product::find($request->id);
        if(!$product){
            return $this->error(__('main.not_found', ['model' => 'Product']), 404);
        }
        $relatedProducts = Product::with(['category', 'brand', 'industries'])
            ->where('status', '!=', 'draft')
            ->where('id', '!=', $product->id)
            ->where(function ($query) use ($product) {
                $query->where('category_id', $product->category_id)
                    ->orWhere('brand_id', $product->brand_id)
                    ->orWhereHas('industries', function ($query) use ($product) {
                        $query->whereIn('industries.id', $product->industries->pluck('id'));
                    });
        });

        if($request->has('paginate') && ($request->paginate >= 1 && $request->paginate <= 100)){
            $relatedProducts = $relatedProducts->paginate($request->paginate);
        }else{
            $relatedProducts = $relatedProducts->paginate(10);
        }

        return $this->successPaginated(
            $relatedProducts,
            ProductResource::collection($relatedProducts),
            'related_products',
            __('main.list_successfully', ['model' => 'Related Products'])
        );

        
    }




}
