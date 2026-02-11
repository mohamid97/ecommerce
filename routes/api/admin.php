<?php

use App\Models\Api\Admin\Country;
use App\Models\Api\Admin\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
// use Illuminate\Support\Facades\Http;
// use Stichoza\GoogleTranslate\GoogleTranslate;


    // Route::get('/debug-token', function (Request $request) {
    //     return response()->json([
    //         'headers' => $request->headers->all(),
    //         'token'   => $request->bearerToken(),
    //     ]);
    // });


    // Route::get('test_trans' , function(){
    //         ini_set('max_execution_time', 0); // 0 = unlimited
    //         set_time_limit(0);
    //         $tr = new GoogleTranslate('ar');
    //         try{

    //          DB::beginTransaction();
    //          $countries = Http::get('https://countriesnow.space/api/v0.1/countries/states')->json()['data'];

    //         foreach ($countries as $country) {

    //             $countryModel = Country::create([
    //                 'iso2'     => $country['iso2'] ?? null,
    //                 'iso3'     => $country['iso3'] ?? null,
    //             ]);
    //             $countryModel->{'name:en'} = $country['name'];
    //             $countryModel->{'name:ar'} = $tr->translate( $country['name']);
    //             $countryModel->save();

      
    //             foreach ($country['states'] as $state) {
    //                 $stateModel = State::create([
    //                     'country_id' => $countryModel->id,
    //                     'state_code' =>$state['state_code'] ?? null,
    //                 ]);

    //                 $stateModel->{'name:ar'} = $tr->translate( $state['name']);
    //                 $stateModel->{'name:en'} = $state['name']; 
    //                 $stateModel->save();

    //             }
               



                
    //         }


    //         DB::commit();
    //         return 'done';

    //         }catch(\Exception $e){
    //             DB::rollBack();
    //             dd($e->getMessage() , $e->getLine() ,$countryModel );
    //             return $e->getMessage();
    //         }


            
    // });

// start Auth 
Route::prefix('v1')->middleware('ckeckLang')->group(function () {



    Route::post('login', 'AuthController@login');
    
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('get-user', 'AuthController@getData');
        Route::post('logout', 'AuthController@logout');
        Route::post('store', 'CrudController@store')->middleware('checkPermision:create');
        Route::post('update', 'CrudController@update')->middleware('checkPermision:update');
        Route::post('delete' , 'CrudController@delete')->middleware('checkPermision:delete');
        Route::post('all' , 'CrudController@all')->middleware('checkPermision:view');
        Route::post('view' , 'CrudController@view')->middleware('checkPermision:view');
        Route::post('gallery/store','CrudController@storeGallery')->middleware('checkPermision:create');
        Route::post('gallery/all','CrudController@viewGallery')->middleware('checkPermision:view');

        // specification
        Route::post('specification/store' , 'CrudController@storeSpecification')->middleware('checkPermision:create');
        Route::post('specification/all','CrudController@viewSpecification')->middleware('checkPermision:view');

        // get brands with category id
        Route::post('category/brands' ,"CategoryController@getBrand");

        // home page 
        Route::get('/home' ,'HomeController@index');

        // start product file route
         require __DIR__.'/admin/product.php';
         require __DIR__.'/admin/bundel.php';




    




    });
    
    

    



    
});