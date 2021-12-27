<?php

namespace App\Services;
use App\Models\User;
use App\Models\Balance;
use App\Notifications\BalanceAlertNotification;
use Illuminate\Support\Facades\Notification;

/**
 * Class CheckRechargeAvail
 * @package App\Services
 */
class CheckRechargeAvail
{

    public function send_alert_email()
    {
        $balance = Balance::get();

        $alert_message ='';

        foreach($balance as $data)
        {
            if($data->balance == 50 || $data->balance == 40 || $data->balance == 30 || $data->balance == 20 || $data->balance == 10 || $data->balance == 5 || $data->balance == 1)
            {
                $alert_message.=$data->type." = ".$data->balance.PHP_EOL;
            }
        }



        $data = [
            'from'=>'pointrecharge@gmail.com',
            'message'=>$alert_message
        ];
        if(strlen($alert_message)>0){
        try {
            Notification::route('mail','dev.jmnation@gmail.com')
                ->notify(new BalanceAlertNotification($data));
           // Notification::send('kazinokib7@gmail.com', new PinSentToEmail($PinData));
            //code...
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    }


    public static function check($requested_amount)
    {
        $instance = new CheckRechargeAvail();
        if(auth()->user()->role == 'admin')
        {
            return true;
        }

        $user_info = User::where('id',auth()->user()->id)->first();
        $current_wallet = $user_info->wallet;
        $limit = $user_info->due;
        $limit_usage = $user_info->limit_usage;
        $due_limit = $limit-$limit_usage;

        if($requested_amount>$current_wallet)
        {
            if($requested_amount>$due_limit)
            {
                return false;
            }
            else
            {
                $instance->send_alert_email();
                return true;
            }

        }
        $instance->send_alert_email();
       return true;



    }
}
