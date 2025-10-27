<?php

use Illuminate\Support\Facades\Route;


Route::prefix('v1')->middleware('ckeckLang')->group(function () {
    
    Route::post('dynamic-endpoint','DynamicEndPoint@dynamicEndpoint');

});                                                                                            