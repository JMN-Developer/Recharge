<!DOCTYPE html>
<html>
<!-- Mirrored from colorlib.com/etc/regform/colorlib-regform-18/ by HTTrack Website Copier/3.x [XR&CO'2014], Mon, 03 Jan 2022 04:50:38 GMT -->
<!-- Added by HTTrack -->
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
<!-- /Added by HTTrack -->

<head>
    <meta charset="utf-8">
    <title>New Reseller</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('registration-form/fonts/material-design-iconic-font/css/material-design-iconic-font.min.css') }}">
    <link rel="stylesheet" href="{{ asset('registration-form/css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://unpkg.com/izitoast/dist/css/iziToast.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <meta name="robots" content="noindex, follow">

</head>

<body>
    <div class="wrapper" style="background-image: url('../registration-form/images/bg-registration-form-2.jpg');">
        <div class="inner">
            <img width="20px" src="{{ asset('registration-form/images/jm-logo.png') }}">
            <form id="registration_form" autocomplete="off">
                <h3>Add New Retailer</h3>
                <div class="form-group">
                    <div class="form-wrapper">
                        <label for="">First Name</label>
                        <input id="first_name" type="text" class="form-control" required>
                    </div>
                    <div class="form-wrapper">
                        <label for="">Last Name</label>
                        <input id="last_name" type="text" class="form-control" required>
                    </div>
                </div>
                <div class="form-wrapper">
                    <label for="">Email</label>
                    <input id="email" type="text" class="form-control" autocomplete="off" readonly onfocus="this.removeAttribute('readonly');" required>
                    <p id="email_alert" class="text-alert"> </p>
                </div>
                <div class="form-wrapper">
                    <label for="">Password</label>
                    <input id="password" type="password" class="form-control" autocomplete="off" required>
                    <i class="toggle-password fa fa-fw fa-eye-slash"></i>

                </div>
                <div id="message">
                    <h4>Password must contain the following:</h4>
                    <p id="letter" class="invalid">A <b>lowercase</b> letter</p>
                    <p id="capital" class="invalid">A <b>capital (uppercase)</b> letter</p>
                    <p id="number" class="invalid">A <b>number</b></p>
                    <p id="s_character" class="invalid">A <b>special character</b></p>
                    <p id="length" class="invalid">Minimum <b>8 characters</b></p>
                  </div>
                <div class="form-wrapper">
                    <label for="">Confirm Password</label>
                    <input id="confirm_password" type="password" class="form-control" required>
                    <i class="toggle-password fa fa-fw fa-eye-slash"></i>
                    <p id="confirm_password_alert" class="text-alert"> Password not match </p>

                </div>


                <div class="form-wrapper">
                    <label for="">Mobile Number</label>
                    <input id="mobile_number" type="text" class="form-control" required>
                </div>
                <div class="form-wrapper">
                    <label for="">Address</label>
                    <textarea id="address" class="form-control" rows="4" required></textarea>
                  
                </div>

                <div class="form-group">
                    <div class="form-wrapper">
                        <label for="">Nationality</label>
                        <input id="nationality" type="text" class="form-control" required>
                    </div>
                    <div class="form-wrapper">
                        <label for="">Partia IVA</label>
                        <input id="partia_iva" type="text" class="form-control" required>
                    </div>
                </div>
                <div class="form-group">
                    <div class="form-wrapper">
                        <label for="">Codiac Fiscale</label>
                        <input id="codiac_fiscale" type="text" class="form-control" required>
                    </div>
                    <div class="form-wrapper">
                        <label for="">Gender</label>
                        <select id="gender" class="form-select form-control" aria-label="Default select example">

                            <option value="male">Male</option>
                            <option value="female">Female</option>

                          </select>
                    </div>
                </div>

                <div class="form-group">
                    <div class="form-wrapper">
                        <label for="">Company Name</label>
                        <input id="company_name" type="text" class="form-control" required>
                    </div>
                    <div class="form-wrapper">
                        <label for="">Payment Method</label>
                        <select id="payment_method" class="form-select form-control" aria-label="Default select example">

                            <option value="male">Cash</option>
                            <option value="female">Bank</option>

                          </select>
                    </div>
                </div>

                <button type="submit">Register Now</button>
            </form>
        </div>
    </div>
    <script src="{{asset('js/jquery.min.js')}}"></script>
    <script src="https://unpkg.com/izitoast/dist/js/iziToast.min.js" type="text/javascript"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
        var email_valid = false;
        var password_valid = false;

        var myInput = document.getElementById("password");
        var letter = document.getElementById("letter");
var capital = document.getElementById("capital");
var number = document.getElementById("number");
var length = document.getElementById("length");
var s_character = document.getElementById("s_character");
        $(function(){
            $("#email_alert").hide();
            $("#confirm_password_alert").hide();
            $("#confirm_password").keyup(function(){
                var password = $("#password").val();
                var confirm_password = $("#confirm_password").val();
                if(password != confirm_password)
                {
                    $("#confirm_password_alert").show();
                    password_valid = false
                }
                else
                {
                    $("#confirm_password_alert").hide();
                    password_valid = true

                }
            })
            $("#email").keyup(function(){
               var email = $("#email").val();
               var regexEmail = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;;
               if (regexEmail.test(email)) {
                 $("#email_alert").hide()
                 $.ajax({
                type: "GET",
                dataType: "json",
                url: '/check_email',
                data: {'email': email},
                success: function(data){

                    if(data.status == true)
                    {
                    $("#email_alert").show()
                    $("#email_alert").text('This email already used. Please try again with another one')
                    email_valid = false;

                    }
                    else
                    {
                        $("#email_alert").hide()
                        email_valid = true;
                    }
                }
                });


                } else {
                    email_valid = false;
                    $("#email_alert").show()
                    $("#email_alert").text('Please enter a valid email address')
                    //console.log(email_valid)

                }



            });

            $(".toggle-password").click(function() {
                $(this).toggleClass("fa-eye fa-eye-slash");
                input = $(this).parent().find("input");
                if (input.attr("type") == "password") {
                    input.attr("type", "text");
                } else {
                    input.attr("type", "password");
                }
            });


            $( "#password" ).focus(function() {

                $("#message").css({"display": "block"});
                // $("#message").css("display":'block');
            });

            $( "#password" ).blur(function() {

                $("#message").css({"display": "none"});
                // $("#message").css("display":'block');
            });

            $("#password").keyup(function(){
                    var lowerCaseLetters = /[a-z]/g;
                    if(myInput.value.match(lowerCaseLetters)) {
                        letter.classList.remove("invalid");
                        letter.classList.add("valid");
                    } else {
                        letter.classList.remove("valid");
                        letter.classList.add("invalid");
                    }

                    // Validate capital letters
                    var upperCaseLetters = /[A-Z]/g;
                    if(myInput.value.match(upperCaseLetters)) {
                        capital.classList.remove("invalid");
                        capital.classList.add("valid");
                    } else {
                        capital.classList.remove("valid");
                        capital.classList.add("invalid");
                    }

                    // Validate numbers
                    var numbers = /[0-9]/g;
                    if(myInput.value.match(numbers)) {
                        number.classList.remove("invalid");
                        number.classList.add("valid");
                    } else {
                        number.classList.remove("valid");
                        number.classList.add("invalid");
                    }

                    // Validate length
                    if(myInput.value.length >= 8) {
                        length.classList.remove("invalid");
                        length.classList.add("valid");
                    } else {
                        length.classList.remove("valid");
                        length.classList.add("invalid");
                     }


                   // Validate special character
                   var characters = /\W|_/g;
                    if(myInput.value.match(characters)) {
                        s_character.classList.remove("invalid");
                        s_character.classList.add("valid");
                    } else {
                        s_character.classList.remove("valid");
                        s_character.classList.add("invalid");
                    }

            });
            $("#registration_form").submit(function(event){
               
                   event.preventDefault();
                    var first_name = $("#first_name").val();
                    var last_name = $("#last_name").val();
                    var password = $("#password").val();
                    var mobile_number = $("#mobile_number").val();
                    var address = $("#address").val();
                    var nationality = $("#nationality").val();
                    var partia_iva = $("#partia_iva").val();
                    var codiac = $("#codiac_fiscale").val();
                    var gender = $("#gender :selected").val();
                    var email = $("#email").val();
                    var company_name = $("#company_name").val();
                    var payment_method = $("#payment_method :selected").val();
                    
                    var formdata = new FormData();
                    formdata.append('first_name',first_name);
                    formdata.append('last_name',last_name);
                    formdata.append('vat_number',partia_iva);
                    formdata.append('nationality',nationality);
                    formdata.append('email',email);
                    formdata.append('address',address);
                    formdata.append('phone',mobile_number);
                    formdata.append('codice_fiscale',codiac);
                    formdata.append('gender',gender);
                    formdata.append('password',password);
                    formdata.append('company_name',company_name);
                    formdata.append('payment_method',payment_method);

                    if(email_valid && password_valid)
                    {

                        $.ajax({
                        processData: false,
                        contentType: false,
                        url: "/create",
                        type:"POST",
                        headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: formdata,
                        success:function(response){
                            swal("New user created successfully.")
                            .then((value) => {
                                location.href='retailer-action'
                            });


                        },
                    });
                    }





            })


        })
    </script>


    {{-- <script async src="https://www.googletagmanager.com/gtag/js?id=UA-23581568-13"></script> --}}
    {{-- <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }

    </script> --}}
    {{-- <script defer src="https://static.cloudflareinsights.com/beacon.min.js/v652eace1692a40cfa3763df669d7439c1639079717194" integrity="sha512-Gi7xpJR8tSkrpF7aordPZQlW2DLtzUlZcumS8dMQjwDHEnw9I7ZLyiOj/6tZStRBGtGgN6ceN6cMH8z7etPGlw==" data-cf-beacon='{"rayId":"6c799b6d7b27e9af","token":"cd0b4b3a733644fc843ef0b185f98241","version":"2021.12.0","si":100}'
        crossorigin="anonymous"></script> --}}
</body>
<!-- Mirrored from colorlib.com/etc/regform/colorlib-regform-18/ by HTTrack Website Copier/3.x [XR&CO'2014], Mon, 03 Jan 2022 04:50:39 GMT -->

</html>
