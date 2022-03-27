

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
<script src="https://unpkg.com/izitoast/dist/js/iziToast.min.js" type="text/javascript"></script>

@endsection
@section('scripts')
<!-- jQuery -->
<script src="{{asset('js/jquery.min.js')}}"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<!-- Bootstrap -->
<script src="{{asset('js/bootstrap.bundle.min.js')}}"></script>
<!-- Theme JS -->
<script src="{{asset('js/admin.js')}}"></script>
<script src="{{asset('js/autocomplete.js')}}"></script>
<script src="{{asset('plugin/intl-tel-input/js/intlTelInput.js')}}"></script>
<!-- Custom JS -->
<script src="{{asset('js/custom.js')}}"></script>
@endsection
@section('js')

<script>
    var offer_count = 0;
  function offer_select(id,amount,offer_description,skuId)
    {
 
      $('.offer-card').removeClass('offer-card-after-click');
       $('.click-check-'+id).addClass('offer-card-after-click');
   
       $("#amount").val(offer_description);
    //    $('#main_amount').text(update_amount);
    $("#main_amount").text(amount);
       $("#bd_amount_field").show();

            
            var option_list = '<option value='+skuId+','+amount+'>'+amount+'Euro('+offer_description+')</option>'
            if(offer_count>0)
            $('.amount_list option:first').remove();
            $('.amount_list').prepend(option_list);
            $(".amount_list option:first").attr("selected", "selected");
            offer_count++;
    }

  function processData(internet,combo)
   {
    $('.voice').empty();
    $('.internet').empty();
    $('.combo').empty();
    for (var i = 0; i < internet.length; i++){
        
            var offer_list =  `<div class="col-md-6 col-xl-3" style="cursor: pointer" onclick="offer_select(`+i+`,${internet[i].amount},'${internet[i].description}','${internet[i].skuId}')">
   <div class="card offer-card click-check-`+i+`">
      <div class="card-body">
         <div class="d-flex">
            <div class="flex-grow-1">
               <div class="row">
                
                  <div class="col-md-10">
                     <div>
                        <p>${internet[i].description}</p>
                        <div class="row">
                           <div class="col-md-6">
                              <p style="width: 80px;font-size:14px" class="border rounded border-dark p-2"> <i class="fas fa-calendar-alt" style="color:rebeccapurple"></i><span style="margin-left:3px">${internet[i].validity}</span></p>
                           </div>
                           <div class="col-md-6">
                              <p style="width: 80px;font-size:15px;color:white;background-color:rebeccapurple;font-weight:bold;text-align:center" class="border rounded border-dark p-2">${internet[i].amount} &euro;</p>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>`
$('.internet').append(offer_list)
      
    }

    for (var i = 0; i < combo.length; i++){
        
        var offer_list =  `<div class="col-md-6 col-xl-3" style="cursor: pointer" onclick="offer_select(`+i+`,${combo[i].amount},'${combo[i].description}','${combo[i].skuId}')">
<div class="card offer-card click-check-`+i+`">
  <div class="card-body">
     <div class="d-flex">
        <div class="flex-grow-1">
           <div class="row">
            
              <div class="col-md-10">
                 <div>
                    <p>${combo[i].description}</p>
                    <div class="row">
                       <div class="col-md-6">
                          <p style="width: 80px;font-size:14px" class="border rounded border-dark p-2"> <i class="fas fa-calendar-alt" style="color:rebeccapurple"></i><span style="margin-left:3px">${combo[i].validity}</span></p>
                       </div>
                       <div class="col-md-6">
                          <p style="width: 80px;font-size:15px;color:white;background-color:rebeccapurple;font-weight:bold;text-align:center" class="border rounded border-dark p-2">${combo[i].amount} &euro;</p>
                       </div>
                    </div>
                 </div>
              </div>
           </div>
        </div>
     </div>
  </div>
</div>
</div>`
$('.combo').append(offer_list)
  
}

   }


      function isNumberKey(evt)
{
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode != 43 && charCode > 31 && (charCode < 48 || charCode > 57))
        return false;
    return true;
}
   // Vanilla Javascript



   $(document).ready(function() {

   var mobile_number =  fetch_number();
   autocomplete(document.getElementById('receiverMobile'), mobile_number);

    $(".amount_list").select2();
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
    $(".offer_section").hide();
    $("#bd_amount_field").hide();
    var input = document.querySelector("#receiverMobile");
  var intl =  window.intlTelInput(input,({
     // options here
   }));

   $("#check_number").click(function(){
    event.preventDefault();
    var number = $('#receiverMobile').val()
    store_number(number);
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
            $('.offer_section').show();
            
            
            if(responses.status==true)
            {

                var response = responses.data;
                var skus = responses.skus;
                var option_list = '';


            for (var i = 0; i < skus.length; i++){
                option_list+='<option value='+skus[i].skuId+','+skus[i].amount+','+skus[i].bd_amount+ '>'+skus[i].amount_text+'</option>'
            }
            $('.amount_list').empty();
            $('.amount_list').append(option_list);

                $("#check_number").hide();

                $(".amount_input_field").show();
                $("#recharge_number").show();
                $('.cover-spin').hide(0);

                $("#operator_name").text(responses.operator_name);
                $("#exchange_rate").val(responses.exchange_rate);
                if(number.startsWith('+880'))
                    {
                        $("#bangladesh_amount").show();
                        $("#international_amount").hide();
                    }
                    else{
                        $("#bangladesh_amount").hide();
                        $("#international_amount").show();
                    }
               processData(responses.internet,responses.combo)

                // $("#receiverMobile").attr('disabled',true);
                // $('.iti__flag-container').attr('disabled',true);

            }
            else
            {

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
                    message: responses.message,


                });


            }
           //console.log(response.status);
           //alert('hello')

        },
       });


   });

   function check_daily_duplicate(number)
   {
        $.ajax({
            type: "GET",
            dataType: "json",
            url: 'check_daily_duplicate',
            data: {'number': number},
            success: function(data){
                if(data == 1)
                {
                    swal({
                        title: "Are you sure to continue this rechagre?",
                        text: "You have already recharged this number today",
                        icon: "warning",
                        buttons: true,
                        dangerMode: true,
                        })
                        .then((willDelete) => {
                        if (willDelete) {
                            recharge_number()
                        } else {
                           //location.reload()
                        }
                        });
                }
                else
                {
                    recharge_number()
                }

            }
        });
   }

   function recharge_number()
   {

    var sku = $(".amount_list :selected").val();
   var sku = sku.split(',');
   var skuId = sku[0];
   var amount = sku[1];
   var bd_amount = sku[2];
   var formdata = new FormData();
    formdata.append('number',$('#receiverMobile').val().split(' ').join(''));
    formdata.append('service_charge',$('#service').val());
    formdata.append('id', skuId);
    formdata.append('amount', amount);
    formdata.append('bd_amount', bd_amount);
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

            if(response.status==true)
            {
                location.reload();
                sessionStorage.setItem('success',true);
                sessionStorage.setItem('message',response.message);
                console.log(response.message);
            }
            else
            {
                location.reload();
                sessionStorage.setItem('error',true);
                sessionStorage.setItem('message',response.message);
            }
           //console.log(response.status);
          // alert('hello')

        },
       });



   }

   $("#recharge_number").click(function(){
    event.preventDefault();
    check_daily_duplicate($('#receiverMobile').val())


   });

   $("#amount").keyup(function(){
        //var countryData = intl.getSelectedCountryData();
        var exchange_rate = $('#exchange_rate').val();
        var value = this.value;
        if(value)
        {
        //var currencyCode = $("#currency_code").val();
        var calculation_text = (exchange_rate*value).toFixed(3);
        //alert(calculation_text)

        $("#bd_amount_field").show();
        $("#main_amount").text(calculation_text);
        }
        else{
            $("#bd_amount_field").hide();
        }

        });

   $('#receiverMobile').keydown(function(){
    $(".amount_input_field").hide();
    $("#calculation_section").hide();
    $("#recharge_number").hide();
    $("#check_number").show();
    $('.offer_section').hide();
   });
    $("#amount").keyup(function(){
        var value = this.value;
        $.ajax({
            type: "GET",
            dataType: "json",
            url: 'bangladeshi_exchange_rate',
            data: {'value': value},
            success: function(data){

                $("#main_amount").text(data);

            }
        });
       
        

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

