  //test for iterating over child elements
  var dropdownArray = [];
  $('.brand-dropdown option').each(function(){
    var img = $(this).attr("data-thumbnail");
    var text = this.innerText;
    var value = $(this).val();
    var item = '<li><img src="'+ img +'" alt="" value="'+value+'"/><span>'+ text +'</span></li>';
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
    $('.selected-brand').attr('value', value).trigger('change');
    $(".brandUlLiContainer").toggle();
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
