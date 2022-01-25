@extends('front.layout.courier')
@section('header')
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>Transaction History</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{asset('css/fontawesome-free/css/all.min.css')}}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
  <link href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css" rel='stylesheet'>
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  {{-- <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet"> --}}
  <link href="https://cdn.datatables.net/1.11.4/css/dataTables.bootstrap.min.css" rel="stylesheet">

  <!-- Theme style -->
  <link rel="stylesheet" href="{{asset('css/admin.min.css')}}">

  <link rel="stylesheet" href="{{asset('css/style.css')}}">
<link rel="icon" href="https://jmnation.com/images/jm-transparent-logo.png"></head>
<style>
    table{
        text-align: center !important
    }
    .date_picker_pair {
    width: 90%;
    }
    .nav>li>a {
    position: relative;
    display: block;
    padding: 10px 15px;
}

.nav-tabs>li>a {
    margin-right: 2px;
    line-height: 1.42857143;
    border: 1px solid transparent;
    border-radius: 4px 4px 0 0;
}


    .nav-tabs>li.active>a, .nav-tabs>li.active>a:focus, .nav-tabs>li.active>a:hover {
        color: #555;
    cursor: default;
    background-color: #fff;
     border: 1px solid #ddd;
    border-bottom-color: transparent;

    }
    .nav-tabs>li {
    float: left;
    margin-bottom: -1px;

    }

/* .sorting_disabled{
    display: none !important;
} */
.sorting_asc{
background-image: none !important;
}
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
          <div class="col-12 mt-3">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title"><i class="fas fa-list" style="margin-right: 10px;"></i>
                  <strong>Transaction History</strong>
                </h3>
              </div>

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


                  @if(auth()->user()->role == 'admin')
                  <div class="col-md-3" style="margin-left:15px">
                    <div class="form-row align-items-center offer_select_option">
                        <label for="inlineFormCustomSelect" style="margin-bottom:14px">Choose Retailer</label>

                        <select  data-placeholder="Select an Option"  class="custom-select reseller" id="reseller" name="type">
                            <option></option>
                         @foreach ( $resellers as $data )
                             <option value="{{ $data->id }}">{{ $data->first_name." ".$data->last_name." (".$data->id.")" }}</option>
                         @endforeach
                          {{-- <option value="offer_table_two">Gift Card</option>
                          <option value="offer_table_two">Calling Card</option> --}}
                        </select>
                      </div>
                  </div>
                  @endif
                  <div class="col-md-2">
                    <input type="button"  onclick="filter()" value="Search" class="btn btn-success" style="margin-top:30px">
                  </div>
              </div>


              <!-- /.card-header -->

              <div class="p-3">



                {{-- <div class="converter_section mt-5">
                  <div class="converter_btn-1">
                    <button type="button" class="btn btn-info btn-sm">Copy</button>
                    <button type="button" class="btn btn-info btn-sm">Exel</button>
                    <button type="button" class="btn btn-info btn-sm">CSV</button>
                    <button type="button" class="btn btn-info btn-sm">PDF</button>
                  </div>
                  <div class="converter_search-1">
                    <div class="card-tools">
                      <div class="input-group input-group-sm">

                      </div>
                    </div>
                  </div>
                </div> --}}
                <div class="recharge_input_table table-responsive p-0">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="active">
                            <a href="#tab-all" data-toggle="tab">All</a>
                        </li>
                        <li>
                            <a href="#tab-international-recharge" data-toggle="tab">International Recharge</a>
                        </li>
                        <li>
                            <a href="#tab-domestic-recharge" data-toggle="tab">Domestic Recharge</a>
                        </li>

                        <li>
                            <a href="#tab-pin" data-toggle="tab">Pin</a>
                        </li>

                        <li>
                            <a href="#tab-white-calling" data-toggle="tab">White Calling</a>
                        </li>
                        <li>
                            <a href="#tab-sim" data-toggle="tab">Sim</a>
                        </li>
                        <li>
                            <a href="#tab-cargo" data-toggle="tab">Cargo</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab-all">
                            <table  id="tab-table-all" class="table table-info table-sm table-bordered table-hover table-head-fixed text-nowrap invoice_table table-striped">
                                <thead>
                                  <tr>
                                    <th style="background-color: black;color:white" >Transaction No</th>
                                    <th style="background-color: black;color:white" >Service</th>
                                    <th style="background-color: black;color:white">Description</th>
                                    <th style="background-color: black;color:white">Before Transaction</th>
                                    <th  style="background-color: black;color:white">After Transaction</th>
                                    <th  style="background-color: black;color:white">Amount</th>


                                  </tr>
                                </thead>
                                <tbody  id='change'>

                                </tbody>

                                <tfoot class="thead-dark" style="background-color: black" >
                                    <tr>
                                      @if(Auth::user()->role == 'admin')
                                      <th scope="col"></th>
                                      @endif
                                        <th scope="col"></th>
                                        <th scope="col"></th>
                                        <th scope="col"></th>
                                        <th scope="col"></th>
                                        <th scope="col"></th>
                                        <th scope="col"></th>



                                      </tr>

                                </tfoot>

                              </table>
                        </div>


                        <div class="tab-pane" id="tab-international-recharge">
                            <table  id="tab-table-international-recharge" class="table table-info table-sm table-bordered table-hover table-head-fixed text-nowrap invoice_table table-striped">
                                <thead>
                                  <tr>
                                    <th style="background-color: black;color:white"  >Transaction No</th>
                                    <th style="background-color: black;color:white" >Service</th>
                                    <th style="background-color: black;color:white">Description</th>
                                    <th style="background-color: black;color:white">Before Transaction</th>
                                    <th  style="background-color: black;color:white">After Transaction</th>
                                    <th  style="background-color: black;color:white">Amount</th>


                                  </tr>
                                </thead>
                                <tbody  id='change'>

                                </tbody>

                                <tfoot class="thead-dark" style="background-color: black" >
                                    <tr>
                                      @if(Auth::user()->role == 'admin')
                                      <th scope="col"></th>
                                      @endif
                                        <th scope="col"></th>
                                        <th scope="col"></th>
                                        <th scope="col"></th>
                                        <th scope="col"></th>
                                        <th scope="col"></th>
                                        <th scope="col"></th>



                                      </tr>

                                </tfoot>

                              </table>
                        </div>


                        <div class="tab-pane" id="tab-domestic-recharge">
                            <table  id="tab-table-domestic-recharge" class="table table-info table-sm table-bordered table-hover table-head-fixed text-nowrap invoice_table table-striped">
                                <thead>
                                  <tr>
                                    <th style="background-color: black;color:white"  >Transaction No</th>
                                    <th style="background-color: black;color:white" >Service</th>
                                    <th style="background-color: black;color:white">Description</th>
                                    <th style="background-color: black;color:white">Before Transaction</th>
                                    <th  style="background-color: black;color:white">After Transaction</th>
                                    <th  style="background-color: black;color:white">Amount</th>


                                  </tr>
                                </thead>
                                <tbody  id='change'>

                                </tbody>

                                <tfoot class="thead-dark" style="background-color: black" >
                                    <tr>
                                      @if(Auth::user()->role == 'admin')
                                      <th scope="col"></th>
                                      @endif
                                        <th scope="col"></th>
                                        <th scope="col"></th>
                                        <th scope="col"></th>
                                        <th scope="col"></th>
                                        <th scope="col"></th>
                                        <th scope="col"></th>



                                      </tr>

                                </tfoot>

                              </table>
                        </div>



                        <div class="tab-pane" id="tab-pin">
                            <table  id="tab-table-pin" class="table table-info table-sm table-bordered table-hover table-head-fixed text-nowrap invoice_table table-striped">
                                <thead>
                                  <tr>
                                    <th style="background-color: black;color:white"  >Transaction No</th>
                                    <th style="background-color: black;color:white" >Service</th>
                                    <th style="background-color: black;color:white">Description</th>
                                    <th style="background-color: black;color:white">Before Transaction</th>
                                    <th  style="background-color: black;color:white">After Transaction</th>
                                    <th  style="background-color: black;color:white">Amount</th>


                                  </tr>
                                </thead>
                                <tbody  id='change'>

                                </tbody>

                                <tfoot class="thead-dark" style="background-color: black" >
                                    <tr>
                                      @if(Auth::user()->role == 'admin')
                                      <th scope="col"></th>
                                      @endif
                                        <th scope="col"></th>
                                        <th scope="col"></th>
                                        <th scope="col"></th>
                                        <th scope="col"></th>
                                        <th scope="col"></th>
                                        <th scope="col"></th>



                                      </tr>

                                </tfoot>

                              </table>
                        </div>


                        <div class="tab-pane" id="tab-white-calling">
                            <table  id="tab-table-white-calling" class="table table-info table-sm table-bordered table-hover table-head-fixed text-nowrap invoice_table table-striped">
                                <thead>
                                  <tr>
                                    <th style="background-color: black;color:white"  >Transaction No</th>
                                    <th style="background-color: black;color:white" >Service</th>
                                    <th style="background-color: black;color:white">Description</th>
                                    <th style="background-color: black;color:white">Before Transaction</th>
                                    <th  style="background-color: black;color:white">After Transaction</th>
                                    <th  style="background-color: black;color:white">Amount</th>


                                  </tr>
                                </thead>
                                <tbody  id='change'>

                                </tbody>

                                <tfoot class="thead-dark" style="background-color: black" >
                                    <tr>
                                      @if(Auth::user()->role == 'admin')
                                      <th scope="col"></th>
                                      @endif
                                        <th scope="col"></th>
                                        <th scope="col"></th>
                                        <th scope="col"></th>
                                        <th scope="col"></th>
                                        <th scope="col"></th>
                                        <th scope="col"></th>



                                      </tr>

                                </tfoot>

                              </table>
                        </div>


                        <div class="tab-pane" id="tab-sim">
                            <table  id="tab-table-sim" class="table table-info table-sm table-bordered table-hover table-head-fixed text-nowrap invoice_table table-striped">
                                <thead>
                                  <tr>
                                    <th style="background-color: black;color:white"  >Transaction No</th>
                                    <th style="background-color: black;color:white" >Service</th>
                                    <th style="background-color: black;color:white">Description</th>
                                    <th style="background-color: black;color:white">Before Transaction</th>
                                    <th  style="background-color: black;color:white">After Transaction</th>
                                    <th  style="background-color: black;color:white">Amount</th>


                                  </tr>
                                </thead>
                                <tbody  id='change'>

                                </tbody>

                                <tfoot class="thead-dark" style="background-color: black" >
                                    <tr>
                                      @if(Auth::user()->role == 'admin')
                                      <th scope="col"></th>
                                      @endif
                                        <th scope="col"></th>
                                        <th scope="col"></th>
                                        <th scope="col"></th>
                                        <th scope="col"></th>
                                        <th scope="col"></th>
                                        <th scope="col"></th>



                                      </tr>

                                </tfoot>

                              </table>
                        </div>


                        <div class="tab-pane" id="tab-cargo">
                            <table  id="tab-table-cargo" class="table table-info table-sm table-bordered table-hover table-head-fixed text-nowrap invoice_table table-striped">
                                <thead>
                                  <tr>
                                    <th style="background-color: black;color:white"  >Transaction No</th>
                                    <th style="background-color: black;color:white" >Service</th>
                                    <th style="background-color: black;color:white">Description</th>
                                    <th style="background-color: black;color:white">Before Transaction</th>
                                    <th  style="background-color: black;color:white">After Transaction</th>
                                    <th  style="background-color: black;color:white">Amount</th>


                                  </tr>
                                </thead>
                                <tbody  id='change'>

                                </tbody>

                                <tfoot class="thead-dark" style="background-color: black" >
                                    <tr>
                                      @if(Auth::user()->role == 'admin')
                                      <th scope="col"></th>
                                      @endif
                                        <th scope="col"></th>
                                        <th scope="col"></th>
                                        <th scope="col"></th>
                                        <th scope="col"></th>
                                        <th scope="col"></th>
                                        <th scope="col"></th>



                                      </tr>

                                </tfoot>

                              </table>
                        </div>




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
  <script>
    /* Code By Webdevtrick ( https://webdevtrick.com ) */
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
<!-- jQuery -->
<script src="{{asset('js/jquery.min.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js" type="text/javascript"></script>
<script src="https://cdn.datatables.net/plug-ins/1.10.25/api/sum().js" type="text/javascript"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
{{-- <script src="https://cdn.datatables.net/1.11.4/js/dataTables.bootstrap.min.js"></script> --}}
<!-- Bootstrap -->
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
    //console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
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
    console.log(start_date,end_date);
    $('a[data-toggle="tab"]').on( 'shown.bs.tab', function (e) {
        $.fn.dataTable.tables( {visible: true, api: true} ).columns.adjust();
    } );


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

        columnDefs: [
    { "orderable": false, "targets": "_all" } // Applies the option to all columns
  ],
  paging:         false,
  scrollY:        600,
  scrollCollapse: true,
        ajax: {

            "url":'transaction-history',
            "type":'get',
            "data":{
                'type':'datatable',
                'start_date':$(".start_date").val(),
                'end_date':$(".end_date").val(),

            },
            dataSrc: function ( data ) {

            wallet = data.data[0].wallet;
            limit = data.data[0].limit;
            sim = data.data[0].sim;
            cargo = data.data[0].cargo;

            return data.data;
            } ,


            },
        deferRender: true,
        columns: [
            //   {data: 'sl_no'},

            {data:'transaction_id',name:'transaction_id',orderable:false},
            {data:'transaction_source',name:'transaction_source'},
            {data:'description',name:'description'},
            {data:'wallet_before_transaction',name:'wallet_before_transaction'},
            {data:'wallet_after_transaction',name:'wallet_after_transaction'},
            {data:'transaction_amount',name:'transaction_amount'},



  ],


  drawCallback: function () {
            var api = this.api();

        $( api.column( 2 ).footer() ).html(
            'Wallet = '+wallet
            );
            $( api.column( 3 ).footer() ).html(
                'Limit = '+limit
            );
            $( api.column( 4 ).footer() ).html(
                'Cargo = '+cargo
            );
            $( api.column( 5 ).footer() ).html(
                'Sim ='+sim
            );
           // datatable_sum(api, false);
        }


    });

    $('#tab-table-international-recharge').DataTable().search( 'International' ).draw();
    $('#tab-table-domestic-recharge').DataTable().search( 'Domestic' ).draw();
    $('#tab-table-pin').DataTable().search( 'Pin' ).draw();
    $('#tab-table-white-calling').DataTable().search( 'White Calling' ).draw();
    $('#tab-table-sim').DataTable().search( 'Sim' ).draw();
    $('#tab-table-cargo').DataTable().search( 'Cargo' ).draw();
    function datatable_sum(dt_selector, is_calling_first) {
        //col start from 0
        @if(Auth::user()->role == 'admin')
        $( dt_selector.column(5).footer() ).html(dt_selector.column( 5, {page:'all'} ).data().sum().toFixed(2));
        $( dt_selector.column(6).footer() ).html(dt_selector.column( 6, {page:'all'} ).data().sum().toFixed(2));
        @else
        $( dt_selector.column(4).footer() ).html(dt_selector.column( 4, {page:'all'} ).data().sum().toFixed(2));
        $( dt_selector.column(5).footer() ).html(dt_selector.column( 5, {page:'all'} ).data().sum().toFixed(2));
        @endif


    }

}
</script>
@endsection