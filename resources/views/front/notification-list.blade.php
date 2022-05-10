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
                    {{$user->notifications()->paginate(5)->links()}}
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

@endsection
