<?php
use Illuminate\Support\Facades\Route;



Route::namespace('Ecommerce\Bundel')->group(function () {
  
    Route::post('view-bundel' , 'BundelController@bundelDetails')->middleware('checkPermision:view');
    Route::post('store-bundel' , 'BundelController@storeBundel')->middleware('checkPermision:create');
    Route::post('update-bundel' , 'BundelController@updateBundel')->middleware('checkPermision:update');
    Route::post('delete-bundel' , 'BundelController@deleteBundel')->middleware('checkPermision:delete');
    Route::post('get-bundels' , 'BundelController@getBundels')->middleware('checkPermision:view');


});