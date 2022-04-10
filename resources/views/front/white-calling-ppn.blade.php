

@extends('front.layout.master')
@section('header')
<head>
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <title>White Calling</title>
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
               {{-- <div class="card-header text-center">
                  <a href="/"><img src="{{ asset('images/jm logo.png') }}" width="80px" height="auto"></a>
               </div> --}}
               <div class="card-body">
                  <p class="login-box-msg">White Calling Pin </p>
                  <div class="row">
                     <div class="col-md-12">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3 receiver_inputs">




                                    <div class="amount_input_field">
                                        <label for="inputMobileNumber" class="form-label" style="margin-right: 43px">Select Amount</label>
                                            <select class="custom-select amount_list" name="amount" id="package" style="width: 85%">
                                                <option value="3576,2">White Calling PINS - Italy 2.00 EUR</option>
                                                <option value="3559,5">White Calling PINS - Italy 5.00 EUR</option>
                                             </select>

                                        <button class="btn btn-info mt-3" id="recharge_number" style="width: 85%;">Get Pin</button>

                                        {{-- <button class="btn btn-primary" style="margin-bottom: 6px; float: right;">Verify</button> --}}
                                     </div>


                            </div>
                        </div>
                            <div class="col-md-6 operator_details">

                                <div class="mb-3" >
                                    <div class="text-center" style="padding-top:10px;padding-bottom:1px;background:#C62604;width:84%;margin-bottom:10px">
                                        <p style="font-weight:bold;color:white;font-size:18px">Pin Number: <span id="pin_number"></span> </p>
                                    </div>

                                    <div style="width: 85%" >
                                        <label for="inputMobileNumber" class="form-label">Send this pin to email</label>
                                        <div class="d-flex">
                                        <input type="email" id="email" class="form-control" placeholder="Email">
                                        <button type="button" class="btn btn-sm btn-info send_pin_to_email">Send </button>
                                         </div>
                                    </div>


                                 </div>
                            </div>
                        </div>

                     </div>

                     <div class="col-md-12"  style="margin-top: 100px">
                        <div class="last_recharge_table">
                           <div class="last_recharge_table_head text-center">
                              <h5><strong>Last 10 White Calling Card</strong></h5>
                           </div>
                           <div class="card-body table-responsive p-0">
                              <table class="table table-sm table-bordered table-hover">
                                 <thead>
                                    <tr class="table-danger">
                                       <th>Pin Number</th>
                                       <th>Control Number</th>
                                       <th>Amount</th>
                                       <th>Profit</th>
                                       <th>Action</th>
                                       {{-- <th>Cost</th>
                                       <th>Profit</th>
                                       <th>Action</th> --}}
                                    </tr>
                                 </thead>
                                 <tbody id="table_data">


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


@endsection
@section('scripts')

<script src="{{asset('plugin/intl-tel-input/js/intlTelInput.js')}}"></script>

@endsection
@section('js')

<script>
   // Vanilla Javascript

    let user_role = '{{ auth()->user()->role }}';



   $(document).ready(function() {
    get_table();
       $(".operator_details").hide();






            function get_table()
    {
        $('#table_data').empty();
        $.ajax({
            type: "GET",
            dataType: "json",
            url: 'get_white_calling_table',
            beforeSend: function () {
            $('.cover-spin').show(0)
            },
            success: function(item){
                console.log(item)
                $('.cover-spin').hide(0);
                var added_row ='';
                var commision_column ='';
                for (var i = 0; i < item.length; i++){
                    if(user_role=='admin')
                    {
                        commision_column = item[i].admin_com
                    }
                    else
                    {
                        commision_column = item[i].reseller_com
                    }
                    added_row+='<tr><td>' + item[i].pin_number +  '</td>'
                    +'<td>' + item[i].control_number +  '</td>'
                    +'<td>' + item[i].amount +  '</td>'
                    +'<td>' + commision_column +  '</td>'
                    +'<td><a class="btn btn-success" href="recharge_invoice/'+item[i].id+'"> Invoice</a></td>'
                    +'</tr>'
                }
                $("#table_data").append(added_row);
            }
        });

    }

    $(".send_pin_to_email").click(function(){
        swal({
  title: "Are you sure?",
  icon: "warning",
  buttons: true,
  dangerMode: true,
})
.then((willDelete) => {
  if (willDelete) {
    var pin_number =  $("#pin_number").text();
    var email = $("#email").val();
    var formdata = new FormData();
    formdata.append('pin_number', pin_number);
    formdata.append('email', email);




      $.ajax({
        processData: false,
        contentType: false,
        url: "send_pin_to_email",
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
            iziToast.success({
                    backgroundColor:"Green",
                    messageColor:'white',
                    iconColor:'white',
                    titleColor:'white',
                    titleSize:'18',
                    messageSize:'18',
                    color:'white',
                    position:'topCenter',
                    timeout: 10000,
                    title: 'Success',
                    message: "Mail Send Successfully",

                });

                location.reload();





        },
       });

  } else {

  }
  });
    });

   $("#recharge_number").click(function(){

    event.preventDefault();

    swal({
  title: "Are you sure?",
  icon: "warning",
  buttons: true,
  dangerMode: true,
})
.then((willDelete) => {
  if (willDelete) {

    var amount_list = $(".amount_list :selected").val();
    var amount_list = amount_list.split(",");
    var skuId = amount_list[0];
    var amount = amount_list[1];
    var formdata = new FormData();
    formdata.append('skuId', skuId);
    formdata.append('amount', amount);




      $.ajax({
        processData: false,
        contentType: false,
        url: "ppn_pin",
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
                get_table();
                $(".operator_details").show();
                $("#pin_number").text(response.pin_number)

                iziToast.success({
                    backgroundColor:"Green",
                    messageColor:'white',
                    iconColor:'white',
                    titleColor:'white',
                    titleSize:'18',
                    messageSize:'18',
                    color:'white',
                    position:'topCenter',
                    timeout: 10000,
                    title: 'Success',
                    message: "Collect Pin Successfully",

                });

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
                    timeout: 10000,
                    title: 'Error',
                    message: "Some Error Occured. Please Try Again",

                });
            }


        },
       });

  } else {

  }
  });



   });



   });
 </script>
@endsection

