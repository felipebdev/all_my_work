<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>Página Expirada</title>

    <link rel="icon" href="{{ asset('xgrow-vendor/assets/img/favicon.ico') }}" type="image/x-icon">

    <!-- FontAwesome -->
    <link href="{{ asset('font-awesome/css/all.min.css') }}" rel="stylesheet">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700;1,800&display=swap');

        * {
            margin: 0;
            padding: 0;
            font-family: 'Open Sans', sans-serif;
        }

        .background-image {
            width: 100%;
            height: 100vh;

            background-image: url('/xgrow-vendor/assets/img/errors/419.jpg');
            background-image: -webkit-image-set(url('/xgrow-vendor/assets/img/errors/419.webp') 1x);

            background-repeat: no-repeat;
            background-position: center center;
            background-size: cover;
        }

        .background-opacity {
            width: 100%;
            height: 100vh;
            position: absolute;
            background-color: #121419;
            opacity: 0.8;
            z-index: 0;
        }

        .content {
            width: 100%;
            height: 100vh;
            overflow-x: auto;
            display: flex;
            align-items: center;
            flex-direction: column;
            position: relative;
            z-index: 1;
        }

        .content .logo {
            width: 16.25rem;
            height: auto;
        }

        .message {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background-color: #222429B2;
            padding: 3.375rem;
            border-radius: 0.625rem;
            backdrop-filter: blur(20px);
            margin: 1rem;
        }

        .icon {
            font-size: 3.633rem;
            color: #93BC1E;
            margin-bottom: 0.809rem;
        }

        h4.message-title {
            font-weight: 700;
            font-size: 1.625rem;
            color: #ffffff;
            margin-bottom: 0.938rem;
            text-align: center;
        }

        p.message-text {
            color: #ffffff;
            text-align: center;
            font-weight: 400;
            font-size: 1.125rem;
            margin-bottom: 2.313rem;
        }

        button.action {
            padding: 0.688rem 2.5rem;
            font-weight: 700;
            font-size: 0.8rem;
            color: #ffffff;
            background-color: #93BC1E;
            border: none;
            border-radius: 0.5rem;
            cursor: pointer;
            transition-duration: 0.2s;
        }
        button.action i {
            margin-right: 0.5rem;
        }
        button.action:hover {
            background-color: #688616;
        }
    </style>
</head>
<body>
    <div class="background-image">
        <div class="background-opacity"></div>
        <div class="content">
            <img class="logo" src="/xgrow-vendor/assets/img/logo/dark.svg" alt="XGROW">
            <div class="message">
                <i class="fas fa-clock icon"></i>
                <h4 class="message-title">A página expirou :(</h4>
                <p class="message-text">
                    Mas não se preocupe, para resolver este problema basta clicar no botão abaixo<br/>
                    para recarregá-la:
                </p>
                <button class="action" onclick="window.location.href = '/'">
                    <i class="fas fa-sync-alt"></i> Recarregar a página
                </button>
            </div>
        </div>
    </div>

    <script src="{{ asset('font-awesome/js/all.min.js') }}" crossorigin="anonymous"></script>
</body>
</html>