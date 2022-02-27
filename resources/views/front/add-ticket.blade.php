@extends('front.layout.courier')
@section('header')
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>Ticket</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{asset('css/fontawesome-free/css/all.min.css')}}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
  <link href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css" rel='stylesheet'>
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <!-- Theme style -->
  <link rel="stylesheet" href="{{asset('css/admin.min.css')}}">

  <link rel="stylesheet" href="{{asset('css/style.css')}}">
<link rel="icon" href="https://jmnation.com/images/jm-transparent-logo.png"></head>
<style>
    .date_picker_pair {
    width: 90%;
}

/* .sorting_disabled{
    display: none !important;
} */
table.dataTable thead .sorting_asc{
    background-image: none !important;
}

</style>


@endsection

@section('content')

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12 mt-3">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title"><i class="fas fa-list" style="margin-right: 10px;"></i>
                  <strong>Ticket</strong>
                </h3>
              </div>


                <div class="card-body pb-0">
                    <form  id="ticket_submit">
                        <div class="form-row">

                      <div class="form-group col-md-12">
                        <label for="inputPassword4">Service</label>

                      </div>

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



              <!-- /.card-header -->


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

@endsection

@section('scripts')

@endsection