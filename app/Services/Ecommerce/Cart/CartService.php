<?php
namespace App\Services\Ecommerce\Cart;


class CartService{

    public function __construct(
        protected CartRepository $repo,
        protected CartAction $action
    ) {}
    public function StoreToCart($userId , $dto){

        // check if product exist
        $this->action->checkProductExists($dto->product_id);
        if(isset($dto->varaint_id)){
            $this->action->checkProductHasOption();
            $this->action->checkVariantExists($dto->varaint_id);
            $this->action->checkStockWithOption($dto->quantity);
        }
        $this->action->checkStock($dto->quantity);

        return $this->repo->createOrUpdateCard($userId , $dto);
        
          
        

    }


    public function RemoveFromCart($userId , $dto){
        $this->action->checkProductExists($dto->productId);
        if(isset($dto->variantId)){
            $this->action->checkProductHasOption();
            $this->action->checkVariantExists($dto->variantId);
        }
        $this->repo->removeFromCart($userId , $dto);
    }

















    


    
}