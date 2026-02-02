<?php
use Illuminate\Support\Facades\Route;

// add stock for no vairant product
Route::namespace('Ecommerce\Product')->group(function () {

    // stocks
    Route::post('update-batch-status' , 'StockController@updateStatus')->middleware('checkPermision:update');
    Route::post('delete-batch' , 'StockController@deleteBatch')->middleware('checkPermision:delete');
    Route::post('batch-details' , 'StockController@batchDetails')->middleware('checkPermision:view');
    Route::post('get-batches' , 'StockController@getBatches')->middleware('checkPermision:view');
    Route::post('add-stock' , 'StockController@addStock')->middleware('checkPermision:create');
    Route::post('update-stock' , 'StockController@updateStock')->middleware('checkPermision:update');


    // variants
    Route::post('variants-combinations' , 'VariantController@variantsCombinations')->middleware('checkPermision:view');
    Route::post('store-variant' , 'VariantController@storeVariant')->middleware('checkPermision:create');






});