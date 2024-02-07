<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Styles -->
        <link rel="icon" type="image/png" href="{{ asset('images/bidhubIcon.png') }}">
        <link href="{{ url('css/milligram.min.css') }}" rel="stylesheet">
        <link href="{{ url('css/app.css') }}" rel="stylesheet">
        <link href="{{ url('css/about.css') }}" rel="stylesheet">
        <link href="{{ url('css/mainFeatures.css') }}" rel="stylesheet">
        <script type="text/javascript">
            // Fix for Firefox autofocus CSS bug
            // See: http://stackoverflow.com/questions/18943276/html-5-autofocus-messes-up-css-loading/18945951#18945951
        </script>
        <script type="text/javascript" src={{ url('js/app.js') }} defer>
        </script>
    </head>
    <body>
        <main >
            <header>
            <div class="header-left">
                <a href="{{ url('/home') }}">
                    <div class="image-container">
                        <img src="{{ asset('images/bidhub.png') }}" alt="BidHub Logo" width="250" height ="250">
                    </div>
                </a>
            </div>
            <div class="header-right">
                @if (Auth::check())
                    @if(Auth::user()->is_admin)
                        <a href="{{ url('/adminDashboard') }}"><button class="button" id="adminDashboard">AdminDashboard</button></a>
                    @endif
                    <form action="{{ route('auth.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="button" id="logout">Logout <span>{{ Auth::user()->name }}</span></button>
                    </form>
                 @endif
            </div>
            </header>
            <section id="content">
                @yield('content')
            </section>
            

        </main>
        <script src="{{ asset('js/bsadmin.js') }}"></script>
    </body>
</html>