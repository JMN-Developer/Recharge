<?php

namespace App\Http\Controllers;

use App\Models\TransactionHistory;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Carbon;
use Auth;


class TransactionController extends Controller
{
    //
    public function index(Request $request)
    {
        if ($request->type=='datatable') {
            $start_date = Carbon::parse($request->start_date)->toDateTimeString();
            $end_date =  Carbon::parse($request->end_date)->addDays(1)->toDateTimeString();
            $user_id = Auth::user()->id;
            $data = TransactionHistory:: where('reseller_id',$user_id)-> whereBetween('created_at', [$start_date, $end_date])->get();
            $wallet_debit = TransactionHistory::where('reseller_id',$user_id)->whereBetween('created_at', [$start_date, $end_date])->where('transaction_wallet','Wallet Request')->where('transaction_type','debit')->sum('amount') ;
            $wallet_credit = TransactionHistory::where('reseller_id',$user_id)->whereBetween('created_at', [$start_date, $end_date])->where('transaction_wallet','International')->orWhere('transaction_wallet','Domestic')->where('transaction_type','credit')->sum('amount') ;
            $wallet = $wallet_debit - $wallet_credit;

            $limit_debit = TransactionHistory::where('reseller_id',$user_id)->whereBetween('created_at', [$start_date, $end_date])->where('wallet_type','limit')->where('transaction_type','debit')->sum('amount') ;
            $limit_credit = TransactionHistory::where('reseller_id',$user_id)->whereBetween('created_at', [$start_date, $end_date])->where('wallet_type','limit')->where('transaction_type','credit')->sum('amount') ;
            $limit = $limit_debit - $limit_credit;

            $sim_debit = TransactionHistory::where('reseller_id',$user_id)->whereBetween('created_at', [$start_date, $end_date])->where('transaction_wallet','Sim')->where('transaction_type','debit')->sum('amount');
            $sim_credit = TransactionHistory::where('reseller_id',$user_id)->whereBetween('created_at', [$start_date, $end_date])->where('transaction_wallet','Sim')->where('transaction_type','credit')->sum('amount');
            $sim = $sim_debit - $sim_credit;

            $cargo_debit = TransactionHistory::where('reseller_id',$user_id)->whereBetween('created_at', [$start_date, $end_date])->where('transaction_wallet','Cargo')->where('transaction_type','debit')->sum('amount');
            $cargo_credit = TransactionHistory::where('reseller_id',$user_id)->whereBetween('created_at', [$start_date, $end_date])->where('transaction_wallet','Cargo')->where('transaction_type','credit')->sum('amount');
            $cargo = $cargo_debit - $cargo_credit;
            foreach($data as $d){
            $d->wallet = $wallet;
            $d->limit = $limit;
            $d->cargo = $cargo;
            $d->sim = $sim;
            }
            return Datatables::of($data)
            ->addColumn('transaction_amount', function($data){
                $text='';
                if($data->transaction_type == 'credit')
                $text.= '<p style="color:green;font-weight:bold">'.$data->amount.'</p>';
                else
                $text.= '<p style="color:red;font-weight:bold">-'.$data->amount.'</p>';


                return $text;
             })
             ->addColumn('description', function($data){
                if($data->transaction_type == 'credit')
                $text = $data->transaction_wallet.' '.$data->wallet_type.' is credited with '.$data->amount.' Euro';
                else
                $text = $data->transaction_wallet.' '.$data->wallet_type.' is debited with '.$data->amount.' Euro';



                return $text;
             })
             ->rawColumns(['transaction_amount','description'])
            ->addIndexColumn()
            ->make(true);
        }
        return view('front.transaction-history');
    }
}
