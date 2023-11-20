@extends('templates.xgrow.no-admin')

@push('after-styles')
    <link href="{{ asset('xgrow-vendor/assets/css/pages/register.css') }}" rel="stylesheet">
    <style>
        input:-webkit-autofill {
            -webkit-box-shadow: 0 0 0 1000px rgba(34, 36, 41, 0.7) inset !important;
            box-shadow: 0 0 0 1000px rgba(34, 36, 41, 0.7) inset !important;
        }
    </style>
@endpush

@push('after-scripts')
    <script src="/js/recaptcha.js"></script>
    <script src="{{ asset('xgrow-vendor/assets/js/toast-config.js') }}"></script>
    <script>
        const registerUrl = @json(route('post.register'));
        const checkEmailUrl = @json(route('check.email.register', ':id'));
        const rustUrl = @json(config('recaptcha.v3.public_key'));
    </script>
    <script src="{{ asset('js/bundle/register.js') }}"></script>
@endpush

@section('content')
    <meta name="grecaptcha-key" content="{{config('recaptcha.v3.public_key')}}">
    <script src="https://www.google.com/recaptcha/api.js?render={{config('recaptcha.v3.public_key')}}"></script>
    <div id="register" class="d-flex flex-column align-items-center justify-content-center">
        <div class="xgrow-img-logo">
            <img src="/xgrow-vendor/assets/img/logo/dark.svg" alt="Xgrow">
        </div>
        <div class="xgrow-card-transparent">
            <div class="header" v-if="activeScreen != 'registerEnd'">
                <h2 class="title m-0">Criando sua conta</h2>
                <span class="subtitle">Preencha os dados para criar sua conta no Xgrow</span>
            </div>
            <div class="xgrow-card-transparent-body mt-3">
                <template v-if="activeScreen != 'registerEnd'">
                    <div class="xgrow-tabs nav nav-tabs mb-3" id="nav-tab">
                        <a class="xgrow-tab-item nav-item nav-link" id="nav-current-lives-tab"
                           :class="{'active': activeScreen.toString() === 'personalData'}">
                            Dados Pessoais
                        </a>

                        <a class="xgrow-tab-item nav-item nav-link" id="nav-previous-lives-tab"
                           :class="{'active': activeScreen.toString() === 'termsAndConditions'}">
                            Termos e condições
                        </a>
                    </div>
                </template>

                <status-modal-component :is-open="statusLoading" :status="status"></status-modal-component>

                <div class="tab-content" id="nav-tabContent">
                    <template v-if="activeScreen != 'registerEnd'">
                        @include('platforms.tabs.register-personal-data')
                        @include('platforms.tabs.register-terms-condition')
                    </template>
                    @include('platforms.tabs.register-end')
                </div>
            </div>
        </div>
    </div>
    @include('elements.toast')
@endsection
