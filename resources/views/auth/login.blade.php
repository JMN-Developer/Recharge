<head>

    <link href="{{asset('frontend')}}/assets/vendor/aos/aos.css" rel="stylesheet">
    <link href="{{asset('frontend')}}/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{asset('frontend')}}/assets/css/style.css" rel="stylesheet">
    <link
    href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Montserrat:300,400,500,600,700"
    rel="stylesheet">
    <style>

    </style>

</head>

<header id="header" class="fixed-top d-flex align-items-center header-transparent" style="height:90px !important">
    <div class="container d-flex align-items-center" style="background: linear-gradient(to right, #888 0%, #72cff3 100%);margin-left:103px;margin-right:103px;padding-top:5px;padding-bottom:5px">

      <a href="{{ route('/') }}" class="logo me-auto" ><img src="{{asset('frontend')}}/assets/img/NEW_JM.png" alt="" class="img-fluid"></a>
      <h1 class="logo me-auto"><a href="{{ route('/') }}"><span>JM</span> Nation</a></h1>

      <nav id="navbar" class="navbar order-last order-lg-0">
        <ul>
          <li><a class="nav-link scrollto active" href="{{ route('/') }}">Home</a></li>
          <li><a class="nav-link scrollto" href="{{ route('/') }}">About</a></li>
          <li><a class="nav-link scrollto" href="{{ route('/') }}">Services</a></li>
          <li><a class="nav-link scrollto" href="{{ route('/') }}">Features</a></li>
          <li><a class="nav-link scrollto" href="{{ route('/') }}">Team</a></li>
          <li><a class="nav-link scrollto" href="{{ route('/') }}">Pricing</a></li>
          <li><a class="nav-link scrollto" href="{{ route('/') }}">Contact</a></li>
          <li style="margin-left: 1rem;"><button type="button" class="btn btn-warning" onclick="location.href='login'">Log In</button></li>
        </ul>
        <i class="bi bi-list mobile-nav-toggle"></i>
      </nav><!-- .navbar -->
    </div>
  </header>

<x-guest-layout>

    <x-jet-authentication-card>
        <x-slot name="logo">

        </x-slot>



        <x-jet-validation-errors class="mb-4" />

        @if (session('status'))
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ session('status') }}
            </div>
        @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div>
                    <x-jet-label for="email" value="{{ __('Email') }}" />
                    <x-jet-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
                </div>

                <div class="mt-4">
                    <x-jet-label for="password" value="{{ __('Password') }}" />

                    <x-jet-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
                </div>

                <div class="block mt-4">
                    <label for="remember_me" class="flex items-center">
                        <x-jet-checkbox id="remember_me" name="remember" />
                        <span class="ml-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                    </label>
                </div>

                <div class="flex items-center justify-end mt-4">
                    @if (Route::has('password.request'))
                        <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('password.request') }}">
                            {{ __('Forgot your password?') }}
                        </a>
                    @endif

                    <x-jet-button class="ml-4">
                        {{ __('Log in') }}
                    </x-jet-button>
                </div>
            </form>


    </x-jet-authentication-card>

</x-guest-layout>

