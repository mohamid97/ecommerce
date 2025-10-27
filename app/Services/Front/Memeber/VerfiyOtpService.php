<?php 
namespace App\Services\Front\Memeber;

use App\Models\Api\Front\Otp;

class VerfiyOtpService{

    public function verifyOtp( $email, $otp): bool
    {
        $record = Otp::where('email', $email)
                          ->where('code', $otp)
                          ->where('expires_at', '>', now())
                          ->first();

        if ($record) {
          
        $record->update(['verfied' => 'YES']); 
            return true;
        }

        return false;
    }

    
    
}