@extends('front.layout.master')
@section('header')
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Retailer Profile</title>
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{asset('css/fontawesome-free/css/all.min.css')}}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{asset('css/admin.min.css')}}">

  <link rel="stylesheet" href="{{asset('css/style.css')}}">
  <link rel="icon" href="https://jmnation.com/images/jm-transparent-logo.png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
  <link rel="stylesheet" href="https://unpkg.com/izitoast/dist/css/iziToast.min.css">
        {{-- datatable css --}}
    <link href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css" rel='stylesheet'>
</head>
<style type="text/css">
  .modal-content{
    border:whitesmoke 6px solid;
  }
  label{
      margin-top: 10px;
      margin-bottom: 0px
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
            <h1 class="d-inline-block">Retailer Details</h1>
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

      <!-- Default box -->
      <div class="card card-solid">
        <div class="card-body pb-0">
            <table id="bill_table"  class="table table-bordered display">
                <thead class="thead-dark" style="background:black;color:white">
                  <tr>
                    <th scope="col">#</th>
                    <th scope="col">Id</th>
                    <th scope="col" style="width: 18%">Reseller Name</th>
                    <th scope="col">Email</th>
                    <th scope="col">International Balance</th>
                    <th scope="col">Domestic Balance</th>
                    <th scope="col" style="width: 12%">International Limit</th>
                    <th scope="col" style="width: 12%">Domestic Limit</th>
                    <th scope="col">Comission</th>
                    <th scope="col">Sim Due</th>
                    <th scope="col">Cargo Due</th>
                    <th scope="col">Profile</th>

                  </tr>
                </thead>
                <tbody>
                    <?php $i=1 ?>
                    @foreach ($data as $item)
                    <tr>
                        <td>{{$i++}}</td>
                        <td>{{ $item->user_id }}</td>
                        <td>{{ $item->first_name }} {{ $item->last_name }}</td>
                        <td>{{ $item->email }}</td>
                        <td class="text-center font-weight-bold">{{ $item->wallet }}</td>
                        <td class="text-center font-weight-bold">{{ $item->domestic_wallet }}</td>
                        <td class="text-center font-weight-bold">{{ $item->limit_usage }}/{{ $item->due }}
                            <br>
                              <span>
                                <button type="button" data-toggle="modal" id="{{$item->id}}modal_id" data-target="#boom2{{$item->id}}" class="btn btn-sm btn-info mt-1">
                                    <i class="fas fa-edit"></i>
                                  </button>
                              </span>
                        </td>
                        <td class="text-center font-weight-bold">{{ $item->domestic_limit_usage }}/{{ $item->domestic_due }}
                            <br>
                              <span>
                                <button type="button" data-toggle="modal" id="{{$item->id}}modal_id" data-target="#boom_domestic{{$item->id}}" class="btn btn-sm btn-info mt-1">
                                    <i class="fas fa-edit"></i>
                                  </button>
                              </span>
                        </td>
                        <td class="text-center">
                            <button style="margin-top:28px" class="btn btn-sm btn-info" data-toggle="modal" id="{{$item->id}}modal_id" data-target="#com{{$item->id}}">
                            <i class="fas fa-edit"></i>
                            </button>
                        </td>
                        <td class="text-center font-weight-bold">{{ $item->sim_wallet }}
                          <br>

                            <span>
                              <button type="button" data-toggle="modal" id="{{$item->id}}modal_id" data-target="#sim{{$item->id}}" class="btn btn-sm btn-info mt-1">
                                  <i class="fas fa-edit"></i>
                                </button>
                            </span>
                      </td>

                        <td class="text-center font-weight-bold">{{ $item->cargo_wallet }}
                            <br>

                              <span>
                                <button type="button" data-toggle="modal" id="{{$item->id}}modal_id" data-target="#cargo2{{$item->id}}" class="btn btn-sm btn-info mt-1">
                                    <i class="fas fa-edit"></i>
                                  </button>
                              </span>
                        </td>
                        <td class="text-center">
                            <a href="/reseller/edit/{{ $item->id }}" class="btn btn-sm btn-info mt-1">
                            <i class="fas fa-edit"></i>
                          </a>
                          <br>
                          <a type="button"  onclick="delete_user({{ $item->id }})" class="btn btn-sm btn-danger mt-1">
                            <i class="fas fa-trash"></i>
                          </a>
                        </td>

                    </tr>
                    @if (Auth::user()->role == 'admin')
                        <div class="modal fade bd-example-modal-sm" id="edit{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-sm">
                            <div class="modal-content">
                                <form action="{{url('/edit_wallet')}}" method="post">
                                @csrf
                                <div>
                                    <input class="form-control" type="hidden" name="user_id" value="{{$item->id}}">
                                    <input class="form-control" value="{{ $item->wallet }}" type="number" step="0.01" name="balance">
                                    <button class="btn btn-success btn-sm"  type="submit">Edit Balance For {{$item->first_name}}</button>
                                </div>
                                </form>
                            </div>
                            </div>
                        </div>

                        <div class="modal fade bd-example-modal-sm" id="boom1{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-sm">
                              <div class="modal-content">
                                <form action="{{url('/add_balance')}}" method="post">
                                  @csrf
                                  <div>
                                    <input class="form-control" type="hidden" name="user_id" value="{{$item->id}}">
                                    <input class="form-control" type="number" step="0.01" name="due">
                                    <button class="btn btn-success btn-sm"  type="submit">Add Due For {{$item->first_name}}</button>
                                  </div>
                                </form>
                              </div>
                            </div>
                          </div>

                          <div class="modal fade bd-example-modal-sm" id="boom2{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-sm">
                              <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit International Limit</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                      <span aria-hidden="true">&times;</span>
                                    </button>
                                  </div>
                                  <div class="modal-body">
                                    <form action="{{url('/edit_limit')}}" method="post">
                                        @csrf
                                        <div>
                                          <input class="form-control" type="hidden" name="user_id" value="{{$item->id}}">
                                          <input class="form-control" type="number" step="0.01" name="due">
                                          <button class="btn btn-success btn-sm mt-3"  type="submit">Edit International Limit For {{$item->first_name}}</button>
                                        </div>
                                      </form>
                                  </div>

                              </div>
                            </div>
                          </div>

                          <div class="modal fade bd-example-modal-sm" id="boom_domestic{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-sm">
                              <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Domestic Limit</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                      <span aria-hidden="true">&times;</span>
                                    </button>
                                  </div>
                                  <div class="modal-body">
                                    <form action="{{url('/edit_limit_domestic')}}" method="post">
                                        @csrf
                                        <div>
                                          <input class="form-control" type="hidden" name="user_id" value="{{$item->id}}">
                                          <input class="form-control" type="number" step="0.01" name="domestic_due">
                                          <button class="btn btn-success btn-sm mt-3"  type="submit">Edit Domestic Limit For {{$item->first_name}}</button>
                                        </div>
                                      </form>
                                  </div>

                              </div>
                            </div>
                          </div>

                          <div class="modal fade bd-example-modal" id="com{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                              <div class="modal-content">
                                  <div class="modal-header">
                                    <h5 class="modal-title">Add Comission</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                      </button>
                                  </div>
                                  <div class="modal-body">
                                    <form action="{{ route("AddCom") }}" method="post">
                                        @csrf
                                        <div>
                                          <input class="form-control" type="hidden" name="user_id" value="{{$item->id}}">

                                          {{-- <label for="">Phone Commission :</label><br>
                                          <small>Default Admin Commission is {{ $item->admin_mobile_commission }}</small>
                                          <input class="form-control"
                                          @if (Auth::user()->role == 'admin')
                                            value="{{$item->admin_mobile_commission}}"
                                          @else
                                            value="{{$item->mobile}}"
                                          @endif
                                          type="number" step="0.01" name="mobile">
                                          <br> --}}
                                          <label for="">Cargo Goods Profit :</label><br>
                                          <small>Default Admin Cargo Goods Proift is {{ $item->cargo_goods_profit }}</small>
                                          <input class="form-control"
                                          @if (Auth::user()->role == 'admin')
                                            value="{{$item->cargo_goods_profit}}"
                                          @endif
                                           type="number" step="0.01" name="cargo_goods_profit"><br>

                                           <label for="">Cargo Document Profit :</label><br>
                                           <small>Default Admin Cargo Documents Proift is {{ $item->cargo_documents_profit }}</small>
                                           <input class="form-control"
                                           @if (Auth::user()->role == 'admin')
                                             value="{{$item->cargo_documents_profit}}"

                                           @endif
                                            type="number" step="0.01" name="cargo_documents_profit"><br>

                                          <label for="">International Recharge Commission :</label><br>
                                          <small>Default Admin Commission is {{ $item->admin_international_recharge_commission }}</small>
                                          <input class="form-control"
                                          @if (Auth::user()->role == 'admin')
                                            value="{{$item->admin_international_recharge_commission}}"
                                          @else
                                            value="{{$item->international_recharge}}"
                                          @endif
                                           type="number" step="0.01" name="international_recharge">
                                           <br>

                                           <label for="">Domestic Recharge Profit :</label><br>
                                           <small>Default Admin Domestic Recharge Profit is {{$item->reseller_profit->domestic_recharge_profit }}</small>

                                           <input class="form-control"
                                           @if (Auth::user()->role == 'admin')
                                           value="{{$item->reseller_profit->domestic_recharge_profit}}"
                                           @endif
                                            type="number" step="0.01" name="domestic_recharge_profit">
                                            <br>
                                           <label for="">Pin Commission :</label><br>
                                          <small>Default Admin Commission is {{ $item->admin_pin_commission }}</small>
                                          <input class="form-control"
                                          @if (Auth::user()->role == 'admin')
                                            value="{{$item->admin_pin_commission}}"
                                          @else
                                            value="{{$item->pin}}"
                                          @endif
                                           type="number" step="0.01" name="pin"> <br>
                                          <button class="btn btn-success btn-sm"  type="submit">Set Commission For {{$item->first_name}}</button>
                                        </div>
                                      </form>
                                  </div>

                              </div>
                            </div>
                        </div>
                        <div class="modal fade bd-example-modal-sm" id="cargo1{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-sm">
                              <div class="modal-content">
                                <form action="{{url('/add_cargo_due')}}" method="post">
                                  @csrf
                                  <div>
                                    <input class="form-control" type="hidden" name="user_id" value="{{$item->id}}">
                                    <input class="form-control" type="number" step="0.01" name="due">
                                    <button class="btn btn-success btn-sm"  type="submit">Add Cargo Due For {{$item->first_name}}</button>
                                  </div>
                                </form>
                              </div>
                            </div>
                          </div>
                          <div class="modal fade bd-example-modal-sm" id="sim{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-sm">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h5 class="modal-title">Sim Due</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                      </button>
                                </div>
                                <div class="modal-body">

                                <form action="{{url('/edit_sim_due')}}" method="post">
                                  @csrf
                                  <div>
                                    <input class="form-control" type="hidden" name="user_id" value="{{$item->id}}">
                                    <input class="form-control" type="number"  step="0.01" name="due"><br>
                                    <button class="btn btn-success btn-sm" style="float:right"  type="submit">Update Sim Due For {{$item->first_name}}</button>
                                  </div>
                                </form>
                                </div>
                              </div>
                            </div>
                          </div>

                          <div class="modal fade bd-example-modal-sm" id="cargo2{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-sm">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h5 class="modal-title">Cargo Due</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                      </button>
                                </div>
                                <div class="modal-body">
                                <form action="{{url('/edit_cargo_due')}}" method="post">
                                  @csrf
                                  <div>
                                    <input class="form-control" type="hidden" name="user_id" value="{{$item->id}}">
                                    <input class="form-control" type="number"  step="0.01" name="due"><br>
                                    <button class="btn btn-success btn-sm"  style="float:right"  type="submit">Update Cargo Due For {{$item->first_name}}</button>
                                  </div>
                                </form>
                                </div>
                              </div>
                            </div>
                          </div>
                    @endif
                    @endforeach
                </tbody>

              </table>
        </div>
        <!-- /.card-body -->
        <div class="card-footer">
          {{-- <nav aria-label="Contacts Page Navigation">
            <ul class="pagination justify-content-center m-0">
              <li class="page-item active"><a class="page-link" href="#">1</a></li>
              <li class="page-item"><a class="page-link" href="#">2</a></li>
              <li class="page-item"><a class="page-link" href="#">3</a></li>
              <li class="page-item"><a class="page-link" href="#">4</a></li>
              <li class="page-item"><a class="page-link" href="#">5</a></li>
              <li class="page-item"><a class="page-link" href="#">6</a></li>
              <li class="page-item"><a class="page-link" href="#">7</a></li>
              <li class="page-item"><a class="page-link" href="#">8</a></li>
            </ul>
          </nav> --}}
        </div>
        <!-- /.card-footer -->
      </div>
      <!-- /.card -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->


  <div class="modal fade" id="modal-default">
    <div class="modal-dialog">
      <div class="modal-content client_message-box">
        <div class="modal-header">
          <h4 class="modal-title">
            <img class="direct-chat-img" src="images/user1-128x128.jpg" alt="message user image" style="margin-right: 10px;">
            Alexander Pierce
          </h4>
          <div class="card-tools">
            <span title="3 New Messages" class="badge badge-primary">3</span>
            <button type="button" class="btn btn-tool" data-dismiss="modal" aria-label="Close">
              <i class="fas fa-times"></i>
            </button>
          </div>
        </div>
        <div class="modal-body">
          <div class="card-body">
            <div class="direct-chat-messages">
              <div class="direct-chat-msg">
                <div class="direct-chat-infos clearfix">
                  <span class="direct-chat-name float-left">Alexander Pierce</span>
                  <span class="direct-chat-timestamp float-right">23 Jan 2:00 pm</span>
                </div>
                <img class="direct-chat-img" src="images/user1-128x128.jpg" alt="message user image">
                <div class="direct-chat-text">
                  Lorem ipsum dolor, sit amet consectetur adipisicing elit. Voluptates, reprehenderit.
                </div>
              </div>


              <div class="direct-chat-msg right">
                <div class="direct-chat-infos clearfix">
                  <span class="direct-chat-name float-right">Sarah Bullock</span>
                  <span class="direct-chat-timestamp float-left">23 Jan 2:05 pm</span>
                </div>
                <img class="direct-chat-img" src="images/user3-128x128.jpg" alt="message user image">
                <div class="direct-chat-text">
                  Lorem ipsum dolor sit amet.
                </div>
              </div>

              <div class="direct-chat-msg">
                <div class="direct-chat-infos clearfix">
                  <span class="direct-chat-name float-left">Alexander Pierce</span>
                  <span class="direct-chat-timestamp float-right">23 Jan 5:37 pm</span>
                </div>
                <img class="direct-chat-img" src="images/user1-128x128.jpg" alt="message user image">
                <div class="direct-chat-text">
                  Lorem ipsum dolor sit amet consectetur adipisicing elit. Sequi, voluptatum quasi.
                </div>
              </div>

              <div class="direct-chat-msg right">
                <div class="direct-chat-infos clearfix">
                  <span class="direct-chat-name float-right">Sarah Bullock</span>
                  <span class="direct-chat-timestamp float-left">23 Jan 6:10 pm</span>
                </div>
                <img class="direct-chat-img" src="images/user3-128x128.jpg" alt="message user image">
                <div class="direct-chat-text">
                  Lorem ipsum dolor sit amet consectetur.
                </div>
              </div>
            </div>
          </div>
          <div class="card-footer">
            <form action="#" method="post">
              <div class="input-group">
                <input type="text" name="message" placeholder="Type Message ..." class="form-control">
                <span class="input-group-append">
                  <button type="button" class="btn btn-primary">Send</button>
                </span>
              </div>
            </form>
          </div>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>


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
  $(function(){
    var toast = document.querySelector('.iziToast');
        var message = sessionStorage.getItem('message');
        sessionStorage.removeItem('message');
    if(toast)
                {
                iziToast.hide({}, toast);
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
  })
  function delete_user(id)
  {

    swal({
  title: "Are you sure?",
  icon: "warning",
  buttons: true,
  dangerMode: true,
})
.then((willDelete) => {
  if (willDelete) {

       var formdata = new FormData();
        formdata.append('id',id);
        let url = "{{ route('delete-reseller') }}";
        $.ajax({
        processData: false,
        contentType: false,
        url: url,
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

          location.reload();
          sessionStorage.setItem('success',true);
          sessionStorage.setItem('message','Retailer  Deleted Successfully');

        },
       });
  } else {

  }
  });
  }
    $('#bill_table').DataTable({

    });

  $(".confirm").confirm({
    title: 'This will delete the user!',
  });
</script>
@endsection
