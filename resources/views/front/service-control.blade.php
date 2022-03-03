@extends('front.layout.courier')
@section('header')
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Retailer Action</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{asset('css/fontawesome-free/css/all.min.css')}}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{asset('css/admin.min.css')}}">

  <link rel="stylesheet" href="{{asset('css/style.css')}}">
   <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js" ></script>

    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
<link rel="icon" href="https://jmnation.com/images/jm-transparent-logo.png"></head>
@endsection

@section('content')
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card mt-3">
              <div class="card-header">
                <h3 class="card-title"><i class="fas fa-list" style="margin-right: 10px;"></i><strong>Retailer Action</strong></h3>


              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-bordered table-sm table-hover table-head-fixed text-nowrap text-center">
                  <thead>
                    <tr>
                        <th>Service Name</th>
                        <th>Action</th>
                    </tr>

                  </thead>
                  <tbody>
                      @foreach($datas as $data)
                      <tr>
                        <td>{{ $data->service_name }}</td>
                        <td>
                            <input data-id="{{$data->id}}" class="toggle-class service" type="checkbox" data-onstyle="success" data-offstyle="danger" data-toggle="toggle" data-on="Active" data-off="InActive" {{ $data->permission ? 'checked' : '' }}>
                          </td>
                        </tr>
                    @endforeach
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


  $(function() {
    $('.service').change(function() {
        var check = 0;

        var status = $(this).prop('checked') == true ? 1 : 0;
        var user_id = $(this).data('id');


        $.ajax({
            type: "GET",
            dataType: "json",
            url: 'service-status-update',
            data: {'status': status, 'user_id': user_id},
            success: function(data){

                if(data.message =='error')
                {
                   // $(this).prop('unchecked')
                    $('.service').bootstrapToggle('off')

                    // alert('You do not have this access')
                }
            }
        });
    })
  })
  </script>
  <script>
  $(function() {
    $('.sim').change(function() {
        var status = $(this).prop('checked') == true ? 1 : 0;
        var user_id = $(this).data('id');


        $.ajax({
            type: "GET",
            dataType: "json",
            url: '/retailer/changeSim',
            data: {'status': status, 'user_id': user_id},
            success: function(data){
              console.log(data.success)
            }
        });
    })
  })
  </script>
  <script>
  $(function() {
    $('.cargo').change(function() {
        var status = $(this).prop('checked') == true ? 1 : 0;
        var user_id = $(this).data('id');
        console.log('hello');

        $.ajax({
            type: "GET",
            dataType: "json",
            url: '/retailer/changeCargo',
            data: {'status': status, 'user_id': user_id},
            success: function(data){
              console.log(data.success)
            }
        });
    })
  })
  </script>
  <script>
  $(function() {
    $('.phone').change(function() {
        var status = $(this).prop('checked') == true ? 1 : 0;
        var user_id = $(this).data('id');
        console.log('hello');

        $.ajax({
            type: "GET",
            dataType: "json",
            url: '/retailer/changePhone',
            data: {'status': status, 'user_id': user_id},
            success: function(data){
              console.log(data.success)
            }
        });
    })
  })
  </script>
  <script>
  $(function() {
    $('.reseller').change(function() {
        var status = $(this).prop('checked') == true ? 1 : 0;
        var user_id = $(this).data('id');
        console.log('hello');

        $.ajax({
            type: "GET",
            dataType: "json",
            url: '/retailer/changeReseller',
            data: {'status': status, 'user_id': user_id},
            success: function(data){
              console.log(data.success)
            }
        });
    })
  })
  </script>
  <script>
    $(function() {
      $('.pin').change(function() {
          var status = $(this).prop('checked') == true ? 1 : 0;
          var user_id = $(this).data('id');


          $.ajax({
              type: "GET",
              dataType: "json",
              url: '/retailer/changePin',
              data: {'status': status, 'user_id': user_id},
              success: function(data){
                console.log(data.success)
              }
          });
      })
    })
    </script>
@endsection

@section('scripts')
<!-- jQuery -->

<!-- Bootstrap -->
<script src="{{asset('js/bootstrap.bundle.min.js')}}"></script>
<!-- Theme JS -->
<script src="{{asset('js/admin.js')}}"></script>
<script src="{{asset('plugin/intl-tel-input/js/intlTelInput.js')}}"></script>
<!-- Custom JS -->
<script src="{{asset('js/custom.js')}}"></script>
@endsection
