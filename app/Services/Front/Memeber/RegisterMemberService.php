<?php
namespace App\Services\Front\Memeber;

use App\Models\Api\Front\Otp;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RegisterMemberService{
    
    public function register($data){
        if(!$this->checkIfVerfied($data['email'])){
            return false;
        }
        $username = $this->generateUniqueUsername($data['first_name'], $data['last_name']);
        $memeber = User::create([
            'first_name'=>$data['first_name'],
            'last_name'=>$data['last_name'],
            'phone'=>$data['phone'],
            'email'=>$data['email'],
            'password'=>Hash::make($data['password']),
            'username'=>$username,
            'type'=>'user'
        ]);
        $this->deleteOtp($data['email']);
        return $memeber;

    } // end register


    protected function deleteOtp($email){
        Otp::where('email' , $email)->delete();
    }


    protected function checkIfVerfied($email){
        if(Otp::where('email' , $email)->where('verfied' , 'YES')->exists()){
            return true;
        }

        return false;
    }

    protected function generateUniqueUsername($firstName, $lastName)
    {
        // Remove special characters and make lowercase
        $cleanFirstName = preg_replace('/[^a-zA-Z0-9]/', '', strtolower($firstName));
        $cleanLastName = preg_replace('/[^a-zA-Z0-9]/', '', strtolower($lastName));
        
        // Initial username attempt
        $baseUsername = $cleanFirstName . '.' . $cleanLastName;
        $username = $baseUsername;
        $counter = 1;
        
        // Check if username exists and increment counter if needed
        while (User::where('username', $username)->exists()) {
            $username = $baseUsername . $counter;
            $counter++;
        }
        
        return $username;
    }



}