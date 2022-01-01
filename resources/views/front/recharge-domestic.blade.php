@extends('front.layout.courier')
@section('header')

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
  <title>Recharge Italy</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{asset('css/fontawesome-free/css/all.min.css')}}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{asset('css/admin.min.css')}}">
  <link rel="stylesheet" href="{{asset('css/loader/index.css')}}">
  <link rel="stylesheet" href="https://unpkg.com/izitoast/dist/css/iziToast.min.css">

  <link rel="stylesheet" href="{{asset('css/style.css')}}">
<link rel="icon" href="https://jmnation.com/images/jm-transparent-logo.png">


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
              <h3 class="text-center mb-5">Indice Brand Richriche</h3>
              <div class="row">
                <div class="col-md-6">
                  <form id="domestic_recharge">
                    @csrf
                    <div class="form-group">
                      <label>Brand</label>

                      <div class="brand-select-list">
                        <button type="button" class="selected-brand text-left" name="selected_brand" value=""></button>
                        <div class="brandUlLiContainer">
                          <ul id="brandUlList" style="max-height: 200px; overflow: auto;"></ul>
                        </div>
                      </div>
                      <input type="hidden" name="operator" id="op">
                      <select id="operator" class="brand-dropdown" value="" style="width: 100%;">

                        <option id="test" value="FASTCARD" data-thumbnail="{{ asset('images/fastweb.png') }}"> Fastweb</option>
                        <option id="test" value="Vodafone" data-thumbnail="{{ asset('images/vodafone.png') }}">Vodafone</option>
                        <option value="Tiscali" data-thumbnail="{{ asset('images/Tiscali.png') }}">Tiscali</option>
                        <option value="Tim" data-thumbnail="{{ asset('images/Tim.png') }}">Tim</option>
                        <option value="Very" data-thumbnail="{{ asset('images/very.png') }}">Very Mobile</option>
                        <option value="Kena" data-thumbnail="{{ asset('images/kena.png') }}">Kena Mobile</option>
                        <option value="Lyca" data-thumbnail="{{ asset('images/lyca.png') }}">LycaMobile</option>
                        <option value="WindTre" data-thumbnail="{{ asset('images/WindTre.png') }}">WindTre</option>
                        <option value="Poste" data-thumbnail="{{ asset('images/PosteMobile.png') }}">PosteMobile Online</option>
                        <option value="Digi" data-thumbnail="{{ asset('images/Digi.png') }}">Digi Mobil</option>
                        <option value="Tim-Carta" data-thumbnail="{{ asset('images/Tim-Carta.png') }}">Tim-Carta Servizi</option>
                        <option value="Coop" data-thumbnail="{{ asset('images/coop-voce-1-480x480.jpeg') }}">CoopVoce</option>
                        <option value="Ho" data-thumbnail="{{ asset('images/Ho.png') }}">Ho Mobile</option>
                        <option value="Vodafone-Carte" data-thumbnail="{{ asset('images/vodafone.png') }}">Vodafone-Carte Servizi</option>
                        <option value="Iliad" data-thumbnail="{{ asset('images/Iliad.png') }}">Iliad</option>
                      </select>
                    </div>
                    <div class="mb-3 phone_number">
                      <label for="inputMobileNumber" class="form-label">Mobile Number</label>
                      <input type="text" class="form-control myNumber" id="inputMobileNumber" name="number" value="" placeholder="Please enter mobile number" autocomplete="off" onkeypress="return isNumberKey(event)">
                    </div>
                    <div id="price">
                      <label for="">Amount</label>
                      <select id="amounts" name="amount" class="form-control amounts">

                      </select>
                    </div>
                    {{-- <div class="mb-3">
                      <label for="inputAmount" class="form-label">Service Charge in EURO</label>
                      <input type="text" class="form-control" id="inputAmount" step="any" name="service" placeholder="Please Enter Service Charge">
                    </div> --}}
                    <div class="mt-3">
                      <input type="submit" class="btn btn-info" style="width: 100%;" value="Recharge">
                    </div>

                  </form>
                </div>
                <div class="col-md-6">
                  <div class="last_recharge_table">
                    <div class="last_recharge_table_head text-center">
                      <h5><strong>Last 10 Recharge</strong></h5>
                    </div>

                    <div class="card-body table-responsive p-0">
                      <table class=" table table-sm table-bordered table-hover">
                        <thead>
                          <tr class="table-danger">
                            <th>Receiver</th>
                            <th>Amount</th>

                            <th>Profit</th>
                            <th class="text-center">Date</th>
                            <th class="text-center">Action</th>
                          </tr>
                        </thead>
                        <tbody id="domestic_recent_recharge">


                          @foreach ($data as $item)
                          <tr class="bg-ocean">
                            <td>{{ $item->number }}</td>
                            <td>{{ $item->amount }}</td>


                                @if(auth()->user()->role == 'admin')
                                <td>{{ $item->admin_com }}</td>
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

@endsection

@section('scripts')
<!-- jQuery -->
<script src="{{asset('js/jquery.min.js')}}"></script>
<script src="https://unpkg.com/izitoast/dist/js/iziToast.min.js" type="text/javascript"></script>
<!-- Bootstrap -->
<script src="{{asset('js/bootstrap.bundle.min.js')}}"></script>
<!-- Theme JS -->
<script src="{{asset('js/admin.js')}}"></script>
<!-- Custom JS -->
<script src="{{asset('js/custom.js')}}"></script>
@endsection

@section('js')
{{-- <script>
  $(function(){
$('#test').click(function(){
var empty = "";
var value = $(this).val();
var table = $('#offer');
$.ajax({
 type: "POST",
 url: "/check-products", // url to request
 data:{
            _token:'{{ csrf_token() }}',
            id: value,
        },
  cache: false,
  dataType: 'json',
 success : function(response){
  $(response).each(function(){
    var data = `<div class="col-sm-6 col-md-4 col-lg-3 mt-3">
                        <div class="Recharge_package">
                          <div class="recharge_tk" onclick="selectAmount(10)">
                            <strong>`+response.amount+` Tk</strong>
                          </div>
                        </div>
                      </div>`;
                      console.log(response.amount);
  })
 }
});
});
});
</script> --}}
<script>
      let recent_domestic_recharge_url = '{{route("load_recent_domestic_recharge")}}';
</script>
<script>

</script>
<script type="text/javascript">
  function isNumberKey(evt)
{
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode != 43 && charCode > 31 && (charCode < 48 || charCode > 57))
        return false;
    return true;
}
    $(function(){

        //load_recent_recharge();
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


    });

    function load_recent_recharge()
    {

        $('#domestic_recent_recharge').empty();
        $.ajax({

        url: recent_domestic_recharge_url,
        type:"get",

        success:function(response){
            var item = response;
            for (var i = 0; i < item.length; i++){
            added_row = '<tr class="bg-ocean">'
        + '<td>' + item[i].number +  '</td>'
        + '<td>' + item[i].amount +  '</td>'
        + '<td>' + item[i].cost +  '</td>'
        + '<td>' + item[i].profit +  '</td>'
        + '<td><a class="btn btn-success" href="recharge_invoice/'+item[i].id+'"> Invoice</a></td>'
        + '</tr>'
        $('#domestic_recent_recharge').append(added_row)
        };
        }
        });

    }
    //Form Submit
    $( "#domestic_recharge" ).submit(function( event ) {
        event.preventDefault();
        var formdata = new FormData();
        formdata.append('amount',$('#amounts').val());
        formdata.append('number',$('#inputMobileNumber').val().split(' ').join(''));
        formdata.append('operator',$('#op').val());
      $.ajax({
        processData: false,
        contentType: false,
        url: "domestic_recharge",
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
         $("#inputMobileNumber").val("");
            //load_recent_recharge();

            $('.cover-spin').hide(0)
            // $(".phone_number").hide();
            // //$(".brandUlLiContainer").toggle();
            // $('#amounts').empty();
            // $('.selected-brand').empty();
            // $('.selected-brand').html('Select Brand');
            // $('.selected-brand').attr('value', '');
            // $(".recharge_amount").hide();
            // $("#inputMobileNumber").val("");




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



    //test for iterating over child elements
    var dropdownArray = [];
    $('.brand-dropdown option').each(function(){
      var img = $(this).attr("data-thumbnail");
      var text = this.innerText;
      var value = $(this).val();
      var item = '<li name="op" id="test"><img src="'+ img +'" alt="" value="'+value+'"/><span>'+ text +'</span></li>';
      dropdownArray.push(item);

    })

    $('#brandUlList').html(dropdownArray);

    // default if needed
    // $('.selected-brand').html(dropdownArray[0]);
    $('.selected-brand').html('Select Brand');
    $('.selected-brand').attr('value', '');

    //change button stuff on click
    $('#brandUlList li').click(function(){
       var img = $(this).find('img').attr("src");
       var value = $(this).find('img').attr('value');
       var text = this.innerText;
       var item = '<li><img src="'+ img +'" alt="" /><span>'+ text +'</span></li>';
      $('.selected-brand').html(item);
      $('#op').attr('value', text);
      $('.selected-brand').attr('value', value).trigger('change');
      $(".brandUlLiContainer").toggle();

      $('#amounts').empty();

      $.ajax({
 type: "POST",
 url: "/check-products", // url to request
 data:{
            _token:'{{ csrf_token() }}',
            id: value,
        },
  cache: false,
  dataType: 'json',
 success : function(response){
  $(response).each(function(index,item){
    var data = '<option value='+item.ean+'>'+item.amount+'</option>';
    $('#amounts').append('<option value='+item.ean+','+item.amount+'>'+item.amount+' Euro</option>');
  })
 }
});

      $(".phone_number").show();
    });

    $(".selected-brand").click(function(){
          $(".brandUlLiContainer").toggle();
    });

    function selectAmount(amount) {
      $('#inputAmount').val(amount);
    }

    $(".phone_number").hide();

    $(".recharge_amount").hide();

    $(document).on('keyup', '.myNumber', function () {
      if ( $(this).val().length >= 10 ) {
        $(".recharge_amount").show();
      }
      else {
        $(".recharge_amount").hide();
      }
    });


</script>
@endsection
