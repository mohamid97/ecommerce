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
    protected array  $relations  = ['category' , 'shipmentDetails' , 'related' , 'options.option','options.values.optionValue','category','brand', 'industries'];

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

    if(isset($this->data['discount']) && isset($this->data['discount_type'])){
        $this->validateDiscount($this->data['discount_type'], $this->data['discount'] , $this->data['sale_price']);
    }
        $this->data['has_options'] = filter_var($this->data['has_options'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
        $this->data['on_demand']   = filter_var($this->data['on_demand'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0;

        $this->uploadSingleImage(['product_image', 'breadcrumb'], 'uploads/products');
        $this->data['slug']  = $this->createSlug($this->data); 
        $product = parent::store($this->getBasicColumn(['product_image','breadcrumb', 'sku', 'barcode', 'sale_price', 'discount', 'discount_type', 'status','has_options' , 'order' , 'brand_id','category_id']));
        $this->processTranslations($product, $this->data, ['title', 'slug' ,'des' , 'small_des' , 'meta_title' , 'meta_des', 'alt_image' , 'title_image']);  
        $storeProductService = app(StoreProductService::class);
        if($product->has_options){
            $product->update(['status' => 'draft']);
            $storeProductService->addProductOption($this->data['product_options'] , $product);
        }         
        $this->syncIndustries($product);
        $storeProductService->storeProductShipment($this->data , $product->id);
        

        return $product;

        
    } // end store product
    






    
    public function update($id){

        if(isset($this->data['discount']) && isset($this->data['discount_type'])){
          $this->validateDiscount($this->data['discount_type'], $this->data['discount'] , $this->data['sale_price']);

        }
            

        $this->data['has_options'] = filter_var($this->data['has_options'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
        $this->data['on_demand']   = filter_var($this->data['on_demand'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0;

        $existingProduct = \App\Models\Api\Admin\Product::findOrFail($id);
        $oldHasOptions = (bool) $existingProduct->has_options;

        $this->uploadSingleImage(['product_image', 'breadcrumb'], 'uploads/products');
       
        $product = parent::update($id , $this->getBasicColumn(['product_image','breadcrumb', 'sku', 'barcode', 'sale_price', 'discount', 'discount_type', 'status','has_options' , 'order' , 'brand_id','category_id']));
        $this->processTranslations($product, $this->data, ['title', 'slug' ,'des' , 'small_des' , 'meta_title' , 'meta_des', 'alt_image' , 'title_image']);
        
        $newHasOptions = (bool) $product->has_options;
        if ($oldHasOptions !== $newHasOptions) {
            \App\Models\Api\Ecommerce\CartItem::where('product_id', $product->id)->delete();

            if ($oldHasOptions && !$newHasOptions) {
                $product->options()->delete();
                $product->variants()->delete();
            }
        }

        $updateProductService = app(UpdateProductService::class);
        

        if($product->has_options){
           $product->update(['status' => 'draft']);
           $updateProductService->updateProductOption($this->data['product_options'],$product);
        }

           
        $this->syncIndustries($product);

        if($product->status != 'active'){
            $this->deactivateProductBundlesAndDeleteItemCart($product->id);
        }
 

        $updateProductService->updateProductShipment($this->data , $product->id);
        return $product;

  
        
    }

    public function delete($id){
        $product = parent::delete($id);
        return $product;
    }


    private function validateDiscount($type, $value, $salePrice){
        if($type == 'percentage' && ($value < 0 || $value > 100)){
            throw new \Exception('Invalid discount percentage value');
        }
        if($type == 'fixed' && ($value < 0 || $value > $salePrice)){
            throw new \Exception('Invalid fixed discount value');
        }
    }

    private function syncIndustries(Product $product): void
    {
        $product->industries()->sync($this->data['industries'] ?? []);
    }

    public function applySearch(Builder $query, string $search ){
        return $query->where(function ($q) use ($search) {
            $q->whereTranslationLike('title', "%$search%")
              ->orWhereTranslationLike('slug', "%$search%");
        });
    }
    

    public function orderBy(Builder $query, string $orderBy, string $direction = 'asc')
    {
        return $query->orderBy($orderBy, $direction);
    }



    private function deactivateProductBundlesAndDeleteItemCart($productId){
        $productBundles = DB::table('bundle_details')->where('product_id', $productId)->get();
        foreach($productBundles as $productBundle){
            $bundle = Bundle::find($productBundle->bundle_id);
            $bundle->update(['status' => 'draft']);
            
        }

        $cartItems = CartItem::where('product_id', $productId)->get();
        foreach($cartItems as $cartItem){
            $cartItem->delete();
        }
    }






    

    

}
