<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Phone;
use App\Models\User;
use App\Services\UpdateWallet;
use DB;
use Illuminate\Http\Request;

class BalanceController extends Controller
{
    //
    public function AddBalance(Request $request)
    {
        //  FETCH SENDER INFO
        $sender_balance = User::select('wallet')->where('id', auth()->user()->id)->get();
        $sender_balance = $sender_balance[0]->wallet;
        // dd($sender_balance);

        //  FETCH RECEIVER INFO
        $user_balance = User::select('wallet')->where('id', $request->user_id)->get('wallet');
        $user_balance = $user_balance[0]->wallet;
        $user_due = User::select('due')->where('id', $request->user_id)->get('due');
        $user_due = $user_due[0]->due;

        if ($request->balance) {
            //  FOR ADMIN
            if (auth()->user()->role == "admin") {
                {
                    $users = User::find($request->user_id);
                    $users->wallet = $user_balance + $request->balance;
                    $users->save();
                    return redirect()->back();
                }
            }

            //  FOR USER
            if (auth()->user()->role == "user") {
                if ($sender_balance > $request->balance) {

                    $users = User::find($request->user_id);
                    $users->wallet = $user_balance + $request->balance;
                    $users->save();

                    $senders = User::find(auth()->user()->id);
                    $senders->wallet = $sender_balance - $request->balance;
                    $senders->save();
                    return redirect()->back();

                }
            }
        }

        if ($request->due) {
            //  FOR ADMIN
            if (auth()->user()->role == "admin") {
                {
                    $users = User::find($request->user_id);
                    $users->wallet = $user_balance + $request->due;
                    $users->due = $user_due + $request->due;
                    $users->save();
                    return redirect()->back();
                }
            }

            //  FOR USER
            if (auth()->user()->role == "user") {
                if ($sender_balance > $request->balance) {

                    $users = User::find($request->user_id);
                    $users->wallet = $user_balance + $request->balance;
                    $users->due = $request->due;
                    $users->save();

                    $senders = User::find(auth()->user()->id);
                    $senders->wallet = $sender_balance - $request->balance;
                    $senders->save();
                    return redirect()->back();

                }
            }
        }
        return redirect()->back()->with('message', 'Not enough balance!');
    }

    public function EditBalance(Request $request)
    {

        $info = User::where('id', $request->user_id)->first();

        if ($info->due >= $request->due) {
            $user = User::where('id', $request->user_id)->update([

                "due" => $info->due - $request->due,

            ]);

            return back();
        } else {
            return back()->with('error', 'Due Amount Is Less Than The Input');
        }
    }

    public function EditLimit(Request $request)
    {
        User::where('id', $request->user_id)->update(['due' => $request->due]);
        return back()->with('success', 'International Limit Updated');
    }

    public function EditLimitDomestic(Request $request)
    {
        User::where('id', $request->user_id)->update(['domestic_due' => $request->domestic_due]);
        return back()->with('success', 'Domestic Limit Updated');
    }

    public function EditDue(Request $request)
    {

        $info = User::where('id', $request->user_id)->first();
        $wallet_before_transaction = $info->cargo_wallet;

        if ($info->cargo_wallet >= $request->due) {
            // $user = User::where('id', $request->user_id)->update([

            //     "cargo_wallet" => $info->cargo_wallet - $request->due

            // ]);

            $user = tap(DB::table('users')->where('id', $request->user_id))->update(["cargo_wallet" => $info->cargo_wallet - $request->due])->first();
            $wallet_after_transaction = $user->sim_wallet;
            UpdateWallet::create_transaction(0, 'credit', 'Cargo', $wallet_before_transaction, $wallet_after_transaction, $request->due, 'main wallet', $request->user_id);
            return back()->with('success', 'Wallet Updated');
        } else {
            return back()->with('error', 'Due Amount Is Less Than The Input');
        }
    }

    // public function update_transaction($total_amount,$reseller_id,$transaction_type)
    // {

    //     TransactionHistory::create([
    //         'reseller_id'=>$reseller_id,
    //         'transaction_id'=>'JM-'. mt_rand(100000,999999),
    //         'transaction_type'=>$transaction_type,
    //         'total_amount'=>$total_amount,
    //         'wallet_amount'=>$total_amount,

    //     ]);
    // }

    public function SimDue(Request $request)
    {

        $info = User::where('id', $request->user_id)->first();
        $wallet_before_transaction = $info->sim_wallet;
        if ($info->sim_wallet >= $request->due) {
            // $user = User::where('id', $request->user_id)->update([

            //     "sim_wallet" => $info->sim_wallet - $request->due

            // ]);

            $user = tap(DB::table('users')->where('id', $request->user_id))->update(["sim_wallet" => $info->sim_wallet - $request->due])->first();
            $wallet_after_transaction = $user->sim_wallet;
            UpdateWallet::create_transaction(0, 'credit', 'Sim', $wallet_before_transaction, $wallet_after_transaction, $request->due, 'main wallet', $request->user_id);
            //$this->update_transaction($request->due,$request->user_id,'Sim');
            return back()->with('success', 'Wallet Updated');
        } else {
            return back()->with('error', 'Due Amount Is Greater Than The Input');
        }
    }

    public function AddDue(Request $request)
    {

        $info = User::where('id', $request->user_id)->first();

        $user = User::where('id', $request->user_id)->update([

            "cargo_due" => $info->cargo_due + $request->due,

        ]);

        return back();
    }

    public function PriceDiscount(Request $request)
    {
        $phone = Phone::find($request->user_id);
        $phone->discount_status = $request->status;
        $phone->save();

        return response()->json(['success' => 'Status change successfully.']);
    }

    public function edit_wallet(Request $request)
    {

        $user = User::where('id', $request->user_id)->update([

            "wallet" => $request->balance,

        ]);

        return back()->with('status', 'Balance Updated Successfully!');

    }
}
