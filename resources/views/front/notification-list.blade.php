@extends('front.layout.master')
@section('header')
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>Notifiction</title>

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
<link rel="icon" href="https://jmnation.com/images/jm-transparent-logo.png">


<!-- Select2 -->
<link rel="stylesheet" href="{{asset('css/select2.min.css')}}">
<!-- Theme style -->
<link rel="stylesheet" href="{{asset('css/admin.min.css')}}">

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
<link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
</head>
<style>
  .unread{
      border-radius: none;
      border-left: 6px solid #A93226;
  }
  .read{
      border-radius: none;
      border-left: 6px solid #229954;
  }
  .date_picker_pair {
    width: 100% !important;
}
</style>


@endsection

@section('content')

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">

        @if (\Session::has('success'))
        <div class="alert alert-success">

               {!! \Session::get('success') !!}

        </div>
    @endif


        <div class="row">
          <div class="col-12 mt-3">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title"><i class="fas fa-list" style="margin-right: 10px;"></i>
                  <strong>Notification</strong>
                </h3>
              </div>




              <!-- /.card-header -->

              <div class="p-3" style="background-color:#F7F9F9">
                  @if(auth()->user()->role =='admin')
                <div>
                    <a class="btn btn-primary" href='{{ route('create-notification') }}' style="margin-bottom: 20px">Create New</a>
                </div>
                <form method="GET" action="{{ route('GeneralNotification') }}">
                <div class="row" style="margin-left:5px">
                    <div class="col-md-3">
                        <div class="date_picker_pair mb-3">
                            <label for="inputSearchDate" class="form-label">Select Date</label>
                            <input type="text" class="form-control" name="daterange" id="inputSearchDate" value="01/01/2018 - 01/15/2018">
                            <input type="hidden" class='start_date' name='start_date'>
                            <input type="hidden" class='end_date' name='end_date'>

                            <!-- <input type="text" name="daterange" value="01/01/2018 - 01/15/2018" /> -->
                          </div>
                      </div>

                      <div class="col-md-3">
                        <div class="form-row align-items-center offer_select_option">
                            <label for="inlineFormCustomSelect" style="margin-bottom:14px">Services</label>

                            <select  data-placeholder="Select an Option"  class="custom-select service"  name="service" required>
                                <option></option>
                                <option value="all">All</option>
                                <option value="International Recharge">International Recharge</option>
                                <option value="Domestic Recharge">Domestic Recharge</option>
                                <option value="Wallet Transaction">Wallet Transaction</option>
                                <option value="Sim">Sim</option>
                                <option value="Cargo">Cargo</option>
                                <option value="Flight">Flight</option>
                                <option value="Others">Others</option>

                            </select>
                          </div>
                      </div>

                      <div class="col-md-3" style="margin-left:15px">
                        <div class="form-row align-items-center offer_select_option">
                            <label for="inlineFormCustomSelect" style="margin-bottom:14px">Choose Retailer</label>

                            <select  data-placeholder="Select an Option"  class="custom-select reseller" id="reseller" name="retailer" required>
                                <option></option>
                                <option value="all">All</option>
                             @foreach ( $resellers as $d )
                                 <option value="{{ $d->id }}">{{ $d->first_name." ".$d->last_name." (".$d->id.")" }}</option>
                             @endforeach

                            </select>
                          </div>
                      </div>

                      <div class="col-md-2">
                        <input type="submit"   value="Search" class="btn btn-success" style="margin-top:30px">
                      </div>
                  </div>
                </form>
                @endif
                <div class="recharge_input_table table-responsive p-0">
                    @foreach($data as $d)

                    <div class="card unread">
                        <div class="card-body">
                            <div>
                                <p style="font-weight: bold">{{$d->service}}<span style="float: right;color:#979A9A">{{$d->time}}</span></p>
                                <p>{{$d->message}}</p>
                            </div>
                        </div>
                    </div>

                    @endforeach
                    <div style="float:right">
                        @if(auth()->user()->role =='user')
                    {{$user->notifications()->paginate(10)->links()}}
                    @else
                    {{$notifications->links()}}
                    @endif
                    </div>

                </div>
              </div>
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
<!-- jQuery -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js" type="text/javascript"></script>
<script src="https://cdn.datatables.net/plug-ins/1.10.25/api/sum().js" type="text/javascript"></script>
<!-- Bootstrap -->

<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

<!-- Theme JS -->


@endsection

@section('js')
<script>
    $(function(){
        $('.reseller').select2({

placeholder: function(){
    $(this).data('placeholder');
}

});

$('.service').select2({

placeholder: function(){
    $(this).data('placeholder');
}

});


var start = moment().subtract(29, 'days');
  var end = moment();
  $(".start_date").val(start.format('YYYY-MM-DD'));
  $(".end_date").val(end.format('YYYY-MM-DD'));



  $('input[name="daterange"]').daterangepicker({
    startDate: start,
    endDate: end,
    ranges: {
       'Today': [moment(), moment()],
       'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
       'Last 7 Days': [moment().subtract(6, 'days'), moment()],
       'Last 30 Days': [moment().subtract(29, 'days'), moment()],
       'This Month': [moment().startOf('month'), moment().endOf('month')],
       'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
    }
  }, function(start, end, label) {
     // alert('hello')

     $(".start_date").val(start.format('YYYY-MM-DD'));
     $(".end_date").val(end.format('YYYY-MM-DD'));

   //  fetch_table($(".start_date").val(),$(".end_date").val());

  });
    })
</script>
@endsection
