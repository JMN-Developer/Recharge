<?php

namespace App\Http\Controllers;

use App\Models\TransactionHistory;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Yajra\DataTables\Facades\DataTables;

class TransactionController extends Controller
{
    //
    public function index(Request $request)
    {
        if ($request->type == 'datatable') {

            $start_date = Carbon::parse($request->start_date)->toDateTimeString();
            $end_date = Carbon::parse($request->end_date)->addDays(1)->toDateTimeString();
            $user_id = Auth::user()->id;

            if (Auth::user()->role == 'admin') {
                if ($request->retailer == 'all') {
                    $data = TransactionHistory::whereBetween('created_at', [$start_date, $end_date])->latest()->select(['*']);
                } else {
                    $user_id = $request->retailer;
                    $data = TransactionHistory::where('reseller_id', $user_id)->whereBetween('created_at', [$start_date, $end_date])->latest()->select(['*']);

                }
            } else if (Auth::user()->role == 'sub') {

                if ($request->retailer == 'all') {

                    $data = TransactionHistory::whereBetween('created_at', [$start_date, $end_date])->where(function ($query) {
                        $query->where('parent_id', Auth::user()->id)
                            ->orWhere('reseller_id', Auth::user()->id);
                    })->latest()->select(['*']);
                } else {
                    $user_id = $request->retailer;
                    $data = TransactionHistory::where('reseller_id', $user_id)->whereBetween('created_at', [$start_date, $end_date])->latest()->select(['*']);

                }
            } else {
                $data = TransactionHistory::where('reseller_id', $user_id)->whereBetween('created_at', [$start_date, $end_date])->latest()->select(['*']);
            }

            return Datatables::eloquent($data)
                ->addColumn('transaction_amount', function ($data) {
                    $text = '';
                    if ($data->transaction_type == 'credit') {
                        $text .= '<p style="color:green;font-weight:bold">' . $data->amount . '</p>';
                    } else {
                        $text .= '<p style="color:red;font-weight:bold">-' . $data->amount . '</p>';
                    }

                    return $text;
                })
                ->addColumn('description', function ($data) {
                    if ($data->transaction_type == 'credit') {
                        $text = $data->transaction_wallet . ' ' . $data->wallet_type . ' is credited with ' . $data->amount . ' Euro';
                    } else {
                        $text = $data->transaction_wallet . ' ' . $data->wallet_type . ' is debited with ' . $data->amount . ' Euro';
                    }

                    return $text;
                })

                ->addColumn('reseller_name', function ($data) {
                    $text = $data->reseller->first_name . " " . $data->reseller->last_name;
                    return $text;
                })
                ->rawColumns(['transaction_amount', 'description', 'reseller_name'])
                ->addIndexColumn()
                ->make(true);
        }
        if (auth()->user()->role == 'admin') {
            $resellers = User::get();
        } else if (auth()->user()->role == 'sub') {
            $resellers = User::where('created_by', auth()->user()->id)->get();
        }

        return view('front.transaction-history', compact('resellers'));
    }
}
