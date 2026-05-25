<?php

use Illuminate\Support\Facades\Route;



Route::get('get-user', 'AuthController@getData');
Route::post('logout', 'AuthController@logout');