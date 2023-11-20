@extends('templates.horizontal.main')

@section('template-custom-js')
    <script src="/vendor/wrappixel/monster-admin/4.2.1/monster/js/custom.min.js"></script>
@endsection

@section('layout-content')

    <section id="wrapper" class="login-register login-sidebar" style="background-image:url(/images/login-bg.jpg);">
        <div class="login-box card">
            <div class="card-body">
                <form method="POST" action="{{ route('verify.store') }}" class="form-horizontal form-material" autocomplete="off">
                    @csrf
                    <a href="javascript:void(0)" class="text-center db">
                        <img src="/images/logo_wide_darkmode.svg" class="img-fluid" alt="logo <?= env('APP_NAME') ?>" />
                    </a>

                    <p class="xgrow-login-title">Insira o código de verificação</p>

                    <div>
                        Enviado para {{$email ?? '****'}}
                    </div>

                    @include('common.errors')
                    @include('common.success')

                    <div class="form-group m-t-40">
                        <div class="col-xs-12">
                            <input name="two_factor_code" type="text"
                                   class="form-control {{ $errors->has('two_factor_code') ? ' is-invalid ' : '' }}"
                                   required autofocus placeholder="Código de verificação"
                                   inputmode="numeric"
                                   value="{{request('code')}}">
                        </div>
                    </div>

                    <div class="form-group text-center m-t-20">
                        <div class="col-xs-12">
                            <button class="btn btn-info btn-lg btn-block text-uppercase waves-effect waves-light"
                                    type="submit">Confirmar
                            </button>
                        </div>
                    </div>

                    <div class="form-group m-b-0">
                        <div class="col-sm-12 text-center">
                            Caso não tenha recebido, tente <a href="{{ route('verify.resend') }}">enviar novamente</a>.
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </section>

@endsection

