<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ticket;
use Auth;
use Illuminate\Support\Carbon;
use DB;
use App\Notifications\TicketNotification;
use Illuminate\Support\Facades\Notification;
use App\Events\TicketRequest;
use App\Models\ticket_response;
use Yajra\DataTables\Facades\DataTables;


class TicketController extends Controller
{
    //
    public function index()
    {
        return view('front.ticket');
    }
    public function ticket_response_view(Request $request)
    {
        $ticket_id = $request->id;
        $ticket_response = ticket_response::where('ticket_id',$ticket_id)->latest()->get();
        $ticket_details = ticket::where('id',$ticket_id)->first();

        return view('front.ticket-response',compact('ticket_response','ticket_details'));
    }
    public function ticket_submit(Request $request)
    {
        $path = $request->document->store('image/ticketDocument', 'public');
        $date = date('dmyhis');
        $resller_id = str_pad(auth()->user()->id, 4, "0", STR_PAD_LEFT);
        $ticket_no = $date.$resller_id;
       $ticket =  ticket::create([
            "reseller_id" => Auth::user()->id,
            'ticket_no'=>$ticket_no,
            "service_name" => $request->service,
            'admin_notification'=>1

        ]);
        ticket_response::create([
            'ticket_id'=>$ticket->id,
            'reseller_message'=>$request->reseller_message,
            'problem_document'=>$path
        ]);
        $data = [
            'from'=>'pointrecharge@gmail.com',
            'message'=>$request->message
        ];

        try {

            // Notification::route('mail','nokibevon7@gmail.com')
            //     ->notify(new TicketNotification($data));
        } catch (\Throwable $th) {
            //throw $th;
        }
        event(new TicketRequest());
    }

    public function complain_notification_count()
    {
        if (auth()->user()->role == "admin") {
            $data = ticket::where("admin_notification", 1)
                ->get()
                ->count();
        } else {
            $data = ticket::where("reseller_id", Auth::user()->id)
                ->where("reseller_notification", 1)
                ->get()
                ->count();
        }

        return $data;
    }
    public function ticket_answer(Request $request)
    {
        $id = $request->id;
        $admin_message = $request->admin_message;
        ticket::where('id',$id)->update(['admin_message'=>$admin_message,'admin_notification'=>0,'reseller_notification'=>1]);
        event(new TicketRequest());
    }

    public function add_ticket_view()
    {
        return view('front.add-ticket');
    }

    public function get_ticket_data()
    {

        if (auth()->user()->role != "admin") {
            // ticket::where("reseller_id", Auth::user()->id)->update([
            //     "reseller_notification" => 1,
            // ]);
            ticket::where("reseller_notification", 1)->update([
                "reseller_notification" => 0,
            ]);
            $data = ticket::where("reseller_id", Auth::user()->id)
                ->orderBy(
                    DB::raw(
                        'case when status= "pending" then 1 when status= "answered" then 2 when status="approved" then 3 end'
                    )
                )
                ->select(['*']);
        } else {
            // ticket::where("admin_notification", 0)->update([
            //     "admin_notification" => 1,
            // ]);
            ticket::where("admin_notification", 1)->update([
                "admin_notification" => 0,
            ]);
            $data = ticket::orderBy(
                DB::raw(
                    'case when status= "pending" then 1 when status= "answered" then 2 when status="approved" then 3 end'
                )
            )->select(['*']);
        }
        foreach ($data as $item) {
            $response = ticket_response::where('tikcet_id',$item->id)->latest()->first()->updated_at;
            $last_response =  Carbon::parse($response)->format(
                "Y-m-d h:i:s"
            );
            $item->last_response = $last_response;
            $item->requested_date = Carbon::parse($item->created_at)->format(
                "Y-m-d h:i:s"
            );
            $item->reseller_name =
                $item->reseller->first_name . " " . $item->reseller->last_name;

        }

        return Datatables::eloquent($data)
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

         ->addColumn('reseller_name', function($data){
           $text = $data->reseller->first_name." ".$data->reseller->last_name;
           return $text;
         })
         ->addColumn('last_response', function($data){
            $text =$data->last_response->updated_at;
            return $text;
          })

         ->rawColumns(['transaction_amount','description','reseller_name','last_response'])
        ->addIndexColumn()
        ->make(true);
        return $data;
    }
}
