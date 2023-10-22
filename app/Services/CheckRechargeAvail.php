<?php

namespace App\Services;

use App\Models\Balance;
use App\Models\User;
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

        $alert_message = '';

        foreach ($balance as $data) {
            if ($data->balance == 50 || $data->balance == 40 || $data->balance == 30 || $data->balance == 20 || $data->balance == 10 || $data->balance == 5 || $data->balance == 1) {
                $alert_message .= $data->type . " = " . $data->balance . PHP_EOL;
            }
        }

        $data = [
            'from' => 'pointrecharge@gmail.com',
            'message' => $alert_message,
        ];
        if (strlen($alert_message) > 0) {
            try {
                Notification::route('mail', 'dev.jmnation@gmail.com')
                    ->notify(new BalanceAlertNotification($data));
                // Notification::send('kazinokib7@gmail.com', new PinSentToEmail($PinData));
                //code...
            } catch (\Throwable$th) {
                //throw $th;
            }
        }

    }

    public static function check($requested_amount, $type)
    {
        $instance = new CheckRechargeAvail();
        if (auth()->user()->role == 'admin') {
            return true;
        }

        $user_info = User::where('id', auth()->user()->id)->first();
        if ($type == 'International' || $type == 'Bangladesh' || $type == 'White Calling') {
            $current_wallet = $user_info->wallet;
            $limit = $user_info->due;
            $limit_usage = $user_info->limit_usage;
            if (auth()->user()->parent->role == 'sub') {
                $parent_current_wallet = auth()->user()->parent->wallet;
                $parent_limit = auth()->user()->parent->due;
                $parent_limit_usage = auth()->user()->parent->limit_usage;
            }

        } else {
            $current_wallet = $user_info->domestic_wallet; // 0
            $limit = $user_info->domestic_due; //200.00
            $limit_usage = $user_info->domestic_limit_usage; //4.17
            if (auth()->user()->parent->role == 'sub') {
                $parent_current_wallet = auth()->user()->parent->domestic_wallet; // 0
                $parent_limit = auth()->user()->parent->domestic_due; // 1000
                $parent_limit_usage = auth()->user()->parent->domestic_limit_usage; //990.68
            }

        }
        $due_limit = $limit - $limit_usage; // 200-4.17 =
        if (auth()->user()->parent->role == 'sub') {
            $parent_due_limit = $parent_limit - $parent_limit_usage; //1000- 990.68 = 9.32

        }

        // if ($requested_amount > $current_wallet) {
        //     if ($requested_amount > $due_limit + $current_wallet) {
        //         Log::channel('sumonLog')->info('First False ' . $requested_amount . ' ' . $type);
        //         return false;
        //     } else {
        //         if (auth()->user()->parent->role == 'sub') {
        //             if ($requested_amount > $parent_due_limit + $parent_current_wallet) {
        //                 Log::channel('sumonLog')->info('Second False ' . $requested_amount . ' ' . $type);
        //                 return false;
        //             }
        //         }
        //         Log::channel('sumonLog')->info('First true ' . $requested_amount . ' ' . $type);
        //         return true;
        //     }
        // }
        // if (auth()->user()->parent->role == 'sub') {
        //     if ($requested_amount > $parent_due_limit + $parent_current_wallet) {
        //         Log::channel('sumonLog')->info('3rd False ' . $requested_amount . ' ' . $type);
        //         return false;
        //     }
        // }
        // Log::channel('sumonLog')->info('Second true ' . $requested_amount . ' ' . $type);
        // return true;

        if ($requested_amount > $due_limit + $current_wallet) {
            return false;
        }

        if (auth()->user()->parent->role == 'sub' && $requested_amount > $parent_due_limit + $parent_current_wallet) {
            return false;
        }

        return true;
    }
}
