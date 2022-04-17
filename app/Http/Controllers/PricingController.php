<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OrderRatings;

class PricingController extends Controller
{
    public function PricingTab(Request $request)
    {
        $orders = OrderRatings::paginate(10);

        return view('front.price-list',compact('orders'));


        // $prices->type = $request->input('type');
        // $prices->weight_start = $request->input('weight_start');
        // $prices->weight_end = $request->input('weight_end');
        // $prices->charge_for_weight = $request->input('charge_for_weight');
        // $prices->charge_for_country = $request->input('charge_for_country');
        // $prices->ef_2 = $request->input('ef_1');
        // $prices->total = $request->input('total');
        // $prices->save();

        // return back()->with('status', 'Order Created Successfully!');
    }

    public function Pricing()
    {
        return view('front.add-pricing');
    }

    public function AddPricing(Request $request)
    {
        $prices = new OrderRatings;
        $prices->type = $request->input('type');
        $prices->charge_type  = $request->input('charge_type');
        $prices->weight_start = $request->input('weight_start');
        $prices->weight_end = $request->input('weight_end');
        $prices->charge_for_weight = $request->input('charge_for_weight');
        $prices->charge_for_country = $request->input('charge_for_country');
        $prices->country_name = $request->input('country_name');
        $prices->status = $request->input('status');
        $prices->total = $request->input('total');
        $prices->save();

        return back()->with('status', 'Price Created Successfully!');
    }

    public function EditPricing($id)
    {

        $orders = OrderRatings::find($id);
        // dd($orders);
        return view('front.edit-pricing',compact('orders'));

    }
    public function EditPricingForReal(Request $request, $id)
    {
        $prices = OrderRatings::find($id); ;
        $prices->type = $request->input('type');
        $prices->weight_start = $request->input('weight_start');
        $prices->weight_end = $request->input('weight_end');
        $prices->charge_for_weight = $request->input('charge_for_weight');
        $prices->charge_for_country = $request->input('charge_for_country');
        $prices->country_name = $request->input('country_name');
        $prices->status = $request->input('status');
        $prices->total = $request->input('total');
        $prices->save();

        return back()->with('status', 'Price Edited Successfully!');
    }
    public function DeletePricing($id)
    {
        OrderRatings::where('id', $id)->delete();
        return back();
    }

    public function SendPricing(Request $request)
    {
        $w = $request->weight;
        $fixed_data = OrderRatings::where('country_name', '=', $request->country)->where('charge_type','fixed')->where('type','Goods')->first();
        $fixed_weight_limit = $fixed_data->weight_end;
      //  $myfile = fopen("test.txt", "a+") or die("Unable to open file!");
        if($fixed_weight_limit>=$w)
        {
            $price = $fixed_data->total;
            return response($price);
        }
        else
        {
            $variable_data = OrderRatings::where('country_name', '=', $request->country)->where('charge_type','variable')->where('type','Goods')->orderBy('weight_end','ASC')->get();
            $variable_weight = $w - $fixed_weight_limit;//95
            $price = $fixed_data->total;

            foreach($variable_data as $data)
            {

                $remaining_weight = $w - $data->weight_end;//105-100




                if($remaining_weight<0)
                {
                    $price +=$variable_weight*$data->total;

                    return response($price);
                }
                else
                {
                    $deduct_weight = $data->weight_end -$data->weight_start+1;//900
                    $price +=$deduct_weight*$data->total;
                    $variable_weight -=$deduct_weight;


                }



            }
            if($variable_weight>0)
            {
                $size = sizeof($variable_data);
                $price += $variable_data[$size-1]->total*$variable_weight;
                return $price;

            }

            //file_put_contents('test.txt',json_encode($variable_data));

        }
        $data = OrderRatings::where('country_name', '=', $request->country)->where('weight_start', '<=', $request->weight)->where('weight_end', '>=', $request->weight)->get('total');
        // $data = $data[0]->total;
        // $data = OrderRatings::where('country_name', '=', $request->country)->where('type', '=', $request->type)->get('total');

        if(count($data) < 1){
            $data = "No data";
        }else{
            $data = $data[0]->total;
        }
        return response($data);
    }

    public function SendPricingForDocs(Request $request)
    {
        $data = OrderRatings::where('country_name', '=', $request->country)->where('type', '=', $request->type)->where('type','Documents')->get('total');

        if(count($data) < 1){
            $data = "No data";
        }else{
            $data = $data[0]->total;
        }

        return response($data);
    }

    public function GetCountryByType(Request $request)
    {
        $data = OrderRatings::where('type', '=', $request->type)->distinct()->get('country_name');

        // if(count($data) < 1){
        //     $data = "No data";
        // }else{
        //     $data = $data[0]->total;
        // }

        return response($data);
    }
}
