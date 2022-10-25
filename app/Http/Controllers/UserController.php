<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\CreateProfileRequest;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Slider;
use Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('front.sign-up');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {

        $users = new User;
        $users->first_name = $request->input('first_name');
        $users->last_name = $request->input('last_name');
        $users->vat_number = $request->input('vat_number');
        $users->nationality = $request->input('nationality');
        $users->email = $request->input('email');
        $users->address = $request->input('address');
        if(Auth::user()->role == 'user')
        $users->role = 'reseller';
        else
        $user->role = 'use';
        
        $users->user_id ='JM-'.mt_rand(10000,99999);

        $users->payment_method = $request->input('payment_method');
        $users->company = $request->input('company_name');
        $users->contact_number = $request->input('phone');

        $users->codice_fiscale = $request->input('codice_fiscale');
        $users->gender = $request->input('gender');;
        $users->wallet = 0;
        $users->created_by = Auth::user()->id;
        $users->password = Hash::make($request['password']);
        $users->save();
        return ['status'=>true];
        //return redirect('/login')->with('status', 'Registered Successfully!');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = User::where('id',$id)->first();

        return view('front.edit_profile',compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProfileRequest $request, $id)
    {
        // $validatedData = $request->validate([
        //     'first_name' => 'required',

        // ], [
        //     'first_name.required' => 'Name is required',

        // ]);
        //file_put_contents('test.txt','hello');


        if($request->password != null){
            $password = Hash::make($request->password);

            $data = User::where('id',$id)->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'vat_number' => $request->vat_number,
                'gender' => $request->gender,
                'address' => $request->address,
                'contact_number' => $request->phone,
                'codice_fiscale' =>$request->codice_fiscale,
                'nationality' => $request->company,
                'password' => $password,


            ]);
        }else{
            $data = User::where('id',$id)->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'vat_number' => $request->vat_number,
                'gender' => $request->gender,
                'address' => $request->address,
                'contact_number' => $request->phone,
                'codice_fiscale' =>$request->codice_fiscale,
                'nationality' => $request->company,


            ]);
        }

       // return back();
        return redirect('/retailer/retailer-details-admin');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $id = $request->id;
        User::where('id', $id)->delete();

       // return back();
    }

    public function slider(Request $request)
    {
        $path = $request->image->store('slider/uploads', 'public');


        $Phones = new Slider;
        $Phones->link = $request->input('link');
        $Phones->image = $path;
        $Phones->save();

        return back()->with('status', 'Sldier Uploaded Successfully!');
    }
    public function check_email(Request $request)
    {
        $email = $request->email;
        $user = User::where('email',$email)->first();
        if($user)
        {
            return ['status'=>true];
        //file_put_contents('test.txt',$request->email." ".'exist');
        //return 'exist';
        }
        else
        {
            return ['status'=>false];
            //file_put_contents('test.txt',$request->email." ".'not exist');
            //return 'not_exist';
        }


    }

    public function updateslider(Request $request)
    {
        $data = Slider::where('id', $request->id)->first();
        if($request->image != null){
            $path = $request->image->store('slider/uploads', 'public');
        }else{
            $path = $data->image;
        }



        $Phones = Slider::where('id', $request->id)->update([
            'link' => $request->input('link'),
            'image' => $path
        ]);

        return back()->with('status', 'Sldier Updated Successfully!');
    }

    public function AddsliderView()
    {
        return view('front.add-slider');
    }

    public function sliderView()
    {
        $data = Slider::latest()->get();
        return view('front.sliders',compact('data'));
    }

    public function slideredit($id)
    {
        $data = Slider::where('id',$id)->first();
        return view('front.edit-slider',compact('data'));
    }

    public function sliderdelete($id)
    {
        $data = Slider::where('id',$id)->delete();
        return back()->with('status','Slider Deleted Successfully!');
    }
}
