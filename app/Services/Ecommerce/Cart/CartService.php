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
        //  check and validate if product has option and stock 
        ($dto->product_option_id) ? $this->ValidateCartOption($dto->quantity) : $this->ValidateCart($dto->quantity);

        $this->repo->createOrUpdateCard($userId , $dto);
          
        

    }

    protected function ValidateCartOption($quantity){
            $this->action->checkProductHasOption();
            $this->action->checkStockWithOption($quantity);
    }

    protected function ValidateCart($quantity){
          $this->action->checkStock($quantity);
    }














    


    
}