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

       // Invalidate all previous tokens on each login.
       $user->tokens()->delete();

       $user->token = $user->createToken(
           'API Token',
           expiresAt: now()->addMinutes((int) config('sanctum_expiration.customer_minutes', 43200))
       )->plainTextToken;
       return $user;

        
    }
}
