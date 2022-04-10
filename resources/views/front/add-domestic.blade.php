@extends('front.layout.master')
@section('header')

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Recharge Italy</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{asset('css/fontawesome-free/css/all.min.css')}}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{asset('css/admin.min.css')}}">

  <link rel="stylesheet" href="{{asset('css/style.css')}}">

  <link rel="icon" href="{{ asset('images/jm-transparent-logo.png') }}">
<link rel="icon" href="https://jmnation.com/images/jm-transparent-logo.png"></head>

@endsection


@section('content')
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid recharge-page">
        <div class="recharge-box">
          <div class="card card-outline card-primary">
            {{-- <div class="card-header text-center">
              <a href="index.html"><img src="{{ asset('images/jm logo.png') }}" width="80px" height="auto"></a>
            </div> --}}
            <div class="card-body">
              <h3 class="text-center mb-5">Indice Brand Richriche</h3>
              <div class="row">
                <div class="col-md-6">
                  <form action="/domestic_product" method="post">
                    @csrf
                    <div class="mb-3">
                      <label for="inputMobileNumber" class="form-label">EAN</label>
                      <input type="text" class="form-control" name="ean" value="" placeholder="Please enter ean">
                    </div>

                    <div class="mb-3">
                      <label for="inputMobileNumber" class="form-label">Commission</label>
                      <input type="text" class="form-control" name="commission" value="" placeholder="Please enter commission">
                    </div>

                    <div class="mb-3">
                      <input class="btn btn-success" type="submit" value="Add">
                    </div>

                  </form>
                </div>
              </div>




            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
        </div>
        <!-- /.login-box -->

      </div>
      <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>

@endsection



@section('js')
<script src="{{asset('js')}}/rechargeDtone.js?{{time()}}"></script>
@endsection
