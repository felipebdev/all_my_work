@extends('templates.application.master')

{{-- JS do Antigo Login --}}
@section('template-custom-js')
    <script src="/vendor/wrappixel/monster-admin/4.2.1/monster/js/custom.min.js"></script>
    <script src="/js/recaptcha.js"></script>
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
                <p class="xgrow-login-title">Informe seu e-mail abaixo que iremos lhe enviar o link de recuperação.</p>

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
                      method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <input id="email" name="email" type="email" class="{{ $errors->has('email') ? 'error' : '' }}"
                           placeholder="E-mail" value="{{ old('email') }}" required/>
                    <button class="xgrow-login-button" type="submit">Redefinir senha</button>
                    <div class="forget-pwd">
                        <a href="{{ route('login') }}">Voltar</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
