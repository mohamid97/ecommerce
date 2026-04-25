<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Front\MessageController;



 
Route::prefix('v1')->middleware('ckeckLang')->group(function () {

    // members Auth system
    Route::prefix('auth')->controller('MemberController')->group(function(){
        Route::post('/send-verification', 'sendVerification');
        Route::post('/verfiy-otp', 'verfiyOtp');
        Route::post('/register', 'register');
        Route::post('/login', 'login');


    });

    Route::middleware('auth:sanctum')->group(function () {
        // start carts with authanicate 
        Route::prefix('carts')->namespace('Ecommerce')->controller('CartController')->group(function(){
            Route::post('/add', 'addToCart');
            Route::post('/delete-all', 'deleteAllFromCart');
            Route::post('/delete-item', 'deleteFromCart');
            Route::get('/view', 'viewCart');
        });

        // orders
        Route::prefix('orders')->namespace('Ecommerce')->controller('OrderController')->group(function(){
            Route::post('/store', 'store');
        });

    });


    // contact and messages
    Route::post('send-message', [MessageController::class, 'sendMessage']);

    // dynamic endpoint to get profile data
    Route::prefix('data')->controller('FrontendController')->group(function(){
        Route::post('get' , 'get');  
        Route::post('dynamic/filter' , 'dynamicFilter');
        Route::post('gallery' , 'getGallery'); 
        Route::post('search' , 'search');     
    });
    
    Route::prefix('applicant')->controller('ApplicantController')->group(function(){
        Route::post('store' , 'store');
        
    });


    Route::prefix('products')->namespace('Ecommerce')->controller('ProductController')->group(function(){
        Route::get('get' , 'get');
        Route::post('details' , 'productDetails');
        Route::post('varaint-details' , 'varaintDetails');
    });


    // bundel 

    Route::prefix('bundles')->namespace('Ecommerce')->controller('BundleController')->group(function(){
        Route::get('get' , 'get');
        Route::post('details' , 'bundleDetails');
    });






    


      




});