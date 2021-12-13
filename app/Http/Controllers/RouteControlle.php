<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ApiList;

class RouteControlle extends Controller
{
    //
    public function index()
    {
        $internationa_api = ApiList::where('type','international')->where('status',1)->first();
        if($internationa_api->company_name == 'Reloadly')
        {
            $controller = app()->make('SuperAdminController');
        }
    }
}
