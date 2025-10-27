<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Api\Admin\Brand;
use App\Models\Api\Admin\Category;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use ResponseTrait;
    public function getBrand(Request $request){
        try{
            $request->validate([
                'id'=>'nullable|exists:categories,id'
            ]);

            if($request->has('id') && $request->id){

              $category = Category::with('brands')->findOrFail($request->id);

              $brands =  $category->brands->map(
                        fn($brand) => [
                            'id'    => $brand->id,
                            'title' => $brand->title, 
                        ]
                        );
            }else{
              $brands = Brand::all();
              
              $brands =  $brands->map(
                        fn($brand) => [
                            'id'    => $brand->id,
                            'title' => $brand->title, 
                        ]
                        );
            }



            return $this->success($brands, __('main.brands'));


        }catch(\Exception $e){
          return $this->error($e->getMessage(), 500);

        }
        
    }
}