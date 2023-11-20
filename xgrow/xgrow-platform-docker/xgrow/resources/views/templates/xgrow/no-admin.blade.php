<!DOCTYPE html>
<html lang="pt-BR" class="dark-mode">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('xgrow-vendor/assets/img/favicon.ico') }}" type="image/x-icon">

    <!-- MUI CSS -->
    <link href="{{ asset('//cdn.muicss.com/mui-0.10.3/css/mui.min.css') }}" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="{{ asset('xgrow-vendor/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- FontAwesome -->
    <link href="{{ asset('font-awesome/css/all.min.css') }}" rel="stylesheet">

    <!-- CustomStyles -->
    <link href="{{ asset('xgrow-vendor/assets/css/colors.css') }}" rel="preload" as="style">
    <link href="{{ asset('xgrow-vendor/assets/css/components.css') }}" rel="stylesheet">
    <link href="{{ asset('xgrow-vendor/assets/css/layout.css') }}" rel="stylesheet">
    <title>XGROW :: Crescimento Exponencial</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,600;0,700;1,300;1,400&display=swap"
        rel="stylesheet">
    @stack('before-styles')
    @stack('after-styles')
</head>

<body class="dom-loading">
    <!-- CONTEUDO DA PAGE DENTRO DESSE DIV -->
    @yield('content')

    @stack('before-scripts')
    <script>
        localStorage.setItem("theme", 'dark');
    </script>
    <script async src="{{ asset('xgrow-vendor/assets/js/themes.js') }}"></script>
    <script src="{{ asset('xgrow-vendor/plugins/jquery/jquery-3.5.1.min.js') }}"></script>
    @stack('jquery')
    <script src="{{ asset('xgrow-vendor/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('font-awesome/js/all.min.js') }}" crossorigin="anonymous"></script>
    <script src="//cdn.muicss.com/mui-0.10.3/js/mui.min.js"></script>
    <script src="{{ asset('xgrow-vendor/assets/js/script.js') }}"></script>
    <script src="{{ asset('/js/helpers.js') }}"></script>

    @if (env('GOOGLE_ANALYTICS_ID'))
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id={{env('GOOGLE_ANALYTICS_ID')}}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', @json(env('GOOGLE_ANALYTICS_ID')));
    </script>
    @endif

    @stack('after-scripts')
</body>

</html>
