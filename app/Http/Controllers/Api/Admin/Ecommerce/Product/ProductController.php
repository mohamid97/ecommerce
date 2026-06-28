<?php

namespace App\Http\Controllers\Api\Admin\Ecommerce\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\Ecommerce\Product\StoreRelatedProductsRequest;
use App\Http\Requests\Api\Admin\Ecommerce\Product\UpdateProductSectionRequest;
use App\Http\Requests\Api\Admin\Ecommerce\Product\UpdateProductVaraintStatusRequest;
use App\Http\Resources\Api\Admin\ProductResource;
use App\Models\Api\Admin\Product;
use App\Models\Api\Admin\RelatedProduct;
use App\Models\Api\Ecommerce\LastPiece;
use App\Models\Api\Ecommerce\NewProduct;
use App\Models\Api\Ecommerce\ProductVariant;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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



    public function addLastPiece(UpdateProductSectionRequest $request)
    {
        // if variant_id provided, ensure it belongs to product
        if ($request->filled('variant_id')) {
            $variant = ProductVariant::find($request->variant_id);
            if (! $variant || $variant->product_id !== (int) $request->id) {
                return $this->error(__('main.invalid_variant_for_product'));
            }
        }

        return $this->addSectionProduct($request->id, LastPiece::class, $request->variant_id ?? null);
    }

    public function deleteLastPiece(UpdateProductSectionRequest $request)
    {
        if ($request->filled('variant_id')) {
            $variant = ProductVariant::find($request->variant_id);
            if (! $variant || $variant->product_id !== (int) $request->id) {
                return $this->error(__('main.invalid_variant_for_product'));
            }
        }

        return $this->deleteSectionProduct($request->id, LastPiece::class, $request->variant_id ?? null);
    }

    public function addNewest(UpdateProductSectionRequest $request)
    {
        return $this->addSectionProduct($request->id, NewProduct::class);
    }

    public function deleteNewest(UpdateProductSectionRequest $request)
    {
        return $this->deleteSectionProduct($request->id, NewProduct::class);
    }

    public function newestProducts(Request $request)
    {
        return $this->sectionProducts($request, NewProduct::query()->select('product_id'), 'newest_products');
    }

    public function lastPieceProducts(Request $request)
    {
        return $this->sectionProducts($request, LastPiece::query()->select('product_id'), 'items');
    }

    public function featuredProducts(Request $request)
    {
        $products = $this->baseProductListQuery($request)
            ->where('is_featured', true);

        return $this->paginatedProducts($products, $request, 'items');
    }


    public function storeRelatedProduct(StoreRelatedProductsRequest $request){
        try{
            DB::beginTransaction();
            $product = Product::findOrFail($request->id);
            if (!empty($request->related_products)) {
                $product->related()->sync($request->related_products);
            }
            DB::commit();
            return $this->success(__('main.updated_successfully'));

        }catch(\Exception $e){
            DB::rollBack();
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

        return $this->success($products , __('main.data_retrieved'));
    }

    return $this->error(__('main.not_founded') ,404);
}

    private function addSectionProduct(int $productId, string $modelClass, ?int $variantId = null)
    {
        $attributes = ['product_id' => $productId, 'variant_id' => $variantId];
        $modelClass::firstOrCreate($attributes);
        return $this->success(__('main.stored_successfully', ['model' => 'Product']));
    }

    private function deleteSectionProduct(int $productId, string $modelClass, ?int $variantId = null)
    {
        $query = $modelClass::where('product_id', $productId);
        if ($variantId !== null) {
            $query->where('variant_id', $variantId);
        }
        $query->delete();

        return $this->success(__('main.updated_successfully'));
    }

    private function sectionProducts(Request $request, $productsSubQuery, string $responseKey)
    {
        $products = $this->baseProductListQuery($request)
            ->whereIn('id', $productsSubQuery);

        return $this->paginatedProducts($products, $request, $responseKey);
    }

    private function baseProductListQuery(Request $request)
    {
        $products = Product::with(['category', 'brand', 'industries'])
            ->withSum('variants as variants_sales_number_sum', 'sales_number');

        if ($request->has('search')) {
            $products->whereHas('translations', function ($query) use ($request) {
                $query->where('title', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('category_id')) {
            $products->where('category_id', $request->category_id);
        }

        if ($request->has('industry_id')) {
            $products->whereHas('industries', function ($query) use ($request) {
                $query->where('industries.id', $request->industry_id);
            });
        }

        if ($request->has('sort') && in_array($request->sort, ['asc', 'desc'])) {
            $products->orderBy('created_at', $request->sort);
        } else {
            $products->latest();
        }

        return $products;
    }

    private function paginatedProducts($products, Request $request, string $responseKey)
    {
        $perPage = $request->has('paginate') && ($request->paginate >= 1 && $request->paginate <= 100)
            ? (int) $request->paginate
            : 10;

        $products = $products->paginate($perPage);

        return $this->successPaginated(
            $products,
            ProductResource::collection($products),
            $responseKey,
            __('main.list_successfully', ['model' => 'Products'])
        );
    }


public function relatedProducts(Request $request)
{
    if ($request->id) {

        $related = RelatedProduct::with('product') // load only id
            ->where('product_id', $request->id)
            ->get()
            ->map(function ($item) {

                return [
                    'id' => $item?->product?->id,
                    'title' => $item?->product?->title, // translatable
                ];
            });

return $this->success($related, __('main.data_retrieved'));
    }

    return $this->error(__('main.not_founded'), 404);
}






    



}
