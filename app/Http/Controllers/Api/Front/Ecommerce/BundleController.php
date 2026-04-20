<?php

namespace App\Http\Controllers\Api\Front\Ecommerce;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Front\Ecommerce\BundelDetailsResourc;
use App\Http\Resources\Api\Front\Ecommerce\BundelResource;
use App\Models\Api\Ecommerce\Bundel;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class BundleController extends Controller
{
    use ResponseTrait;
    public function get(Request $request){

        $bundles = Bundel::query();
        $bundles->where('status' , 'active');
        if($request->has('paginate') && ($request->paginate >= 1 && $request->paginate <= 100)){
            $bundles = $bundles->paginate($request->paginate);
        }else{
            $bundles = $bundles->paginate(10);
        }

        return $this->successPaginated(
            $bundles,
            BundelResource::collection($bundles),
            'bundles',
            __('main.list_successfully', ['bundles' => 'Bundles'])
        );

    }




    public function details(Request $request){
        $bundle = Bundel::with(['category', 'brand'])->where('status' , 'active')->findOrFail($request->id);
        return $this->success(new BundelDetailsResourc($bundle) , __('main.show_successfully' , ['bundle']));
    }
}
