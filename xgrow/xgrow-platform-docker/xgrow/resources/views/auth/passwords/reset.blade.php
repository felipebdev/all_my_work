@extends('templates.application.master')

{{-- JS do Antigo Login --}}
@section('template-custom-js')
    <script src="{{ asset('xgrow-vendor/assets/js/toast-config.js') }}"></script>
    <script src="/vendor/wrappixel/monster-admin/4.2.1/monster/js/custom.min.js"></script>
    <script>
        function validateForm() {
            let email = document.getElementById('email').value;
            let password = document.getElementById('password').value;
            let passwordConfirm = document.getElementById('password-confirm').value;

            if (email.trim() === "") {
                errorToast('Algum erro aconteceu!', 'Email deve ser preenchido.');
                return false;
            }
            if (password.trim() === "") {
                errorToast('Algum erro aconteceu!', 'Senha deve ser preenchida.');
                return false;
            }
            if (passwordConfirm.trim() === "") {
                errorToast('Algum erro aconteceu!', 'Confirmação de senha deve ser preenchida.');
                return false;
            }
            if (password.trim() !== passwordConfirm.trim()) {
                errorToast('Algum erro aconteceu!', 'As senhas não são iguais.');
                return false;
            }
            if (password.trim().length < 8 || passwordConfirm.trim().length < 8) {
                errorToast('Algum erro aconteceu!', 'A senha precisa ter no mínimo 8 caracteres.');
                return false;
            }
        }
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

        .xgrow-login-title {
            max-width: 300px;
        }
    </style>
@endpush

@section('layout-content')
    <section id="wrapper" class="xgrow-login-container xgrow-background-image">
        <div class="row h-100 xgrow-main-login align-items-center justify-content-center">
            <div class="col-lg-6 col-md-6 col-12 xgrow-left-login d-flex flex-column align-items-lg-end
                align-items-md-end align-items-center">
                <div class="xgrow-img-logo">
                    <img src="{{ asset('xgrow-vendor/assets/img/logo/dark.svg') }}" alt="XGrow">
                </div>
                <p class="xgrow-login-phrase">Faça um upgrade na sua <span>experiência de ensino</span>
                </p>
            </div>
            <div class="col-lg-6 col-md-6 col-12 xgrow-right-login d-flex flex-column align-items-lg-start
                align-items-md-start align-items-center">
                <p class="xgrow-login-title">Informe seus dados abaixo para realizar a alteração de sua senha.</p>

                @if (session('status'))
                    <div class="xgrow-login-success">
                        {{ session('status') }}
                    </div>
                @endif

                @if (!$errors->isEmpty())
                    <span class="xgrow-login-error">
                        <ul style="margin: 0; padding-left: 10px">
                        @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </span>
                @endif

                <form class="d-flex flex-column align-items-lg-start align-items-md-start align-items-center"
                      method="POST" action="{{ route('password.request') }}" onsubmit="return validateForm()">
                    @csrf

                    <input type="hidden" name="token" value="{{ $token }}">
                    <input type="hidden" name="user_type" value="platformusers">

                    <input id="email" placeholder="Email" type="email" name="email"
                           class="{{ $errors->has('email') ? 'error' : '' }}"
                           value="{{ $email ?? old('email') }}" required autofocus>

                    <input id="password" placeholder="Password" type="password" name="password"
                           class="{{ $errors->has('password') ? 'error' : '' }}" required>

                    @if ($errors->has('password'))
                        <span class="invalid-feedback">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                    @endif

                    <input id="password-confirm" placeholder="{{ __('Confirm Password') }}" type="password"
                           name="password_confirmation" required>

                    <button class="xgrow-login-button" type="submit">Redefinir senha</button>
                    <div class="forget-pwd">
                        <a href="{{ route('login') }}">Voltar</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
    @include('elements.toast-bs4')
@endsection
