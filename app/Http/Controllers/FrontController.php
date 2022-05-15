<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
        
        //file_put_contents('test.txt',$request->name.' '.$request->email.' '.$request->subject.' '.$request->message);
    }
}
