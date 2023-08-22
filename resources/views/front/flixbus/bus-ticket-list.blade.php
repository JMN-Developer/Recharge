@extends('front.layout.master')
@section('header')

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bus Ticket List</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="{{ asset('css/fontawesome-free/css/all.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('css/admin.min.css') }}">

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="icon" href="{{ asset('images/jm-transparent-logo.png') }}">
    <link rel="icon" href="https://jmnation.com/images/jm-transparent-logo.png">
</head>
@endsection

@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">


            <div class="row">
                <div class="col-12 phone_order_header d-block">
                    <div class="order_page_header d-inline-block mb-2">
                        <h4 class="d-inline-block"><i class="fas fa-copy"></i>Order List</h4>
                        <a href="{{ route('cargo-new-order') }}" class="d-inline-block"
                            style="float: right;"><i class="fas fa-plus-circle"></i>New Order</a>
                    </div>
                    <div class="input-group mb-4">
                        <input type="text" class="form-control light-table-filter" data-table="table-info"
                            placeholder="Search old order" aria-label="Search old order"
                            aria-describedby="basic-addon2">
                        <span class="input-group-text" id="basic-addon2"><i class="fas fa-search"></i></span>
                    </div>
                    {{-- <div class="mb-2 text-center">
              <button type="button" class="btn btn-success btn-sm cargo_order_list_btn">
                <i class="fas fa-file-excel"></i>
                Export Excel
              </button>
              <button type="button" class="btn btn-danger btn-sm cargo_order_list_btn">
                <i class="fas fa-file-pdf"></i>
                Export PDF
              </button>
              <button type="button" class="btn btn-info btn-sm cargo_order_list_btn">
                <i class="fas fa-print"></i>
                Print
              </button>
            </div> --}}
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- SELECT2 EXAMPLE -->
            <div class="card card-default">
                <div class="card-body">
                    <div class="row pb-3">
                        <div class="col-12  table-responsive p-0" style="height: 550px;">
                            <table
                                class="table-info table table-sm table-bordered table-hover table-head-fixed text-nowrap">
                                <thead>
                                    <tr>
                                    <th style="background: #faaeae;">#</th>
                                        <th style="background: #faaeae;">User Email</th>
                                        <th style="background: #faaeae;">Departure Station</th>
                                        <th style="background: #faaeae;">Arrival Station</th>
                                        <th style="background: #faaeae;">Departure Date</th>
                                        <th style="background: #faaeae;">Total Passengers</th>
                                        <th style="background: #faaeae;">Ticket Unit Price</th>
                                        <th style="background: #faaeae;">Total Price</th>
                                        <th style="background: #faaeae;">Ticket Purchase Date</th>
                                        <th style="background: #faaeae;">Status</th>
                                        <!-- <th style="background: #faaeae;">Status</th> -->
                                        <th style="background: #faaeae;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($busTickets as $busTicket)

                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $busTicket['user_email'] }}</td>
                                            <td>{{ $busTicket['departure_station_name'] }}</td>
                                            <td>{{ $busTicket['arrival_station_name'] }}</td>
                                            <td>{{ $busTicket['departure_date'] }}</td>
                                            <td>{{ $busTicket['total_passengers'] }}</td>
                                            <td>{{ $busTicket['ticket_unit_price'] }}</td>
                                            <td>{{ $busTicket['ticket_total_price'] }}</td>
                                            <td>{{ $busTicket['ticket_purchase_date'] }}</td>
                                            <td>
            @if ($busTicket['status'] == 0)
                <span style="color: green;">Available</span>
            @else
                <span style="color: red;">Cancelled</span>
            @endif
        </td>
                                            <td>
                                            @if ($busTicket['status'] == 0)
                                                <div class="btn-group cargo_t-action_btn">
                                                    <button type="button"
                                                        class="btn btn-info dropdown-toggle dropdown-icon"
                                                        data-toggle="dropdown"></button>
                                                    <div class="dropdown-menu" role="menu">
                                                        <a class="dropdown-item" target="_blank"
                                                            href="{{ $busTicket['invoice'] }}"><i
                                                                class="fas fa-print"></i> Invoice</a>
                                                        <a class="dropdown-item" target="_blank"
                                                            href="{{ $busTicket['booking_confirmation'] }}"><i
                                                                class="fas fa-eye"></i> Ticket</a>
                                                                @if( $busTicket['cancel_available_status'])
                            <!-- Update this part of the code -->
<a class="dropdown-item cancel-ticket-btn" href="#" data-ticket-id="{{ $busTicket['id'] }}"><i class="fas fa-times"></i> Cancel Ticket</a>

                        @else

                            <a class="dropdown-item disabled" href="#"><i class="fas fa-times"></i> Cancel  Ticket</a>
                        @endif
                                                    </div>
                                                </div>
                                                @else
                                                <div class="btn-group cargo_t-action_btn">
                                                    <button type="button"
                                                        class="btn btn-info dropdown-toggle dropdown-icon"
                                                        data-toggle="dropdown"></button>
                                                    <div class="dropdown-menu" role="menu">
                                                        <a class="dropdown-item" target="_blank"
                                                            href="{{ $busTicket['cancel_meta']['documents'][0]['href'] }}"><i
                                                                class="fas fa-print"></i> Voucher</a>
                                                        <a class="dropdown-item" target="_blank"
                                                            href="{{ $busTicket['cancel_meta']['documents'][1]['href'] }}"><i
                                                                class="fas fa-eye"></i> Invoice</a>

                                                    </div>
                                                </div>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>

                            </table>
                        </div>
                    </div>
                    <!-- /.row -->


                    <!-- /.card-footer -->
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>

<script>
    /* Code By Webdevtrick ( https://webdevtrick.com ) */
    (function (document) {
        'use strict';

        var TableFilter = (function (Arr) {

            var _input;

            function _onInputEvent(e) {
                _input = e.target;
                var tables = document.getElementsByClassName(_input.getAttribute('data-table'));
                Arr.forEach.call(tables, function (table) {
                    Arr.forEach.call(table.tBodies, function (tbody) {
                        Arr.forEach.call(tbody.rows, _filter);
                    });
                });
            }

            function _filter(row) {
                var text = row.textContent.toLowerCase(),
                    val = _input.value.toLowerCase();
                row.style.display = text.indexOf(val) === -1 ? 'none' : 'table-row';
            }

            return {
                init: function () {
                    var inputs = document.getElementsByClassName('light-table-filter');
                    Arr.forEach.call(inputs, function (input) {
                        input.oninput = _onInputEvent;
                    });
                }
            };
        })(Array.prototype);

        document.addEventListener('readystatechange', function () {
            if (document.readyState === 'complete') {
                TableFilter.init();
            }
        });

    })(document);

</script>
<!-- /.content-wrapper -->
@endsection
@section('scripts')
<!-- jQuery -->

<script>
    $(function () {
        $('.cancel-ticket-btn').click(function (e) {
            e.preventDefault();

            var ticketId = $(this).data('ticket-id');

            var cancelUrl = "/cancel-ticket/" + ticketId;

            $.ajax({
                url: cancelUrl,
                type: 'GET',
                success: function (response) {
                    if(response.status){
                        alert('Ticket cancel successfully')
                        window.location.reload()
                    }
                    else{
                        alert('Some error occured during cancel ticket.')
                    }

                    // Optionally, you can update the table or perform any other action to indicate that the ticket was canceled.
                },
                error: function (error) {
                    // Handle the error response here, if needed
                    console.error('Error canceling the ticket.');
                }
            });
        });

    })

</script>
@endsection
