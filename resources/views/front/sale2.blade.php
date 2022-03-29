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
<link rel="icon" href="https://jmnation.com/images/jm-transparent-logo.png"></head>
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

    </section>
    <!-- /.content -->
  </div>

<!-- jQuery -->
<script src="{{ asset('js/jquery.min.js') }}"></script>
<!-- Bootstrap -->
<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
<!-- Select2 -->
<script src="{{ asset('js/select2.full.min.js') }}"></script>
<!-- Bootstrap4 Duallistbox -->
<script src="{{asset('js/jquery.bootstrap-duallistbox.min.js')}}"></script>
<!-- daterangepicker -->
<script src="{{ asset('js/moment.min.js') }}"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="{{ asset('js/tempusdominus-bootstrap-4.min.js') }}"></script>
<!-- dropzonejs -->
<script src="{{ asset('js/dropzone.min.js') }}"></script>
<!-- Theme JS -->
<script src="{{ asset('js/admin.js') }}"></script>
<!-- Custom JS -->
<script src="{{ asset('js/custom.js') }}"></script>




<script>
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
