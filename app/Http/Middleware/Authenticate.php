<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        //file_put_contents('test.txt','tested');
        return route('login');
        // if (! $request->expectsJson()) {
        //     file_put_contents('test.txt','tested');
        //    return route('/');
        // }
    }
}
