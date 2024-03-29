@extends('front.layout.master')
@section('header')
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>Sim Sell</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{asset('css/fontawesome-free/css/all.min.css')}}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{asset('css/admin.min.css')}}">

  <link rel="stylesheet" href="{{asset('css/style.css')}}">
  <!-- Tempusdominus Bootstrap 4 -->
  <link rel="stylesheet" href="{{ asset('css/tempusdominus-bootstrap-4.min.css') }}">
  <!-- Select2 -->
  <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
  <link rel="stylesheet" href="{{ asset('css/multiple_image_preview.css') }}">
  <link rel="icon" href="https://jmnation.com/images/jm-transparent-logo.png">
  <style>

  </style>

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
            <div class="order_page_header">
              <h4>SIM Activation Form</h4>
            </div>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <form action="{{ route('buy-sim') }}" method="POST" enctype="multipart/form-data">
      @csrf
      <div class="container-fluid">
        <div class="card card-default">
          <div class="card-body">
            <div class="row">
              <div class="col-12">
                <div class="sim_information_row">
                  <h5 class="text-bold">Vendita SIM</h5>
                  <hr>
                  <p>Operator: <b>{{ $data->operator }}</b></p>
                  <input type="hidden" name="operator" value="{{ $data->operator }}">
                  <p>Numero ICCID: <b>{{ $data->iccid }}</b></p>
                  <input type="hidden" name="iccid" value="{{ $data->iccid }}">
                  <p>Numero di celulare: <b>{{ $data->sim_number }}</b></p>
                  <input type="hidden" name="sim_number" value="{{ $data->sim_number }}">
                  <input type="hidden" name="buy_price" value="{{ $data->buy_price }}">
                  <input type="hidden" name="sim_id" value="{{ $data->id }}">
                </div>
                <div class="form-group">
                  @if (Auth::user()->role == 'admin')
                    <div class="offer_select_option">
                      <label for="exampleSelect">Offer</label>
                      <a href="/offer" type="button" class="btn btn-sm btn-info">Add Offer</a>
                    </div>
                    @endif
                  <select name="offer" class="custom-select" id="offer">

                    <option value="No Offer" disabled selected hidden>Please Choose...</option>

                    @foreach ($offer as $item)
                        <option value="{{ $item->offer }}">{{ $item->offer }}</option>
                    @endforeach
                  </select>
                </div>
                <div id="offer-table" class="col-md-10">
                  {{-- <table class="table table-bordered">
                    <tr>
                      <td>Costo al mese :</td>
                      <td></td>
                    </tr>
                    <tr>
                      <td><strong>Ricarica totale :</strong></td>
                      <td></td>
                    </tr>
                    <tr>
                      <td> <strong>Offerta valida per :</strong></td>
                      <td></td>
                    </tr>
                    <tr>
                      <td> <strong>Pacchetto Internet gratuito (GB) :</strong></td>
                      <td></td>
                    </tr>
                    <tr>
                      <td> <strong>Minuti gratuiti per operatore locale :</strong></td>
                      <td></td>
                    </tr>
                    <tr>
                      <td> <strong>Minuti gratuiti per internazionale :</strong></td>
                      <td></td>
                    </tr>
                    <tr>
                      <td> <strong>Minuti illimitati a :</strong></td>
                      <td></td>
                    </tr>
                    <tr>
                      <td> <strong>Minuti internazionali validi per :</strong></td>
                      <td></td>
                    </tr>
                    <tr>
                      <td><strong>Altre informazioni :</strong></td>
                      <td></td>
                    </tr>
                  </table> --}}
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="card card-default">
          <div class="card-body">
            <div class="row">
              <div class="col-md-10">

                <div class="row">
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label for="inputFirstName" class="form-label">First Name</label>
                      <input name="fname" type="text" class="form-control" id="inputFirstName" placeholder="Inter Client First Name" required>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label for="inputLastName" class="form-label">Last Name</label>
                      <input name="lname" type="text" class="form-control" id="inputLastName" placeholder="Inter Client Last Name" required>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-12">
                    <div class="mb-3">
                      <label for="inputNID" class="form-label">Codici Fiscali</label>
                      <input name="codice" type="text" class="form-control" id="inputNID" placeholder="Inter Client Codici number" required>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Date of Birth</label>
                      <div class="input-group date" id="birthDate" data-target-input="nearest">
                        <input name="dob" type="text" class="form-control datetimepicker-input" data-toggle="datetimepicker" data-target="#birthDate" required/>
                        <div class="input-group-append" data-target="#birthDate" data-toggle="datetimepicker">
                          <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label for="inputNationality" class="form-label">Nationality</label>
                      <input name="nationality" type="text" class="form-control" id="inputNationality" placeholder="Inter Client Nationality" required>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="selectGender">Gender</label>
                      <select name="gender" class="custom-select" id="selectGender" required>
                        <option>Select Gender</option>
                        <option>Male</option>
                        <option>Female</option>
                        <option>Others</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label for="inputSalePrice" class="form-label">Sale Prices</label>
                      <input type="text" name="sell_price" class="form-control" id="inputSalePrice" placeholder="Inter Sell Price" required>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <p style="font-weight: bold;margin-bottom:2px">Personal Document1:</p>
                    <ol>
                      <li>Upload pdf file of your NID (Max-size 2MB)</li>
                      <li>Upload a recent color photograph</li>
                    </ol>
                    <div class="custom-file mb-3">
                      <input type="file" class="custom-file-input" id="single_files"  name="file" required>
                      <label class="custom-file-label" for="customFile">Choose file</label>
                    </div>

                  </div>

                  <div class="col-md-6">
                    <p style="font-weight: bold;margin-bottom:2px">Personal Document2(Optional):</p>
                    <ol>
                      <li>Upload pdf file of your NID (Max-size 2MB)</li>
                      <li>Upload a recent color photograph</li>
                    </ol>
                    <div class="custom-file mb-3">
                      <input type="file" class="custom-file-input" id="multiple_files" name="file2">
                      <label class="custom-file-label" for="customFile">Choose file</label>
                    </div>

                  </div>

                </div>
                {{-- <div class="row">
                  <div class="col-12">
                    <div class="mb-3">
                      <label for="interAddress" class="form-label">Address</label>
                      <textarea  class="form-control" id="interAddress" rows="4"></textarea>
                    </div>
                  </div>
                </div> --}}
              </div>
              <div class="col-md-10 sim-act-form-right" style="margin-top: 10px">

                <div class="row">
                  <div class="col-6">
                    <div class="form-check bg-ash">
                      <input class="form-check-input" type="checkbox" name="sim-activation-additional" id="sim-activation-additional">
                      <label class="form-check-label text-bold" for="sim-activation-additional">
                        Portabilita
                      </label>
                    </div>
                  </div>
                </div>
                <div class="activation-additional-info">
                  <div class="row">
                    <div class="col-6">
                      <div class="form-group">
                        <label>Oparetor</label>
                        <select name="alt_operator" class="form-control select2" style="width: 100%;">
                          @foreach ($operator as $item)
                              <option value="{{ $item->operator }}">{{ $item->operator }}</option>
                          @endforeach
                        </select>
                      </div>
                    </div>
                    <div class="col-6">
                      <div class="mb-3">
                        <label for="inputIccidNumber" class="form-label">ICCID Number</label>
                        <input type="text" name="alt_iccid" class="form-control" id="inputIccidNumber" placeholder="Inter ICCID Number">
                      </div>
                    </div>

                    <div class="col-6">
                      <div class="mb-3">
                        <label for="inputMobileNumber" class="form-label">Sim Number</label>
                        <input type="text" name="alt_sim_number" class="form-control" id="inputMobileNumber" placeholder="Inter Mobile Number">
                      </div>
                    </div>

                  </div>


                </div>
                <div class="card-body">
                  <div id="actions" class="row">

                  </div>
                  <div class="table table-striped files" id="previews">
                    <div id="template" class="row mt-2">
                      <div class="col-auto">
                        <span class="preview"><img src="data:," alt="" data-dz-thumbnail /></span>
                      </div>
                      <div class="col d-flex align-items-center">
                        <p class="mb-0">
                          <span class="lead" data-dz-name></span>
                          (<span data-dz-size></span>)
                        </p>
                        <strong class="error text-danger" data-dz-errormessage></strong>
                      </div>
                      <div class="col-4 d-flex align-items-center">
                        {{-- <div class="progress progress-striped active w-100" role="progressbar" aria-valuemin="0" aria-valuemax="100"
                          aria-valuenow="0">
                          <div class="progress-bar progress-bar-success" style="width:0%;" data-dz-uploadprogress></div>
                        </div> --}}
                      </div>
                      <div class="col-auto d-flex align-items-center">
                        <div class="btn-group">
                          <button style="display: none" class="btn btn-primary start">
                            {{-- <i class="fas fa-upload"></i> --}}
                            {{-- <span></span> --}}
                          </button>
                          {{-- <button data-dz-remove class="btn btn-warning cancel">
                            <i class="fas fa-times-circle"></i>
                            <span>Cancel</span>
                          </button>
                          <button data-dz-remove class="btn btn-danger delete">
                            <i class="fas fa-trash"></i>
                            <span>Delete</span>
                          </button> --}}
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="row justify-content-center">
              <div class="col-md-8">
                <input style="width: 100%" type="submit" value="Buy Now" class="btn btn-success">
              </div>
            </div>
            <!-- /.row -->
          </div>
          <!-- /.card-body -->
        </div>
        <!-- /.card -->
      </div>
      <!-- /.container-fluid -->
    </form>
    </section>
    <!-- /.content -->
  </div>


<!-- Bootstrap4 Duallistbox -->
<script src="{{asset('js/jquery.bootstrap-duallistbox.min.js')}}"></script>

<!-- Tempusdominus Bootstrap 4 -->
<script src="{{ asset('js/tempusdominus-bootstrap-4.min.js') }}"></script>
<!-- dropzonejs -->
<script src="{{ asset('js/dropzone.min.js') }}"></script>
<script src="{{ asset('js/multiple_image_preview.js') }}"></script>



<script>
  $(".custom-file-input").on("change", function() {
  var fileName = $(this).val().split("\\").pop();
  $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
});

  $(function(){
$('#offer').change(function(){
var empty = "";
var value = $(this).val();
var sim = $('#sim_id').val();
var table = $('#offer');
$.ajax({
 type: "POST",
 url: "https://jmnation.com/api/offer-check", // url to request
 headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
         },
 data:{
            // _token:'{{ csrf_token() }}',
            id: value,
            sim_id: sim
        },
  cache: false,
  dataType: 'json',
 success : function(response){
  $("#offer-table").empty();
   // populate data here
   var offer = `<table class="table table-bordered">
              <tr>
              <th>Offer Details</th>
              </tr>
              <tr>
                <td><strong>Costo al mese :</strong>` +response.costo+`</td>
                <td></td>
              </tr>
              <tr>
                <td><strong>Ricarica totale :</strong>` +response.ricarica+`</td>
                <td></td>
              </tr>
              <tr>
                <td> <strong>Offerta valida per :</strong>` +response.valida+`</td>
                <td></td>
              </tr>
              <tr>
                <td> <strong>Pacchetto Internet gratuito (GB) :</strong>` +response.internet+`</td>
                <td></td>
              </tr>
              <tr>
                <td> <strong>Minuti gratuiti per operatore locale :</strong>` +response.minuti+`</td>
                <td></td>
              </tr>
              <tr>
                <td> <strong>Minuti gratuiti per internazionale :</strong>` +response.minuti_internazionale+`</td>
                <td></td>
              </tr>
              <tr>
                <td> <strong>Minuti illimitati a :</strong>` +response.minuti_illimitati+`</td>
                <td></td>
              </tr>
              <tr>
                <td> <strong>Minuti internazionali validi per :</strong>` +response.minuti_internazionali_validi+`</td>
                <td></td>
              </tr>
              <tr>
                <td><strong>Altre informazioni :</strong> `+response.altre_informazioni+`</td>
                <td></td>
              </tr>
            </table>`
            $("#offer-table").append(offer);
 }
});
});
});
</script>










<!-- Page specific script -->
<script>
  $(function () {



    //Initialize Select2 Elements
    $('.select2').select2()

    //Initialize Select2 Elements
    $('.select2bs4').select2({
      theme: 'bootstrap4'
    })
    //Date picker
    $('#birthDate').datetimepicker({
      format: 'L'
    });

  })


  // Get the template HTML and remove it from the doumenthe template HTML and remove it from the doument
  var previewNode = document.querySelector("#template")
  previewNode.id = ""
  var previewTemplate = previewNode.parentNode.innerHTML
  previewNode.parentNode.removeChild(previewNode)

  var myDropzone = new Dropzone(document.body, { // Make the whole body a dropzone
    url: "/target-url", // Set the url
    thumbnailWidth: 80,
    thumbnailHeight: 80,
    parallelUploads: 20,
    previewTemplate: previewTemplate,
    autoQueue: false, // Make sure the files aren't queued until manually added
    previewsContainer: "#previews", // Define the container to display the previews
    clickable: ".fileinput-button" // Define the element that should be used as click trigger to select files.
  })

  myDropzone.on("addedfile", function(file) {
    // Hookup the start button
    file.previewElement.querySelector(".start").onclick = function() { myDropzone.enqueueFile(file) }
  })

  // Update the total progress bar
  myDropzone.on("totaluploadprogress", function(progress) {
    document.querySelector("#total-progress .progress-bar").style.width = progress + "%"
  })

  myDropzone.on("sending", function(file) {
    // Show the total progress bar when upload starts
    document.querySelector("#total-progress").style.opacity = "1"
    // And disable the start button
    file.previewElement.querySelector(".start").setAttribute("disabled", "disabled")
  })

  // Hide the total progress bar when nothing's uploading anymore
  myDropzone.on("queuecomplete", function(progress) {
    document.querySelector("#total-progress").style.opacity = "0"
  })

  // Setup the buttons for all transfers
  // The "add files" button doesn't need to be setup because the config
  // `clickable` has already been specified.
  document.querySelector("#actions .start").onclick = function() {
    myDropzone.enqueueFiles(myDropzone.getFilesWithStatus(Dropzone.ADDED))
  }
  document.querySelector("#actions .cancel").onclick = function() {
    myDropzone.removeAllFiles(true)
  }


</script>

@endsection
