@extends('front.layout.courier')
@section('header')
<head>
  <meta charset="utf-8">
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Report</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{asset('css/fontawesome-free/css/all.min.css')}}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
  <link href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css" rel='stylesheet'>
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
  <link rel="stylesheet" href="{{asset('css/loader/index.css')}}">

  <!-- Theme style -->
  <link rel="stylesheet" href="{{asset('css/admin.min.css')}}">
  <link rel="stylesheet" href="{{asset('css/style.css')}}">

    {{-- Dashboard --}}
    <link href="{{ asset('dashboard/libs/flatpickr/flatpickr.min.css')}}" rel="stylesheet" type="text/css" />

    <!-- App css -->
    {{-- <link href="{{ asset('dashboard/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" id="bs-default-stylesheet" /> --}}
    <link href="{{ asset('dashboard/css/app.min.css')}}" rel="stylesheet" type="text/css" id="app-default-stylesheet" />

    <link href="{{ asset('dashboard/css/bootstrap-dark.min.css')}}" rel="stylesheet" type="text/css" id="bs-dark-stylesheet" disabled />
    <link href="{{ asset('dashboard/css/app-dark.min.css')}}" rel="stylesheet" type="text/css" id="app-dark-stylesheet" disabled />

    <!-- icons -->
    <link href="{{ asset('dashboard/css/icons.min.css')}}" rel="stylesheet" type="text/css" />

<link rel="icon" href="https://jmnation.com/images/jm-transparent-logo.png"></head>
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

</style>


@endsection

@section('content')

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <div class="content-page" style="margin-left:0px;margin-top:5px";>
        <div class="content">

            <!-- Start Content-->
            <div class="container-fluid">

                <!-- start page title -->
                <div class="row">
                    <div class="col-12">
                        <div class="row" style="margin-left:10px;margin-top:20px">
                            <div class="col-md-3">
                                <div class="date_picker_pair mb-3">
                                    <label for="inputSearchDate" class="form-label">Select Date</label>
                                    <input type="text" class="form-control" name="daterange" id="inputSearchDate" value="01/01/2018 - 01/15/2018">
                                    <input type="hidden" class='start_date'>
                                    <input type="hidden" class='end_date'>

                                    <!-- <input type="text" name="daterange" value="01/01/2018 - 01/15/2018" /> -->
                                  </div>
                              </div>



                              <div class="col-md-2">
                                <input type="button"  onclick="filter()" value="Filter" class="btn btn-success" style="margin-top:30px">
                              </div>
                          </div>

                    </div>
                </div>
                <!-- end page title -->

                <div class="row">
                    <div class="col-md-6 col-xl-3">
                        <div class="card">
                            <div class="card-body">

                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <span class="text-muted text-uppercase fs-12 fw-bold">All Recharge</span>
                                        <p style="margin-top:7px"><span style="font-weight: bold">Sale:</span><span id="all_sale"></span><span style="font-weight: bold;margin-left:10px">Profit:</span><span id="all_profit"></span></p>

                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-xl-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <span class="text-muted text-uppercase fs-12 fw-bold">International</span>
                                        <p style="margin-top:7px"><span style="font-weight: bold">Sale:</span><span id="international_sale" ></span><span style="font-weight: bold;margin-left:10px">Profit:</span><span  id="international_profit"></span></p>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-xl-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <span class="text-muted text-uppercase fs-12 fw-bold">Domestic</span>
                                        <p style="margin-top:7px"><span style="font-weight: bold">Sale:</span><span id="domestic_sale"></span><span style="font-weight: bold;margin-left:10px">Profit:</span><span id="domestic_profit"></span></p>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-xl-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <span class="text-muted text-uppercase fs-12 fw-bold">Pin</span>
                                        <p style="margin-top:7px"><span style="font-weight: bold">Sale:</span><span id="pin_sale"></span><span style="font-weight: bold;margin-left:10px">Profit:</span><span id="pin_profit"></span></p>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-xl-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <span class="text-muted text-uppercase fs-12 fw-bold">White Calling</span>
                                        <p style="margin-top:7px"><span style="font-weight: bold">Sale:</span><span id="white_calling_sale"></span><span style="font-weight: bold;margin-left:10px">Profit:</span><span id="white_calling_profit"></span></p>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="col-md-6 col-xl-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <span class="text-muted text-uppercase fs-12 fw-bold">Sim</span>
                                        <p style="margin-top:7px"><span style="font-weight: bold">Sale:</span><span id="sim_sale"></span><span style="font-weight: bold;margin-left:10px">Profit:</span><span id="sim_profit"></span></p>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="col-md-6 col-xl-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <span class="text-muted text-uppercase fs-12 fw-bold">Cargo</span>
                                        <p style="margin-top:7px"><span style="font-weight: bold">Sale:</span><span id="cargo_sale"></span><span style="font-weight: bold;margin-left:10px">Profit:</span><span id="cargo_profit"></span></p>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="col-md-3" style="float: right">
                                    <div class="date_picker_pair mb-3">

                                        <input type="text" class="form-control" name="daterange_all_recharge" id="inputSearchDate" value="01/01/2018 - 01/15/2018">

                                        <!-- <input type="text" name="daterange" value="01/01/2018 - 01/15/2018" /> -->
                                      </div>
                                  </div>
                                <h5 class="card-title mb-0 header-title">All Recharge</h5>
                                <div class="p-2 m-2 bg-white rounded shadow all_chart" style="margin-top: 50px !important">


                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="col-md-3" style="float: right">
                                    <div class="date_picker_pair mb-3">

                                        <input type="text" class="form-control" name="daterange_domestic_recharge" id="inputSearchDate" value="01/01/2018 - 01/15/2018">


                                      </div>
                                  </div>
                                <div class="dropdown float-end">


                                </div>
                                <h5 class="card-title mb-0 header-title">Domestic Recharge</h5>
                                <div class="p-2 m-2 bg-white rounded shadow domestic_recharge_chart" style="margin-top: 50px !important">


                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="col-md-3" style="float: right">
                                    <div class="date_picker_pair mb-3">

                                        <input type="text" class="form-control" name="daterange_international_recharge" id="inputSearchDate" value="01/01/2018 - 01/15/2018">


                                      </div>
                                  </div>
                                <div class="dropdown float-end">


                                </div>
                                <h5 class="card-title mb-0 header-title">International Recharge</h5>
                                <div class="p-2 m-2 bg-white rounded shadow international_recharge_chart" style="margin-top: 50px !important">


                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="col-md-3" style="float: right">
                                    <div class="date_picker_pair mb-3">

                                        <input type="text" class="form-control" name="daterange_pin" id="inputSearchDate" value="01/01/2018 - 01/15/2018">


                                      </div>
                                  </div>
                                <div class="dropdown float-end">


                                </div>
                                <h5 class="card-title mb-0 header-title">Pin</h5>
                                <div class="p-2 m-2 bg-white rounded shadow pin_chart" style="margin-top: 50px !important">


                                </div>

                            </div>
                        </div>
                    </div>


                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="col-md-3" style="float: right">
                                    <div class="date_picker_pair mb-3">

                                        <input type="text" class="form-control" name="daterange_white_calling" id="inputSearchDate" value="01/01/2018 - 01/15/2018">


                                      </div>
                                  </div>
                                <div class="dropdown float-end">

                                </div>
                                <h5 class="card-title mb-0 header-title">White Calling</h5>
                                <div class="p-2 m-2 bg-white rounded shadow white_calling_chart" style="margin-top: 50px !important">


                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="col-md-3" style="float: right">
                                    <div class="date_picker_pair mb-3">

                                        <input type="text" class="form-control" name="daterange_sim" id="inputSearchDate" value="01/01/2018 - 01/15/2018">


                                      </div>
                                  </div>
                                <div class="dropdown float-end">

                                </div>
                                <h5 class="card-title mb-0 header-title">Sim</h5>
                                <div class="p-2 m-2 bg-white rounded shadow sim_chart" style="margin-top: 50px !important">


                                </div>

                            </div>
                        </div>
                    </div>


                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="col-md-3" style="float: right">
                                    <div class="date_picker_pair mb-3">

                                        <input type="text" class="form-control" name="daterange_cargo" id="inputSearchDate" value="01/01/2018 - 01/15/2018">


                                      </div>
                                  </div>
                                <div class="dropdown float-end">

                                </div>
                                <h5 class="card-title mb-0 header-title">Cargo</h5>
                                <div class="p-2 m-2 bg-white rounded shadow cargo_chart" style="margin-top: 50px !important">


                                </div>

                            </div>
                        </div>
                    </div>




                    {{-- <div class="col-xl-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="col-md-3" style="float: right">
                                    <div class="date_picker_pair mb-3">

                                        <input type="text" class="form-control" name="daterange_all_recharge" id="inputSearchDate" value="01/01/2018 - 01/15/2018">


                                      </div>
                                  </div>
                                <div class="dropdown float-end">

                                </div>
                                <h5 class="card-title mb-0 header-title">Profit Growth</h5>

                                <div id="profit-chart" class="apex-charts mt-3" dir="ltr"></div>
                            </div>
                        </div>
                    </div> --}}


                </div>
                {{-- <div class="row">
                    <div class="col-xl-6 col-md-6">
                        <div class="card">
                            <div class="card-body">

                                <h5 class="card-title mt-0 mb-0 header-title">Sales By Category</h5>
                                <div id="sales-by-category-chart" class="apex-charts mb-0 mt-4" dir="ltr"></div>
                            </div>
                            <!-- end card-body-->
                        </div>
                    </div>

                    <div class="col-xl-6 col-md-6">
                        <div class="card">
                            <div class="card-body">

                                <h5 class="card-title mt-0 mb-0 header-title">Profit By Category</h5>
                                <div id="profit-by-category-chart" class="apex-charts mb-0 mt-4" dir="ltr"></div>
                            </div>
                            <!-- end card-body-->
                        </div>
                    </div>
                </div> --}}

                <!-- stats + charts -->

                <!-- row -->

                <!-- products -->
                <div class="row">

                    <!-- end col-->
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-body">
                                {{-- <a href="#" class="btn btn-primary btn-sm float-end">
                                    <i class='uil uil-export me-1'></i> Export
                                </a> --}}
                                <h5 class="card-title mt-0 mb-0 header-title">Top 5 Reseller</h5>

                                <div class="table-responsive mt-4">
                                    <table class="table table-hover table-nowrap mb-0">
                                        <thead>
                                            <tr>
                                                <th scope="col">Reseller Id</th>
                                                <th scope="col">Reseller Name</th>
                                                <th scope="col">Total Sale</th>
                                                <th scope="col">Total Profit</th>

                                            </tr>
                                        </thead>
                                        <tbody id="top_reseller">

                                        </tbody>
                                    </table>
                                </div>
                                <!-- end table-responsive-->
                            </div>
                            <!-- end card-body-->
                        </div>
                        <!-- end card-->
                    </div>
                    <!-- end col-->
                </div>
                <!-- end row -->

                <!-- widgets -->

                <!-- end row -->

            </div>
            <!-- container -->

        </div>
        <!-- content -->

        <!-- Footer Start -->

        <!-- end Footer -->

    </div>

  </div>
  <!-- /.content-wrapper -->

@endsection

@section('scripts')

<script src="{{ asset('dashboard/js/vendor.min.js')}}"></script>

<!-- optional plugins -->
<script src="{{ asset('dashboard/libs/moment/min/moment.min.js')}}"></script>
<script src="{{ asset('dashboard/libs/apexcharts/apexcharts.min.js')}}"></script>
<script src="{{ asset('dashboard/libs/flatpickr/flatpickr.min.js')}}"></script>

<!-- page js -->
{{-- <script src="{{ asset('dashboard/js/pages/dashboard.init.js')}}"></script> --}}

<!-- App js -->
<script src="{{ asset('dashboard/js/app.min.js')}}"></script>
<!-- jQuery -->
{{-- <script src="{{asset('js/jquery.min.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js" type="text/javascript"></script>
<script src="https://cdn.datatables.net/plug-ins/1.10.25/api/sum().js" type="text/javascript"></script>
<!-- Bootstrap -->
--}}
<script src="{{asset('js/bootstrap.bundle.min.js')}}"></script>
<script src="{{asset('js/moment.min.js')}}"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

<!-- Theme JS -->
<script src="{{asset('js/admin.js')}}"></script>
<!-- Custom JS -->
<script src="{{asset('js/custom.js')}}"></script>

@endsection

@section('js')

<script>


$(function() {

    var start = moment().subtract(29, 'days');
     var end = moment();
//   $(".start_date").val(start.format('YYYY-MM-DD'));
//   $(".end_date").val(end.format('YYYY-MM-DD'));
    get_data(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'));



    $('.reseller').select2({

    placeholder: function(){
        $(this).data('placeholder');
    }

    });

    $(".reseller").change(function(){
        fetch_table($(".start_date").val(),$(".end_date").val())

    });

    $("#ExampleSelect").change(function(){
        fetch_table($(".start_date").val(),$(".end_date").val())

    });


    $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });






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

     $(".start_date").val(start.format('YYYY-MM-DD'));
     $(".end_date").val(end.format('YYYY-MM-DD'));
     get_data($(".start_date").val(),$(".end_date").val())


  });


  $('input[name="daterange_all_recharge"]').daterangepicker({
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

    filter_separate_data('all',start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'))

  });


  $('input[name="daterange_international_recharge"]').daterangepicker({
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
    filter_separate_data('international_recharge',start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'))


  });

  $('input[name="daterange_domestic_recharge"]').daterangepicker({
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
    filter_separate_data('domestic_recharge',start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'))

    //  get_data($(".start_date").val(),$(".end_date").val())


  });


  $('input[name="daterange_pin"]').daterangepicker({
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
    filter_separate_data('pin',start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'))
  });


  $('input[name="daterange_white_calling"]').daterangepicker({
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

    filter_separate_data('white_calling',start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'))


  });


  $('input[name="daterange_sim"]').daterangepicker({
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

    filter_separate_data('sim',start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'))

  });


  $('input[name="daterange_cargo"]').daterangepicker({
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


    filter_separate_data('cargo',start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'))

  });


    // var formdata = new FormData();
    // formdata.append('')


    //fetch_table($(".start_date").val(),$(".end_date").val())


});
function filter()
{
    get_data($(".start_date").val(),$(".end_date").val())
    //fetch_table()
}


function filter_separate_data(type,start_date,end_date)
{

     let url = "{{ route('get-report-data-separate') }}";
        var formdata = new FormData();
        formdata.append('start_date',start_date)
        formdata.append('end_date',end_date)
        formdata.append('type',type)
        $.ajax({
        processData: false,
        contentType: false,
        url: url ,
        type:"POST",
        headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
         },
        data: formdata,
        beforeSend: function () {
            $('.cover-spin').show(0)
            },
        complete: function () { // Set our complete callback, adding the .hidden class and hiding the spinner.
            $('.cover-spin').hide(0)
            },
        success:function(data){
        var obj = JSON.parse(data);
        process_separate_data(obj)
        //obj[0].international_script;

        },
       });
}
function process_separate_data(obj)
{
   // console.log(obj.chart_container)
    if(obj.type =='international_recharge')
    {
        $('.international_recharge_chart').empty();
        $('.international_recharge_chart').append(obj.chart_container)
    }
    else if(obj.type =='domestic_recharge')
    {

        $('.domestic_recharge_chart').empty();
        $('.domestic_recharge_chart').append(obj.chart_container)
    }
    else if(obj.type =='pin')
    {
        $('.pin_chart').empty();
        $('.pin_chart').append(obj.chart_container)
    }
    else if(obj.type =='white_calling')
    {
        $('.white_calling_chart').empty();
        $('.white_calling_chart').append(obj.chart_container)
    }
    else if(obj.type =='sim')
    {
        $('.sim_chart').empty();
        $('.sim_chart').append(obj.chart_container)
    }
    else if(obj.type =='cargo')
    {
        $('.cargo_chart').empty();
        $('.cargo_chart').append(obj.chart_container)
    }
    else if(obj.type =='all')
    {
        $('.all_chart').empty();
        $('.all_chart').append(obj.chart_container)
    }
}

function process_data(obj)

{

        $("#top_reseller").empty();
        $('.all_chart').empty();
        $('.sim_chart').empty();
        $('.cargo_chart').empty();
        $('.international_recharge_chart').empty();
        $('.domestic_recharge_chart').empty();
        $('.pin_chart').empty();
        $('.white_calling_chart').empty();
        $('.all_chart').append(obj.all_container)
        $('.international_recharge_chart').append(obj.international_container)
        $('.domestic_recharge_chart').append(obj.domestic_container)
        $('.pin_chart').append(obj.pin_container)
        $('.white_calling_chart').append(obj.white_calling_container)
        $('.sim_chart').append(obj.sim_container)
        $('.cargo_chart').append(obj.cargo_container)
        $('#all_sale').text(obj.all_sale)
        $('#all_profit').text(obj.all_profit)
        $('#international_sale').text(obj.international_sale)
        $('#international_profit').text(obj.international_profit)
        $('#domestic_sale').text(obj.domestic_sale)
        $('#domestic_profit').text(obj.domestic_profit)
        $('#pin_sale').text(obj.pin_sale)
        $('#pin_profit').text(obj.pin_profit)
        $('#white_calling_sale').text(obj.white_calling_sale)
        $('#white_calling_profit').text(obj.white_calling_profit)

        $('#sim_sale').text(obj.sim_sale)
        $('#sim_profit').text(obj.sim_profit)
        $('#cargo_sale').text(obj.cargo_sale)
        $('#cargo_profit').text(obj.cargo_profit)
        var item = obj.top_reseller;
        for (var i = 0; i < item.length; i++){
            added_row = '<tr class="bg-ocean">'
        + '<td>' + item[i].reseller_id +  '</td>'
        + '<td>' + item[i].reseller_name +  '</td>'
        + '<td>' + item[i].sale +  '</td>'
        + '<td>' + item[i].profit +  '</td>'
        + '</tr>'
        $('#top_reseller').append(added_row)
        };


}

function get_data(start,end)
    {
        //console.log("Hello "+$(".start_date").val()+" "+$(".end_date").val())
        let url = "{{ route('get-report-data') }}";
        var formdata = new FormData();
        formdata.append('start_date',start)
        formdata.append('end_date',end)
        $.ajax({
        processData: false,
        contentType: false,
        url: url ,
        type:"POST",
        headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
         },
        data: formdata,
        beforeSend: function () {
            $('.cover-spin').show(0)
            },
        complete: function () { // Set our complete callback, adding the .hidden class and hiding the spinner.
            $('.cover-spin').hide(0)
            },
        success:function(data){
        var obj = JSON.parse(data);
        process_data(obj)
        //obj[0].international_script;

        },
       });
    }

</script>
<script>


</script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts">  </script>
{{-- <script src="{{ $international_chart->cdn() }}"></script>

{{ $international_chart->script() }} --}}
@endsection
