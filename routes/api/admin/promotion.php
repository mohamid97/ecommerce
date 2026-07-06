<?php
use Illuminate\Support\Facades\Route;



Route::namespace('Ecommerce\Promotion')->group(function () {
 Route::post('store-promotion' , 'PromotionController@storePromotion')->middleware('checkEcommercePermision:promotion,create:create');
 Route::post('update-promotion' , 'PromotionController@updatePromotion')->middleware('checkEcommercePermision:promotion,update:update');
 Route::post('delete-promotion' , 'PromotionController@deletePromotion')->middleware('checkEcommercePermision:promotion,delete:delete');
 Route::post('promotion-details' , 'PromotionController@promotionDetails')->middleware('checkEcommercePermision:promotion,view:view');
 Route::post('get-promotions' , 'PromotionController@getPromotions')->middleware('checkEcommercePermision:promotion,view:view');  



});