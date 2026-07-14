<?php
use Illuminate\Support\Facades\Route;

Route::namespace('Ecommerce')->group(function () {
    Route::post('/order/all' , 'OrderController@all')->middleware('checkEcommercePermision:order,view');
    Route::post('/order/view' , 'OrderController@view')->middleware('checkEcommercePermision:order,view');
    Route::post('/order/user-summary' , 'OrderController@userSummary')->middleware('checkEcommercePermision:order,view');
    Route::post('/order/update-status' , 'OrderController@updateStatus')->middleware('checkEcommercePermision:order,update');
    Route::post('/order/delete' , 'OrderController@delete')->middleware('checkEcommercePermision:order,delete');
    Route::post('/order/create-guest' , 'OrderController@createGuest')->middleware('checkEcommercePermision:order,create');

});
