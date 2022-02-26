@extends('front.layout.courier')
@section('header')
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>data Action</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{asset('css/fontawesome-free/css/all.min.css')}}">
  <link href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css" rel='stylesheet'>
  <!-- Theme style -->
  <link rel="stylesheet" href="{{asset('css/admin.min.css')}}">

  <link rel="stylesheet" href="{{asset('css/style.css')}}">
   <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js" ></script>
   <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js" type="text/javascript"></script>

    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
    {{-- <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script> --}}

    <link rel="icon" href="https://jmnation.com/images/jm-transparent-logo.png">
<style>
        .tab_li {
        display: block;
        float: left;
        width: 33%;
        text-align: center;
        background: #343436;
        color:white;
        /* adjust */
        height: 50px;
        /* adjust */
        padding: 9px;
        font-weight: bold;
        /*adjust*/
    }

    .tab-group {
        justify-content: center;
        display: flex;
        list-style: none;
        padding: 0;

    }
    .tab a{
        color:white;
        padding: 2px;
        font-size:20px;
        display: block;
    }
    .tab.active {
        background: #D30E00;
        color: white;
        padding: 6px;
        font-size:20px;
       // color: black;
    }

    .tab.active a{
        font-size:20px;
        color: white;
        padding: 6px;
        display: block;

    }



    .tab-content>div:nth-last-child(-n+2) {
        display: none;
    }

    .toggle.btn{
        min-width: 100px;
        min-height: 40px
    }
    .table th{
        height: 25px;
    }

</style>
</head>
@endsection

@section('content')
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card mt-3">
              <div class="card-header">
                <h3 class="card-title"><i class="fas fa-list" style="margin-right: 10px;"></i><strong>Api Activation</strong></h3>


              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <ul class="tab-group nav nav-pills"  role="tablist">
                    <li class="tab active tab_li nav-item"><a href="#international">International</a></li>
                    <li class="tab tab_li nav-item"><a href="#domestic">Domestic</a></li>
                    <li class="tab tab_li nav-item"><a href="#bangladesh">Bangladesh</a></li>
                </ul>

                <div class="tab-content">
                    <div id="international">
                        <table class="table table-bordered  table-head-fixed text-nowrap text-center table-striped">
                            <thead >
                                <tr>
                                  <th style="background-color: black;color:white">Name</th>
                                  <th style="background-color: black;color:white">Activation</th>
                                  {{-- <th style="background-color: black;color:white">Price</th> --}}

                              </thead>
                              <tbody id="international_table">

                              </tbody>
                        </table>
                    </div>
                    <div id="domestic">
                        <table class="table table-bordered table-head-fixed text-nowrap text-center table-striped">
                            <thead>
                                <tr>
                                  <th style="background-color: black;color:white">Name</th>
                                  <th style="background-color: black;color:white">Activation</th>
                                    {{-- <th style="background-color: black;color:white">Price</th> --}}
                              </thead>
                              <tbody id="domestic_table">

                              </tbody>
                        </table>
                    </div>

                    <div id="bangladesh">
                        <table class="table table-bordered table-head-fixed text-nowrap text-center table-striped">
                            <thead>
                                <tr>
                                  <th style="background-color: black;color:white">Name</th>
                                  <th style="background-color: black;color:white">Activation</th>
                                  <th style="background-color: black;color:white">Euro Rate Per Hunderd BDT</th>
                                    {{-- <th style="background-color: black;color:white">Price</th> --}}
                              </thead>
                              <tbody id="bangladesh_table">

                              </tbody>
                        </table>
                    </div>
                </div>



              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
        </div>
        <!-- /.row -->

      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
  <script>



  $(function() {


  })
  </script>
  <script>
  $(function() {

    get_data();


function get_data()
{

    var added_row='';
    $.ajax({
        type: "GET",
        dataType: "json",
        url: '/ApiControl/get_data',
        success: function(data){
            var item = data;


        for (var i = 0; i < item.length; i++){
            var checked = item[i].status?'checked':'';
        added_row = '<tr class="bg-ocean">'
    + '<td>' + item[i].dummy_name +  '</td>'
    + '<td><input  data-id="'+item[i].id+'" class="toggle-class international" type="checkbox" data-onstyle="success" data-offstyle="danger" data-toggle="toggle" data-on="Active" data-off="InActive" '+checked+' ></td>'



    if(item[i].type == 'International')
      {
        added_row+= '</tr>';
    $('#international_table').append(added_row);
      }
      else if(item[i].type == 'Domestic')
        {
            added_row+= '</tr>';
    $('#domestic_table').append(added_row);
        }

        else
        {
            var euro_input = '<p><input id="euro_rate_per_hundred_bdt" onkeypress="return isNumberKeyDecimal(event)" type="text" value='+item[i].euro_rate_per_hundred_bdt+'></p>';
            added_row+='<td>'+euro_input+'</td>';
            added_row+= '</tr>';

    $('#bangladesh_table').append(added_row);
        }


    };


    $('#international_table input').bootstrapToggle();
    $('#domestic_table input').bootstrapToggle();
    $('#bangladesh_table input[type="checkbox"]').bootstrapToggle();

          //console.log(data.success)
        }
    });
}



$('.tab a').on('click', function(e) {

    e.preventDefault();

    $(this).parent().addClass('active');
    $(this).parent().siblings().removeClass('active');

    target = $(this).attr('href');

    $('.tab-content > div').not(target).hide();

    $(target).fadeIn(600);

});

$(document).on("blur", '#euro_rate_per_hundred_bdt', function(event) {
    $.ajax({
            type: "GET",
            dataType: "json",
            url: '/ApiControl/update_euro_rate',
            data: {'value':$("#euro_rate_per_hundred_bdt").val()},
            success: function(data){
            $('#international_table').empty();
            $('#domestic_table').empty();
            $('#bangladesh_table').empty();
              get_data();
              //console.log(data.success)
            }
        });
})

    $(document).on("change", '.international', function(event) {


        var status = $(this).prop('checked') == true ? 1 : 0;
        var id = $(this).data('id');


        $.ajax({
            type: "GET",
            dataType: "json",
            url: '/ApiControl/change_status',
            data: {'status': status, 'id': id,'type':'International'},
            success: function(data){
            $('#international_table').empty();
            $('#domestic_table').empty();
            $('#bangladesh_table').empty();
              get_data();
              //console.log(data.success)
            }
        });
    })




  })
  </script>


@endsection

@section('scripts')
<!-- jQuery -->

<!-- Bootstrap -->
<script src="{{asset('js/bootstrap.bundle.min.js')}}"></script>
<!-- Theme JS -->
<script src="{{asset('js/admin.js')}}"></script>
<script src="{{asset('plugin/intl-tel-input/js/intlTelInput.js')}}"></script>
<!-- Custom JS -->
<script src="{{asset('js/custom.js')}}"></script>
@endsection
