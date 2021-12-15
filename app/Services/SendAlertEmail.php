<?php

namespace App\Services;
use App\Models\Balance;
use App\Notifications\BalanceAlertNotification;
use Illuminate\Support\Facades\Notification;

/**
 * Class SendAlertEmail
 * @package App\Services
 */
class SendAlertEmail
{
    public static function send_balance_alert_email()
    {
        $PinData = [
            'from'=>'pointrecharge@gmail.com',
            'pin'=>$request->pin_number
        ];
        try {
            Notification::route('mail',$request->email)
                ->notify(new PinSentToEmail($PinData));
           // Notification::send('kazinokib7@gmail.com', new PinSentToEmail($PinData));
            //code...
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

}
