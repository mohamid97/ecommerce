<?php
use Illuminate\Support\Facades\Route;

// add stock for no vairant product
Route::namespace('Ecommerce\Product')->group(function () {


    // products 
    Route::post('/make-featured' , 'ProductController@makeFeatured')->middleware('checkPermision:update');
    Route::post('/update-product-varaint-status' , 'ProductController@updateStatusProductOrVaraint')->middleware('checkPermision:update');
    Route::post('/store-related-products' , 'ProductController@storeRelatedProduct')->name('checkPermision:update');
    Route::post('/related-products' , 'ProductController@relatedProducts')->name('checkPermision:view');
    Route::post('/filter-product' , 'ProductController@filterProduct')->name('checkPermision:view');
    
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
    Route::post('update-variant','VariantController@updateVariant')->middleware('checkPermision:update');
    Route::post('/varints-product' , 'VariantController@varintsProduct')->middleware('checkPermision:view');
    Route::post('/view-variant' , 'VariantController@viewVariant')->middleware('checkPermision:view');
    Route::post('/delete-variant' , 'VariantController@deleteVariant')->middleware('checkPermision:delete'); 
    Route::post('/make-default' , 'VariantController@makeDefault')->middleware('checkPermision:update');



    




    // general and special image for variant
    Route::post('store-general-image' , 'GalleryController@storeGeneralImage')->middleware('checkPermision:create');
    Route::post('store-special-image' , 'GalleryController@storeSpecialImage')->middleware('checkPermision:create');
    Route::post('get-general-images' , 'GalleryController@generalImages')->middleware('checkPermision:view');
    Route::post('get-special-images' , 'GalleryController@specialImages')->middleware('checkPermision:view');
    Route::post('delete-general-image' , 'GalleryController@deleteGeneralImage')->middleware('checkPermision:delete');
    Route::post('delete-special-image' , 'GalleryController@deleteSpecialImage')->middleware('checkPermision:delete');




    // filter product with varaints
    Route::post('filter-product-varaints' , 'VariantController@filterProductaraints')->middleware('checkPermision:view');



    






});






















