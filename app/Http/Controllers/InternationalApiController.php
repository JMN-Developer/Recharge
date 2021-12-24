<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ApiList;



class InternationalApiController extends Controller
{
    //
    public function index()
    {

        $internationa_api = ApiList::where('type','international')->where('status',1)->first();
        $internationa_api->company_name = 'DTONE';
        if($internationa_api->company_name == 'Reloadly')
        {
            $method = new ReloadlyController();
            return $method->index();
        }

        if($internationa_api->company_name == 'Prepay')
        {
            $method = new PpnController();
            return $method->index();
        }
        if($internationa_api->company_name == 'Ding Connect')
        {
            $method = new RechargeController();
            return $method->RechargeInt();
        }

        if($internationa_api->company_name == 'DTONE')
        {
            $method = new DtOneController();
            return $method->index();
        }
    }
}
