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





    // contact and messages
    Route::post('send-message', [MessageController::class, 'sendMessage']);


    // dynamic endpoint to get profile data
    Route::prefix('data')->controller('FrontendController')->group(function(){
        Route::post('get' , 'get');  
        Route::post('dynamic/filter' , 'dynamicFilter');
        Route::post('gallery' , 'getGallery');     
    });
    
    Route::prefix('applicant')->controller('ApplicantController')->group(function(){
        Route::post('store' , 'store');
        
    });
    



      




});