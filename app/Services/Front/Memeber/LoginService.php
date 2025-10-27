<?php 
namespace App\Services\Front\Memeber;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginService{
    
    public function login($data){
      
       $user = User::where('email', $data['email'])->first();
       if (!$user || !Hash::check($data['password'], $user->password)){
         return false;
       } 

       $user->token = $user->createToken('API Token')->plainTextToken;
       return $user;

        
    }
}