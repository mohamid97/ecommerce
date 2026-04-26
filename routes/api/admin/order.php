<?php
use Illuminate\Support\Facades\Route;

Route::namespace('Ecommerce')->group(function () {
    Route::post('/order/all' , 'OrderController@all')->middleware('checkEcommercePermision:order,view');
    Route::post('/order/view' , 'OrderController@view')->middleware('checkEcommercePermision:order,view');

});
