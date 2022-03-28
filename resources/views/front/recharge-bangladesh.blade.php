

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
                                        <label for="inputMobileNumber" class="form-label" style="">Amount in BDT</label>
                                        <input type="text" id="amount" class="form-control" name="amount" placeholder="Amount" onkeypress="return isNumberKeyDecimal(event)"  style="width: 84%">
                                        <input type="hidden" id="operator_id" >
                                        <input type="hidden" id="bd_amount">
                                        <input type="hidden" id="operator_name" >
                                        <input type="hidden" id="exchange_rate">
                                        <p style="color: red;font-weight:bold" id="bd_amount_field"><span id="main_amount"></span> Euro</p>

                                             <label class="form-label">Service Charge in EURO</label>
                                             <input type="number" step="any" id="service" name="service"  value="0" class="form-control" placeholder="Enter Service Charge (Optional)" style="width: 84%">

                                        <button class="btn btn-info mt-3" id="recharge_number" style="width: 85%;">Recharge</button>

                                        {{-- <button class="btn btn-primary" style="margin-bottom: 6px; float: right;">Verify</button> --}}
                                     </div>


                                 <button class="btn btn-info mt-3" style="width: 84%;" id="check_number">Continue</button>

                            </div>
                        </div>
                            <div class="col-md-6 ">

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
                                       <th class="text-center">Date</th>
                                       <th class="text-center" >Action</th>

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
                                       <td>{{ $item->service }}</td>
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




                  </div>

               </div>
               <!-- /.card-body -->
            </div>
            <div class="card card-outline card-primary offer_section">
                <div class="card-body">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="active">
                            <a href="#internet" data-toggle="tab">Internet</a>
                        </li>

                        <li>
                            <a href="#combo" data-toggle="tab">Combo</a>
                        </li>
                        <li>
                            <a href="#voice" data-toggle="tab">Voice</a>
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
    // $('.nav-tabs li').on('click', function(e) {
    //     console
    //     e.preventDefault();

    //     $(this).parent().addClass('active');
    //     $(this).parent().siblings().removeClass('active');

    //     target = $(this).attr('href');

    //     $('.tab-content > div').not(target).hide();

    //     $(target).fadeIn(600);

    // });
</script>
<script>
    function offer_select(id,amount,update_amount,offer_description)
    {


        //console.log(amount+" "+update_amount)
      $('.offer-card').removeClass('offer-card-after-click');
       $('.click-check-'+id).addClass('offer-card-after-click');
       $("#amount").val(offer_description);
       $('#main_amount').text(update_amount);
       $("#bd_amount").val(amount);
       $("#bd_amount_field").show();
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

    // $("#amount").keyup(function(){
    //     var value = this.value;
    //     $.ajax({
    //         type: "GET",
    //         dataType: "json",
    //         url: 'bangladeshi_exchange_rate',
    //         data: {'value': value},
    //         success: function(data){

    //             $("#main_amount").text(data);

    //         }
    //     });
    // })



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

   }));
   intl.setCountry("bd")
   function processData(obj)
   {
    $('.voice').empty();
    $('.internet').empty();
    $('.combo').empty();
    for (var i = 0; i < obj.length; i++){
        if(obj[i].offer_type == 'voice')
        {
            var offer_list =  `<div class="col-md-6 col-xl-3" style="cursor: pointer" onclick="offer_select(`+i+`,${obj[i].amount},${obj[i].update_amount},'${obj[i].offer_description}')">
   <div class="card offer-card click-check-`+i+`">
      <div class="card-body">
         <div class="d-flex">
            <div class="flex-grow-1">
               <div class="row">
                  <div class="col-md-2">
                    <img src="{{ asset('storage/${obj[i].operator_logo}') }}" width="30px" height="30px">
                  </div>
                  <div class="col-md-10">
                     <div>
                        <p>${obj[i].offer_description}</p>
                        <div class="row">
                           <div class="col-md-6">
                              <p style="width: 80px;font-size:14px" class="border rounded border-dark p-2"> <i class="fas fa-calendar-alt" style="color:rebeccapurple"></i><span style="margin-left:3px">${obj[i].offer_validity}</span></p>
                           </div>
                           <div class="col-md-6">
                              <p style="width: 80px;font-size:15px;color:white;background-color:rebeccapurple;font-weight:bold;text-align:center" class="border rounded border-dark p-2">${obj[i].update_amount} &euro;</p>
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
$('.voice').append(offer_list)
        }

       else if(obj[i].offer_type == 'internet')
        {
            var offer_list =  `<div class="col-md-6 col-xl-3" style="cursor: pointer" onclick="offer_select(`+i+`,${obj[i].amount},${obj[i].update_amount},'${obj[i].offer_description}')">
   <div class="card offer-card click-check-`+i+`">
      <div class="card-body">
         <div class="d-flex">
            <div class="flex-grow-1">
               <div class="row">
                  <div class="col-md-2">
                    <img src="{{ asset('storage/${obj[i].operator_logo}') }}" width="30px" height="30px">
                  </div>
                  <div class="col-md-10">
                     <div>
                        <p>${obj[i].offer_description}</p>
                        <div class="row">
                           <div class="col-md-6">
                              <p style="width: 80px;font-size:14px" class="border rounded border-dark p-2"> <i class="fas fa-calendar-alt" style="color:rebeccapurple"></i><span style="margin-left:3px">${obj[i].offer_validity}</span></p>
                           </div>
                           <div class="col-md-6">
                              <p style="width: 80px;font-size:15px;color:white;background-color:rebeccapurple;font-weight:bold;text-align:center" class="border rounded border-dark p-2"> ${obj[i].update_amount} &euro;</p>
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
        else{
            var offer_list =  `<div class="col-md-6 col-xl-3" style="cursor: pointer" onclick="offer_select(`+i+`,${obj[i].amount},${obj[i].update_amount},'${obj[i].offer_description}')">
   <div class="card offer-card click-check-`+i+`">
      <div class="card-body">
         <div class="d-flex">
            <div class="flex-grow-1">
               <div class="row">
                  <div class="col-md-2">
                     <img src="{{ asset('storage/${obj[i].operator_logo}') }}" width="30px" height="30px">
                  </div>
                  <div class="col-md-10">
                     <div>
                        <p>${obj[i].offer_description}</p>
                        <div class="row">
                           <div class="col-md-6">
                              <p style="width: 80px;font-size:14px" class="border rounded border-dark p-2"> <i class="fas fa-calendar-alt" style="color:rebeccapurple"></i><span style="margin-left:3px">${obj[i].offer_validity}</span></p>
                           </div>
                           <div class="col-md-6">
                              <p style="width: 80px;font-size:15px;color:white;background-color:rebeccapurple;font-weight:bold;text-align:center" class="border rounded border-dark p-2"> ${obj[i].update_amount} &euro;</p>
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

   }

   $("#check_number").click(function(){
    event.preventDefault();
    var number = $('#receiverMobile').val()
    store_number(number);
    if(number.startsWith('+39'))
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
                    timeout: 10000,
                    title: 'Error',
                    message: 'This Section is only for Bangladeshi Recharge. Please go to Recharge Italy Section',


                });
                return;
    }

   else if(number.startsWith('+88') || number.startsWith('01'))
   {

    var formdata = new FormData();
    formdata.append('number',$('#receiverMobile').val());
    formdata.append('countryIso', intl.getSelectedCountryData().iso2);
      $.ajax({
        processData: false,
        contentType: false,
        url: "bangladeshi_operator_details",
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
                $("#check_number").hide();
                $(".amount_input_field").show();
                $("#recharge_number").show();
                $("#operator_id").val(responses.operator_id);
                $("#operator_name").val(responses.operator_name);
                $("#exchange_rate").val(responses.exchange_rate)
                $(".offer_section").show();
                $('.cover-spin').hide(0);
                processData(responses.offer_data);

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


        },
       });

     }
     else{
        iziToast.error({
                    backgroundColor:"#D12C09",
                    messageColor:'white',
                    iconColor:'white',
                    titleColor:'white',
                    titleSize:'18',
                    messageSize:'18',
                    color:'white',
                    position:'topCenter',
                    timeout: 10000,
                    title: 'Error',
                    message: 'This Section is only for Bangladeshi Recharge. Please go to Recharge International Section',


                });
                return;
     }



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

   var formdata = new FormData();
    formdata.append('number',$('#receiverMobile').val().split(' ').join(''));
    formdata.append('amount', $('#main_amount').text());

    formdata.append('operator_id',$("#operator_id").val());
    formdata.append('operator_name',$("#operator_name").val());
    var regExp = /[a-zA-Z]/g;
    if(regExp.test($("#amount").val())){
    var bd_amount = $("#bd_amount").val();
    } else {
        var bd_amount = $("#amount").val();

    }
    formdata.append('bd_amount',bd_amount);
  // console.log(bd_amount);
    //return;
    //formdata.append('updated_amount',$('#amount').val());
    formdata.append('service_charge',$("#service").val());

      $.ajax({
        processData: false,
        contentType: false,
        url: "bangladeshi_recharge",
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
                //console.log(response.message);
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

   $('#receiverMobile').keydown(function(){
    $(".amount_input_field").hide();
    $("#calculation_section").hide();
    $("#recharge_number").hide();
    $("#check_number").show();
    $(".offer_section").hide();

   });

   $('#amount').keydown(function(){
    value = this.value;
    var regExp = /[a-zA-Z]/g;
    if(regExp.test(value)){
      $("#amount").val('');
    } else {

    }

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
    //    $('.iti__flag-container').click(function() {

    //      var countryCode = $('.iti__selected-flag').attr('title');
    //      var countryCode = countryCode.replace(/[^0-9]/g,'')
    //      $('#receiverMobile').val("");
    //      $('#receiverMobile').val("+"+countryCode+" "+ $('#receiverMobile').val());
    //   });
   });
 </script>
@endsection

