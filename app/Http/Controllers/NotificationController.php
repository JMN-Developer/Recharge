<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\user;
//use Notification;
use App\Notifications\GeneralNotification;
use Illuminate\Support\Facades\Notification;
use App\Events\GeneralNotificationEvent;
use Illuminate\Support\Carbon;
use Illuminate\Notifications\DatabaseNotification;
use DB;
use Log;

class NotificationController extends Controller
{
    //

    public function index()
    {
        $notifications ='';
        $user_id = auth()->user()->id;
        $user = user::find($user_id);
        $user->unreadNotifications->markAsRead();
        $data = [];
        if(auth()->user()->role == 'user')
        {
        foreach ($user->notifications()->paginate(5) as $notification) {
            $notification_data = json_decode(json_encode($notification->data));
            array_push($data,['service'=>$notification_data->service,'message'=>$notification_data->message,'time'=>$notification->created_at.'('.$notification->created_at->diffForHumans(Carbon::now()).')','read_status'=>$notification->read_at]);
        }
          }
          else
          {
            $notifications = DatabaseNotification::latest()->paginate(5);
            foreach ($notifications as $notification) {
                $notification_data = json_decode(json_encode($notification->data));
                array_push($data,['service'=>$notification_data->service,'message'=>$notification_data->message,'time'=>$notification->created_at.'('.$notification->created_at->diffForHumans(Carbon::now()).')','read_status'=>$notification->read_at]);
            }
          }

        $data = json_decode(json_encode($data));
        return view('front.notification-list',compact('data','user','notifications'));
    }
    public function create_notification()
    {
        $resellers = User::get();
        //$user = User::find(23);
        //$user->unreadNotifications->markAsRead();
        return view('front.create-notification',compact('resellers'));
    }
    public function sendNotification(Request $request) {
        $resellers = $request->reseller;
        $reseller_id = [];
        for($i=0;$i<sizeof($resellers);$i++)
        {
            array_push($reseller_id,$resellers[$i]);
        }
        $users = User::whereIn('id',$reseller_id)->get();

        // $userSchema = User::get();
        //file_put_contents('test.txt',json_encode($users));
        $data = [
            'service' => $request->service,
            'message'=>$request->message

        ];
        // //$userSchema->notify(new GeneralNotification($offerData));
        try{
            Notification::send($users, new GeneralNotification($data));
        }
        catch(Throwable $th){
            Log::error("General Notification Error: ".$th);
        }
        try{
            event(new GeneralNotificationEvent());
        }
        catch(\Throwable $th){
            Log::error("General Event Error: ".$th);
        }
        return redirect()->route('GeneralNotification')->with('success','Ticket Submitted Successfully');

// dd('Task completed!');
    }
    public function general_notification_count()
    {
        if (auth()->user()->role == "admin") {
            $data = 0;
        } else {
            $data = auth()->user()->unreadNotifications()->count() ;
        }
            //$data = 5;
        return $data;
    }
}
