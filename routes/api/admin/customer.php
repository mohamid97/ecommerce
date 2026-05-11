<?php

use Illuminate\Support\Facades\Route;

Route::controller('CustomerController')->group(function () {
    Route::post('/customer/all', 'all')->middleware('permission:view user');
    Route::post('/customer/view', 'view')->middleware('permission:view user');
    Route::post('/customer/orders', 'orders')->middleware('checkEcommercePermision:order,view');
});
