<?php
namespace App\Services\Front\Memeber;

use App\Models\Api\Front\Otp;

class GenerateOtp{

    
    
    public function generateOtp($email){
        $otp = rand(100000, 999999);

        Otp::updateOrCreate(
            ['email' => $email],
            [
                'code' => $otp,
                'expires_at' => now()->addMinutes(5),
            ]
        );

        return $otp;
    }

    

    
}