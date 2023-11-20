@extends('templates.application.master')

@section('template-custom-js')
    <script src="/vendor/wrappixel/monster-admin/4.2.1/monster/js/custom.min.js"></script>
@endsection

@push('after-styles')
    <link rel="stylesheet" href="{{ asset('xgrow-vendor/assets/css/pages/login.css') }}">
    <style>
        a {
            color: #eeeeee;
            font-weight: 400;
        }

        a:hover {
            color: #c6c6c6;
        }

        .custom-container {
            color: #FFFFFF;
            background: rgba(18, 20, 25, .9);
            min-height: 548px;
            width: 60%;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }

        .custom-container > h3 {
            font-style: normal;
            font-size: 25px;
            line-height: 34px;
            color: #FFFFFF;
            margin-bottom: 40px;
        }

        .custom-button {
            background: #82BF23;
            border-radius: 8px;
            min-width: 200px;
            min-height: 48px;
            font-size: 1.1rem;
            line-height: 2.1rem;
            border-color: #82BF23;;
        }

        .custom-button:hover {
            background: #70a41d;
            border-color: #70a41d;
            opacity: 1;
        }

        ul {
            padding-left: 1rem;
        }

        @media (max-width: 600px) {
            .custom-container {
                min-height: 348px;
                width: 80%;
                padding: 60px 20px;
                text-align: left;
            }

            .xgrow-main-login {
                margin-top: 50px;
            }
        }
    </style>
@endpush

@section('layout-content')
    <section id="wrapper" class="xgrow-login-container xgrow-background-image">
        <div class="xgrow-main-login w-100 d-flex flex-column align-items-center">
            <div class="xgrow-img-logo">
                <img src="{{ asset('xgrow-vendor/assets/img/logo/dark.svg') }}" alt="Xgrow">
            </div>
            <div class="custom-container">
                @if($res)
                    <h3>Obrigado.</h3>
                    <ul>
                        <li class="mb-2">
                            Tive um problema técnico, quero entrar em contato com o suporte -
                            <a href="mailto:suporte@xgrow.com">Clique aqui</a>
                        </li>
                        <li class="mb-2">
                            Quero receber meus dados de acesso novamente.
                            <a href="{{route('password.reset')}}">Clique aqui</a>
                        </li>
                    </ul>
                @else
                    <h3>Obrigado, estamos aguardando você</h3>
                @endif
                <a href="{{route('login')}}" class="btn btn-success custom-button">Ir para login</a>
            </div>
        </div>
    </section>
@endsection


{{--<div class="col-lg-6 col-md-6 col-12 xgrow-left-login d-flex flex-column align-items-lg-end--}}
{{--                align-items-md-end align-items-center">--}}
{{--    <div class="xgrow-img-logo">--}}
{{--        <img src="{{ asset('xgrow-vendor/assets/img/logo_wide_darkmode.svg') }}" alt="Logo Xgrow">--}}
{{--    </div>--}}
{{--    <p class="xgrow-login-phrase">Sua plataforma de<br/><span>conteúdo e crescimento</span><br/>exponencial--}}
{{--    </p>--}}
{{--</div>--}}
{{--<div class="col-lg-6 col-md-6 col-12 xgrow-right-login d-flex flex-column align-items-lg-start--}}
{{--                align-items-md-start align-items-center">--}}
{{--    <p class="xgrow-login-title">Informe seus dados abaixo para realizar a alteração de sua senha.</p>--}}
{{--</div>--}}
