<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\FrontPageEmailNotification;

class FrontController extends Controller
{
    //
    public function index()
    {
        //file_put_contents('test.txt','hello');
        return view('frontend.index');
    }
    public function send_frontpage_email(Request $request)
    {
       // file_put_contents('test.txt','hello');
        $data = [
            'from'=>'pointrecharge@gmail.com',
            'name'=>$request->name,
            'subject'=>$request->subject,
            'email'=>$request->email,
            'message'=>$request->message
        ];
        try {
            Notification::route('mail','admin@jmnation.com')
                ->notify(new FrontPageEmailNotification($data));
           // Notification::send('kazinokib7@gmail.com', new PinSentToEmail($PinData));
            //code...
        } catch (\Throwable $th) {
            //throw $th;
            log::info($th);
        }
        //file_put_contents('test.txt',$request->name.' '.$request->email.' '.$request->subject.' '.$request->message);
    }
}
