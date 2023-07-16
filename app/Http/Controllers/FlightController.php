<?php

namespace App\Http\Controllers;

class FlightController extends Controller
{
    //
    public function add_flight()
    {
        return view('front.add-flight');
        // file_put_contents('test.txt','hel');
    }
}
