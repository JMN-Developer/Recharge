

@extends('front.layout.courier')
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
   <link rel="stylesheet" href="{{asset('css/loader/index.css')}}">
   <link rel="stylesheet" href="https://unpkg.com/izitoast/dist/css/iziToast.min.css">
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
               <div class="card-header text-center">
                  <a href="/"><img src="{{ asset('images/jm logo.png') }}" width="80px" height="auto"></a>
               </div>
               <div class="card-body">
                  <p class="login-box-msg">Mobile Recharge or Offers </p>
                  <div class="row">
                     <div class="col-md-6">
                        <div class="mb-3 receiver_inputs">
                            <label for="inputMobileNumber" class="form-label">Receiver Number</label>

                            <input type="text" id="receiverMobile" class="form-control receiver_input_form" name="number" placeholder="Receiver Number">

                            <button class="btn btn-info mt-3" style="width: 100%;" id="check_number">Check</button>

                            {{-- <button class="btn btn-primary" style="margin-bottom: 6px; float: right;">Verify</button> --}}
                         </div>

                         <div>

                         </div>

                         <div class="mb-3" id="amount_input_field" >
                            <div class="text-center" style="padding-top:20px">
                                <p style="font-weight:bold;color:red;font-size:18px">Minimum Amount: <span id="min_amount">20</span> <span style="padding-left:60px">Max Amount: <span id="max_amount">50</span></span></p>
                            </div>
                            <label for="inputMobileNumber" class="form-label">Amount</label>
                            <input type="text" id="amount" class="form-control " name="number" placeholder="Amount" style="width: 84%;">
                            <input type="hidden" id="exchange_rate">
                            <input type="hidden" id="currency_code">
                            <p id="calculation" style="padding-top:10px;color:red;font-weight:bold;"><p>

                            <button class="btn btn-info mt-3" style="width: 100%;">Check</button>

                            {{-- <button class="btn btn-primary" style="margin-bottom: 6px; float: right;">Verify</button> --}}
                         </div>
                     </div>

                     <div class="col-md-6">
                        <div class="last_recharge_table">
                           <div class="last_recharge_table_head text-center">
                              <h5><strong>Last 10 Recharge</strong></h5>
                           </div>
                           <div class="card-body table-responsive p-0">
                              <table class="table table-sm table-bordered table-hover">
                                 <thead>
                                    <tr class="table-danger">
                                       <th>Receiver</th>
                                       <th>Operator</th>
                                       <th>Cost</th>
                                       <th>Profit</th>
                                       <th>Action</th>
                                    </tr>
                                 </thead>
                                 <tbody>
                                    @foreach ($data as $item)
                                    <tr class="bg-ocean">
                                       <td>{{ $item->number }}</td>
                                       <td>{{ $item->operator }}</td>
                                       <td>{{ $item->cost }}</td>
                                       @if(auth()->user()->role == 'admin')
                                       <td>{{ $item->admin_com }}</td>
                                       @else
                                       <td>{{ $item->reseller_com }}</td>
                                       @endif
                                       <td> <a class="btn btn-success" href="recharge_invoice/{{ $item->id }}"> Invoice</a> </td>
                                    </tr>
                                    @endforeach
                                 </tbody>
                              </table>
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
<script src="https://unpkg.com/izitoast/dist/js/iziToast.min.js" type="text/javascript"></script>

@endsection
@section('scripts')
<!-- jQuery -->
<script src="{{asset('js/jquery.min.js')}}"></script>
<!-- Bootstrap -->
<script src="{{asset('js/bootstrap.bundle.min.js')}}"></script>
<!-- Theme JS -->
<script src="{{asset('js/admin.js')}}"></script>
<script src="{{asset('plugin/intl-tel-input/js/intlTelInput.js')}}"></script>
<!-- Custom JS -->
<script src="{{asset('js/custom.js')}}"></script>
@endsection
@section('js')
<script>
   // Vanilla Javascript



   $(document).ready(function() {
      $("#amount_input_field").hide();
    var input = document.querySelector("#receiverMobile");
  var intl =  window.intlTelInput(input,({
     // options here
   }));

   $("#check_number").click(function(){
    event.preventDefault();



    var formdata = new FormData();
    formdata.append('number',$('#receiverMobile').val());
    formdata.append('countryIso', intl.getSelectedCountryData().iso2);
      $.ajax({
        processData: false,
        contentType: false,
        url: "reloadly_operator_details",
        type:"POST",
        headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
         },
        data: formdata,
        beforeSend: function () {
            $('.cover-spin').show(0)
            },
        complete: function () { // Set our complete callback, adding the .hidden class and hiding the spinner.
            $('.cover-spin').hide(0)
            },
        success:function(response){
            $("#check_number").hide();
            $("#amount_input_field").show();
            $('.cover-spin').hide(0);
            $('#min_amount').text(response.minAmount);
            $('#max_amount').text(response.maxAmount);
            $("#exchange_rate").val(response.fx.rate);
            $("#currency_code").val(response.fx.currencyCode);

            if(response.status==true)
            {

                //console.log(response.message);
            }
            else
            {

            }
           //console.log(response.status);
           //alert('hello')

        },
       });


   });

    $("#amount").keyup(function(){
        //var countryData = intl.getSelectedCountryData();
        var exchange_rate = $('#exchange_rate').val();
        var value = this.value;
        var currencyCode = $("#currency_code").val();
        var calculation_text = (exchange_rate*value).toFixed(3)+" "+currencyCode+" will receive";
        //alert(calculation_text)
        $("#calculation").text(calculation_text);

        });
       $('.iti__flag-container').click(function() {

         var countryCode = $('.iti__selected-flag').attr('title');
         var countryCode = countryCode.replace(/[^0-9]/g,'')
         $('#receiverMobile').val("");
         $('#receiverMobile').val("+"+countryCode+" "+ $('#receiverMobile').val());
      });
   });
 </script>
@endsection

