@extends('front.layout.courier')
@section('header')
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
  <title>Ticket</title>

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
            <h1 class="d-inline-block">Ticket</h1>
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
            <div class="card-body pb-0">
                <form  id="ticket_submit">
                    <div class="form-row">


                      <div class="form-group col-md-6">
                        <label for="inputPassword4">Message</label>
                        <textarea id="message" class="form-control" rows="3"></textarea>
                      </div>

                      <div class="form-group col-md-4">
                        <label for="inputPassword4">Attachment</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="document" name="document">
                            <label class="custom-file-label " for="customFile">Choose file</label>
                          </div>
                      </div>
                      <div class="form-group col-md-2">
                        <input type='submit' class="btn btn-primary" value="Submit" style="margin-top: 32px">
                      </div>

                    </div>


                  </form>
            </div>
        </div>
        @endif


        <div class="modal fade" id="edit" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
            <div class="modal-dialog">
            <div class="modal-content">

                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Admin Answer</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                <div class="modal-body">
                <div >
                    <form id="approved_form">




                        <div class="form-group">
                            <label for="exampleFormControlTextarea1">Message</label>
                            <textarea id="admin_message" class="form-control" rows="4"> </textarea>
                            <input id="hidden_id" type="hidden">
                        </div>


                            <button  value="accept"  type="submit" class="btn btn-primary" style="float: right;margin-left:10px">Submit</button>





                    {{-- <input class="form-control" type="hidden" name="user_id" value="{{$item->id}}">
                    <input class="form-control" value="{{ $item->wallet }}" type="number" step="0.01" name="balance">
                    <button class="btn btn-success btn-sm"  type="submit">Edit Balance For {{$item->first_name}}</button> --}}
                </div>
            </div>

            </div>
            </div>
        </div>
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

                    <th scope="col">Problem</th>
                    <th scope="col">Attachment</th>
                    <th scope="col">Answer</th>
                    <th scope="col">Status</th>
                    <th scope="col">Date</th>

                    @if(auth()->user()->role =='admin')

                    <th scope="col">Action</th>
                    @endif
                  </tr>
                </thead>
                <tbody id="wallet_data">


                </tbody>
            </form>
              </table>
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
    let get_ticket_data = '{{route("get-ticket-data")}}';
    let user_role = '{{ auth()->user()->role }}';
</script>
<script>

    $(function(){

        get_data();
        $('input[type="file"]').change(function(e){
        var fileName = e.target.files[0].name;
        //$(e.target).siblings('.custom-file-label').html(fileName);//more than one file
        $('.custom-file-label').html(fileName);
    });


        $( "#ticket_submit" ).submit(function( event ) {
        event.preventDefault();
        var formdata = new FormData();

        formdata.append('message',$("#message").val());
        formdata.append('document',$('#document')[0].files[0]);
      //  formdata.append('wallet_type' $(".wallet_type :selected").val());

      $.ajax({
        processData: false,
        contentType: false,
        url: "ticket/ticket_submit",
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

    // $("#accept_direct").click(function(){
    //   alert('hel')

    // })


    $( "#approved_form" ).submit(function( event ) {
      event.preventDefault();
      var formdata = new FormData();
        formdata.append('id',$("#hidden_id").val());
        formdata.append('admin_message',$("#admin_message").val());
        $.ajax({
        processData: false,
        contentType: false,
        url: "ticket/ticket_answer",
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
                    message: "Updated Successfully",

                });
                var table = $('.wallet_request_table').DataTable();
            get_data();
            $("#edit").modal('hide');



        },
       });





    })

    function approve_amount(id)
    {
        $("#hidden_id").val(id);
        $("#edit").modal('show');

    }


    function get_data()
    {

        var table = $('.wallet_request_table').DataTable();
         table.destroy();
        $.ajax({

        url:get_ticket_data ,
        type:"get",
        beforeSend: function () {
            $('.cover-spin').show(0)
            },

        success:function(response){

            $('.cover-spin').hide(0)
            var item = response;



            for (var i = 0; i < item.length; i++){
                var status = item[i].status;
                var url = '{{ URL::asset('/storage//') }}'
                var image = "<a href=\"" + url+'/'+item[i].document + "\"  download>File</a>"
            let class_name = '';
            if(status == 'pending')
            {
                class_name = 'yellow';
            }
            else if(status == 'answered')
            {
                class_name = 'green';
            }
            else
            {
                class_name = 'red';
            }
            admin_column = '<td class="text-center"><button onclick="approve_amount('+item[i].id+')" class="btn btn-success" >Answer</button></td>';
            added_row = '<tr class="bg-ocean">'
        + '<td>' + Number(i+1) +  '</td>'
        ;
        if(user_role == 'admin')
        {
            added_row+='<td>' + item[i].reseller_name +  '</td>'
        }

        added_row+=
            '<td>' + item[i].problem_description +  '</td>'
        + '<td>' + image +  '</td>'
        + '<td>' + item[i].admin_message +  '</td>'
        + '<td class="'+class_name+'" style="font-weight:bold">' + item[i].status +  '</td>'
        + '<td>' + item[i].requested_date +  '</td>'
        ;
        if(user_role == 'admin')
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
