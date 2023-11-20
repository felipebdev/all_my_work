@extends('templates.application.master')

@push('after-styles')
    <link rel="stylesheet" href="{{ asset('xgrow-vendor/assets/css/pages/login.css') }}">
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
                <p class="xgrow-login-title">Insira o código de verificação</p>

                @if (!$errors->isEmpty())
                    <span class="xgrow-login-error">
                        @foreach ($errors->all() as $error)
                            - {{ $error }}
                        @endforeach
                    </span>
                @endif

                @if(session()->has('message'))
                    <p class="alert alert-info">
                        {{ session()->get('message') }}
                    </p>
                @endif
                <form method="POST" action="{{ route('verify.store') }}" autocomplete="off">
                    {{ csrf_field() }}
                    <p>
                        Código enviado para<br>
                        {{$email}}
                    </p>

                    <div class="input-group mb-3">
                        <input name="two_factor_code" type="text"
                               class="{{ $errors->has('two_factor_code') ? 'error' : '' }}"
                               required autofocus placeholder="Código de verificação"
                               inputmode="numeric"
                               value="{{request('code')}}">

                    </div>

                    <div class="row">
                        <div class="col-6">
                            <button type="submit" class="xgrow-login-button" type="submit">Confirmar</button>
                        </div>
                    </div>

                    <p>
                        Caso não tenha recebido, tente <a href="{{ route('verify.resend') }}">enviar novamente</a>.
                    </p>
                </form>
            </div>
        </div>
    </section>
@endsection

