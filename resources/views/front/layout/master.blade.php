<?php
  use Carbon\Carbon;
  $admin_profit = App\Models\RechargeHistory::whereYear('created_at', Carbon::now()->year)
    ->whereMonth('created_at', Carbon::now()->month)
    ->where('type','International')
    ->sum('admin_com');
  $reseller_profit = App\Models\RechargeHistory::whereYear('created_at', Carbon::now()->year)
    ->where('reseller_id',Auth::user()->id)
    ->where('type','International')
    ->whereMonth('created_at', Carbon::now()->month)
    ->sum('reseller_com');

$current_wallet = App\Models\User::sum('wallet');
$current_limit = App\Models\User::sum('due');
$current_limit_usage = App\Models\User::sum('limit_usage');
$services = App\Models\service_control::get(['service_name','permission'])->toArray();



$total_due = $current_wallet+($current_limit-$current_limit_usage);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{asset('css/style.css')}}">
</head>
@yield('header')

<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
    </ul>
                          {{-- @if (\Session::has('error'))
                              <div class="alert alert-danger">
                                  <ul>
                                      <li>{!! \Session::get('error') !!}</li>
                                  </ul>
                              </div>
                          @endif
                          @if (\Session::has('status'))
                              <div class="alert alert-success">
                                  <ul>
                                      <li>{!! \Session::get('status') !!}</li>
                                  </ul>
                              </div>
                          @endif

                          @if (\Session::has('success'))
                          <div class="alert alert-success">
                              <ul>
                                  <li>{!! \Session::get('success') !!}</li>
                              </ul>
                          </div>
                      @endif --}}
    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <!-- Navbar Search -->
      <li class="nav-item">
        <a class="nav-link" data-widget="navbar-search" href="#" role="button">
          <i class="fas fa-search"></i>
        </a>
        <div class="navbar-search-block">
          <form class="form-inline">
            <div class="input-group input-group-sm">
              <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
              <div class="input-group-append">
                <button class="btn btn-navbar" type="submit">
                  <i class="fas fa-search"></i>
                </button>
                <button class="btn btn-navbar" type="button" data-widget="navbar-search">
                  <i class="fas fa-times"></i>
                </button>
              </div>
            </div>
          </form>
        </div>
      </li>

      <!-- Messages Dropdown Menu -->
      {{-- <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="far fa-comments"></i>
          <span class="badge badge-danger navbar-badge">3</span>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <a href="#" class="dropdown-item">
            <!-- Message Start -->
            <div class="media">
              <img src="images/user1-128x128.jpg" alt="User Avatar" class="img-size-50 mr-3 img-circle">
              <div class="media-body">
                <h3 class="dropdown-item-title">
                  Brad Diesel
                  <span class="float-right text-sm text-danger"><i class="fas fa-star"></i></span>
                </h3>
                <p class="text-sm">Call me whenever you can...</p>
                <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
              </div>
            </div>
            <!-- Message End -->
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <!-- Message Start -->
            <div class="media">
              <img src="images/user8-128x128.jpg" alt="User Avatar" class="img-size-50 img-circle mr-3">
              <div class="media-body">
                <h3 class="dropdown-item-title">
                  John Pierce
                  <span class="float-right text-sm text-muted"><i class="fas fa-star"></i></span>
                </h3>
                <p class="text-sm">I got your message bro</p>
                <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
              </div>
            </div>
            <!-- Message End -->
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <!-- Message Start -->
            <div class="media">
              <img src="images/user3-128x128.jpg" alt="User Avatar" class="img-size-50 img-circle mr-3">
              <div class="media-body">
                <h3 class="dropdown-item-title">
                  Nora Silvester
                  <span class="float-right text-sm text-warning"><i class="fas fa-star"></i></span>
                </h3>
                <p class="text-sm">The subject goes here</p>
                <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
              </div>
            </div>
            <!-- Message End -->
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item dropdown-footer">See All Messages</a>
        </div>
      </li>
      <!-- Notifications Dropdown Menu -->
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="far fa-bell"></i>
          <span class="badge badge-warning navbar-badge">15</span>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <span class="dropdown-item dropdown-header">15 Notifications</span>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fas fa-envelope mr-2"></i> 4 new messages
            <span class="float-right text-muted text-sm">3 mins</span>
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fas fa-users mr-2"></i> 8 friend requests
            <span class="float-right text-muted text-sm">12 hours</span>
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fas fa-file mr-2"></i> 3 new reports
            <span class="float-right text-muted text-sm">2 days</span>
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a>
        </div>
      </li> --}}
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar fixd-sidebar sidebar-dark-primary">
    <!-- Brand Logo -->
    <a href="/" class="brand-link" style="background: #fff;">
      <img src="{{ asset('images/jm-transparent-logo.png') }}" alt="Courier Logo" class="brand-image" style="opacity: .8">
      <span class="brand-text font-weight-bold"><strong>JM</strong> NATION</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        {{-- <div class="image mt-2">
          <img src="images/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
        </div> --}}
        <div class="info sidebar_profile_info">
          <p style="padding-top:12px">{{Auth::user()->first_name}}</p>
          <p style="padding-top:5px">{{ Auth::user()->company }}</p>
          <p style="padding-top:5px">{{ Auth::user()->user_id }}</p>
          <a href="/reseller/edit/{{ Auth::user()->id}}" class="mr-2">
            <small>Profile</small>
          </a>
          <a href="{{url('/logout')}}">
            <small>Log Out</small>
          </a>
        </div>
      </div>

      <div class="profile-info mt-3">
        <div class="row">
          <div class="col-12">
          @if ( Auth::user()->role == 'admin2' )
            <p class='text-center' style="color: #b9ff38;">JM Nation </p>
          @endif
            @if (auth()->user()->role =='user' )
            {{-- <p>   <a href="{{ route('wallet-request') }}" class="notification">
                <span style="font-weight:bold;color:black">WR</span>
                <span id="wallet_notification_count" class="badge wallet_notification_count"></span>
              </a></p> --}}
            <p style="color: #b9ff38;"><b class="mr-2">International Wallet:</b><span>{{ Auth()->user()->wallet }}</span>

            </p>
            <p><b>International Limit:  </b><span style="margin-left:10px">{{ auth()->user()->limit_usage }}/{{ auth()->user()->due }}</span></p>
            <p><b>Domestic Wallet:  </b><span style="margin-left:10px">{{ auth()->user()->domestic_wallet }}</span></p>
            <p><b>Domestic Limit:  </b><span style="margin-left:10px">{{ auth()->user()->domestic_limit_usage }}/{{ auth()->user()->domestic_due }}</span></p>

            <p style="color: #b9ff38;"><b class="mr-2">Sim:</b><span>{{ Auth()->user()->sim_wallet }}</span></p>
            <p style="color: #b9ff38;"><b class="mr-2">Cargo:</b><span>{{ Auth()->user()->cargo_wallet }}</span></p>


            {{-- <p style="color: #b9ff38;"><b class="mr-2">Profit:</b><span>{{ $reseller_profit }}</span></p> --}}
            @endif
          </div>

          {{-- <div class="col-12">



                <p><b class="mr-2">Corriere: </b><span>{{ Auth::user()->cargo_due }}</span></p>

          </div> --}}

        </div>
        @if (auth()->user()->role == 'admin')
          <div class="row">
            @php
              $ding = DB::table('balances')->where('type','ding')->latest()->first();
              $domestic = DB::table('balances')->where('type','domestic')->latest()->first();
              $reloadly = DB::table('balances')->where('type','reloadly')->latest()->first();
              $ppn =  DB::table('balances')->where('type','ppn')->latest()->first();
              $dtone =  DB::table('balances')->where('type','dtone')->latest()->first();
              $ssl =  DB::table('balances')->where('type','ssl')->latest()->first();
            @endphp
             {{-- <div class="col-12">
              <b class="mr-2">Profit:</b><span>{{ $admin_profit }}&euro;</span>
            </div> --}}

            <div class="col-12">
                <p><b class="mr-2">Current Wallet: </b><span>{{ $total_due }}</span> </p>
              </div>
            <div class="col-12">
              <b class="mr-2">Domestic1:</b><span>{{ $domestic->balance }}&euro;</span>
            </div>
            <div class="col-12">
              <b class="mr-2">International1:</b><span>{{ $ding->balance }}&euro;</span>
            </div>


              <div class="col-12">
                <b class="mr-2">International2:</b><span>{{ $ppn->balance }}&euro;</span>
              </div>

              <div class="col-12">
                <b class="mr-2">International3:</b><span>{{ $reloadly->balance }}&euro;</span>
              </div>

              <div class="col-12">
                <b class="mr-2">International4:</b><span>{{ $dtone->balance }}&euro;</span>
              </div>

              <div class="col-12">
                <b class="mr-2">Bangladesh:</b><span>{{ $ssl->balance }} TK</span>
              </div>

          </div>
        @endif
        {{-- <div class="row">
          <div class="col-6">
            <p><i class="nav-icon fas fa-sim-card mr-4"></i>1230</p>
          </div>
          <div class="col-6">
            <b class="mr-2">Due:</b><span>0.00</span>
          </div>
        </div>
        <div class="row">
          <div class="col-6">
            <p><i class="nav-icon fas fa-truck mr-3"></i>5000</p>
          </div>
          <div class="col-6">
            <b class="mr-2">Due:</b><span>0.00</span>
          </div>
        </div>
        <div class="row">
          <div class="col-6">
            <p><i class="nav-icon fas fa-mobile-alt mr-4 pr-1"></i>300</p>
          </div>
          <div class="col-6">
            <b class="mr-2">Due:</b><span>0.00</span>
          </div>
        </div> --}}
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2 mb-5">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

          <li class="@if(Route::currentRouteName() == '/') nav-item menu-open @endif nav-item">
            <a href="/" class="@if(Route::currentRouteName() == '/') nav-link active @endif nav-link">
              <i class="fa fa-home" aria-hidden="true"></i>
              <p>
                Home
              </p>
            </a>
          </li>
          @if (Auth::user()->recharge_permission == 1 && Auth::user()->role!='admin2')
            <li class="@if(Route::currentRouteName() == 'recharge-int' ||
                          Route::currentRouteName() == 'recharge-italy' ||
                          Route::currentRouteName() == 'bangladesh' ||
                          Route::currentRouteName() == 'international' ||
                          Route::currentRouteName() == 'recharge-gift-card' ||
                          Route::currentRouteName() == 'recharge-calling-card' ||
                          Route::currentRouteName() == 'recharge-reloadly' ||
                          Route::currentRouteName() == 'calling-card' ||
                          Route::currentRouteName() == 'pin' ||
                          Route::currentRouteName() == 'recharge-invoice' ||
                          Route::currentRouteName() == 'pin-invoice' ||
                          Route::currentRouteName() == 'print-all-invoice') nav-item menu-open @endif nav-item">
              <a href="#" class="@if(Route::currentRouteName() == 'recharge-int' ||
                          Route::currentRouteName() == 'recharge-italy' ||
                          Route::currentRouteName() == 'bangladesh' ||
                          Route::currentRouteName() == 'recharge-gift-card' ||
                          Route::currentRouteName() == 'international' ||
                          Route::currentRouteName() == 'recharge-calling-card' ||
                          Route::currentRouteName() == 'pin' ||
                          Route::currentRouteName() == 'recharge-invoice' ||
                          Route::currentRouteName() == 'pin-invoice' ||
                          Route::currentRouteName() == 'print-all-invoice') nav-link active @endif nav-link nav-link">
                <i class="nav-icon fab fa-rev"></i>
                <p>
                  Recharge
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                @if (Auth::user()->role == 'admin'  || service_permission('Bangladeshi Recharge',$services) == 1)
                <li class="nav-item">
                    <a href="{{ route('bangladesh') }}" class="@if(Route::currentRouteName() == 'bangladesh') nav-link active @endif nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Bangladesh</p>
                    </a>
                  </li>
                @endif
                @if (Auth::user()->role == 'admin'  || service_permission('International Recharge',$services) == 1)
                <li class="nav-item">
                  <a href="{{ route('international') }}" class="@if(Route::currentRouteName() == 'international') nav-link active @endif nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>International</p>
                  </a>
                </li>
                @endif
                @if (Auth::user()->role == 'admin'  || service_permission('Domestic Recharge',$services) == 1)
                <li class="nav-item">
                  <a href="{{ route('recharge-italy') }}" class="@if(Route::currentRouteName() == 'recharge-italy') nav-link active @endif nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Italy</p>
                  </a>
                </li>
                @endif
                @if ((Auth::user()->role == 'admin'  || service_permission('Pin',$services) == 1) && (Auth::user()->pin_permission == 1))
                <li class="nav-item">
                  <a href="{{ route('pin') }}" class="@if(Route::currentRouteName() == 'pin') nav-link active @endif nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Gift Card</p>
                  </a>
                </li>
                @endif
                @if(Auth::user()->role == 'admin'  || service_permission('White Calling',$services) == 1)
                <li class="nav-item">
                    <a href="{{ route('calling-card') }}" class="@if(Route::currentRouteName() == 'calling-card') nav-link active @endif nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>White Calling Card</p>
                    </a>
                  </li>
                @endif
                <li class="nav-item">
                  <a href="{{ route('recharge-invoice') }}" class="@if(Route::currentRouteName() == 'recharge-invoice') nav-link active @endif nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>All Invoices</p>
                  </a>
                </li>
                {{-- @if (Auth::user()->pin_permission == 1)
                <li class="nav-item">
                  <a href="{{ route('pin-invoice') }}" class="@if(Route::currentRouteName() == 'pin-invoice') nav-link active @endif nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Pin Invoices</p>
                  </a>
                </li>
                @endif --}}
                {{-- <li class="nav-item">
                  <a href="/recharge/recharge-gift-card" class="@if(Route::currentRouteName() == 'recharge-gift-card') nav-link active @endif nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Gift Card</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/recharge/recharge-calling-card" class="@if(Route::currentRouteName() == 'recharge-calling-card') nav-link active @endif nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Calling Card</p>
                  </a>
                </li> --}}
              </ul>
            </li>
          @endif
          @if (Auth::user()->sim_permission == 1  && Auth::user()->role!='admin2' &&  service_permission('Sim',$services) == 1)
            <li class="@if(Route::currentRouteName() == 'sim-activation' ||
                          Route::currentRouteName() == 'sim-selling' ||
                          Route::currentRouteName() == 'operator' ||
                          Route::currentRouteName() == 'wi-fi') nav-item menu-open @endif nav-item">
              <a href="#" class="@if(Route::currentRouteName() == 'sim-activation' ||
                          Route::currentRouteName() == 'sim-selling' ||
                          Route::currentRouteName() == 'operator' ||
                          Route::currentRouteName() == 'wi-fi') nav-link active @endif nav-link">
                <i class="nav-icon fas fa-sim-card"></i>
                <p>
                  SIM<span class="badge sim_notification_count">3</span>
                  <i class="fas fa-angle-left right"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
              @if (Auth::user()->role == 'admin')
                <li class="nav-item">
                  <a href="/operator" class="@if(Route::currentRouteName() == 'operator') nav-link active @endif nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>SIM Operator</p>
                  </a>
                </li>
              @endif
                <li class="nav-item">
                  <a href="{{ route('sim-activation') }}" class="@if(Route::currentRouteName() == 'sim-activation') nav-link active @endif nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>SIM Activation</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{ route('sim-selling') }}" class="@if(Route::currentRouteName() == 'sim-selling') nav-link active @endif nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>SIM Selling <span class="badge sim_notification_count">3</span></p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{ route('wi-fi') }}" class="@if(Route::currentRouteName() == 'wi-fi') nav-link active @endif nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Offers</p>
                  </a>
                </li>
              </ul>
            </li>
          @endif
          @if ((Auth::user()->cargo_permission == 1  && Auth::user()->role!='admin2') &&  service_permission('Cargo',$services) == 1)
            <li class="@if(Route::currentRouteName() == 'cargo-new-order' || Route::currentRouteName() == 'order-list' || Route::currentRouteName() == 'order-tracking-view' ||Route::currentRouteName() == 'pricing-list' || Route::currentRouteName() == 'order-invoice-view') nav-item menu-open @endif nav-item">
              <a href="#" class="@if(Route::currentRouteName() == 'cargo-new-order' || Route::currentRouteName() == 'order-list' || Route::currentRouteName() == 'order-tracking-view' ||Route::currentRouteName() == 'pricing-list' || Route::currentRouteName() == 'order-invoice-view') nav-link active @endif nav-link">
                <i class="nav-icon fas fa-truck"></i>
                <p>
                  Cargo(Courier Service)
                  <i class="fas fa-angle-left right"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="{{ route('cargo-new-order') }}" class="@if(Route::currentRouteName() == 'cargo-new-order') nav-link active @endif nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>New Order</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{ route('order-list') }}" class="@if(Route::currentRouteName() == 'order-list') nav-link active @endif nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Order List</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{ route('order-tracking-view')}}" class="@if(Route::currentRouteName() == 'order-tracking-view') nav-link active @endif nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Tracking</p>
                  </a>
                </li>
                {{-- <li class="nav-item">
                  <a href="/cargo/order-invoice" class="@if(Route::currentRouteName() == 'order-invoice-view') nav-link active @endif nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Invoice</p>
                  </a>
                </li> --}}
                @if (Auth::user()->role == 'admin')
                <li class="nav-item">
                  <a href="/cargo/pricing-list" class="@if(Route::currentRouteName() == 'pricing-list') nav-link active @endif nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Manage Pricing</p>
                  </a>
                </li>
                @endif
              </ul>
            </li>
          @endif
          @if (Auth::user()->mobile_permission == 1  && Auth::user()->role!='admin2' )
            <li class="@if(Route::currentRouteName() == 'phone-order' ||Route::currentRouteName() == 'add-phone-view' ||  Route::currentRouteName() == 'selling-list') nav-item menu-open @endif nav-item">
              <a href="#" class="@if(Route::currentRouteName() == 'phone-order' || Route::currentRouteName() == 'selling-list') nav-link active menu-open @endif nav-link">
                <i class="nav-icon fas fa-mobile-alt"></i>
                <p>
                  Phone
                  <i class="fas fa-angle-left right"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                @if (Auth::user()->role == 'admin')
                <li class="nav-item">
                  <a href="/phone/add-phone-view" class="@if(Route::currentRouteName() == 'add-phone-view') nav-link active @endif nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Add Phone</p>
                  </a>
                </li>
                @endif
                <li class="nav-item">
                  <a href="{{ route('phone-order') }}" class="@if(Route::currentRouteName() == 'phone-order') nav-link active @endif nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Order</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{ route('selling-list') }}" class="@if(Route::currentRouteName() == 'selling-list') nav-link active @endif nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Selling</p>
                  </a>
                </li>
              </ul>
            </li>
          @endif
          @if ( service_permission('Flight',$services) == 1)
          <li class="@if(Route::currentRouteName() == 'add-flight') nav-item menu-open @endif nav-item">
            <a href="{{url('flights/')}}" target="_blank"  class="@if(Route::currentRouteName() == 'setting') nav-link active @endif nav-link">
              <i class="nav-icon fas fa-plane-departure"></i>
              <p>
                Flight
              </p>
            </a>
          </li>
          @endif

            <li class="@if(Route::currentRouteName() == 'retailer-details' || Route::currentRouteName() == 'retailer-details-admin' || Route::currentRouteName() == 'retailer-action' || Route::currentRouteName() == 'retailer-sign-up') nav-item menu-open @endif nav-item">
              <a href="#" class="@if(Route::currentRouteName() == 'retailer-details' || Route::currentRouteName() == 'retailer-details-admin' || Route::currentRouteName() == 'retailer-action' || Route::currentRouteName() == 'retailer-sign-up') nav-link active @endif nav-link">
                <i class="nav-icon fas fa-users"></i>
                <p>
                  Retailer
                  <i class="fas fa-angle-left right"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  @if(auth()->user()->role=='user' )
                  {{-- <a href="{{ route('retailer-details') }}" class="@if(Route::currentRouteName() == 'retailer-details') nav-link active @endif nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Retailer Details</p>
                  </a> --}}
                  @elseif(auth()->user()->role=='admin')

                  <a href="{{ route('retailer-details-admin') }}" class="@if(Route::currentRouteName() == 'retailer-details-admin') nav-link active @endif nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Retailer Details</p>
                  </a>

                  @endif

                </li>
                @if (Auth::user()->reseller_permission == 1)
                <li class="nav-item">
                  <a href="/retailer/retailer-action" class="@if(Route::currentRouteName() == 'retailer-action') nav-link active @endif nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Retailer Action</p>
                  </a>
                </li>
                @endif

                @if (Auth::user()->reseller_permission == 1)
                <li class="nav-item">
                  <a href="{{ route('retailer-sign-up') }}" class="@if(Route::currentRouteName() == 'retailer-sign-up') nav-link active @endif nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>New Retailer</p>
                  </a>
                </li>
                @endif
              </ul>
            </li>
            <li class="@if(Route::currentRouteName() == 'GeneralNotification') nav-item menu-open @endif nav-item">
                <a href="{{ route('GeneralNotification') }}" class="@if(Route::currentRouteName() == 'GeneralNotification') nav-link active @endif nav-link">
                  <i class="fas fa-wallet" aria-hidden="true"></i>
                  <p>
                    Notification <span class="badge general_notification_count">3</span>

                  </p>
                </a>
              </li>

            <li class="@if(Route::currentRouteName() == 'setting') nav-item menu-open @endif nav-item">
                <a href="{{ route('setting') }}" class="@if(Route::currentRouteName() == 'setting') nav-link active @endif nav-link">
                  <i class="fa fa-cog" aria-hidden="true"></i>
                  <p>
                    Settings
                  </p>
                </a>
              </li>
              @if ( service_permission('Transaction History',$services) == 1)

              <li class="@if(Route::currentRouteName() == 'transaction-history') nav-item menu-open @endif nav-item">
                <a href="{{ route('transaction-history') }}" class="@if(Route::currentRouteName() == 'transaction-history') nav-link active @endif nav-link">
                  <i class="fa fa-history" aria-hidden="true"></i>
                  <p>
                    Transaction History

                  </p>
                </a>
              </li>
              @endif
              @if(auth()->user()->role == 'user')
              <li class="@if(Route::currentRouteName() == 'contact-info') nav-item menu-open @endif nav-item">
                <a href="{{ route('contact-info') }}" class="@if(Route::currentRouteName() == 'contact-info') nav-link active @endif nav-link">
                  <i class="fa fa-address-book" aria-hidden="true"></i>
                  <p>
                    Contact

                  </p>
                </a>
              </li>
              @endif


              @if(auth()->user()->role == 'admin')
              <li class="@if(Route::currentRouteName() == 'report') nav-item menu-open @endif nav-item">
                <a href="{{ route('report') }}" class="@if(Route::currentRouteName() == 'report') nav-link active @endif nav-link">
                  <i class="fas fa-chart-line" aria-hidden="true"></i>
                  <p>
                    Report

                  </p>
                </a>
              </li>
              @endif

              @if(service_permission('Support',$services) == 1)
              <li class="@if(Route::currentRouteName() == 'ticket') nav-item menu-open @endif nav-item">
                <a href="{{ route('ticket') }}" class="@if(Route::currentRouteName() == 'ticket') nav-link active @endif nav-link">
                  <i class="fas fa-wallet" aria-hidden="true"></i>
                  <p>
                    Support <span class="badge complain_notification_count">3</span>

                  </p>
                </a>
              </li>
              @endif

              @if(auth()->user()->role == 'admin' || auth()->user()->role == 'user'  )
              <li class="@if(Route::currentRouteName() == 'wallet-request') nav-item menu-open @endif nav-item">
                <a href="{{ route('wallet-request') }}" class="@if(Route::currentRouteName() == 'wallet-request') nav-link active @endif nav-link">
                  <i class="fas fa-wallet" aria-hidden="true"></i>
                  <p>
                    Wallet Request<span class="badge wallet_notification_count">3</span>

                  </p>
                </a>
              </li>
              @endif


              @if(auth()->user()->role == 'admin')
              <li class="@if(Route::currentRouteName() == 'service-control') nav-item menu-open @endif nav-item">
                <a href="{{ route('service-control') }}" class="@if(Route::currentRouteName() == 'service-control') nav-link active @endif nav-link">
                  <i class="fas fa-wallet" aria-hidden="true"></i>
                  <p>
                    Service Control

                  </p>
                </a>
              </li>
              @endif


            @if(auth()->user()->role == 'admin')
              <li class="@if(Route::currentRouteName() == 'api-activation') nav-item menu-open @endif nav-item">
                <a href="#" class="@if(Route::currentRouteName() == 'api-activation') nav-link active @endif nav-link">
                  <i class="nav-icon fas fa-code-branch"></i>
                  <p>
                    Api Control
                    <i class="fas fa-angle-left right"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">

                    <a href="{{ route('api-activation') }}" class="@if(Route::currentRouteName() == 'api-activation') nav-link active @endif nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Activate</p>
                    </a>


                  </li>

                </ul>
              </li>
              @endif

        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>
  <div class="cover-spin"></div>
  @yield('content')

    <!-- Main Footer -->
  <footer class="main-footer">
    <strong>Copyright &copy; 2014-2021 <a href="#">jmnation.com</a>.</strong> All rights reserved.
  </footer>
</div>
<!-- ./wrapper -->

<!--
=======================
  REQUIRED SCRIPTS
=======================
-->
<script src="{{ mix('/js/app.js') }}"></script>
{{-- <script src="{{ asset('js/app.js') }}" defer></script> --}}
<script src="{{asset('js/jquery.min.js')}}"></script>
{{-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script> --}}
<script src="{{asset('js/bootstrap.bundle.min.js')}}"></script>
<script src="{{asset('js/moment.min.js')}}"></script>
<script src="{{asset('js/admin.js')}}"></script>
<script src="{{asset('js/autocomplete.js')}}"></script>
<script src="{{asset('js/jquery.min.js')}}"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://unpkg.com/izitoast/dist/js/iziToast.min.js" type="text/javascript"></script>
<script src="{{asset('js/custom.js')}}"></script>
<script src="{{asset('js')}}/home.js?{{time()}}"></script>
  @yield('scripts')

  @yield('js')


</body>
</html>
