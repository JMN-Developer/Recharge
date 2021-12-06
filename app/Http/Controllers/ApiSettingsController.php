<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\ApiList;

class ApiSettingsController extends Controller
{
    //
    public function get_data()
    {
        $datas = ApiList::get();
        return $datas;
    }
    public function ApiActivation()
    {
        $datas = ApiList::get();
        return view('front.api-activation',compact('datas'));
    }
    public function change_status(Request $request)
    {
        $status = $request->status;
       // file_put_contents('test.txt',$request->type." ".$request->id);
        if($status ==1)
        {
            $api_list =  ApiList::where('id',$request->id)->first();
            if($api_list->type == 'International')
            {
            ApiList::where('id',$request->id)->where('type','International')->update(['status'=>1]);
            ApiList::where('id','!=',$request->id)->where('type','International')->update(['status'=>0]);
            }
            else
            {
                ApiList::where('id',$request->id)->where('type','Domestic')->update(['status'=>1]);
                ApiList::where('id','!=',$request->id)->where('type','Domestic')->update(['status'=>0]);
            }
        }

        return true;


       // file_put_contents('test.txt',$request->type." ".$request->id." ".$request->status);
    }
}
