<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Phone;
use App\Models\Slider;

class AuthController extends Controller
{
    //
    public function index()
    {

        if (auth()->check()) {
            $data = Phone::where('status', 'available')->get();
            $slider = Slider::latest()->get();
            return view('front.index', compact('data', 'slider'));
        } else {
            return view('frontend.index');
        }
    }
}
