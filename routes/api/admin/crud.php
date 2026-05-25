<?php

use Illuminate\Support\Facades\Route;




Route::post('store', 'CrudController@store')->middleware('checkPermision:create');
Route::post('update', 'CrudController@update')->middleware('checkPermision:update');
Route::post('delete' , 'CrudController@delete')->middleware('checkPermision:delete');
Route::post('all' , 'CrudController@all')->middleware('checkPermision:view');
Route::post('view' , 'CrudController@view')->middleware('checkPermision:view');