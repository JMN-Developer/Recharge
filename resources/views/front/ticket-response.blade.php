@extends('front.layout.master')
@section('header')
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>Ticket</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{asset('css/fontawesome-free/css/all.min.css')}}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <link href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css" rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/css/iziToast.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{asset('css/admin.min.css')}}">

    <link rel="stylesheet" href="{{asset('css/style.css')}}">
  <link rel="icon" href="https://jmnation.com/images/jm-transparent-logo.png">

</head>
  <style>
      .date_picker_pair {
      width: 90%;
  }

  /* .sorting_disabled{
      display: none !important;
  } */
  table.dataTable thead .sorting_asc{
      background-image: none !important;
  }
  .user-response{
      background-color: #FDEFFC;
  }
  .admin-response{
      background-color: #E7FBF7;
  }
  .title{
      font-weight: bold
  }


  </style>

@endsection

@section('content')

<div class="content-wrapper">
    <section class="content">
        <div class="container-fluid">
          @if (\Session::has('success'))
          <div class="alert alert-success">

                 {!! \Session::get('success') !!}

          </div>
           @endif
          <div class="row">
              <div class="col-6 mt-3">
                  <div class="card">
                    <div class="card-header">
                      <h3 class="card-title"><i class="fas fa-list" style="margin-right: 10px;"></i>
                        <strong>Ticket Details</strong>
                      </h3>
                    </div>


                      <div class="card-body pb-0">
                          <p><span class="font-weight-bold p-2">Reseller Name:</span><span>{{ $ticket_details->reseller->first_name." ". $ticket_details->reseller->last_name }}</span></p>
                          <p><span class="font-weight-bold p-2">Ticket No:</span>{{ $ticket_details->ticket_no }}</p>
                          <p><span class="font-weight-bold p-2">Service:</span>{{ $ticket_details->service_name }}</p>
                      </div>



                    <!-- /.card-header -->


                  </div>
                  <!-- /.card -->
                </div>
                <div class="col-6 mt-3">
                  <div class="card">
                    <div class="card-header">
                      <h3 class="card-title"><i class="fas fa-list" style="margin-right: 10px;"></i>
                        <strong>Ticket Details</strong>
                      </h3>
                    </div>


                      <div class="card-body pb-0">
                          <p><span class="font-weight-bold p-2">Status:</span>{{ $ticket_details->status }} @if($ticket_details->status == 'Close')<span><button onclick="re_open_ticket({{$ticket_details->id}})" class="btn btn-sm btn-primary" style="margin-left:10px">Re Open</button></span>@endif</p>
                          <p><span class="font-weight-bold p-2">Opened:</span>{{ $ticket_details->created_at }}</p>
                          <p><span class="font-weight-bold p-2">Response:</span>{{ $ticket_details->last_response->updated_at }}</p>
                      </div>



                    <!-- /.card-header -->


                  </div>
                  <!-- /.card -->
                </div>
            <div class="col-12 mt-3">
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title"><i class="fas fa-list" style="margin-right: 10px;"></i>
                    <strong>Ticket Response</strong>
                  </h3>
                </div>

                @foreach($ticket_response as $response)
                  @if($response->user->role != 'admin')
                  <div class="card-body user-response pb-0" style="margin: 10px;border-radius:5px">
                      <p class="title" >{{ $ticket_details->reseller->first_name." ". $ticket_details->reseller->last_name }} (Reseller) <span style="float: right">{{$response->response_time}}</span></p>

                      <p >{{$response->message }}</p>
                      @if($response->document)
                      <div style="padding: 10px">
                          <img width="500px" height="270px" class="img-thumbnail" src="{{ asset("storage/$response->document ") }}">

                      </div>
                      @endif


                  </div>
                  @else
                  <div class="card-body admin-response pb-0" style="margin: 10px;border-radius:5px">
                      <p class="title" >Admin <span style="float: right">{{$response->response_time}}</span></p>
                      <p>{{$response->message }}</p>
                      @if($response->document)
                      <div style="padding: 10px">
                          <img width="500px" height="270px" class="img-thumbnail" src="{{ asset("storage/$response->document ") }}">

                      </div>
                      @endif


                  </div>
                  @endif
                  @endforeach


              </div>
                @if($ticket_details->status == 'Open')
                <div class="card">
                  <div class="card-body pb-0">
                      <form  action="{{ route('ticket-reply') }}" method="POST" enctype="multipart/form-data">
                          @csrf
                          <div class="form-group col-md-12">
                              <label for="inputPassword4">Message</label>
                              <textarea id="message" class="form-control" rows="6" name="reseller_message" required></textarea>
                              <input type='hidden' name="ticket_id" value="{{ $ticket_details->id }}">
                            </div>

                            <div class="form-group col-md-6">
                              <label for="inputPassword4">Attachment(Optional)</label>
                              <div class="custom-file">
                                  <input type="file" class="custom-file-input" id="document" name="document">
                                  <label class="custom-file-label " for="customFile">Choose file</label>
                                </div>
                            </div>
                            <div class="form-group col-md-12">
                              <input type='submit' class="btn btn-primary" value="Submit" style="margin-top: 32px;">
                              <button type="button" class="btn btn-danger" style="margin-top: 32px;" onclick="back()">Close</button>
                            </div>

                          </div>
                      </form>
                  </div>
                  @endif
              </div>
              <!-- /.card -->
            </div>

          </div>
          <!-- /.row -->

        <!-- /.container-fluid -->
      </section>
      <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->



    <!-- /.content -->

@endsection

@section('scripts')
<!-- jQuery -->

<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js" type="text/javascript"></script>
<script src="https://cdn.datatables.net/plug-ins/1.10.25/api/sum().js" type="text/javascript"></>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>


@endsection

@section('js')

    <script>
      function back() {
      window.history.back()

    }
        $(function(){
            var toast = document.querySelector('.iziToast');
        var message = sessionStorage.getItem('message');
        sessionStorage.removeItem('message');

        if(toast)
                {
                iziToast.hide({}, toast);
                }


            if ( sessionStorage.getItem('success') ) {
            sessionStorage.removeItem('success');


            iziToast.success({
                    backgroundColor:"Green",
                    messageColor:'white',
                    iconColor:'white',
                    titleColor:'white',
                    titleSize:'18',
                    messageSize:'18',
                    color:'white',
                    position:'topCenter',
                    timeout: 50000,
                    title: 'Success',
                    message: message,

                });
                //console.log(response.message);

            }
        });

        function re_open_ticket(id)
        {
            swal({
  title: "Are you sure?",
  icon: "warning",
  buttons: true,
  dangerMode: true,
})
.then((willDelete) => {
  if (willDelete) {

    $.ajax({
            type: "GET",

            url: '/ticket/reopen',
            data: {'id': id},
            success: function(data){
                location.reload()
                sessionStorage.setItem('success',true);
                sessionStorage.setItem('message','Your ticket is now Re-opened. You can contact through this ticket ');
            }
        });





  } else {

  }
  });


        }
    </script>

@endsection
