

@extends('front.layout.master')
@section('header')
<head>
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <title>Recharge International</title>
   <!-- Google Font: Source Sans Pro -->
   <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
   <!-- Font Awesome -->
   <link rel="stylesheet" href="{{asset('css/fontawesome-free/css/all.min.css')}}">
   <!-- Theme style -->
   <link rel="stylesheet" href="{{asset('css/admin.min.css')}}">
   <link rel="stylesheet" href="{{asset('plugin/intl-tel-input/css/intlTelInput.min.css')}}">
   <meta name="csrf-token" content="{{ csrf_token() }}" />
   <link rel="stylesheet" href="{{asset('css/style.css')}}">
   <link rel="stylesheet" href="{{asset('css/autocomplete.css')}}">
   <link rel="stylesheet" href="{{asset('css/loader/index.css')}}">
   <link rel="stylesheet" href="https://unpkg.com/izitoast/dist/css/iziToast.min.css">
<link rel="icon" href="https://jmnation.com/images/jm-transparent-logo.png">

<style>
    .offer-card:hover{
        border: 2px solid rebeccapurple
    }

    .offer-card-after-click{
        border:2px  solid rebeccapurple
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

</style>

</head>

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
                  <a href="/"><img src="{{ asset('images/jm logo.png') }}" width="80px" height="auto"></a>
               </div> --}}
               <div class="card-body">
                  <p class="login-box-msg">Mobile Recharge or Offers </p>
                  <div class="row">
                     <div class="col-md-12">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3 receiver_inputs">
                                    <label for="inputMobileNumber" class="form-label">Receiver Number</label>

                                    <input type="text" id="receiverMobile" class="form-control receiver_input_form" name="number" placeholder="Receiver Number" onkeypress="return isNumberKey(event)">

                                    <div class="amount_input_field">
                                        <div id="international_amount">
                                            <label for="inputMobileNumber" class="form-label" style="margin-right: 43px">Amount</label>
                                            <select class="custom-select amount_list" name="amount" id="package" style="width: 85%">

                                             </select>
                                        </div>
                                        <div id="bangladesh_amount">
                                            <label for="inputMobileNumber" class="form-label" style="">Amount in BDT</label>
                                            <input type="text" id="amount" class="form-control" name="amount" placeholder="Amount" onkeypress="return isNumberKeyDecimal(event)"  style="width: 84%">
                                            <input type="hidden" id="operator_id" >
                                            <input type="hidden" id="bd_amount">
                                            <input type="hidden" id="exchange_rate">
                                            <input type="hidden" id="sku_id">

                                            <p style="color: red;font-weight:bold" id="bd_amount_field"><span id="main_amount"></span> Euro</p>
                                        </div>
                                             <label class="form-label">Service Charge in EURO</label>
                                             <input type="number" step="any" id="service" name="service" class="form-control" placeholder="Enter Service Charge (Optional)" style="width: 84%">

                                        <button class="btn btn-info mt-3" id="recharge_number" style="width: 85%;">Recharge</button>

                                        {{-- <button class="btn btn-primary" style="margin-bottom: 6px; float: right;">Verify</button> --}}
                                     </div>

                                     <div class="amount_input_field">
                                        <div class="mb-3 text-center">

                                            {{-- <img style="height:112px; margin-right:101px" id="operator_image" alt="Operator Logo Not Found" > --}}

                                         </div>
                                        <div class="mb-3" >
                                            <div class="text-center" style="background:#C62604;width:84%;margin-bottom:10px">
                                                <p style="padding:5px;font-weight:bold;color:white;font-size:16px">Operator Name: <span id="operator_name"></span> </p>
                                            </div>

                                            <div class="text-center" style="background:#C62604;width:84%;margin-bottom:10px">
                                                <p style="padding:5px;font-weight:bold;color:white;font-size:16px">Profit : <span id="operator_name">{{ auth()->user()->admin_international_recharge_commission }}%</span> </p>
                                            </div>

                                         </div>
                                    </div>


                                 <button class="btn btn-info mt-3" style="width: 84%;" id="check_number">Continue</button>

                            </div>
                        </div>
                        <div class="col-md-6">

                                <div class="last_recharge_table">
                                   <div class="last_recharge_table_head text-center">
                                      <h5><strong>Last 5 Recharge</strong></h5>
                                   </div>
                                   <div class="card-body table-responsive p-0">
                                      <table class="table table-sm table-bordered table-hover">
                                         <thead>
                                            <tr class="table-danger">
                                               <th>Receiver</th>
                                               <th>Operator</th>
                                               <th>Amount</th>
                                               <th>Profit</th>
                                               <th class="text-center">Date</th>
                                               <th class="text-center" >Action</th>
                                               {{-- <th>Cost</th>
                                               <th>Profit</th>
                                               <th>Action</th> --}}
                                            </tr>
                                         </thead>
                                         <tbody>
                                            @foreach ($data as $item)
                                            <tr class="bg-ocean">
                                               <td>{{ $item->number }}</td>
                                               <td>{{ $item->operator }}</td>
                                               <td>{{ $item->amount }}</td>
                                               @if(auth()->user()->role == 'admin')
                                               <td>{{ $item->admin_com+$item->discount }}</td>
                                               @else
                                               <td>{{ $item->reseller_com }}</td>
                                               @endif
                                               <td class="text-center">{{ $item->created_at }}</td>
                                               <td class="text-center"> <a class="btn btn-success" href="recharge_invoice/{{ $item->id }}"> Invoice</a> </td>
                                               {{-- <td>{{ $item->cost }}</td>

                                               <td> <a class="btn btn-success" href="recharge_invoice/{{ $item->id }}"> Invoice</a> </td> --}}
                                            </tr>
                                            @endforeach
                                         </tbody>
                                      </table>
                                   </div>
                                </div>

                        </div>


                        </div>

                     </div>






                  </div>
                  <div class="card card-outline card-primary offer_section" style="margin-top:10px ">
                    <div class="card-body">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="active">
                                <a href="#internet" data-toggle="tab">Internet</a>
                            </li>

                            <li>
                                <a href="#combo" data-toggle="tab">Combo</a>
                            </li>







                        </ul>
                        <div class="tab-content offer_list">


                                <div  class="tab-pane active" id="internet">
                                    <div class="row internet">

                                    </div>
                                </div>

                                <div  class="tab-pane" id="combo">
                                    <div class="row combo">

                                    </div>
                                </div>
                                <div  class="tab-pane " id="voice">
                                    <div class="row voice">

                                    </div>


                                </div>


                        </div>


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
<!-- /.content-wrapper -->


@endsection
@section('scripts')

<script src="{{asset('plugin/intl-tel-input/js/intlTelInput.js')}}"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>


@endsection
@section('js')
<script src="{{asset('js')}}/rechargeDtone.js?{{time()}}"></script>

@endsection
