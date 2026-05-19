<?php

use Illuminate\Support\Facades\Route;

Route::namespace('Ecommerce\\Statistics')->prefix('statistics')->group(function () {
    Route::post('/overview', 'HomeController@overview')->middleware('checkEcommercePermision:statistics,view');
    Route::post('/recent-orders', 'HomeController@recentOrders')->middleware('checkEcommercePermision:statistics,view');
    Route::post('/order-status-percentages', 'HomeController@orderStatusPercentages')->middleware('checkEcommercePermision:statistics,view');
    Route::post('/best-selling', 'HomeController@bestSelling')->middleware('checkEcommercePermision:statistics,view');
});
