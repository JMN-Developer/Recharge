@extends('front.layout.courier')
@section('header')
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>Wallet Request</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{asset('css/fontawesome-free/css/all.min.css')}}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{asset('css/admin.min.css')}}">

  <link rel="stylesheet" href="{{asset('css/style.css')}}">
  <link rel="icon" href="https://jmnation.com/images/jm-transparent-logo.png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
        {{-- datatable css --}}
    <link href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css" rel='stylesheet'>
    <link rel="stylesheet" href="{{asset('css/loader/index.css')}}">
    <link rel="stylesheet" href="https://unpkg.com/izitoast/dist/css/iziToast.min.css">
</head>
<style type="text/css">
  .modal-content{
    border:whitesmoke 6px solid;
  }
  .red{
      color:red;
      text-transform: uppercase;
  }
  .green{
      color:#2AC330;
      text-transform: uppercase;
  }
  .yellow{
      color:#2CD2DB;
      text-transform: uppercase;
  }
</style>
@endsection

@section('content')
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-12">
            <h1 class="d-inline-block">Wallet Request</h1>
            <div class="search-form d-inline-block" style="float: right;">
              {{-- <div class="input-group" data-widget="sidebar-search">
                <input class="form-control" type="search" placeholder="Search" aria-label="Search">
                <div class="input-group-search">
                  <button class="btn btn-sidebar">
                    <i class="fas fa-search fa-fw"></i>
                  </button>
                </div>
              </div> --}}
            </div>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        @if(auth()->user()->role != 'admin')
        <div class="card card-solid">
            <div class="card-body pb-0 text-center">


                <form id="wallet_submit" class="form-inline" style="justify-content: center;padding-bottom:10px">
                    <div class="form-group mb-2" style="width: 128px">
                      <label for="staticEmail2" class="sr-only">Email</label>
                      <input type="text" readonly class="form-control-plaintext" id="staticEmail2" value="Requested Amount" disabled>
                    </div>
                    <div class="form-group mx-sm-3 mb-2">
                      <label for="inputPassword2" class="sr-only">Amount</label>
                      <input type="text" class="form-control" id="amount" placeholder="Amount">
                    </div>
                    <div class="form-group mb-2" style="width: 120px">
                        <label for="staticEmail2" class="sr-only" >Message</label>
                        <input type="text" readonly class="form-control-plaintext" id="staticEmail2" value="Message(Optional)" disabled>
                      </div>
                      <div class="form-group mx-sm-4 mb-2">
                       <textarea id="message"></textarea>
                      </div>
                    <button type="submit" class="btn btn-primary mb-2">Submit</button>
                  </form>




            </div>
        </div>
        @endif
      <!-- Default box -->
      <div class="card card-solid">
        <div class="card-body pb-0">
            <table id="wallet_request_table"  class="table table-bordered display wallet_request_table">
                <thead class="thead-dark" style="background:black;color:white">
                  <tr>
                    <th scope="col">#</th>
                    @if(auth()->user()->role =='admin')
                    <th scope="col">Reseller Name</th>
                    @endif
                    <th scope="col">Message</th>
                    <th scope="col">Requested Amount</th>
                    <th scope="col">Approved Amount</th>
                    <th scope="col">Requested Date</th>
                    <th scope="col">Approved Date</th>
                    <th scope="col">Status</th>
                    @if(auth()->user()->role =='admin')
                    <th scope="col">Action</th>
                    @endif
                  </tr>
                </thead>
                <tbody id="wallet_data">


                </tbody>

              </table>
        </div>

        <div class="modal fade" id="edit" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
            <div class="modal-dialog">
            <div class="modal-content">

                @csrf
                <div style="padding: 30px">
                    <form id="approved_form">
                        <div class="form-group">
                          <label for="exampleInputEmail1">Requested Amount</label>
                          <input type="text" class="form-control" id="requested_amount" aria-describedby="emailHelp" placeholder="Requested Amount" disabled>
                        </div>
                        <div class="form-group">
                          <label for="exampleInputPassword1">Approved Amount</label>
                          <input type="text" class="form-control" id="approved_amount" placeholder="Approved Amount">
                          <input type="hidden" id="due_id">
                        </div>

                        <button type="submit" class="btn btn-primary" style="float: right;">Submit</button>
                      </form>
                    {{-- <input class="form-control" type="hidden" name="user_id" value="{{$item->id}}">
                    <input class="form-control" value="{{ $item->wallet }}" type="number" step="0.01" name="balance">
                    <button class="btn btn-success btn-sm"  type="submit">Edit Balance For {{$item->first_name}}</button> --}}
                </div>

            </div>
            </div>
        </div>
        <!-- /.card-body -->

        <!-- /.card-footer -->
      </div>
      <!-- /.card -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->


  <!-- /.modal -->
@endsection

@section('scripts')
<!-- jQuery -->
<script src="{{asset('js/jquery.min.js')}}"></script>
<!-- Bootstrap 4 -->
<script src="{{asset('js/bootstrap.bundle.min.js')}}"></script>
<!-- Theme JS -->
<script src="{{asset('js/admin.js')}}"></script>
<!-- Custome JS -->
<script src="{{asset('js/custom.js')}}"></script>
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>
<script src="https://unpkg.com/izitoast/dist/js/iziToast.min.js" type="text/javascript"></script>
<script>
    let get_wallet_data = '{{route("get-wallet-data")}}';
    let user_role = '{{ auth()->user()->role }}';
</script>
<script>

    $(function(){

        get_data();



        $( "#wallet_submit" ).submit(function( event ) {
        event.preventDefault();
        var formdata = new FormData();
        formdata.append('amount',$('#amount').val());
        formdata.append('message',$("#message").val());
      $.ajax({
        processData: false,
        contentType: false,
        url: "/amount_request",
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
            get_data();
            //load_recent_recharge();

            $('.cover-spin').hide(0)
            $("#amount").val("");
            $("#message").val("");
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
                    message: "Your Request has been placed successfully",

                });


        },
       });

    });

    })
    $( "#approved_form" ).submit(function( event ) {
        event.preventDefault();

        swal({
  title: "Are you sure?",
  icon: "warning",
  buttons: true,
  dangerMode: true,
})
.then((willDelete) => {
  if (willDelete) {

    var formdata = new FormData();
        formdata.append('id',$("#due_id").val());
        formdata.append('approved_amount',$("#approved_amount").val());
        $.ajax({
        processData: false,
        contentType: false,
        url: "/approved_amount",
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

            $("#edit").modal('hide');
            get_data();
        },
       });
  } else {

  }
});




    })
    function approve_amount(id)
    {
        $("#due_id").val(id);
        $.ajax({
            type: "GET",
            dataType: "json",
            url: '/get_requested_amount',
            data: {'id': id},
            success: function(data){
                $("#requested_amount").val(data.requested_amount)
                $("#edit").modal('show');
            }
        });

    }
//     function get_data2()
//     {

//     var table = $('.invoice_table').DataTable();
//     table.destroy();

//     var table = $('.invoice_table').DataTable({

//         processing: true,
//         serverSide: true,

//         ordering:false,
//         searchPanes: {
//             orderable: false
//         },
//         dom: 'Plfrtip',
//         columnDefs: [
//     { "orderable": false, "targets": "_all" } // Applies the option to all columns
//   ],
//         ajax: {

//             "url":'get_all_invoice',
//             "type":'POST',
//             "data":{
//                 'start_date':$(".start_date").val(),
//                 'end_date':$(".end_date").val(),
//                 'type':$('#ExampleSelect option:selected').val(),
//                 'retailer_id':$('#reseller option:selected').val()

//             }


//             },
//         deferRender: true,
//         columns: [
//             //   {data: 'sl_no'},

//             {data:'txid',name:'txid',orderable:false},
//             {data:'number',name:'number'},
//             {data:'date',name:'date'},
//             {data:'type',name:'type'},
//             {data:'cost',name:'cost'},
//             {data:'profit',name:'profit'},
//             {data:'invoice',name:'invoice'}


//   ],

//   drawCallback: function () {
//             var api = this.api();
//             datatable_sum(api, false);
//         }


//     });
//     }

    function get_data()
    {

        var table = $('.wallet_request_table').DataTable();
         table.destroy();
        $('#wallet_data').empty();
        $.ajax({

        url:get_wallet_data ,
        type:"get",

        success:function(response){


            var item = response;


            for (var i = 0; i < item.length; i++){
                var status = item[i].status;
            let class_name = '';
            if(status == 'pending')
            {
                class_name = 'yellow';
            }
            else if(status == 'approved')
            {
                class_name = 'green';
            }
            else
            {
                class_name = 'red';
            }
            admin_column = '<td class="text-center"><button onclick="approve_amount('+item[i].id+')" class="btn btn-success"  >Edit</button></td>';
            added_row = '<tr class="bg-ocean">'
        + '<td>' + Number(i+1) +  '</td>'
        ;
        if(user_role == 'admin')
        {
            added_row+='<td>' + item[i].reseller_name +  '</td>'
        }
        added_row+=
        '<td>' + item[i].message +  '</td>'
        + '<td>' + item[i].requested_amount +  '</td>'
        + '<td>' + item[i].approved_amount +  '</td>'
        + '<td>' + item[i].requested_date +  '</td>'
        + '<td>' + item[i].approved_date +  '</td>'
        + '<td class="'+class_name+'" style="font-weight:bold">' + item[i].status +  '</td>'
        ;
        if(user_role == 'admin' && item[i].status == 'pending')
        {
        added_row+=admin_column
        }

        + '</tr>';
        $('#wallet_data').append(added_row)
        };
        $('.wallet_request_table').DataTable({
            "pageLength": 100,
            "sort":false
        });
        }
        });

    }



  $(".confirm").confirm();
</script>
@endsection
