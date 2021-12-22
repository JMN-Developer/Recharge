@extends('front.layout.courier')
@section('header')
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Retailer Profile</title>

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
</head>
<style type="text/css">
  .modal-content{
    border:whitesmoke 6px solid;
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
                    <th scope="col">Balance</th>
                    <th scope="col" style="width: 12%">Limit</th>
                    <th scope="col">Comission</th>
                    <th scope="col">Cargo Due</th>
                    <th scope="col">Profile</th>

                  </tr>
                </thead>
                <tbody>
                    <?php $i=1 ?>
                    @foreach ($data as $item)
                    <tr>
                        <td>{{$i++}}</td>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->first_name }} {{ $item->last_name }}</td>
                        <td>{{ $item->email }}</td>
                        <td class="text-center font-weight-bold">{{ $item->wallet }}</td>
                        <td class="text-center font-weight-bold">{{ $item->due }}
                            <br>
                              <span>
                                <button type="button" data-toggle="modal" id="{{$item->id}}modal_id" data-target="#boom2{{$item->id}}" class="btn btn-sm btn-info mt-1">
                                    <i class="fas fa-edit"></i>
                                  </button>
                              </span>
                        </td>
                        <td class="text-center">
                            <button style="margin-top:28px" class="btn btn-sm btn-info" data-toggle="modal" id="{{$item->id}}modal_id" data-target="#com{{$item->id}}">
                            <i class="fas fa-edit"></i>
                            </button>
                        </td>
                        <td class="text-center font-weight-bold">{{ $item->cargo_due }}
                            <br>
                            <button type="button" data-toggle="modal" id="{{$item->id}}modal_id" data-target="#cargo1{{$item->id}}" class="btn btn-sm btn-primary mt-1">
                                <i class="fas fa-plus"></i>
                              </button>
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
                          <a href="/reseller/delete/{{ $item->id }}" class="btn btn-sm btn-danger mt-1 confirm">
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
                                    <h5 class="modal-title">Edit Limit</h5>
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
                                          <button class="btn btn-success btn-sm mt-3"  type="submit">Edit Limit For {{$item->first_name}}</button>
                                        </div>
                                      </form>
                                  </div>

                              </div>
                            </div>
                          </div>

                          <div class="modal fade bd-example-modal-sm" id="com{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-sm">
                              <div class="modal-content">
                                <form action="{{ route("AddCom") }}" method="post">
                                  @csrf
                                  <div>
                                    <input class="form-control" type="hidden" name="user_id" value="{{$item->id}}">
                                    <label for="">Sim Commission :</label><br>
                                    <small>Default Admin Commission is {{ $item->admin_sim_commission }}</small>
                                    <input class="form-control"
                                    @if (Auth::user()->role == 'admin')
                                      value="{{$item->admin_sim_commission}}"
                                    @else
                                      value="{{$item->sim}}"
                                    @endif
                                    type="number" step="0.01" name="sim">
                                    <label for="">Phone Commission :</label><br>
                                    <small>Default Admin Commission is {{ $item->admin_mobile_commission }}</small>
                                    <input class="form-control"
                                    @if (Auth::user()->role == 'admin')
                                      value="{{$item->admin_mobile_commission}}"
                                    @else
                                      value="{{$item->mobile}}"
                                    @endif
                                    type="number" step="0.01" name="mobile">
                                    <label for="">Cargo Commission :</label><br>
                                    <small>Default Admin Commission is {{ $item->admin_cargo_commission }}</small>
                                    <input class="form-control"
                                    @if (Auth::user()->role == 'admin')
                                      value="{{$item->admin_cargo_commission}}"
                                    @else
                                      value="{{$item->cargo}}"
                                    @endif
                                     type="number" step="0.01" name="cargo">
                                    <label for="">International Recharge Commission :</label><br>
                                    <small>Default Admin Commission is {{ $item->admin_international_recharge_commission }}</small>
                                    <input class="form-control"
                                    @if (Auth::user()->role == 'admin')
                                      value="{{$item->admin_international_recharge_commission}}"
                                    @else
                                      value="{{$item->international_recharge}}"
                                    @endif
                                     type="number" step="0.01" name="international_recharge">

                                     <label for="">International Recharge Profit :</label><br>

                                     <input class="form-control"
                                     @if (Auth::user()->role == 'admin')
                                     value="{{$item->reseller_profit->international_recharge_profit}}"
                                     @endif
                                      type="number" step="0.01" name="international_recharge_profit">


                                    <label for="">Domestic Recharge Commission :</label><br>
                                    <small>Default Admin Commission is {{ $item->admin_recharge_commission }}</small>
                                    <input class="form-control"
                                    @if (Auth::user()->role == 'admin')
                                      value="{{$item->admin_recharge_commission}}"
                                    @else
                                      value="{{$item->recharge}}"
                                    @endif
                                     type="number" step="0.01" name="recharge"> <br>

                                     <label for="">Domestic Recharge Profit :</label><br>

                                     <input class="form-control"
                                     @if (Auth::user()->role == 'admin')
                                     value="{{$item->reseller_profit->domestic_recharge_profit}}"
                                     @endif
                                      type="number" step="0.01" name="domestic_recharge_profit">

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


                          <div class="modal fade bd-example-modal-sm" id="cargo2{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-sm">
                              <div class="modal-content">
                                <form action="{{url('/edit_cargo_due')}}" method="post">
                                  @csrf
                                  <div>
                                    <input class="form-control" type="hidden" name="user_id" value="{{$item->id}}">
                                    <input class="form-control" type="number"  step="0.01" name="due">
                                    <button class="btn btn-success btn-sm"  type="submit">Edit Cargo Due For {{$item->first_name}}</button>
                                  </div>
                                </form>
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
<script>
    $('#bill_table').DataTable({

    });

  $(".confirm").confirm({
    title: 'This will delete the user!',
  });
</script>
@endsection
