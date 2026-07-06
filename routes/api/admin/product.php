<?php
use Illuminate\Support\Facades\Route;

// add stock for no vairant product
Route::namespace('Ecommerce\Product')->group(function () {


    // products 
    Route::post('/make-featured' , 'ProductController@makeFeatured')->middleware('checkPermision:update');
    Route::post('/add-last-piece' , 'ProductController@addLastPiece')->middleware('checkPermision:update');
    Route::post('/delete-last-piece' , 'ProductController@deleteLastPiece')->middleware('checkPermision:update');
    Route::post('/add-newest-product' , 'ProductController@addNewest')->middleware('checkPermision:update');
    Route::post('/delete-newest-product' , 'ProductController@deleteNewest')->middleware('checkPermision:update');
    Route::post('/newest-products' , 'ProductController@newestProducts')->middleware('checkPermision:view');
    Route::post('/last-piece-products' , 'ProductController@lastPieceProducts')->middleware('checkPermision:view');
    Route::post('/featured-products' , 'ProductController@featuredProducts')->middleware('checkPermision:view');
    Route::post('/update-product-varaint-status' , 'ProductController@updateStatusProductOrVaraint')->middleware('checkPermision:update');
    Route::post('/store-related-products' , 'ProductController@storeRelatedProduct')->name('checkPermision:update');
    Route::post('/related-products' , 'ProductController@relatedProducts')->name('checkPermision:view');
    Route::post('/filter-product' , 'ProductController@filterProduct')->name('checkPermision:view');
    




    // stocks
    Route::post('update-batch-status' , 'StockController@updateStatus')->middleware('checkEcommercePermision:product,update');
    Route::post('delete-batch' , 'StockController@deleteBatch')->middleware('checkEcommercePermision:product,delete');
    Route::post('batch-details' , 'StockController@batchDetails')->middleware('checkEcommercePermision:product,view');
    Route::post('get-batches' , 'StockController@getBatches')->middleware('checkEcommercePermision:product,view');
    Route::post('add-stock' , 'StockController@addStock')->middleware('checkEcommercePermision:product,create');
    Route::post('bulk-store-stock' , 'StockController@bulkAddStock')->middleware('checkEcommercePermision:product,create');
    Route::post('update-stock' , 'StockController@updateStock')->middleware('checkEcommercePermision:product,update');







    // variants
    Route::post('bulk-store-variants' , 'BulkVariantController@storeBulkVariants')->middleware('checkEcommercePermision:product,create');
    Route::post('variants-combinations' , 'VariantController@variantsCombinations')->middleware('checkEcommercePermision:product,create');
    Route::post('store-variant' , 'VariantController@storeVariant')->middleware('checkEcommercePermision:product,create');
    Route::post('update-variant','VariantController@updateVariant')->middleware('checkEcommercePermision:product,update');
    Route::post('varints-product' , 'VariantController@varintsProduct')->middleware('checkEcommercePermision:product,view');
    Route::post('view-variant' , 'VariantController@viewVariant')->middleware('checkEcommercePermision:product,view');
    Route::post('delete-variant' , 'VariantController@deleteVariant')->middleware('checkEcommercePermision:product,delete'); 
    Route::post('make-default' , 'VariantController@makeDefault')->middleware('checkEcommercePermision:product,update');
    Route::post('import-variants' , 'VariantController@importVariants')->middleware('checkEcommercePermision:product,create');






    




    // general and special image for variant
    Route::post('store-general-image' , 'GalleryController@storeGeneralImage')->middleware('checkEcommercePermision:product,create');
    Route::post('store-special-image' , 'GalleryController@storeSpecialImage')->middleware('checkEcommercePermision:product,create');
    Route::post('get-general-images' , 'GalleryController@generalImages')->middleware('checkEcommercePermision:product,view');
    Route::post('get-special-images' , 'GalleryController@specialImages')->middleware('checkEcommercePermision:product,view');
    Route::post('delete-general-image' , 'GalleryController@deleteGeneralImage')->middleware('checkEcommercePermision:product,delete');
    Route::post('delete-special-image' , 'GalleryController@deleteSpecialImage')->middleware('checkEcommercePermision:product,delete');



    



    // filter product with varaints
    Route::post('filter-product-varaints' , 'VariantController@filterProductaraints')->middleware('checkEcommercePermision:product,view');



    






});



















