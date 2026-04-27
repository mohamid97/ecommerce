<?php

namespace App\Http\Controllers\Api\Front\Ecommerce;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Front\Ecommerce\BundelDetailsResourc;
use App\Http\Resources\Api\Front\Ecommerce\BundelResource;
use App\Models\Api\Ecommerce\Bundel;
use App\Traits\ResponseTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class BundleController extends Controller
{
    use ResponseTrait;
    public function get(Request $request){
        $perPage = ($request->has('paginate') && ($request->paginate >= 1 && $request->paginate <= 100))
            ? (int) $request->paginate
            : 10;

        $page = LengthAwarePaginator::resolveCurrentPage();

        $bundlesCollection = Bundel::with([
            'category',
            'brand',
            'bundelDetails.product.variants',
        ])
            ->where('status', 'active')
            ->get()
            ->filter(fn ($bundle) => $bundle->hasOnlyAvailableItems())
            ->values();

        $bundles = new LengthAwarePaginator(
            $bundlesCollection->forPage($page, $perPage),
            $bundlesCollection->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return $this->successPaginated(
            $bundles,
            BundelResource::collection($bundles),
            'bundles',
            __('main.list_successfully', ['model' => 'Bundles'])
        );

    }




    public function bundleDetails(Request $request){
        try {
            $bundle = Bundel::with([
                'category',
                'brand',
                'bundelDetails.product.variants.variants.optionValue.option',
            ])
                ->where('status', 'active')
                ->findOrFail($request->id);

            if (!$bundle->hasOnlyAvailableItems()) {
                return $this->error('Bundle not found or unavailable', 404);
            }

            return $this->success(new BundelDetailsResourc($bundle) , __('main.show_successfully' , ["model"=>'bundle']));
        } catch (ModelNotFoundException $e) {
            return $this->error('Bundle not found or unavailable', 404);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }
}
