<?php
namespace App\Services\Admin\Ecommerce\Product;

use App\Models\Api\Admin\Product;
use App\Models\Api\Ecommerce\NoOptionStock;
use App\Services\BaseModelService;
use App\Traits\HandlesImage;
use App\Traits\StoreMultiLang;
use Illuminate\Database\Eloquent\Builder;
use App\Services\Admin\Ecommerce\Product\StoreProductService;
use App\Services\Admin\Ecommerce\Product\UpdateProductService;
class ProductService extends BaseModelService
{
    use StoreMultiLang , HandlesImage;
    protected string $modelClass = Product::class;
    protected array  $relations  = ['category' , 'brand'];

    public function all($request){      
        $product = parent::all($request);
        return $product;
    }

    public function view($id){
        $productDetails = parent::view($id);
        return $productDetails;
    }

    
    public function store()
    {
        $this->uploadSingleImage(['product_image', 'breadcrumb'], 'uploads/products');
        $this->data['slug']  = $this->createSlug($this->data); 
        $product = parent::store($this->getBasicColumn(['product_image','breadcrumb', 'sku', 'barcode', 'sale_price', 'discount', 'discount_type', 'status','has_options' , 'order' , 'brand_id','category_id']));
        $this->processTranslations($product, $this->data, ['title', 'slug' ,'des' , 'small_des' , 'meta_title' , 'meta_des', 'alt_image' , 'title_image']);  
        $storeProductService = app(StoreProductService::class);
        ($product->has_options) ? $storeProductService->addProductOption($this->data['product_options'],$product->id): $storeProductService->completeProductData($this->data,$product->id);  
        return $product;
        
    } // end store product
    






    
    public function update($id ){
        $this->uploadSingleImage(['product_image', 'breadcrumb'], 'uploads/products');
        $product = parent::update($id , $this->getBasicColumn(['product_image','breadcrumb', 'sku', 'barcode', 'cost_price', 'sales_price', 'discount', 'discount_type', 'status','has_options' , 'order' , 'brand_id','category_id']));
        $this->processTranslations($product, $this->data, ['title', 'slug' ,'des' , 'small_des' , 'meta_title' , 'meta_des', 'alt_image' , 'title_image']);
        $updateProductService = app(UpdateProductService::class);
        ($product->has_options) ? $updateProductService->updateProductOption($this->data['product_options'],$product->id): $updateProductService->completeProductData($this->data,$product->id);  
        return $product;
        
    }

    public function delete($id){
        $product = parent::delete($id);
        return $product;
    }


    // public function applySearch(Builder $query, string $search ){
    //     return $query->where(function ($q) use ($search) {
    //         $q->whereTranslationLike('title', "%$search%")
    //           ->orWhereTranslationLike('slug', "%$search%");
    //     });
    // }
    

    // public function orderBy(Builder $query, string $orderBy, string $direction = 'asc')
    // {
    //     return $query->orderBy($orderBy, $direction);
    // }






    

    

}