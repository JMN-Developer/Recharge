<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Phone;
use App\Models\Slider;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    //
    public function index()
    {
        if(auth()->check())
        {
            if(auth()->user()->role == 'admin')
            {
            $data = Phone::where('status', 'available')->get();
            $slider = Slider::latest()->get();
            return view('front.index',compact('data','slider'));
            }
            else
            dd('System Under Maintenance');

        }
        else
        {
            return view('frontend.index');
        }
    }
}