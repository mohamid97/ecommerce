<?php

namespace App\Http\Controllers\Api\Front;

use App\Events\Front\SendEmailVerfication;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Front\Member\EmailRequest;
use App\Http\Requests\Api\Front\Member\LoginMemeberRequest;
use App\Http\Requests\Api\Front\Member\RegisterMemberRequest;
use App\Http\Requests\Api\Front\Member\VerfiyOtpRequest;
use App\Http\Resources\Api\Front\Memeber\LoginMemeberResource;
use App\Services\Front\Memeber\CheckEmailExist;
use App\Services\Front\Memeber\LoginService;
use App\Services\Front\Memeber\RegisterMemberService;
use App\Services\Front\Memeber\VerfiyOtpService;
use App\Traits\ResponseTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MemberController extends Controller
{
    use ResponseTrait;
    
    public function sendVerification(EmailRequest $request , CheckEmailExist $checkEmail)
    {
        try{      
            DB::beginTransaction();
            if($checkEmail->checkEmailExist($request->email)){            
                return $this->error( __('main.cant_verfiy_email') , 404);
            }
            event(new SendEmailVerfication($request->email));
            DB::commit();
            return $this->success(['email'=>$request->email] , __('main.otp_sent'));

        }catch(\Exception $e){
            DB::rollBack();
            return $this->error($e->getMessage() , 500);

        }

        

    } // end send verfication code



    public function verfiyOtp(VerfiyOtpRequest $request , VerfiyOtpService $verfiyOtp){
       
        try{
            DB::beginTransaction();
            if($verfiyOtp->verifyOtp($request->email , $request->otp)){
               DB::commit(); 
               return $this->success(['email'=>$request->email] , __('main.email_verfied'));
            }
            DB::rollBack();
            return $this->error(__('main.invalid_expiry_email') , 401);         
        }catch(\Exception $e){
            DB::rollBack();
            return $this->error($e->getMessage() , 500);

        }

    } //end verfiy otp


    public function register(RegisterMemberRequest $request , RegisterMemberService $register){
        try{
         DB::beginTransaction();
         if( $register->register($request->only('first_name' , 'last_name' , 'email' , 'password' , 'phone'))){
            DB::commit();
            return $this->success(['email'=>$request->email] , __('main.member_register'));
         }

         DB::rollBack();
         return $this->error( __('main.error_happend') ,  500);         
 
        }catch(\Exception $e){
            DB::rollBack();
            return $this->error($e->getMessage() , 500);

        }
        
    }


    public function login(LoginMemeberRequest $request , LoginService $login){
        try{
            if($user = $login->login($request->only('email' , 'password'))){
            
                return $this->success( new LoginMemeberResource($user) , __('main.memeber_data'));
            }
           return $this->error( __('main.error_happend') ,  500); 

        }catch(\Exception $e){
            DB::rollBack();
            return $this->error($e->getMessage() , 500);

        }
    }

        
        




    



    
}