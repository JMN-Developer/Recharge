<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class user
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

            if(Auth::user()->role != 'admin2'){
                return $next($request);
            }
            else{
                return redirect()->route('/')->with('error',"You do not have this feature access");
            }

        }
        else
        {
            return redirect()->route('/');
        }
    }
}
