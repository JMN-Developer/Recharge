<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ApiList;
class InternationalApi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {

        $internationa_api = ApiList::where('type','international')->where('status',1)->first();
        //file_put_contents('test.txt',$internationa_api->company_name);
        if($internationa_api->company_name == 'Reloadly')
        {
           return redirect()->route('recharge-reloadly');
        }
        if($internationa_api->company_name == 'Ding Connect')
        {
            return redirect()->route('recharge-int');
        }

        if($internationa_api->company_name == 'Prepay')
        {
            return redirect()->route('recharge-ppn');
        }
        return $next($request);
    }
}
