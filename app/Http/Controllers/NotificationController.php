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

    public function index(Request $request)
    {

        $notifications = '';
        $user_id = auth()->user()->id;
        $user = user::find($user_id);
        $user->unreadNotifications->markAsRead();
        $data = [];
        if (auth()->user()->parent->role == 'sub') {
            foreach ($user->notifications()->paginate(10) as $notification) {
                $notification_data = json_decode(json_encode($notification->data));
                array_push($data, ['service' => $notification_data->service, 'message' => $notification_data->message, 'time' => $notification->created_at . '(' . $notification->created_at->diffForHumans(Carbon::now()) . ')', 'read_status' => $notification->read_at]);
            }
        } else if (auth()->user()->role == 'sub') {
            if ($request->has('service')) {
                $start_date = Carbon::parse($request->start_date)->toDateTimeString();
                $end_date = Carbon::parse($request->end_date)->addDays(1)->toDateTimeString();
                $retailer_id = $request->retailer;
                $service = $request->service;
                if ($retailer_id == 'all' && $service == 'all') {
                    $notifications = DatabaseNotification::whereBetween('created_at', [$start_date, $end_date])->where(function ($query) {
                        $query->whereJsonContains('data', ['send_from' => auth()->user()->id])
                            ->orWhere('notifiable_id', auth()->user()->id);

                    })->latest()->paginate(10);
                } else if ($retailer_id == 'all' && $service != 'all') {
                    $notifications = DatabaseNotification::where('data->service', $service)->whereBetween('created_at', [$start_date, $end_date])->where(function ($query) {
                        $query->whereJsonContains('data', ['send_from' => auth()->user()->id])
                            ->orWhere('notifiable_id', auth()->user()->id);

                    })->latest()->paginate(10);
                } else if ($retailer_id != 'all' && $service != 'all') {
                    $notifications = DatabaseNotification::where('notifiable_id', $retailer_id)->where('data->service', $service)->whereBetween('created_at', [$start_date, $end_date])->where(function ($query) {
                        $query->whereJsonContains('data', ['send_from' => auth()->user()->id])
                            ->orWhere('notifiable_id', auth()->user()->id);

                    })->latest()->paginate(10);
                }
                // file_put_contents('test.txt',$request->start_date.' '.$request->end_date);
            } else
                $notifications = DatabaseNotification::whereJsonContains('data', ['send_from' => $user_id])->orWhere(function ($query) {
                    $query->whereJsonContains('data', ['send_from' => auth()->user()->id])
                        ->orWhere('notifiable_id', auth()->user()->id);

                })->latest()->paginate(10);

            foreach ($notifications as $notification) {
                $notification_data = json_decode(json_encode($notification->data));
                array_push($data, ['service' => $notification_data->service, 'message' => $notification_data->message, 'time' => $notification->created_at . '(' . $notification->created_at->diffForHumans(Carbon::now()) . ')', 'read_status' => $notification->read_at]);
            }
        } else {
            if ($request->has('service')) {
                $start_date = Carbon::parse($request->start_date)->toDateTimeString();
                $end_date = Carbon::parse($request->end_date)->addDays(1)->toDateTimeString();
                $retailer_id = $request->retailer;
                $service = $request->service;
                if ($retailer_id == 'all' && $service == 'all') {
                    $notifications = DatabaseNotification::whereBetween('created_at', [$start_date, $end_date])->latest()->paginate(10);
                } else if ($retailer_id == 'all' && $service != 'all') {
                    $notifications = DatabaseNotification::where('data->service', $service)->whereBetween('created_at', [$start_date, $end_date])->latest()->paginate(10);
                } else if ($retailer_id != 'all' && $service != 'all') {
                    $notifications = DatabaseNotification::where('notifiable_id', $retailer_id)->where('data->service', $service)->whereBetween('created_at', [$start_date, $end_date])->latest()->paginate(10);
                }
                // file_put_contents('test.txt',$request->start_date.' '.$request->end_date);
            } else
                $notifications = DatabaseNotification::latest()->paginate(10);

            foreach ($notifications as $notification) {
                $notification_data = json_decode(json_encode($notification->data));
                array_push($data, ['service' => $notification_data->service, 'message' => $notification_data->message, 'time' => $notification->created_at . '(' . $notification->created_at->diffForHumans(Carbon::now()) . ')', 'read_status' => $notification->read_at]);
            }
        }

        $data = json_decode(json_encode($data));
        //file_put_contents('test.txt',json_encode($data));
        $resellers = user::where('role', '!=', 'admin')->latest()->get();
        return view('front.notification-list', compact('data', 'user', 'notifications', 'resellers'));
    }
    public function create_notification()
    {
        if (auth()->user()->role == 'admin')
            $resellers = User::get();
        else
            $resellers = User::where('created_by', auth()->user()->id)->get();
        //$user = User::find(23);
        //$user->unreadNotifications->markAsRead();
        return view('front.create-notification', compact('resellers'));
    }
    public function sendNotification(Request $request)
    {
        $resellers = $request->reseller;
        $reseller_id = [];
        for ($i = 0; $i < sizeof($resellers); $i++) {
            array_push($reseller_id, $resellers[$i]);
        }
        $users = User::whereIn('id', $reseller_id)->get();

        // $userSchema = User::get();
        //file_put_contents('test.txt',json_encode($users));
        $data = [
            'service' => $request->service,
            'message' => $request->message,
            'send_from' => auth()->user()->id

        ];
        //$userSchema->notify(new GeneralNotification($offerData));
        try {
            Notification::send($users, new GeneralNotification($data));
        } catch (Throwable $th) {
            Log::error("General Notification Error: " . $th);
        }
        try {
            //event(new GeneralNotificationEvent());
        } catch (\Throwable $th) {
            Log::error("General Event Error: " . $th);
        }
        return redirect()->route('GeneralNotification')->with('success', 'Notification Created Successfully');

        // dd('Task completed!');
    }
    public function general_notification_count()
    {
        if (auth()->user()->role == "admin") {
            $data = 0;
        } else {
            $data = auth()->user()->unreadNotifications()->count();
        }
        //$data = 5;
        return $data;
    }
}