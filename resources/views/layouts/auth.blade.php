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
        <link href="{{ url('css/auth.css') }}" rel="stylesheet">
        <script type="text/javascript">
            // Fix for Firefox autofocus CSS bug
            // See: http://stackoverflow.com/questions/18943276/html-5-autofocus-messes-up-css-loading/18945951#18945951
        </script>
        <script type="text/javascript" src={{ url('js/app.js') }} defer>
        </script>
    </head>
    <body>
        <div class="header-left">
            <a href="{{ url('/home') }}">
                <div class="image-container">
                    <img src="{{ asset('images/bidhub.png') }}" alt="BidHub Logo" width="250" heigth ="250">
                </div>
            </a>
        </div>
        <main>
            <section id="content">
                @yield('content')
            </section>
        </main>
        <script src="{{ asset('js/auth.js') }}"></script>
        <script src="{{ asset('js/bsadmin.js') }}"></script>
    </body>
</html>