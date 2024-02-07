<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>
        <link rel="icon" type="image/png" href="{{ asset('images/bidhubIcon.png') }}">
        <!-- Styles -->
        <link href="{{ url('css/admin.css') }}" rel="stylesheet">
        <link href="{{ url('css/adminResponsive.css') }}" rel="stylesheet">
        <script type="text/javascript">
            // Fix for Firefox autofocus CSS bug
            // See: http://stackoverflow.com/questions/18943276/html-5-autofocus-messes-up-css-loading/18945951#18945951
        </script>
        <script type="text/javascript" src={{ url('js/app.js') }} defer>
        </script>
    </head>
    <body> 
        <header>
            <div class="logosec">
                <a href="{{ url('/home') }}">
                    <div class="logo">BidHub</div>
                </a>
                <img src="{{ asset('images/menuicn.png') }}" class="icn menuicn" id="menuicn" alt="menu-icon">
            </div>

            <div class="searchbar">
                <input type="text" placeholder="Search" id="searchInput">
                <div class="searchbtn">
                    <img src="{{ asset('images/searchLupa.png') }}" class="icn srchicn" alt="search-icon">
                </div>
                <div id="searchResults" class="search-results"></div>
            </div>

            <div class="message">
                <div class="dp">
                    <a href="{{ url('/users/' . Auth::id()) }}">
                        <img src="{{ Auth::user()->profile_picture ? asset(Auth::user()->profile_picture) : asset('images/defaultAvatar.jpg') }}" class="dpicn" alt="dp">
                    </a>
                </div>
            </div>

        </header>

        <section id="content">
            @yield('content')
        </section>
        <script src="{{ asset('js/adminDash.js') }}"></script>
        <script src="{{ asset('js/toggleAuctions.js') }}"></script>
        <script src="{{ asset('js/searchUsers.js') }}"></script>
    </body>
</html>