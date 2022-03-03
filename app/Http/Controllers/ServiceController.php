<?php

namespace App\Http\Controllers;

use App\Models\service_control;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    //
    public function index()
    {
        $datas = service_control::get();
        return view('front.service-control',compact('datas'));
    }
    public function status_update(Request $request)
    {
        $user = service_control::find($request->user_id);
        $user->permission = $request->status;
        $user->save();

        return response()->json(['success'=>'Status change successfully.']);
    }
}
