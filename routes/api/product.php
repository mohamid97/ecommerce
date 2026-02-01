<?php
use Illuminate\Support\Facades\Route;
// add stock for no vairant product
Route::post('get-batches' , 'ProductController@getBatches')->middleware('checkPermision:view');
Route::post('add-stock' , 'ProductController@addStock')->middleware('checkPermision:create');
Route::post('update-stock' , 'ProductController@updateStock')->middleware('checkPermision:update');