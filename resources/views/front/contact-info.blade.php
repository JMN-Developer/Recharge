@extends('front.layout.master')
@section('header')
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Settings</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="css/fontawesome-free/css/all.min.css">

  <!-- Theme style -->
  <link rel="stylesheet" href="css/admin.min.css">

  <link rel="stylesheet" href="css/style.css">
  <link rel="icon" href="{{ asset('images/jm-transparent-logo.png') }}">
<link rel="icon" href="https://jmnation.com/images/jm-transparent-logo.png">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
</head>

@endsection

@section('content')

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row">


        </div>
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
    <div class="main_content pb-5">
      <div class="container-fluid">
        <div class="row mt-4 d-flex justify-content-center">
          <div class="col-10 px-4">
            <div class="card">
              <div class="card-body ">

                <p style = 'font-weight:bold'>CONTO INTESA SAN PAOLO:<span>BHUIYAN MOHAMMAD MAHADI HASSAN <br> IT98R0538702684000003767767</span> </p>
              </div>
              <!-- /.card-body -->
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
  <!-- /.content-wrapper -->


  <!-- Modal -->

  <!-- /.Modal -->
@endsection

@section('scripts')


<!--
=======================
  REQUIRED SCRIPTS
=======================
-->

<!-- jQuery -->
<script src="js/jquery.min.js"></script>
<!-- Bootstrap -->
<script src="js/bootstrap.bundle.min.js"></script>
<!-- Theme JS -->
<script src="js/admin.js"></script>
<!-- Custom JS -->
<script src="js/custom.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>
  <script>
    $(".confirm").confirm();
  </script>
@endsection
