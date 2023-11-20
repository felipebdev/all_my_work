<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>Internal Server Error</title>

    <link rel="icon" href="{{ asset('xgrow-vendor/assets/img/favicon.ico') }}" type="image/x-icon">
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,700,1,0"/>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,300;0,400;0,600;0,700;1,300;1,400&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,600;0,700;1,300;1,400&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            width: 100%;
            height: 100vh;
        }

        body {
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            padding: 1rem;
        }

        .img-cover {
            width: 100%;
            object-fit: cover;
            height: 100%;
            position: absolute;
            z-index: 0;
            filter: blur(1px);
        }

        .content {
            z-index: 1;
            position: relative;
            display: flex;
            max-width: 650px;
            flex-direction: column;
            text-align: center;
            align-items: center;
            gap: 1rem;
        }

        .content img {
            width: 200px;
            margin-bottom: 20px
        }

        .content h1 {
            font-family: 'Open Sans', serif;
            font-style: normal;
            font-weight: 600;
            font-size: 22px;
            line-height: 160%;
            text-align: center;
            text-transform: uppercase;
            color: #D1D1D1;
            margin-bottom: 1rem;
        }

        .content p {
            color: #ffffff;
            font-family: 'Roboto', sans-serif;
            font-size: 2rem;
            line-height: 51.2px;
            font-weight: 600;
            margin-bottom: .5rem;
        }

        .content p span {
            color: #ADFF2F;
        }

        .content p.subtitle {
            color: #ffffff;
            font-family: 'Roboto', sans-serif;
            font-size: 1.625rem;
            line-height: 41.6px;
            font-weight: 400;
        }

        .content .xgrow-button {
            display: flex;
            flex-direction: row;
            font-family: 'Roboto', sans-serif;
            padding: 11px 40px;
            width: fit-content;
            height: 46px;
            background: #93BC1E;
            border-radius: 8px;
            color: #FFFFFF;
            gap: 1rem;
            text-decoration: none;
            align-items: center;
            margin: 3rem auto;
        }

        footer {
            bottom: 2rem;
            position: absolute;
            text-align: center;
            color: #FFFFFF;
            font-family: 'Roboto', sans-serif;
            font-size: 1rem;
        }

        footer a{
            color: #FFFFFF;
        }

        footer .logo-img {
            height: 42px;
            margin-bottom: 1rem;
        }

        @media only screen and (max-width: 576px) {
            .content p {
                font-size: 1.1rem;
                line-height: 2rem;
                margin-bottom: .5rem;
            }

            .content p.subtitle {
                font-size: 1rem;
                line-height: 1.5rem;
            }

            footer {
                font-size: .9rem;
            }
        }
    </style>
</head>
<body>
<img src="{{ asset('xgrow-vendor/assets/img/errors/503.png') }}" alt="Background da XGROW" class="img-cover">
<div class="content">
    <h1>Em manutenção</h1>
    <p>Estamos trabalhando na plataforma para <span>melhorar</span> a sua <span>experiência</span></p>
    <p class="subtitle">Mas não se preoucupe, voltaremos ao ar em instantes</p>
    <a href="mailto:suporte@xgrow.com" class="btn xgrow-button">
        <span class="material-symbols-outlined">support_agent</span> Falar com o suporte
    </a>
</div>
<footer>
    <img src="{{ asset('/xgrow-vendor/assets/img/logo/dark.svg') }}" alt="XGROW Learining Experience"
         class="mt-5 mb-2 logo-img"/>
    <p>
        XGROW © 2022 Todos direitos reservados. <a href="https://xgrow.com/terms">Termos de Uso</a> | <a href="https://xgrow.com/politics">Política de Privacidade</a>
    </p>
</footer>
</body>
</html>
