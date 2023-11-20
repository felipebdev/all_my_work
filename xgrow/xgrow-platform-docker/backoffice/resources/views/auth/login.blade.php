@extends('templates.horizontal.main')

@section('template-custom-js')

    <script src="/vendor/wrappixel/monster-admin/4.2.1/monster/js/custom.min.js"></script>

    <script>
        $(function () {
            $("#back-to-login").click(function () {
                $("#loginform").slideDown()
                $("#recoverform").fadeOut()
            })
        })
    </script>

@endsection

@section('layout-content')

<section id="wrapper" class="login-register login-sidebar" style="background-image:url(/images/login-bg.jpg);">
    <div class="login-box card">
        <div class="card-body">

            <form class="form-horizontal form-material" id="loginform" method="POST" action="{{ route('login') }}">
                @csrf
                <a href="javascript:void(0)" class="text-center db">
                    <img src="/images/logo_wide_darkmode.svg" class="img-fluid" alt="logo <?= env('APP_NAME') ?>" />
                </a>

                @include('common.errors')
                @include('common.success')

                <div class="form-group m-t-40">
                    <div class="col-xs-12">
                        <input id="email" type="email" placeholder="Email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required autofocus>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-xs-12">
                        <input id="password" type="password" placeholder="Password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-12">
                        <div class="checkbox checkbox-primary pull-left p-t-0">
                            <input id="checkbox-signup" type="checkbox" {{ old('remember') ? 'checked' : '' }}>
                            <label for="checkbox-signup"> {{ __('Remember Me') }} </label>
                        </div>
                        <a href="javascript:void(0)" id="to-recover" class="text-dark pull-right"><i class="fa fa-lock m-r-5"></i> {{ __('Forgot Your Password?') }}</a> </div>
                </div>
                <div class="form-group text-center m-t-20">
                    <div class="col-xs-12">
                        <button class="btn btn-info btn-lg btn-block text-uppercase waves-effect waves-light" type="submit"> {{ __('Login') }}</button>
                    </div>
                </div>

            </form>
            <form class="form-horizontal" id="recoverform" method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="form-group ">
                    <div class="col-xs-12">
                        <h3>{{ __('Reset Password') }}</h3>
                        <p class="text-muted">Enter your Email and instructions will be sent to you! </p>
                    </div>
                </div>
                <div class="form-group ">
                    <div class="col-xs-12">
                        <input id="emailRecover" type="email" placeholder="Email"  class="form-control" name="email"  required>
                    </div>
                </div>
                <div class="form-group text-center m-t-20">
                    <div class="col-xs-12">
                        <button class="btn btn-primary btn-lg btn-block text-uppercase waves-effect waves-light" type="submit"> {{ __('Send Password Reset Link') }}</button>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-12">
                        <a href="javascript:void(0)" id="back-to-login" class="text-dark pull-right">
                            <i class="fa fa-backward m-r-5"></i>
                            Back to Login
                        </a>
                    </div>
                </div>
            </form>

        </div>
    </div>
</section>

@endsection
