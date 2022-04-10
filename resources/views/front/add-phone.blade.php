@extends('front.layout.master')
@section('header')
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Add Phone</title>

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
<div class="content-wrapper">
	<div class="container-fluid">
		<form enctype="multipart/form-data" method="post" action="{{route('add-phone')}}">
			@csrf
			@if (session('status'))
              	<div class="alert alert-success">
                  	{{ session('status') }}
              	</div>
          	@endif
		  <div class="form-group">
		    <label for="formGroupExampleInput">Phone Name</label>
		    <input type="text" class="form-control" id="formGroupExampleInput" name="phone" placeholder="Example input">
		  </div>
		  <div class="form-group">
		    <label for="formGroupExampleInput">Price</label>
		    <input type="number" class="form-control" id="formGroupExampleInput" name="price" placeholder="Example input">
		  </div>
		  <div class="form-group">
		    <label for="formGroupExampleInput">Discount Price</label>
		    <input type="number" class="form-control" id="formGroupExampleInput" name="dis_price" placeholder="Example input">
		  </div>
		  <div class="form-group">
		    <label for="formGroupExampleInput">Image</label>
		    <input type="file" class="form-control" id="formGroupExampleInput" name="image" placeholder="Example input">
		  </div>
		  <div class="mb-3">
		  	<label for="exampleFormControlTextarea1" class="form-label">Example textarea</label>
		  	<textarea class="form-control" id="exampleFormControlTextarea1" name="description" rows="3"></textarea>
		  </div>
		  <button class="btn btn-success" type="submit">Add Phone</button>
		</form>
	</div>
</div>
@endsection

@section('js')

<script src="{{asset('js/tempusdominus-bootstrap-4.min.js')}}"></script>

<script src="{{asset('js/bs-custom-file-input.min.js')}}"></script>

@endsection
