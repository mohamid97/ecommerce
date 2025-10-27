<?php
namespace App\Services\Front\Memeber;

use App\Models\User;

class CheckEmailExist{

    public function checkEmailExist($email){
        if(User::where('email' , $email)->exists()){
            return true;
        }
        return false;   
    }

    
    
}