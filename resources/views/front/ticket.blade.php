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
                  <strong>Ticket</strong>
                </h3>
              </div>




              <!-- /.card-header -->

              <div class="p-3">
                <div>
                    <a class="btn btn-primary" href='{{ route('add-ticket-view') }}' style="margin-bottom: 20px">Open New Ticket</a>
                </div>
                <div class="recharge_input_table table-responsive p-0">
                  <table class="table table-info table-sm table-bordered table-hover table-head-fixed text-nowrap invoice_table table-striped">
                    <thead>
                      <tr>
                        @if(Auth::user()->role == 'admin')
                        <th style="background-color: black;color:white"  >Reseller</th>
                        @endif
                        <th style="background-color: black;color:white">Ticket Number</th>
                        <th style="background-color: black;color:white">Service</th>
                        <th style="background-color: black;color:white">Last Response</th>
                        <th style="background-color: black;color:white">Status</th>
                        <th style="background-color: black;color:white">Action</th>


                      </tr>
                    </thead>
                    <tbody  id='change'>

                    </tbody>


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

@endsection

@section('scripts')
<!-- jQuery -->
<script src="{{asset('js/jquery.min.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
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
let get_ticket_data = '{{route("get-ticket-data")}}';

$(function() {





    $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });


    fetch_table()


});

function fetch_table()
{



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

            "url":get_ticket_data,
            "type":'get',
            },
        deferRender: true,
        columns: [
            //   {data: 'sl_no'},
            @if(Auth::user()->role == 'admin')
            {data:'reseller_name',name:'reseller_name',orderable:false},
            @endif
            {data:'ticket_no',name:'ticket_no'},
            {data:'service_name',name:'service_name'},
            {data:'last_response',name:'last_response'},
            {data:'status',name:'status'},
            {data:'action',name:'action'},


  ],


    });


}
</script>
@endsection
