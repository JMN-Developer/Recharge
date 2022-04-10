$(function(){
    notification_count();
    sim_notification_count();
    complain_notification_count();
    general_notification_count();
  });

  $(".custom-file-input").on("change", function() {
    var fileName = $(this).val().split("\\").pop();
    $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
  });


  var a = Echo.channel('events')
  .listen('DueRequest', (e) => {
      notification_count()
      sim_notification_count();
  });

var a = Echo.channel('events')
  .listen('SimRequest', (e) => {
    sim_notification_count();

  });
var a = Echo.channel('events')
  .listen('TicketRequest', (e) => {

  complain_notification_count();

  });

  var a = Echo.channel('events')
  .listen('GeneralNotificationEvent', (e) => {

  general_notification_count();

  });


  function general_notification_count()
  {
    $.ajax({
        type: "GET",
        dataType: "json",
        url: '/general_notification_count',
        success: function(data){
        $('.general_notification_count').text(data)

        }
    });
  }

  function notification_count()
  {
    $.ajax({
        type: "GET",
        dataType: "json",
        url: '/wallet_notification_count',
        success: function(data){
        $('.wallet_notification_count').text(data)
        }
    });
  }
  function isNumberKeyDecimal(evt)
  {
      var charCode = (evt.which) ? evt.which : event.keyCode
      if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
          return false;
      return true;
  }
  function complain_notification_count()
  {

    $.ajax({
        type: "GET",
        dataType: "json",
        url: '/complain_notification_count',
        success: function(data){

        $('.complain_notification_count').text(data)
        }
    });
  }
  function sim_notification_count()
  {
    $.ajax({
        type: "GET",
        dataType: "json",
        url: '/sim_notification_count',
        success: function(data){

        $('.sim_notification_count').text(data)
        }
    });
  }
