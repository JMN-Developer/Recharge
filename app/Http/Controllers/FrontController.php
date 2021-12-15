<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FrontController extends Controller
{
    //
    public function index()
    {
        file_put_contents('test.txt','hello');
        return view('frontend.index');
    }
}
