<?php

use Illuminate\Support\Facades\Route;


Route::prefix('v1')->middleware('ckeckLang')->group(function () {

    Route::prefix('auth:sanctum')->controller('CartController')->group(function(){
        // start carts with authanicate 
        Route::prefix('carts')->group(function(){
            Route::post('store' , 'addToCart');

        });
    });

    
});