<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token For security -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Profitley</title>

    <link rel="stylesheet" href="{{url('css/dashlite.css')}}">
    <link id="skin-default" rel="stylesheet" href="{{url('css/theme.css')}}">
    @php
        $loggedInUser = \Auth::user();
    @endphp
    <script type="text/javascript">
        var APP_BASE_URL = "{{config('constant.APP_BASE_URL')}}";
        var LOGIN_USER_ID = {{ $loggedInUser->id }}
    </script>
    <script src="{{url('js/jquery-3.5.1.min.js?t='.time())}}"></script>
    <script src="https://code.jquery.com/jquery-migrate-3.3.1.min.js" integrity="sha256-APllMc0V4lf/Rb5Cz4idWUCYlBDG3b0EcN1Ushd3hpE=" crossorigin="anonymous"></script>
    
    <script src="{{url('js/firebase-app.js?t='.time()) }}"></script>
    <script src="{{url('js/firebase-auth.js?t='.time()) }}"></script>
    <script src="{{url('js/firebase-firestore.js?t='.time()) }}"></script>
    <script src="{{url('js/firebase-database.js?t='.time()) }}"></script>

    @stack('headerScripts')
</head>

<body>
    @yield('content')
</body>
</html>