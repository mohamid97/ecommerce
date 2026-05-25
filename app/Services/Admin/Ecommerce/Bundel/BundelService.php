<?php

namespace App\Services\Admin\Ecommerce\Bundel;

use App\Models\Api\Ecommerce\Bundel;
use App\Services\Admin\Ecommerce\Bundel\Actions\StoreBundelAction;
use App\Services\Admin\Ecommerce\Bundel\Actions\UpdateBundelAction;
use Exception;

class BundelService {

    public function __construct(
        private StoreBundelAction $store,
        private UpdateBundelAction $update,
        private CheckBundleProductsStatusAction $checkBundleProductsStatus,
        private RemoveBundleFromCartAction $removeBundleFromCart,
    ) {}

    public function getBundels($request){
        return Bundel::with(['category' , 'brand'])->latest()->get();

    }

    public function storeBundel($data) {
        return $this->store->storeBundel($data);
    }

    public function updateBundel($data) {
        return $this->update->updateBundel($data);
    }

    public function updateBundelStatus(array $data): Bundel
    {
        $bundel = Bundel::with(['category', 'brand'])->findOrFail($data['id']);
        // need if make bundle active need to make sure that all products  or varint in the bundle are active?
        if($data['status'] == 'active'){
            $this->checkBundleProductsStatus->execute($bundel);
        }else{
            // remove bundle from cart  if exists because bundle will be inactive
            $this->removeBundleFromCart->execute($bundel);
        }
        
        $bundel->update(['status' => $data['status']]);
        return $bundel;
    }

    public function deleteBundel($data) {
        if (Bundel::where('id', $data['id'])->exists()) {
            Bundel::where('id', $data['id'])->delete();
            return true;
        }
        throw new Exception(__('main.not_found', ['model' => 'Bundel']));
    }




}
