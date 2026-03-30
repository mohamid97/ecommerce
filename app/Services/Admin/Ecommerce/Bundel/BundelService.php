<?php

namespace App\Services\Admin\Ecommerce\Bundel;

use App\Models\Api\Ecommerce\Bundel;
use App\Services\Admin\Ecommerce\Bundel\Actions\StoreBundelAction;
use App\Services\Admin\Ecommerce\Bundel\Actions\UpdateBundelAction;
use Exception;

// BundelService.php
class BundelService {

    public function __construct(
        private StoreBundelAction $store,
        private UpdateBundelAction $update,
    ) {}

    public function getBundels($request){
        return Bundel::with(['categoey' , 'brand'])->get();

    }

    public function storeBundel($data) {
        return $this->store->storeBundel($data);
    }

    public function updateBundel($data) {
        return $this->update->updateBundel($data);
    }

    public function deleteBundel($data) {
        if (Bundel::where('id', $data['bundel_id'])->exists()) {
            Bundel::where('id', $data['bundel_id'])->delete();
            return true;
        }
        throw new Exception(__('main.not_found', ['model' => 'Bundel']));
    }
}