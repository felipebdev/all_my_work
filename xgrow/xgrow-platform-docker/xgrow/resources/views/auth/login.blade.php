@extends('templates.application.master')

{{-- JS do Antigo Login --}}
@section('template-custom-js')
    <script src="/vendor/wrappixel/monster-admin/4.2.1/monster/js/custom.min.js"></script>
    <script>
        $(function() {
            $('#back-to-login').click(function() {
                $('#loginform').slideDown();
                $('#recoverform').fadeOut();
            });
        });
    </script>
    <script src="/js/recaptcha.js"></script>
    <script>
        const eyeIcon = document.querySelector('.input-password__icon');
        const passwordField = document.querySelector('.input-password__input');

        const toggleShowPassword = () => {
            if (eyeIcon.classList.contains('fa-eye')) {
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
                passwordField.type = 'text';
            } else {
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
                passwordField.type = 'password';
            }
        };

        eyeIcon.addEventListener('click', () => toggleShowPassword());


    </script>
@endsection

@push('after-styles')
    <link rel="stylesheet" href="{{ asset('xgrow-vendor/assets/css/pages/login.css') }}">
    <style>
        .forget-pwd {
            display: flex;
            align-items: center;
        }

        .forget-pwd i {
            color: #93BC1F;
            font-size: 1.25rem;
            margin-right: 6px;
        }

        .forget-pwd a {
            color: #FFFFFF;
            font-weight: 400;
        }

        .forget-pwd a:hover {
            color: #c6c6c6;
        }

        /*Alert*/
        div.alert {
            background: #E28A22;
            border-radius: 8px;
            max-width: 312px;
        }

        div.alert h1 {
            font-weight: 500;
            font-size: 1.5rem;
            color: #FFFFFF;
        }

        div.alert p {
            font-weight: 300;
            font-size: 0.875rem;
        }

        div.alert .close {
            font-weight: 300;
            font-size: 2rem;
            color: #FFFFFF;
        }
    </style>
@endpush

@section('layout-content')
    <meta name="grecaptcha-key" content="{{ config('recaptcha.v3.public_key') }}">
    <script src="https://www.google.com/recaptcha/api.js?render={{ config('recaptcha.v3.public_key') }}"></script>
    <section id="wrapper" class="xgrow-login-container xgrow-background-image">
        <div class="row h-100 xgrow-main-login align-items-center justify-content-center overflow-auto">
            <div
                class="col-lg-6 col-md-6 col-12 xgrow-left-login d-flex flex-column align-items-lg-end
                align-items-md-end align-items-center">
                <div class="xgrow-img-logo">
                    <img src="{{ asset('xgrow-vendor/assets/img/logo/dark.svg') }}" alt="XGrow">
                </div>
                <p class="xgrow-login-phrase">Faça um upgrade na sua <span>experiência de ensino</span>
                </p>
            </div>
            <div
                class="col-lg-6 col-md-6 col-12 xgrow-right-login d-flex flex-column align-items-lg-start
                align-items-md-start align-items-center">
                @if (
                    \Carbon\Carbon::now() > \Carbon\Carbon::create(2023, 3, 14, 00) &&
                        \Carbon\Carbon::now() < \Carbon\Carbon::create(2023, 3, 16, 3))
                    <div class="alert alert-dismissible fade show" role="alert">
                        <h1>ATENÇÃO</h1>
                        <p>Nesta quarta-feira (15/03) realizaremos uma atualização na Xgrow e durante esse
                            período a plataforma, checkout e learning área poderão sofrer instabilidades de acesso.</p>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
                <p class="xgrow-login-title">Acesse usando seu e-mail</p>

                @if (!$errors->isEmpty())
                    <span class="xgrow-login-error">
                        @foreach ($errors->all() as $error)
                            - {{ $error }}
                        @endforeach
                    </span>
                @endif

                <form class="d-flex flex-column align-items-lg-start align-items-md-start align-items-center" action=""
                    id="loginform" method="POST" action="{{ route('login') }}" data-grecaptcha-action="message">
                    @csrf
                    <input class="{{ $errors->has('email') ? 'error' : '' }}" type="email" name="email" id="email"
                        placeholder="E-mail" required />
                    <div class="input-password">
                        <input class="input-password__input {{ $errors->has('password') ? 'error' : '' }}" type="password" name="password"
                            id="password" placeholder="Senha" required />
                        <i id="loginIcon" class="input-password__icon fas fa-eye"></i>
                    </div>
                    <button class="xgrow-login-button" type="submit">Entrar</button>
                    <div class="forget-pwd">
                        <i class="fas fa-question-circle"></i>
                        <a href="{{ route('password.reset') }}">Esqueci minha senha</a>
                    </div>
                </form>
                <div id="armored_website">
                    <param id="aw_preload" value="true" />
                    <param id="aw_use_cdn" value="true" />
                </div>
                <script type="text/javascript" src="//cdn.siteblindado.com/aw.js"></script>
            </div>
        </div>
    </section>
@endsection
