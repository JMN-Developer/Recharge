
  var offer_count = 0;
  function offer_select(id,amount,offer_description,skuId,bd_amount = 0)
    {

      $('.offer-card').removeClass('offer-card-after-click');
      $('.click-check-'+id).addClass('offer-card-after-click');

       $("#amount").val(offer_description);
       $("#sku_id").val(skuId);
       $("#bd_amount").val(bd_amount);

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
   function processDataBangladesh(obj)
   {
    $('.voice').empty();
    $('.internet').empty();
    $('.combo').empty();
    for (var i = 0; i < obj.length; i++){
        if(obj[i].offer_type == 'voice')
        {
            var offer_list =  `<div class="col-md-6 col-xl-3" style="cursor: pointer"  onclick="offer_select(`+i+`,${obj[i].update_amount},'${obj[i].offer_description}','',${obj[i].amount})">
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
            var offer_list =  `<div class="col-md-6 col-xl-3" style="cursor: pointer" onclick="offer_select(`+i+`,${obj[i].update_amount},'${obj[i].offer_description}','',${obj[i].amount})">
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
            var offer_list =  `<div class="col-md-6 col-xl-3" style="cursor: pointer"  onclick="offer_select(`+i+`,${obj[i].update_amount},'${obj[i].offer_description}','',${obj[i].amount})">
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
    if(number.startsWith('+880'))
     $('.nav-tabs').append('  <li><a href="#voice" data-toggle="tab">Voice</a></li>')
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
                        processDataBangladesh(responses.bd_offer_data);

                    }
                    else{
                        $('.amount_list').empty();
                        $("#bangladesh_amount").hide();
                        $("#international_amount").show();
                        var response = responses.data;
                        var skus = responses.skus;
                        var option_list = '';


                    for (var i = 0; i < skus.length; i++){
                        option_list+='<option value='+skus[i].skuId+','+skus[i].amount+','+skus[i].bd_amount+ '>'+skus[i].amount_text+'</option>'
                     }
                     $('.amount_list').append(option_list);
                     processData(responses.internet,responses.combo)
                    }




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
                  swal({
                     title: "Are you sure to continue this rechagre?",
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

            }
        });
   }

   function recharge_number()
   {

    var sku = $(".amount_list :selected").val();
    if(sku){
   var sku = sku.split(',');
   var skuId = sku[0];
   var amount = sku[1];
    }
   var bd_amount = $("#bd_amount").val();
//console.log($("#bd_amount").val());
   var formdata = new FormData();
    formdata.append('number',$('#receiverMobile').val());
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
            $("#bd_amount").val(value);
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

