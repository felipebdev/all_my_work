<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>Page Not Found</title>

    <link rel="icon" href="{{ asset('xgrow-vendor/assets/img/favicon.ico') }}" type="image/x-icon">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Zen+Dots&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');
        /* @import url('https://fonts.googleapis.com/css2?family=Days+One&display=swap'); */
        /* @import url('https://fonts.googleapis.com/css2?family=Michroma&display=swap'); */

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
        }

        .background-image {
            width: 100%;
            height: 100%;

            background-image: url("{{ asset('xgrow-vendor/assets/img/errors/404.jpg') }}");
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center center;

            display: flex;
            align-items: center;
            justify-content: flex-end;
        }

        .content {
            float: right;
            margin-right: 30%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .content img {
            width: 200px;
        }

        .content h1 {
            color: #c4cf00;
            font-family: 'Zen Dots', cursive;
            /* font-family: 'Days One', sans-serif; */
            /* font-family: 'Michroma', sans-serif; */
            font-size: 100px;
            line-height: 80px;
        }

        .content p {
            color: #ffffff;
            font-family: 'Poppins', sans-serif;
            font-size: 25px;
        }

        .content p span {
            font-weight: 600;
        }

        .content a {
            display: inline-block;
            background-color: #c4cf00;
            color: #000000;
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            font-size: 12px;
            text-decoration: none;
            padding: 12px 50px;
            border-radius: 50px;
            margin-top: 20px;
        }

        @media only screen and (max-width: 1300px) {
            .background-image {
                background-position: 20% center;
            }

            .content {
                margin-right: 20%;
            }
        }

        @media only screen and (max-width: 800px) {
            .background-image {
                justify-content: center;
                background-image:
                    linear-gradient(
                        rgba(0, 0, 0, 0.75),
                        rgba(0, 0, 0, 0.75)
                    ),
                    url("{{ asset('xgrow-vendor/assets/img/errors/404.jpg') }}");
            }

            .content {
                margin-right: 0;
            }
        }

        @media only screen and (max-width: 325px) {
            .content h1 {
                font-size: 80px;
            }

            .content p {
            font-size: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="background-image">
        <div class="content">
            <img src="{{ asset('/xgrow-vendor/assets/img/logo_wide_darkmode.svg') }}" alt="XGROW Learining Experience"/>
            <h1>404</h1>
            <p>PAGE <span>NOT FOUND</span></p>
        </div>
    </div>
</body>
</html>
