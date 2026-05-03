<?php

use Illuminate\Support\Facades\Route;

Route::prefix('wishlists')->namespace('Ecommerce')->controller('WishlistController')->group(function(){
    Route::post('all', 'index')->middleware('checkEcommercePermision:wishlist,view');
    Route::post('view', 'show')->middleware('checkEcommercePermision:wishlist,view');
    // Route::post('delete-item', 'deleteItem')->middleware('checkEcommercePermision:wishlist,delete');
    // Route::post('user-clear', 'clearByUser')->middleware('checkEcommercePermision:wishlist,delete');
});
