<?php

use Illuminate\Support\Facades\Route;

Route::prefix('carts')->namespace('Ecommerce')->controller('CartController')->group(function(){
    Route::post('all', 'index')->middleware('checkEcommercePermision:cart,view');
    Route::post('view', 'show')->middleware('checkEcommercePermision:cart,view');
    Route::post('delete-item', 'deleteItem')->middleware('checkEcommercePermision:cart,delete');
    Route::post('delete-all', 'deleteAll')->middleware('checkEcommercePermision:cart,delete');
    Route::post('user-clear', 'clearByUser')->middleware('checkEcommercePermision:cart,delete');
});
