

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
                                 @if ($stage == 'check_number')
                                 <form action="{{ route('check-changed-product') }}" method="POST">
                                    @elseif($stage == 'get_product')
                                 <form class="international_recharge" action="{{ route('international_recharge') }}" method="POST">
                                    @else
                                 <form action="{{ route('check-operator') }}" method="POST">
                                    @endif
                                    @csrf
                                    <div class="mb-3 receiver_inputs">
                                       <label for="inputMobileNumber" class="form-label">Receiver Number</label>
                                       @if ($stage == 'initial')
                                       <input type="text" id="receiverMobile" value="{{ $datas[0]['UatNumber'] ?? '' }}" class="form-control receiver_input_form" name="number" placeholder="Receiver Number" onkeypress="return isNumberKey(event)">
                                       @else
                                       <input type="text" value="{{ $datas['number'] ?? '' }}" class="form-control receiver_input_form" name="number" placeholder="Receiver Number" readonly>
                                       @endif
                                       {{-- <button class="btn btn-primary" style="margin-bottom: 6px; float: right;">Verify</button> --}}
                                    </div>
                                    @if ($stage == 'check_number')
                                    <div class="form-group">
                                       <label for="selectOparetor">Oparetor</label>
                                       <select class="custom-select operators" name="operator" id="operators">
                                          <option value="">Select Operator</option>
                                          @foreach ($operators as $item)
                                             @if ( strpos($item['Name'], 'Data') == false)
                                                <option {{ ( $datas['operator'] ?? '' == $item['ProviderCode']) ? 'selected' : '' }} value="{{ $item['ProviderCode'] }}">{{ $item['Name'] }}</option>
                                             @endif
                                          @endforeach
                                       </select>
                                    </div>

                                    @elseif($stage == 'initial')

                                    @else

                                    @endif
                                    @if (isset($stage))
                                    @if ($stage == 'get_product')
                                    @if ($count > 1)
                                    <div class="form-group">
                                       <label for="selectPackage">Select Amount</label>
                                       <select class="custom-select amount" name="amount" id="package">
                                          @foreach ($prods as $item)
                                          <?php
                                             $admin_international_com = ($item['Maximum']['SendValue']/100)*Auth::user()->admin_international_recharge_commission;
                                            // $reseller_international_com = ($admin_international_com/100)*Auth::user()->reseller_profit->international_recharge_profit
                                          ?>
                                          <option value="{{ $item['SkuCode'] }},{{ $item['Maximum']['SendValue']}}">
                                             {{ $item['Maximum']['SendValue'] +$admin_international_com }} Euro
                                             <h7 style="font-size: 10px;">({{ $item['Maximum']['ReceiveValueExcludingTax'] }} {{ $item['Maximum']['ReceiveCurrencyIso'] }} will be received)</h7>
                                          </option>
                                          @endforeach
                                       </select>
                                    </div>
                                    <label class="form-label">Service Charge in EURO</label>
                                    <input type="number" step="any" name="service" class="form-control" placeholder="Enter Service Charge (Optional)">
                                    @else
                                    <div class="mb-3">
                                       <label for="inputAmount" class="form-label">Amounts (EUR)</label>
                                       <input oninput="cost()" id="amount" type="number" step="any"
                                       min="{{ $prods['0']['Minimum']['SendValue'] + (($prods['0']['Minimum']['SendValue']/100)*Auth::user()->admin_international_recharge_commission) + (($prods['0']['Minimum']['SendValue']/100)*Auth::user()->international_recharge) }}"
                                       max="{{ $prods['0']['Maximum']['SendValue'] + (($prods['0']['Maximum']['SendValue']/100)*Auth::user()->admin_international_recharge_commission) + (($prods['0']['Maximum']['SendValue']/100)*Auth::user()->international_recharge) }}"
                                       class="form-control" name="amount"
                                       placeholder="Between Euro {{ $prods['0']['Maximum']['SendValue'] + (($prods['0']['Maximum']['SendValue']/100)*Auth::user()->admin_international_recharge_commission) + (($prods['0']['Maximum']['SendValue']/100)*Auth::user()->international_recharge)}}  -  Euro {{ $prods['0']['Minimum']['SendValue'] + (($prods['0']['Minimum']['SendValue']/100)*Auth::user()->admin_international_recharge_commission) + (($prods['0']['Minimum']['SendValue']/100)*Auth::user()->international_recharge)}}">
                                       <input type="hidden" name="Sku_Code" value="{{ $prods['0']['SkuCode'] }}" id="skucode">
                                       <input type="hidden" id="admin_com" value="{{ Auth::user()->admin_international_recharge_commission }}">
                                       <input type="hidden" id="reseller_com" value="{{ Auth::user()->international_recharge }}">
                                       <small style="font-size: 18px;text-align: center;font-weight: bold;color: red;" id="price"></small><br>
                                       <input type="hidden" name="received_amount" id="received_amount">
                                    </div>
                                    <label class="form-label">Service Charge in EURO</label>
                                    <input type="number" step="any" id="service" name="service" class="form-control" placeholder="Enter Service Charge (Optional)">
                                    @endif
                                    @endif
                                    @endif


                                    @if ($stage == 'check_number')
                                    <input type="submit" class="btn btn-info mt-3" value="Get Products" style="width: 100%;">
                                    @elseif($stage == 'get_product')
                                    <button type="button" onclick="international_recharge()" class="btn btn-info mt-3" style="width: 100%;" >Recharge</button>
                                    {{-- <input type="button" id="international_recharge" class="btn btn-info mt-3" value="Recharge" style="width: 100%;"> --}}
                                    @else
                                    <input type="submit" class="btn btn-info" value="Continue" style="width: 85%;">
                                    @endif
                              </div>
                              <div class="col-md-6">
                                  @if($stage == 'get_product')
                                <div class="mb-3 text-center">

                                    <img style="height:112px; margin-right:101px" id="operator_image" alt="Operator Logo Not Found" src="{{ $logo }}" >

                                 </div>
                                <div class="mb-3" >
                                    <div class="text-center" style="padding-top:10px;padding-bottom:1px;background:#C62604;width:84%;margin-bottom:10px">
                                        <p style="font-weight:bold;color:white;font-size:18px">Operator Name: {{ $datas['operator'] }} </p>
                                    </div>

                                 </div>
                                 @endif
                              </div>
                        </div>

                     </div>
                     </form>
                     <div class="col-md-12" style="margin-top:100px ">
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
                                       <th class="text-center">Date</th>
                                       <th class="text-center">Action</th>
                                    </tr>
                                 </thead>
                                 <tbody>
                                    @foreach ($data as $item)
                                    <tr class="bg-ocean">
                                       <td>{{ $item->number }}</td>
                                       <td>{{ $item->operator }}</td>
                                       <td>{{ $item->cost }}</td>
                                       @if(auth()->user()->role == 'admin')
                                       <td>{{ $item->admin_com+$item->discount }}</td>
                                       @else
                                       <td>{{ $item->reseller_com }}</td>
                                       @endif
                                       <td class="text-center">{{ $item->created_at }}</td>
                                       <td class="text-center"> <a class="btn btn-success" href="recharge_invoice/{{ $item->id }}"> Invoice</a> </td>
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
<script>

   function cost(){
      var amount = document.getElementById('amount').value;
      var admin = document.getElementById('admin_com').value;
      var reseller = document.getElementById('reseller_com').value;
      var admin_cost = (amount/100)*admin;
      var reseller_cost = (amount/100)*reseller;
      var cost = (admin_cost + reseller_cost);
      var skucode = document.getElementById('skucode').value;
      var am = Number(amount);
      var pm = Number(cost);
      var token   = $('meta[name="csrf-token"]').attr('content');
      var send = amount - cost;

      $.ajax({
        url: "/recharge/estimate",
        type:"POST",
        headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
         },
        data: {
          SendValue:send,
          SkuCode:skucode,
          BatchItemRef:Math.floor(Math.random() * 100000000000),
        },
        success:function(response){
           console.log(response);
          console.log(response.Items[0].Price.ReceiveValue);
          document.getElementById("price").innerHTML = 'You Will Receive ' + response.Items[0].Price.ReceiveValue +' ' +response.Items[0].Price.ReceiveCurrencyIso+'. ';
          document.getElementById("received_amount").setAttribute('value',response.Items[0].Price.ReceiveValue);
        },
       });
   }
</script>
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
      function isNumberKey(evt)
{
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode != 43 && charCode > 31 && (charCode < 48 || charCode > 57))
        return false;
    return true;
}
    function international_recharge()
    {

    var amount = $('select[name=amount] option:selected').val();;
    var number =$("input[name=number]").val();
    var number = number.split(' ').join('');
    var skuId = $("input[name=Sku_Code]").val();
    var service_charge = $("input[name=service]").val();

   // alert(amount)

    var formdata = new FormData();
    formdata.append('number',number);
    formdata.append('Sku_Code', skuId);
    formdata.append('amount',amount);
    formdata.append('service_charge',service_charge);

    //formdata.append('countryCode', intl.getSelectedCountryData().iso2);



      $.ajax({
        processData: false,
        contentType: false,
        url: "ding_recharge",
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
                window.location.href = 'international';

                sessionStorage.setItem('error',true);
                sessionStorage.setItem('message',response.message);
            }
           //console.log(response.status);
           //alert('hello')

        },
       });
    }


   // Vanilla Javascript
   var input = document.querySelector("#receiverMobile");
   window.intlTelInput(input,({
     // options here
   }));

   $(document).ready(function() {
    $(".amount").select2();
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

       $('.iti__flag-container').click(function() {
         var countryCode = $('.iti__selected-flag').attr('title');
         var countryCode = countryCode.replace(/[^0-9]/g,'')
         $('#receiverMobile').val("");
         $('#receiverMobile').val("+"+countryCode+" "+ $('#receiverMobile').val());
      });
   });
 </script>
@endsection

