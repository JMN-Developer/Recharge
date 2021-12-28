

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
                     <div class="col-md-12">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3 receiver_inputs">
                                    <label for="inputMobileNumber" class="form-label">Receiver Number</label>

                                    <input type="text" id="receiverMobile" class="form-control receiver_input_form" name="number" placeholder="Receiver Number">

                                    <div class="amount_input_field">
                                        <label for="inputMobileNumber" class="form-label" style="margin-right: 43px">Amount</label>
                                            <select class="custom-select amount_list" name="amount" id="package" style="width: 85%">

                                             </select>

                                             <label class="form-label">Service Charge in EURO</label>
                                             <input type="number" step="any" id="service" name="service" class="form-control" placeholder="Enter Service Charge (Optional)" style="width: 84%">

                                        <button class="btn btn-info mt-3" id="recharge_number" style="width: 85%;">Recharge</button>

                                        {{-- <button class="btn btn-primary" style="margin-bottom: 6px; float: right;">Verify</button> --}}
                                     </div>


                                 <button class="btn btn-info mt-3" style="width: 84%;" id="check_number">Continue</button>

                            </div>
                        </div>
                            <div class="col-md-6 amount_input_field">
                                <div class="mb-3 text-center">

                                    <img style="height:112px; margin-right:101px" id="operator_image" alt="Operator Logo Not Found" >

                                 </div>
                                <div class="mb-3" >
                                    <div class="text-center" style="padding-top:10px;padding-bottom:1px;background:#C62604;width:84%;margin-bottom:10px">
                                        <p style="font-weight:bold;color:white;font-size:18px">Operator Name: <span id="operator_name"></span> </p>
                                    </div>

                                 </div>
                            </div>
                        </div>

                     </div>

                     <div class="col-md-12"  style="margin-top: 100px">
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
                                       <th>Amount</th>
                                       <th>Profit</th>
                                       <th>Action</th>
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
                                       <td>{{ $item->admin_com }}</td>
                                       @else
                                       <td>{{ $item->reseller_com }}</td>
                                       @endif
                                       <td> <a class="btn btn-success" href="recharge_invoice/{{ $item->id }}"> Invoice</a> </td>
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
    var toast = document.querySelector('.iziToast');
        var message = sessionStorage.getItem('message');
        sessionStorage.removeItem('message');

        if(toast)
                {
                iziToast.hide({}, toast);
                }
        if ( sessionStorage.getItem('error') ) {
            sessionStorage.removeItem('error');

                iziToast.error({
                    backgroundColor:"#D12C09",
                    messageColor:'white',
                    iconColor:'white',
                    titleColor:'white',
                    titleSize:'18',
                    messageSize:'18',
                    color:'white',
                    position:'topCenter',
                    timeout: 30000,
                    title: 'Error',
                    message: message,


                });

                //console.log(response.message);

            }

            if ( sessionStorage.getItem('success') ) {
            sessionStorage.removeItem('success');


            iziToast.success({
                    backgroundColor:"Green",
                    messageColor:'white',
                    iconColor:'white',
                    titleColor:'white',
                    titleSize:'18',
                    messageSize:'18',
                    color:'white',
                    position:'topCenter',
                    timeout: 30000,
                    title: 'Success',
                    message: message,

                });
                //console.log(response.message);

            }


     $(".amount_input_field").hide();
    $("#calculation_section").hide();
    $("#recharge_number").hide();
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
        url: "dtone_operator_details",
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
        success:function(responses){

            if(responses.status==true)
            {
                var response = responses.data;
                var skus = responses.skus;
                var option_list = '';


            for (var i = 0; i < skus.length; i++){
                option_list+='<option value='+skus[i].skuId+'>'+skus[i].amount_text+'</option>'
            }
            $('.amount_list').append(option_list);

                $("#check_number").hide();

                $(".amount_input_field").show();
                $("#recharge_number").show();
                $('.cover-spin').hide(0);

                $("#operator_name").text(responses.operator_name);

                // $("#receiverMobile").attr('disabled',true);
                // $('.iti__flag-container').attr('disabled',true);

            }
            else
            {
                location.reload();
                sessionStorage.setItem('error',true);
                sessionStorage.setItem('message',response.message);
            }
           //console.log(response.status);
           //alert('hello')

        },
       });


   });

   $("#recharge_number").click(function(){
    event.preventDefault();
   var skuId = $(".amount_list :selected").val();
//    var amount_list = amount_list.split(",");
//     var skuId = amount_list[0];
//     var amount = amount_list[1];

    var formdata = new FormData();
    formdata.append('number',$('#receiverMobile').val().split(' ').join(''));
    formdata.append('service_charge',$('#service').val());
    formdata.append('id', skuId);
    formdata.append('countryCode', intl.getSelectedCountryData().iso2);




      $.ajax({
        processData: false,
        contentType: false,
        url: "dtone_recharge",
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
            $('.cover-spin').hide(0);
            // $("#check_number").hide();
            // $("#amount_input_field").show();

            // $('#min_amount').text(response.minAmount);
            // $('#max_amount').text(response.maxAmount);
            // $("#exchange_rate").val(response.fx.rate);
            // $("#currency_code").val(response.fx.currencyCode);

            if(response.status==true)
            {
                location.reload();
                sessionStorage.setItem('success',true);
                sessionStorage.setItem('message',response.message);
                //console.log(response.message);
            }
            else
            {
                location.reload();
                sessionStorage.setItem('error',true);
                sessionStorage.setItem('message',response.message);
            }
           //console.log(response.status);
           //alert('hello')

        },
       });


   });

   $('#receiverMobile').keydown(function(){
    $(".amount_input_field").hide();
    $("#calculation_section").hide();
    $("#recharge_number").hide();
    $("#check_number").show();

   });
    $("#amount").keyup(function(){
        //var countryData = intl.getSelectedCountryData();
        var exchange_rate = $('#exchange_rate').val();
        var value = this.value;
        if(value)
        {
        var currencyCode = $("#currency_code").val();
        var calculation_text = (exchange_rate*value).toFixed(0)+" "+currencyCode+" will receive";
        //alert(calculation_text)
        $("#calculation_section").show();
        $("#calculation").text(calculation_text);
        }
        else{
            $("#calculation_section").hide();
        }

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

