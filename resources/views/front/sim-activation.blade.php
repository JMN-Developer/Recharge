@extends('front.layout.master')
@section('header')
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
  <title>SIM List</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{asset('css/fontawesome-free/css/all.min.css')}}">
  <!-- Select2 -->
  <link rel="stylesheet" href="{{asset('css/select2.min.css')}}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{asset('css/admin.min.css')}}">
  <link href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css" rel='stylesheet'>

  <link rel="stylesheet" href="{{asset('css/style.css')}}">
<link rel="icon" href="https://jmnation.com/images/jm-transparent-logo.png">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
<link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
  <style>
    table.dataTable thead .sorting_asc{
    background-image: none !important;
}
    </style>
</head>
@endsection

@section('content')
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        @if(auth()->user()->role == 'admin')
        <div class="modal" tabindex="-1" role="dialog" id="sim_edit">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Edit</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <form action="update-sim" method="POST">
                @csrf
              <div class="modal-body">
                <div class="row">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label>Oparetor</label>
                      <select class="form-control select2" id="operator_name" name="operator" style="width: 100%;" required>
                        @foreach ($operator as $op)
                          <option value="{{ $op->operator }}">{{ $op->operator }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>

                  <div class="col-md-12">
                    <div class="mb-3">
                      <label for="inputLastName" class="form-label">SIM Number</label>
                      <input type="text" class="form-control" name="sim_number" id="sim_number" required >
                      <input type="hidden" name="id" id="hidden_id">
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="mb-3">
                      <label for="inputLastName" class="form-label">ICCID Number</label>
                      <input type="text" class="form-control" name="iccid" id="iccid" required  >
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="form-group">
                      <label>Re-Seller</label>
                      <select class="form-control select2" name="re_seller" style="width: 100%;" id="reseller" required>
                        @foreach ($user as $item)
                            <option value="{{ $item->id }}">{{ $item->first_name." ".$item->last_name.'('.$item->user_id.')' }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="mb-3">
                      <label for="inputLastName" class="form-label">Buy Price</label>
                      <input type="text" class="form-control" name="original_price" id="original_price" required >
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="mb-3">
                      <label for="inputLastName" class="form-label">Selling Price</label>
                      <input type="text" class="form-control" name="buy_price" id="buy_price" required >
                    </div>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Save changes</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              </div>
              </form>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-12">
            <div class="card mt-3">
              <div class="card-header mb-3">
                <h3 class="card-title"><strong>Add to List</strong></h3>
              </div>
              <!-- /.card-header -->
              <form action="add-sim" method="POST">
                @csrf
                <div class="row px-3">
                    <div class="col-md-3">
                      <div class="form-group">
                        <label>Oparetor</label>
                        <select class="form-control select2" name="operator" style="width: 100%;" required>
                          @foreach ($operator as $operator)
                            <option value="{{ $operator->operator }}">{{ $operator->operator }}</option>
                          @endforeach
                        </select>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="mb-3">
                        <label for="inputLastName" class="form-label">SIM Number</label>
                        <input type="text" class="form-control" name="sim_number" required >
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="mb-3">
                        <label for="inputLastName" class="form-label">ICCID Number</label>
                        <input type="text" class="form-control" name="iccid" required >
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="form-group">
                        <label>Re-Seller</label>
                        <select class="form-control select2" name="re_seller" style="width: 100%;" required>
                          @foreach ($user as $item)
                              <option value="{{ $item->id }}">{{ $item->first_name." ".$item->last_name.'('.$item->user_id.')' }}</option>
                          @endforeach
                        </select>
                      </div>
                    </div>
                </div>
                <div class="row px-3">
                    <div class="col-md-3">
                      <div class="mb-3">
                        <label for="inputLastName" class="form-label">Buy Price</label>
                        <input type="text" class="form-control" name="original_price" required >
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="mb-3">
                        <label for="inputLastName" class="form-label">Selling Price</label>
                        <input type="text" class="form-control" name="buy_price" required >
                      </div>
                    </div>
                    <div class="col-md-2">
                        <input type="submit" class="form-control btn btn-success" value="Add" style="margin-top: 31px;" required>
                    </div>
                </div>
              </form>
            </div>
          </div>
        </div>
        @endif
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title"><i class="fas fa-list" style="margin-right: 10px;"></i><strong>Sale List</strong></h3>

                <div class="card-tools">

                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-1" style="margin-top: 10px">
                <table class="table table-info table-sm table-bordered table-hover table-head-fixed text-nowrap sim_table">
                  <thead  style="text-align: center">
                    <tr>
                      <th style="background: #faaeae;">SL</th>
                      <th style="background: #faaeae;">Operator</th>
                      @if(Auth::user()->role == 'admin')
                      <th style="background: #faaeae;">Reseller</th>
                      <th style="background: #faaeae;">Status</th>
                     @endif
                      <th style="background: #faaeae;">ICCID Number</th>
                      <th style="background: #faaeae;">SIM Number</th>
                      <th style="background: #faaeae;">Buy Date</th>
                      <th style="background: #faaeae;">Sell Price</th>
                      <th class="text-center" style="background: #faaeae;">Action</th>
                      @if(Auth::user()->role == 'admin')
                      <th style="background: #faaeae;">Edit</th>
                      @endif
                    </tr>
                  </thead>
                  <tbody style="text-align: center">

                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
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
var text = row.textContent.toLowerCase(), val = _input.value.toLowerCase();
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

<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js" type="text/javascript"></script>
<script src="https://cdn.datatables.net/plug-ins/1.10.25/api/sum().js" type="text/javascript"></script>
@endsection

@section('js')
<script>
  var user = {!! auth()->user() !!};
  function sim_edit(id)
    {
      $("#hidden_id").val(id)
      $.ajax({
            type: "GET",
            dataType: "json",
            url: 'sim_edit',
            data: {'sim_id': id},
            success: function(data){
              $("#sim_number").val(data.sim_number)
              $("#iccid").val(data.iccid)
              $("#buy_price").val(data.buy_price)
              $("#original_price").val(data.original_price)
              $("#operator_name").val(data.operator).change();
              $("#reseller").val(data.reseller_id).change();
             // $('#operator_name option[value='+data.operator+']').attr('selected','selected');
              $('#sim_edit').modal('show')
            }
        });

    }

</script>
<script>
  $(function () {

    get_data();
      //Initialize Select2 Elements
      $('.select2').select2()

      //Initialize Select2 Elements
      $('.select2bs4').select2({
        theme: 'bootstrap4'
      })
      function get_data()
      {




var table = $('.sim_table').DataTable();
table.destroy();

var table = $('.sim_table').DataTable({

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

"url":"{{ route('sim-activation') }}",
"type":'GET',
'data':{
  'type':'datatable'
}

},

    deferRender: true,
    columns: [
        //   {data: 'sl_no'},

        {data:'id',name:'id',orderable:false},
        {data:'operator',name:'operator'},
        @if(Auth::user()->role == 'admin')
        {

         name:'reseller',
         data:'reseller',

        },
        {

       name:'status',
       data:'status',

      },
        @endif
        {data:'iccid',name:'iccid'},
        {data:'sim_number',name:'sim_number'},
        {data:'buy_date',name:'buy_date'},
        {data:'buy_price',name:'buy_price'},
        {data:'action',name:'action'},
        @if(Auth::user()->role == 'admin')
        {data:'edit_column',name:'edit_column'},
        @endif


        ],



    });


}


    })

</script>
@endsection
