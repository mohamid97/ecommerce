<?php

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

    Route::prefix('carts')->namespace('Ecommerce')->controller('CartController')->group(function(){
        Route::post('/guest/view', 'viewGuestCart');
    });

    Route::middleware(['auth:sanctum', 'abilities:customer:*'])->group(function () {
        Route::prefix('auth')->controller('MemberController')->group(function(){
            Route::get('/user', 'getUserData');
            Route::post('/update-user', 'updateUserData');
            Route::post('/complete-profile', 'completeProfile');
        });

        // start carts with authanicate 
        Route::prefix('carts')->namespace('Ecommerce')->controller('CartController')->group(function(){
            Route::post('/add', 'addToCart');
            Route::post('/update-quantity', 'updateQuantity');
            Route::post('/delete-all', 'deleteAllFromCart');
            Route::post('/delete-item', 'deleteFromCart');
            Route::get('/view', 'viewCart');
        });

        // wishlists
        Route::prefix('wishlists')->namespace('Ecommerce')->controller('WishlistController')->group(function(){
            Route::post('/add', 'add');
            Route::post('/toggle', 'toggle');
            Route::post('/delete-item', 'remove');
            Route::get('/view', 'view');
        });

        // orders
        Route::prefix('orders')->namespace('Ecommerce')->controller('OrderController')->group(function(){
            Route::get('/', 'index');
            Route::post('/store', 'store');
            Route::get('/{order_number}', 'show');
        });

    });

    // guest order (no auth)
    Route::prefix('orders')->namespace('Ecommerce')->controller('OrderController')->group(function(){
        Route::post('/guest/store', 'storeGuest');
    });


    // contact and messages
    Route::post('send-message', [MessageController::class, 'sendMessage']);

    // dynamic endpoint to get profile data
    Route::prefix('data')->controller('FrontendController')->group(function(){
        Route::post('get' , 'get')->middleware('allowFrontendModels');
        Route::post('dynamic/filter' , 'dynamicFilter');
        Route::post('gallery' , 'getGallery'); 
        Route::post('search' , 'search');     
    });
    
    Route::prefix('applicant')->controller('ApplicantController')->group(function(){
        Route::post('store' , 'store');
        
    });

    // consultation + training forms
    Route::prefix('consultation')->controller('\App\Http\Controllers\Api\Front\ConsultationController')->group(function(){
        Route::post('store', 'store');
    });

    Route::prefix('training')->controller('\App\Http\Controllers\Api\Front\TrainingController')->group(function(){
        Route::post('store', 'store');
    });


    Route::prefix('products')->namespace('Ecommerce')->controller('ProductController')->group(function(){
        Route::get('get' , 'get');
        Route::get('last-piece' , 'lastPiece');
        Route::get('newest' , 'newest');
        Route::get('industries' , 'industries');
        Route::get('industry-products' , 'productsByIndustry');
        Route::post('details' , 'productDetails');
        Route::post('varaint-details' , 'varaintDetails');
        Route::post('related' , 'relatedProducts');
    });

    Route::prefix('industries')->namespace('Ecommerce')->controller('ProductController')->group(function(){
        Route::get('get' , 'industries');
        Route::post('products' , 'productsByIndustry');
    });


    // bundel 

    Route::prefix('bundles')->namespace('Ecommerce')->controller('BundleController')->group(function(){
        Route::get('get' , 'get');
        Route::post('details' , 'bundleDetails');
    });


    Route::prefix('coupons')->namespace('Ecommerce')->controller('CouponController')->group(function(){
        Route::post('validate' , 'validateCoupon');
    });


    Route::prefix('points')->namespace('Ecommerce')->controller('PointController')->group(function(){
        Route::post('calculate' , 'calculate');
    });








    


      




});
