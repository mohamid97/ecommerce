<?php

namespace App\Listeners\Front;

use App\Events\Front\SendEmailVerfication;
use App\Mail\Front\SendOtpMail;
use App\Services\Front\Memeber\GenerateOtp;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
class EmailVerfication
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
      
    }

    /**
     * Handle the event.
     */
    public function handle(SendEmailVerfication $event): void
    {
        $otp = app(GenerateOtp::class)->generateOtp($event->email);
        Mail::to($event->email)->send(new SendOtpMail($otp));
        
    }
}