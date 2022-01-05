@extends('front.layout.courier')
@section('header')
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>Print All Invioce</title>

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
                        <div class="page-title-box">
                            <h4 class="page-title">Dashboard</h4>
                            <div class="page-title-right">
                                <form class="float-sm-end mt-3 mt-sm-0">
                                    <div class="row g-2">
                                        <div class="col-md-auto">
                                            <div class="mb-1 mb-sm-0">
                                                <input type="text" class="form-control" id="dash-daterange" style="min-width: 210px;" />
                                            </div>
                                        </div>
                                        <div class="col-md-auto">
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class='uil uil-file-alt me-1'></i>Download
                                                    <i class="icon"><span data-feather="chevron-down"></span></i></button>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <a href="#" class="dropdown-item notify-item">
                                                        <i data-feather="mail" class="icon-dual icon-xs me-2"></i>
                                                        <span>Email</span>
                                                    </a>
                                                    <a href="#" class="dropdown-item notify-item">
                                                        <i data-feather="printer" class="icon-dual icon-xs me-2"></i>
                                                        <span>Print</span>
                                                    </a>
                                                    <div class="dropdown-divider"></div>
                                                    <a href="#" class="dropdown-item notify-item">
                                                        <i data-feather="file" class="icon-dual icon-xs me-2"></i>
                                                        <span>Re-Generate</span>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end page title -->

                <div class="row">
                    <div class="col-md-6 col-xl-2">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <span class="text-muted text-uppercase fs-12 fw-bold">Total</span>
                                        <p style="margin-top:7px"><span style="font-weight: bold">Sale:</span><span>100</span><span style="font-weight: bold;margin-left:10px">Profit:</span><span>200</span></p>
                                        
                                    </div>
                                   
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-xl-2">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <span class="text-muted text-uppercase fs-12 fw-bold">International</span>
                                        <p style="margin-top:7px"><span style="font-weight: bold">Sale:</span><span>100</span><span style="font-weight: bold;margin-left:10px">Profit:</span><span>200</span></p>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-xl-2">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <span class="text-muted text-uppercase fs-12 fw-bold">Domestic</span>
                                        <p style="margin-top:7px"><span style="font-weight: bold">Sale:</span><span>100</span><span style="font-weight: bold;margin-left:10px">Profit:</span><span>200</span></p>
                                    </div>
                                   
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-xl-2">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <span class="text-muted text-uppercase fs-12 fw-bold">Pin</span>
                                        <p style="margin-top:7px"><span style="font-weight: bold">Sale:</span><span>100</span><span style="font-weight: bold;margin-left:10px">Profit:</span><span>200</span></p>
                                    </div>
                                  
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-xl-2">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <span class="text-muted text-uppercase fs-12 fw-bold">White Calling</span>
                                        <p style="margin-top:7px"><span style="font-weight: bold">Sale:</span><span>100</span><span style="font-weight: bold;margin-left:10px">Profit:</span><span>200</span></p>
                                    </div>
                                  
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
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
                </div>

                <!-- stats + charts -->
                <div class="row">
                   

                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="dropdown float-end">
                                    {{-- <a href="#" class="dropdown-toggle arrow-none text-muted" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="uil uil-ellipsis-v"></i>
                                    </a> --}}
                                   
                                </div>
                                <h5 class="card-title mb-0 header-title">Sale Growth</h5>

                                <div id="sale-chart" class="apex-charts mt-3" dir="ltr"></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="dropdown float-end">
                                    {{-- <a href="#" class="dropdown-toggle arrow-none text-muted" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="uil uil-ellipsis-v"></i>
                                    </a> --}}
                                   
                                </div>
                                <h5 class="card-title mb-0 header-title">Profit Growth</h5>

                                <div id="profit-chart" class="apex-charts mt-3" dir="ltr"></div>
                            </div>
                        </div>
                    </div>

                    
                </div>
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
                                                <th scope="col">Reseller Contact No</th>
                                                <th scope="col">Total Sale</th>
                                               
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>#98754</td>
                                                <td>ASOS  High</td>
                                                <td>Otto B</td>
                                                <td>$79.49</td>
                                               
                                            </tr>
                                            <tr>
                                                <td>#98753</td>
                                                <td>Marco Lightweight Shirt</td>
                                                <td>Mark P</td>
                                                <td>$125.49</td>
                                              
                                            </tr>
                                            <tr>
                                                <td>#98752</td>
                                                <td>Half Sleeve Shirt</td>
                                                <td>Dave B</td>
                                                <td>$35.49</td>
                                                
                                            </tr>
                                            <tr>
                                                <td>#98751</td>
                                                <td>Lightweight Jacket</td>
                                                <td>Shreyu N</td>
                                                <td>$49.49</td>
                                               
                                            </tr>
                                            <tr>
                                                <td>#98750</td>
                                                <td>Marco Shoes</td>
                                                <td>Rik N</td>
                                                <td>$69.49</td>
                                                
                                            </tr>
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
  <script>
   
    (function(document) {
    'use strict';


    var TableFilter = (function(Arr) {

        var _input;

        function _onInputEvent(e) {
            _input = e.target;
            var tables = document.getElementsByClassName(_input.getAttribute('data-table'));
            Arr.forEach.call(tables, function(table) {
                Arr.forEach.call(table.tBodies, function(tbody) {
                    Arr.forEach.call(tbody.rows, _filter);
                });
            });
        }

        function _filter(row) {
            var text = row.textContent.toLowerCase(),
                val = _input.value.toLowerCase();
            row.style.display = text.indexOf(val) === -1 ? 'none' : 'table-row';
        }

        return {
            init: function() {
                var inputs = document.getElementsByClassName('light-table-filter');
                Arr.forEach.call(inputs, function(input) {
                    input.oninput = _onInputEvent;
                });
            }
        };
    })(Array.prototype);

    document.addEventListener('readystatechange', function() {
        if (document.readyState === 'complete') {
            TableFilter.init();
        }
    });

})(document);
  </script>
@endsection

@section('scripts')

<script src="{{ asset('dashboard/js/vendor.min.js')}}"></script>

<!-- optional plugins -->
<script src="{{ asset('dashboard/libs/moment/min/moment.min.js')}}"></script>
<script src="{{ asset('dashboard/libs/apexcharts/apexcharts.min.js')}}"></script>
<script src="{{ asset('dashboard/libs/flatpickr/flatpickr.min.js')}}"></script>

<!-- page js -->
<script src="{{ asset('dashboard/js/pages/dashboard.init.js')}}"></script>

<!-- App js -->
<script src="{{ asset('dashboard/js/app.min.js')}}"></script>
<!-- jQuery -->
{{-- <script src="{{asset('js/jquery.min.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js" type="text/javascript"></script>
<script src="https://cdn.datatables.net/plug-ins/1.10.25/api/sum().js" type="text/javascript"></script>
<!-- Bootstrap -->
<script src="{{asset('js/bootstrap.bundle.min.js')}}"></script>
<script src="{{asset('js/moment.min.js')}}"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
 --}}
<!-- Theme JS -->
<script src="{{asset('js/admin.js')}}"></script>
<!-- Custom JS -->
<script src="{{asset('js/custom.js')}}"></script>

@endsection

@section('js')
<script>


$(function() {

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

     fetch_table($(".start_date").val(),$(".end_date").val());


   // $('.invoice_table').DataTable().ajax.reload(null,false);
    // $("#start_date").val(start.format('YYYY-MM-DD'));
    // $("#end_date").val(end.format('YYYY-MM-DD'));
    //   var url = '/recharge/filebydate/'+start.format('YYYY-MM-DD')+'/'+end.format('YYYY-MM-DD');
      //window.location = url;
       //console.log('/filebydate/'+start.format('YYYY-MM-DD')+'/'+end.format('YYYY-MM-DD'));
    console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
  });


    // var formdata = new FormData();
    // formdata.append('')


    fetch_table($(".start_date").val(),$(".end_date").val())


});

function filter()
{

    fetch_table($(".start_date").val(),$(".end_date").val())
}

function fetch_table(start_date,end_date)
{

    var user_role = $("#user_role").val();

    var table = $('.invoice_table').DataTable();
    table.destroy();

    var table = $('.invoice_table').DataTable({

        processing: true,
        serverSide: true,

        ordering:false,
        searchPanes: {
            orderable: false
        },
        dom: 'Plfrtip',
        columnDefs: [
    { "orderable": false, "targets": "_all" } // Applies the option to all columns
  ],
        ajax: {

            "url":'get_all_invoice',
            "type":'POST',
            "data":{
                'start_date':$(".start_date").val(),
                'end_date':$(".end_date").val(),
                'type':$('#ExampleSelect option:selected').val(),
                'retailer_id':$('#reseller option:selected').val()

            }


            },
        deferRender: true,
        columns: [
            //   {data: 'sl_no'},

            {data:'txid',name:'txid',orderable:false},
            {data:'number',name:'number'},
            {data:'date',name:'date'},
            {data:'recharge_type',name:'type'},
            {data:'cost',name:'cost'},
            {data:'profit',name:'profit'},
            {data:'invoice',name:'invoice'}


  ],

  drawCallback: function () {
            var api = this.api();
            datatable_sum(api, false);
        }


    });
    function datatable_sum(dt_selector, is_calling_first) {
        //col start from 0
        $( dt_selector.column(4).footer() ).html(dt_selector.column( 4, {page:'current'} ).data().sum().toFixed(2));
        $( dt_selector.column(5).footer() ).html(dt_selector.column( 5, {page:'current'} ).data().sum().toFixed(2));


    }

}
</script>
@endsection
