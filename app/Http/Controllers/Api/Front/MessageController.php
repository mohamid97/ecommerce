<?php

namespace App\Http\Controllers\Api\Front;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Front\messages\MessageRequest;
use App\Http\Resources\Api\Front\Message\MessageResource;
use App\Mail\Admin\ContactMail;
use App\Models\Api\Front\Message;
use App\Traits\ResponseTrait;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;



class MessageController extends Controller
{
    use ResponseTrait;
    
    public function sendMessage(MessageRequest $request){

        try{
            DB::beginTransaction();
            if(!$this->validateRecaptcha($request->recaptcha_token)){
                return $this->error( __('invalid_recaptcha') , 422);
            }
            $message = Message::create($request->all());
            Mail::to(config('mail.from.address'))->send(new ContactMail($message));
            DB::commit();
            return $this->success(new MessageResource($message), __('stored_successfully' , ['model'=> 'Message']) );
        }catch(\Exception $e){
            DB::rollBack();
            $this->error($e->getMessage() , 500);

        }


    }

    private function validateRecaptcha($token):bool
    {
        $secretKey = config('services.recaptcha.secret');
        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => $secretKey,
            'response' => $token,
        ]);

        $recaptchaData = $response->json();
        if (!($recaptchaData['success'] ?? false)) {
            return true;
        }
        return false;
    }

    


}