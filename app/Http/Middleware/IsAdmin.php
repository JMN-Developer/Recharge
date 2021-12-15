<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsAdmin
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
        if (Auth::check()) {

            if(Auth::user()->role == 'admin'){
                return $next($request);
            }
            else{
                return redirect("login")->with('error',"You do not have admin access");
            }

        }
        else
        {
            return redirect("admin_login");
        }
    }
}
