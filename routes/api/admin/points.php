<?php
use Illuminate\Support\Facades\Route;

Route::namespace('Ecommerce')->group(function () {
    Route::post('get-points-tiers', 'PointsController@getTiers')->middleware('checkEcommercePermision:points,view');
    Route::post('store-points-tier', 'PointsController@storeTier')->middleware('checkEcommercePermision:points,create');
    Route::post('delete-points-tier', 'PointsController@deleteTier')->middleware('checkEcommercePermision:points,delete');



});
