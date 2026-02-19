<?php
use Illuminate\Support\Facades\Route;



Route::namespace('Ecommerce\Promotion')->group(function () {
 Route::post('store-promotion' , 'PromotionController@storePromotion')->middleware('checkPermision:create');
 Route::post('update-promotion' , 'PromotionController@updatePromotion')->middleware('checkPermision:update');
 Route::post('delete-promotion' , 'PromotionController@deletePromotion')->middleware('checkPermision:delete');
 Route::post('promotion-details' , 'PromotionController@promotionDetails')->middleware('checkPermision:view');
 Route::post('get-promotions' , 'PromotionController@getPromotions')->middleware('checkPermision:view');  



});