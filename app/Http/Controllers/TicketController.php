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
use Illuminate\Support\Facades\Log;


class TicketController extends Controller
{
    //

    public function update_ticket_status(Request $request)
    {
        $id = $request->id;
        ticket::where('id',$id)->update(['status'=>$request->status,'reseller_notification'=>1]);
//file_put_contents('test.txt',$request->status);
        if($request->status =='Close')
        {
            $ticket = ticket::where('id',$id)->first();
           $this->send_ticket_closing_mail($ticket->ticket_no,$ticket->reseller->email,$ticket->reseller->first_name.' '.$ticket->reseller->last_name,$ticket->service_name);
        }
        event(new TicketRequest());

        //file_put_contents('test.txt',$request->status);
    }
    public function index()
    {
        return view('front.ticket');
    }
    public function ticket_reply(Request $request)
    {
        $ticket_info = ticket::where('id',$request->ticket_id)->first();
        $ticket_no = $ticket_info->ticket_no;
        $service_name = $ticket_info->service_name;
        if($request->document)
        {
        $path = $request->document->store('image/ticketDocument', 'public');
        }
        else
        {
            $path = NULL;
        }
        ticket_response::create([
            'ticket_id'=>$request->ticket_id,
            'message'=>$request->reseller_message,
            'document'=>$path,
            'user_id'=>Auth::user()->id
        ]);
        if(Auth::user()->role == 'admin')
        {
            ticket::where('id',$request->ticket_id)->update(['reseller_notification'=>1]);
            $this->send_mail_reply($ticket_no,$service_name,$request->reseller_message,$ticket_info->reseller->email,$ticket_info->reseller->first_name.' '.$ticket_info->reseller->last_name);
        }
        else{
            ticket::where('id',$request->ticket_id)->update(['admin_notification'=>1]);
            $this->send_mail_reply($ticket_no,$service_name,$request->reseller_message,'support@jmnation.com','Admin');
        }
        event(new TicketRequest());




        return redirect()->back()->with('success','Response Submitted');
    }

    public function ticket_reopen(Request $request)
    {
        $id = $request->id;
        ticket::where('id',$id)->update(['status'=>'Open','admin_notification'=>1]);
        $ticket = ticket::where('id',$id)->first();
        $this->send_ticket_closing_mail($ticket->ticket_no,$ticket->reseller->email,$ticket->reseller->first_name.' '.$ticket->reseller->last_name,$ticket->service_name);
        event(new TicketRequest());
    }

    public function send_ticket_reopening_mail($ticket_no,$email,$user_name,$service_name)
    {


        $data = [
            'ticket_id'=>$ticket_no,
            'type'=>'Reopen User',
            'user_name'=>$user_name,
            'service_name'=>$service_name,
        ];
        //file_put_contents('test.txt',json_encode($data));
        try {

             Notification::route('mail',$email)
                 ->notify(new TicketNotification($data));
        } catch (\Throwable $th) {
            Log::info($th);
        }

        $data = [
            'ticket_id'=>$ticket_no,
            'type'=>'Reopen Admin',
            'user_name'=>$user_name,
            'service_name'=>$service_name,
        ];
        //file_put_contents('test.txt',json_encode($data));
        try {

             Notification::route('mail',$email)
                 ->notify(new TicketNotification($data));
        } catch (\Throwable $th) {
            Log::info($th);
        }


    }



    public function send_mail_reply($ticket_no,$service_name,$message,$email,$name)
    {


        $data = [
            'ticket_id'=>$ticket_no,
            'type'=>'reply',
            'service_name'=>$service_name,
            'user_name'=>$name,
            'message'=>$message
        ];

        try {

             Notification::route('mail',$email)
                 ->notify(new TicketNotification($data));
        } catch (\Throwable $th) {
            Log::info($th);
        }


    }

    public function send_ticket_closing_mail($ticket_no,$email,$user_name,$service_name)
    {


        $data = [
            'ticket_id'=>$ticket_no,
            'type'=>'Closing User',
            'user_name'=>$user_name,
            'service_name'=>$service_name,
        ];
        //file_put_contents('test.txt',json_encode($data));
        try {

             Notification::route('mail',$email)
                 ->notify(new TicketNotification($data));
        } catch (\Throwable $th) {
            Log::info($th);
        }

        $data = [
            'ticket_id'=>$ticket_no,
            'type'=>'Closing Admin',
            'user_name'=>$user_name,
            'service_name'=>$service_name,
        ];
        //file_put_contents('test.txt',json_encode($data));
        try {

             Notification::route('mail',$email)
                 ->notify(new TicketNotification($data));
        } catch (\Throwable $th) {
            Log::info($th);
        }


    }



    public function ticket_response_view(Request $request)
    {
        $ticket_no = $request->id;
        $ticket_details = ticket::where('ticket_no',$ticket_no)->first();
        $ticket_response = ticket_response::where('ticket_id',$ticket_details->id)->get();
        if(Auth::user()->role == 'user')
        ticket_response::where('ticket_id',$ticket_details->id)->update(['message_read_status_user'=>1]);
        else
        ticket_response::where('ticket_id',$ticket_details->id)->update(['message_read_status_admin'=>1]);
        foreach($ticket_response as $data)
        {
            $data->response_time =$data->created_at.'('.$data->created_at->diffForHumans(Carbon::now()).')';
        }


        return view('front.ticket-response',compact('ticket_response','ticket_details'));
    }


    public function send_mail($ticket_no,$status,$service_name,$message)
    {


        $data = [
            'ticket_id'=>$ticket_no,
            'status'=>$status,
            'type'=>'Admin',
            'service_name'=>$service_name,
            'user_name'=>'Admin',
            'message'=>$message
        ];

        try {

             Notification::route('mail','support@jmnation.com')
                 ->notify(new TicketNotification($data));
        } catch (\Throwable $th) {
            Log::info($th);
        }

        $data = [
            'ticket_id'=>$ticket_no,
            'status'=>$status,
            'type'=>'User',
            'service_name'=>$service_name,
            'user_name'=>Auth::user()->first_name." ".Auth::user()->last_name,
            'message'=>$message
        ];

        try {

             Notification::route('mail',Auth::user()->email)
                 ->notify(new TicketNotification($data));
        } catch (\Throwable $th) {
            Log::info($th);
        }
    }
    public function ticket_submit(Request $request)
    {
        if($request->document)
        {
        $path = $request->document->store('image/ticketDocument', 'public');
       // $file = storage_path() . "/app/public/image/ticketDocument" . $path;
        }
        else
        {
            $path = NULL;
            //$file =NULL;
        }
        $date = date('dmyhis');
        $resller_id = str_pad(auth()->user()->id, 4, "0", STR_PAD_LEFT);
        $ticket_no = $date.$resller_id;
        $ticket =  ticket::create([
            "reseller_id" => Auth::user()->id,
            'ticket_no'=>$ticket_no,
            "service_name" => $request->service,
            'admin_notification'=>1,
            'status'=>'Open'

        ]);
        ticket_response::create([
            'ticket_id'=>$ticket->id,
            'message'=>$request->reseller_message,
            'document'=>$path,
            'user_id'=>Auth::user()->id
        ]);
       $this->send_mail($ticket->ticket_no,$ticket->status,$request->service,$request->reseller_message);

        event(new TicketRequest());

        return redirect()->route('ticket')->with('success','Ticket Submitted Successfully');
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
                ->latest()
                ->select(['*']);
        } else {
            // ticket::where("admin_notification", 0)->update([
            //     "admin_notification" => 1,
            // ]);
            ticket::where("admin_notification", 1)->update([
                "admin_notification" => 0,
            ]);
            $data = ticket::latest()->select(['*']);
        }


        return Datatables::eloquent($data)



         ->addColumn('reseller_name', function($data){
           $text = $data->reseller->first_name." ".$data->reseller->last_name;
           return $text;
         })
         ->addColumn('last_response', function($data){
             if($data->last_response)
            $text =  $data->last_response->updated_at;
            else
            $text = 'Not Response Yet';
            return $text;
          })
          ->addColumn('message_read_status', function($data){
              if(Auth::user()->role == 'admin')
            $ticket_response = ticket_response::where('ticket_id',$data->id)->where('message_read_status_admin',0)->first();
            else
            $ticket_response = ticket_response::where('ticket_id',$data->id)->where('message_read_status_user',0)->first();

            if($ticket_response)
            $text = 'Unread';
            else
            $text = 'Read';
            return $text;
          })
          ->addColumn('status', function($data){
              if($data->status =='Open')
            $text = '<p class="text-center" style="background-color:#BC0000;color:white">'.$data->status.'</p>';
            else
            $text = '<p class="text-center" style="background-color:#717D7E;color:white">'.$data->status.'</p>';
            return $text;
          })

          ->addColumn('update_status', function($data){
            $text ='<select class="status">
                <option value="Close">Close</option>
                <option value="Open">Open</option>
            </select>
            <a href="javascript:void(0);" class="btn btn-sm btn-success" onclick="update_status('.$data->id.')">Update</a>';
            return $text;
          })




          ->addColumn('action', function($data){
            $text ='<p class="text-center" style="margin:1px !important"><a class="btn btn-sm btn-primary" href="ticket/ticket-response/'.$data->ticket_no.'">Show Details</a></p>';
            return $text;
          })

         ->rawColumns(['transaction_amount','description','reseller_name','last_response','action','update_status','status','message_read_status'])
        ->addIndexColumn()
        ->make(true);
        return $data;
    }
}
