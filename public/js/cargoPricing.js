$(function () {
    //Initialize Select2 Elements
    $('.select2').select2()

    //Initialize Select2 Elements
    $('.select2bs4').select2({
      theme: 'bootstrap4'
    })
    //Date picker
    $('#customerBirthDate').datetimepicker({
      format: 'yyyy-mm-dd'
    });
    $('#receiverBirthDate').datetimepicker({
      format: 'yyyy-mm-dd'
    });
    $('#expectedDelivaryDate').datetimepicker({
      format: 'yyyy-mm-dd'
    });

  })


  $(function () {
      bsCustomFileInput.init();
    });

  function myFunction() {
   let totalCharge = document.getElementById("total");
   let change = document.getElementById("total").value;
   let countryCharge = document.getElementById("charge_for_country").value;
   let weightCharge = document.getElementById("charge_for_weight").value;


   totalCharge.setAttribute('value', parseInt(weightCharge) + parseInt(countryCharge));

   // let totalCharge = document.getElementById("total");

   console.log(change);
   // totalCharge.setAttribute('value','')

  }

  function myFunction2() {
   let totalCharge = document.getElementById("total");
   let change = document.getElementById("total").value;
   let countryCharge = document.getElementById("charge_for_country").value;
   let weightCharge = document.getElementById("charge_for_weight").value;


   totalCharge.setAttribute('value', parseInt(weightCharge) + parseInt(countryCharge));

   // let totalCharge = document.getElementById("total");

   console.log(change);
   // totalCharge.setAttribute('value','')

  }
