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
  <!-- Theme style -->
  <link rel="stylesheet" href="{{asset('css/admin.min.css')}}">

  <link rel="stylesheet" href="{{asset('css/style.css')}}">
<link rel="icon" href="https://jmnation.com/images/jm-transparent-logo.png"></head>
<style>
    .date_picker_pair {
    width: 90%;
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
                  <strong>Invoice List</strong>
                </h3>
              </div>

              <div class="row" style="margin-left:10px;margin-top:20px">
                <div class="col-md-4">
                    <div class="date_picker_pair mb-3">
                        <label for="inputSearchDate" class="form-label">Select Date</label>
                        <input type="text" class="form-control" name="daterange" id="inputSearchDate" value="01/01/2018 - 01/15/2018">
                        <input type="hidden" class='start_date'>
                        <input type="hidden" class='end_date'>
                        <input type="hidden" id="user_role" value="{{ auth()->user()->role }}">
                        <!-- <input type="text" name="daterange" value="01/01/2018 - 01/15/2018" /> -->
                      </div>
                  </div>

                  <div class="col-md-4">
                    <div class="form-row align-items-center offer_select_option">
                        <label for="inlineFormCustomSelect" style="margin-bottom:14px">Tipo Ricarica</label>

                        <select class="custom-select" id="ExampleSelect" name="type">
                          <option value="all">All</option>
                          <option value="International">Recharge International</option>
                          <option value="Domestic">Recharge Domestic</option>
                          {{-- <option value="offer_table_two">Gift Card</option>
                          <option value="offer_table_two">Calling Card</option> --}}
                        </select>
                      </div>
                  </div>
                  <div class="col-md-2">
                    <input type="button"  onclick="filter()" value="Search" class="btn btn-success" style="margin-top:30px">
                  </div>
              </div>


              <!-- /.card-header -->

              <div class="p-3">


                <div class="agent_amount_table">
                  <table class="table table-sm table-bordered">
                    <thead class="table-danger">
                      <tr>
                        <th>Total Cost</th>
                        <th>Total Profit</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr class="bg-sky">
                        <td>{{ $cost }} &euro;</td>
                        <td>{{ $profit }} &euro;</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                <div class="converter_section mt-5">
                  <div class="converter_btn-1">
                    <button type="button" class="btn btn-info btn-sm">Copy</button>
                    <button type="button" class="btn btn-info btn-sm">Exel</button>
                    <button type="button" class="btn btn-info btn-sm">CSV</button>
                    <button type="button" class="btn btn-info btn-sm">PDF</button>
                  </div>
                  <div class="converter_search-1">
                    <div class="card-tools">
                      <div class="input-group input-group-sm">
                        {{-- <input type="text" name="table_search" data-table="table-info" class="form-control float-right light-table-filter" placeholder="Search"> --}}

                        {{-- <div class="input-group-append">
                          <button type="submit" class="btn btn-default">
                            <i class="fas fa-search"></i>
                          </button>
                        </div> --}}
                      </div>
                    </div>
                  </div>
                </div>
                <div class="recharge_input_table table-responsive p-0">
                  <table class="table table-info table-sm table-bordered table-hover table-head-fixed text-nowrap invoice_table">
                    <thead>
                      <tr>
                        <th style="background: #faaeae;">Requestld</th>
                        <th style="background: #faaeae;">Numero</th>
                        <th style="background: #faaeae;">Data</th>
                        <th style="background: #faaeae;">Genere</th>
                        <th style="background: #faaeae;">Importo</th>
                        <th style="background: #faaeae;">Profit</th>

                      </tr>
                    </thead>
                    <tbody  id='change'>

                    </tbody>

                    <tfoot>
                        <tr>
                            <th scope="col"></th>
                            <th scope="col"></th>
                            <th scope="col"></th>
                            <th scope="col">Total</th>
                            <th scope="col">0</th>
                            <th scope="col">0</th>

                          </tr>

                    </tfoot>

                  </table>
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
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js" type="text/javascript"></script>
<script src="https://cdn.datatables.net/plug-ins/1.10.25/api/sum().js" type="text/javascript"></script>
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
        order:false,


        ajax: {

            "url":'get_all_invoice',
            "type":'POST',
            "data":{
                'start_date':$(".start_date").val(),
                'end_date':$(".end_date").val(),
                'type':$('#ExampleSelect option:selected').val()
            }


            },
        deferRender: true,
        columns: [
            //   {data: 'sl_no'},

            {data:'txid',name:'txid'},
            {data:'number',name:'number'},
            {data:'created_at',name:'created_at'},
            {data:'type',name:'type'},
            {data:'cost',name:'cost'},
            {data:'profit',name:'profit'},


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
