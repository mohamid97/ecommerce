<?php
use Illuminate\Support\Facades\Route;



Route::namespace('Ecommerce\Bundel')->group(function () {
  
    Route::post('view-bundel' , 'BundelController@bundelDetails')->middleware('checkEcommercePermision:bundel,view');
    Route::post('store-bundel' , 'BundelController@storeBundel')->middleware('checkEcommercePermision:bundel,create');
    Route::post('update-bundel' , 'BundelController@updateBundel')->middleware('checkEcommercePermision:bundel,update');
    Route::post('update-bundel-status' , 'BundelController@updateBundelStatus')->middleware('checkEcommercePermision:bundel,update');
    Route::post('delete-bundel' , 'BundelController@deleteBundel')->middleware('checkEcommercePermision:bundel,delete');
    Route::post('get-bundels' , 'BundelController@getBundels')->middleware('checkEcommercePermision:bundel,view');


});
