<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\User;
use Auth;
use App\Services\UpdateWallet;
use DB;


class OrderController extends Controller
{
    public function AddOrder(Request $request)
    {
        $ordeaars = Order::all();
        $ooyeh = count($ordeaars);
       // $randomNumber = $ooyeh.random_int(1000, 9999).$ooyeh;


        if(!empty($request->label)){
            $request->file('label')->store('public');
            $labelFileName = $request->label->hashName();
        }


        $orders = new Order;
        $orders->reseller_id = $request->input('reseller_id');
        $orders->first_name = $request->input('first_name');
        $orders->rfirst_name = $request->input('rfirst_name');
        $orders->surname = $request->input('surname');
        $orders->rsurname = $request->input('rsurname');
        $orders->dob = $request->input('dob');
        $orders->rdob = $request->input('rdob');
        $orders->document_number = $request->input('document_number');
        $orders->rdocument_number = $request->input('rdocument_number');
        $orders->phone = $request->input('phone');
        $orders->rphone = $request->input('rphone');
        $orders->email = $request->input('email');
        $orders->remail = $request->input('remail');
        $orders->address = $request->input('address');
        $orders->raddress = $request->input('raddress');
        $orders->delivery_condition = $request->input('delivery_condition');
        $orders->order_id = transaction_cargo($orders->delivery_condition);
        $orders->country = $request->input('country');
        $orders->rcountry = $request->input('rcountry');
        $orders->order_description = $request->input('description');
        $orders->addiCharge = $request->input('addiCharge');
        $orders->total = $request->input('total');

        $main_price = $orders->total-$orders->addiCharge;
       // file_put_contents('test.txt',$orders->total." ".$orders->addiCharge." ".round(((Auth::user()->cargo_documents_profit/100)*$main_price),2));
        // $orders->state = $request->input('state');
        // $orders->rstate = $request->input('rstate');
        // $orders->dist = $request->input('dist');
        // $orders->rdist = $request->input('rdist');
        // $orders->city = $request->input('city');
        // $orders->rcity = $request->input('rcity');
        // $orders->expected_date_to_receive = $request->input('expected_date_to_receive');

        // $orders->numberOfBox = $request->input('numberOfBox');
        // $orders->goods_value = $request->input('goods_value');
        // $orders->productType = $request->input('productType');
        $orders->weight = $request->input('weight');
        // $orders->perKg = $request->input('perKg');
        // $orders->cusCharge = $request->input('cusCharge');
        // $orders->homeDeliveryCharge = $request->input('homeDeliveryCharge');

        if($orders->delivery_condition == 'Goods')
        {

            $orders->agent_comm = round(((Auth::user()->cargo_goods_profit/100)*$main_price),2);
         }
         else
         {

            $orders->agent_comm = round(((Auth::user()->cargo_documents_profit/100)*$main_price),2);
         }

        // $orders->delivery_way = $request->input('delivery_way');
        // $orders->departure_airport = $request->input('departure_airport');
        // $orders->arrival_airport = $request->input('arrival_airport');
        $orders->product1 = $request->input('product1');
        $orders->quantity1 = $request->input('qty1');
        if(!empty($labelFileName)){
            $orders->label = $labelFileName;
        }
        $orders->status = 'available';
        $orders->save();

        $user = User::where('id', $orders->reseller_id)->first();

        $total = $request->total;

        // $reseller_comission = ($total/100)*$user->cargo;

        // $admin_comission = ($total/100)*$user->admin_cargo_commission;


        if($orders->delivery_condition == 'Goods')
        {
            $this->update_cargo_wallet($main_price,Auth::user()->cargo_goods_profit,$orders->id);
         }
         else
         {
            $this->update_cargo_wallet($main_price,Auth::user()->cargo_documents_profit,$orders->id);
         }




        // $up = User::where('id',$user->id)->update([
        //     'cargo_due' => $total - $reseller_comission -  $admin_comission
        // ]);

        return back()->with('status', 'Order Created Successfully!');

    }


    public function update_cargo_wallet($cargo_price,$percentage,$id)
    {
        // $user = User::where('id',$reseller_id)->first();
        $cargo_price = $cargo_price -  round((($percentage/100)*$cargo_price),2);
        $wallet_before_transaction = auth()->user()->cargo_wallet;
       // User::where('id',Auth::user()->id)->update(['cargo_wallet'=>Auth::user()->cargo_wallet+$cargo_price]);
        $user = tap(DB::table('users')->where('id', Auth::user()->id)) ->update(['cargo_wallet'=>Auth::user()->cargo_wallet+$cargo_price])->first();
        $wallet_after_transaction =$user->cargo_wallet;
        UpdateWallet::create_transaction($id,'debit','Cargo',$wallet_before_transaction,$wallet_after_transaction,$cargo_price,'wallet',Auth::user()->id);
    }

    public function update_status(Request $request)
    {


        if($request->status =='confirmed')
        {
            $update = Order::where('id', $request->id)->update([
                'status' => $request->status,
                'cost'=>$request->cost
            ]);
        }
        else
        {
        $update = Order::where('id', $request->id)->update([
            'status' => $request->status
        ]);
        }

        return back()->with('status', 'Status Updated Successfully!');
    }


}
