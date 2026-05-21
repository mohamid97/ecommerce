<?php

use Illuminate\Support\Facades\Route;


Route::prefix('v1')->middleware('ckeckLang')->group(function () {
    
    Route::post('dynamic-endpoint','DynamicEndPoint@dynamicEndpoint');
    Route::prefix('govs')->controller('GovsController')->group(function(){
        Route::get('get', 'get');
    });

});                                                                                            