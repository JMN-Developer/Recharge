<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ticket;
use Auth;
use Illuminate\Support\Carbon;
use DB;

class TicketController extends Controller
{
    //
    public function index()
    {
        return view('front.ticket');
    }
    public function ticket_submit(Request $request)
    {
        $path = $request->document->store('image/ticketDocument', 'public');
        $date = date('dmyhis');
        $resller_id = str_pad(auth()->user()->id, 4, "0", STR_PAD_LEFT);
        $ticket_no = $date.$resller_id;
        ticket::create([
            "reseller_id" => Auth::user()->id,
            'ticket_no'=>$ticket_no,
            "problem_description" => $request->message,
            'problem_document'=>$path

        ]);
       // event(new DueRequest());
    }
    public function ticket_answer(Request $request)
    {
        $id = $request->id;
        $admin_message = $request->admin_message;
        ticket::where('id',$id)->update(['admin_message'=>$admin_message]);
    }

    public function get_ticket_data()
    {

        if (auth()->user()->role != "admin") {
            // ticket::where("reseller_id", Auth::user()->id)->update([
            //     "reseller_notification" => 1,
            // ]);
            $data = ticket::where("reseller_id", Auth::user()->id)
                ->orderBy(
                    DB::raw(
                        'case when status= "pending" then 1 when status= "answered" then 2 when status="approved" then 3 end'
                    )
                )
                ->get();
        } else {
            // ticket::where("admin_notification", 0)->update([
            //     "admin_notification" => 1,
            // ]);
            $data = ticket::orderBy(
                DB::raw(
                    'case when status= "pending" then 1 when status= "answered" then 2 when status="approved" then 3 end'
                )
            )->get();
        }
        foreach ($data as $item) {
            $item->requested_date = Carbon::parse($item->created_at)->format(
                "Y-m-d h:i:s"
            );
            $item->reseller_name =
                $item->reseller->first_name . " " . $item->reseller->last_name;

        }
        return $data;
    }
}
