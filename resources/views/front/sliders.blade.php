@extends('front.layout.master')
@section('header')
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Print All Invioce</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{asset('css/fontawesome-free/css/all.min.css')}}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{asset('css/admin.min.css')}}">

  <link rel="stylesheet" href="{{asset('css/style.css')}}">
<link rel="icon" href="https://jmnation.com/images/jm-transparent-logo.png"></head>

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
                  <strong>Slider List</strong>
                </h3>
              </div>
              <!-- /.card-header -->

              <div class="p-3">
                <div class="recharge_input_table table-responsive p-0" style="height: 550px;">
                  <table class="table table-info table-sm table-bordered table-hover table-head-fixed text-nowrap">
                    <thead>
                      <tr>
                        <th style="background: #faaeae;">id</th>
                        <th style="background: #faaeae;">Link</th>
                        <th style="background: #faaeae;">Image</th>
                        <th style="background: #faaeae;">Action</th>
                      </tr>
                    </thead>
                    <tbody id='change'>
                      @foreach ($data as $item)
                      <tr class="bg-ocean">
                        <td>
                          {{ $item->id }}
                        </td>
                        <td>{{ $item->link }}</td>
                        <td> <img src="{{ asset('storage/'.$item->image) }}" class="img-fluid" style="width: 200px "> </td>
                        <td>
                          <a class="btn btn-primary" href="{{ route('slider-edit', $item->id) }}"> Edit</a>
                          <a class="btn btn-danger" href="{{ route('slider-delete', $item->id) }}"> Delete</a>
                        </td>
                      </tr>
                      @endforeach
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
<
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />


@endsection

@section('js')
<script>
$(function() {
  var start = moment().subtract(29, 'days');
  var end = moment();
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


      var url = '/filebydate/'+start.format('YYYY-MM-DD')+'/'+end.format('YYYY-MM-DD');
      window.location = url;
       console.log('/filebydate/'+start.format('YYYY-MM-DD')+'/'+end.format('YYYY-MM-DD'));
    console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end).format('YYYY-MM-DD');
  });
});
</script>
@endsection
