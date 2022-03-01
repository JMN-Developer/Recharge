@extends('front.layout.courier')
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
  <!-- Theme style -->
  <link rel="stylesheet" href="{{asset('css/admin.min.css')}}">

  <link rel="stylesheet" href="{{asset('css/style.css')}}">
<link rel="icon" href="https://jmnation.com/images/jm-transparent-logo.png"></head>
<style>
    .date_picker_pair {
    width: 90%;
}
.user-response{
    background-color:#ECF0F1
}

.admin-response{
    background-color:#CEF7E0
}
.title{
    font-size: 18px;
    font-weight: bold;
}



/* .sorting_disabled{
    display: none !important;
} */
table.dataTable thead .sorting_asc{
    background-image: none !important;
}

</style>


@endsection

@section('content')

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
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
                        <p><span class="font-weight-bold p-2">Status:</span>{{ $ticket_details->status }}</p>
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

                <div class="card-body pb-0 user-response" style="margin: 10px;border-radius:5px">
                    <p class="title" >{{ $ticket_details->reseller->first_name." ". $ticket_details->reseller->last_name }} (Admin)</p>
                    <p>{{$response->reseller_message }}</p>
                    @if($response->problem_document)
                    <div>
                        <img width="500px" height="270px" class="img-thumbnail" src="{{ asset("storage/$response->problem_document ") }}">

                    </div>
                    @endif



                </div>
                @if($response->admin_message)
                <div class="card-body pb-0 user-response" style="margin: 10px;border-radius:5px">
                    <p class="title">Admin</p>
                    <p class="font-weight-bold"> {{$response->admin_message }}</p>

                </div>
                @endif
                @endforeach




              <!-- /.card-header -->


            </div>
            <!-- /.card -->
          </div>

        </div>
        <!-- /.row -->

      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

@endsection

@section('scripts')
<script src="{{asset('js/jquery.min.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(function(){
        $('.service').select2({

    placeholder: function(){
        $(this).data('placeholder');
    }

    });
    });
</script>

@endsection
