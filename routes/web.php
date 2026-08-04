<?php

use App\Http\Controllers\Api\Front\Ecommerce\MetaFeedController;
use Illuminate\Support\Facades\Route;

Route::get('/meta-feed.xml', MetaFeedController::class);
Route::get('/products/meta-feed.xml', MetaFeedController::class);
Route::get('/products/meta-feed', MetaFeedController::class);
