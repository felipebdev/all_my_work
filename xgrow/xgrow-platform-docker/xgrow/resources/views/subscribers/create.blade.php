@extends('templates.xgrow.main')

@push('jquery')
    <script src="{{ asset('xgrow-vendor/assets/js/toast-config.js') }}"></script>
    <script>
        function validate() {
            let password = $('input[name=password]').val();
            let re_password = $('input[name=re_password]').val();

            if (password.length > 0 || re_password.length > 0) {
                if (password !== re_password) {
                    errorToast('Senhas diferentes!', 'As senhas devem ser iguais.');
                    return false;
                }
            }

            let edtPassword = document.getElementsByName('password')[0];
            let password2 = edtPassword.value.trim();
            if (!(/[0-9]/.test(password2)) || !(/[a-zA-Z]/.test(password2)) || !(/[^A-Za-z0-9]/.test(password2)) || !(password2.length > 4)) {
                errorToast('Erro', 'Obrigatório no mínimo 5 caracteres incluindo: letras, números e pelo menos um caractere especial.');
                return false;
            }

            const countrySelected = $('#address_country').val();
            if (countrySelected == '') {
              errorToast('Algum erro aconteceu!', `Informe o país!`);
               return false;
            }

            return true;
        }

    </script>
@endpush

@push('before-scripts')
@endpush

@push('after-scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"
        integrity="sha512-pHVGpX7F/27yZ0ISY+VVjyULApbDlD0/X0rgGbTqCE7WFW5MezNTWG/dnhtbBuICzsd0WQPgpE4REBLv+UqChw=="
        crossorigin="anonymous"></script>
@endpush

@push('after-styles')
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link href="{{ asset('xgrow-vendor/assets/css/pages/subscribers_index.css') }}" rel="stylesheet">
    <link rel="stylesheet" type="text/css"
        href="https://cdn.jsdelivr.net/npm/spectrum-colorpicker2@2.0.0/dist/spectrum.min.css">
@endpush

@section('content')
    <nav class="xgrow-breadcrumb my-3 mb-0" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Início</a></li>
            <li class="breadcrumb-item"><a href="/subscribers">Alunos</a></li>
            <li class="breadcrumb-item active"><span>Novo</span></li>
        </ol>
    </nav>

    <div class="xgrow-card card-dark p-3">
        <!-- <div class="xgrow-card-header px-3">
                <div class="d-flex flex-column">
                    <p class="xgrow-card-title">Novo inscrito</p>
                    <p class="xgrow-card-subtitle" style="font-weight: 600;">*Campos obrigatórios</p>
                </div>
            </div> -->

        <form method="POST" action="{{ url('/subscribers') }}" onsubmit="return validate()" autocomplete="off">
            <div class="xgrow-card-body px-3 py-1">
                @if ($plans->count() == 0)
                    <div class="alert alert-warning">
                        É necessário ter ao menos um plano cadastrado para prosseguir!
                    </div>
                @else
                    @include('subscribers.form')
                    {{ csrf_field() }}
                    {{ method_field('POST') }}
                @endif
            </div>

            <div class="xgrow-card-footer p-3 border-top">
                <button type="submit" class="xgrow-button">Cadastrar</button>
            </div>
        </form>
    </div>
    @include('elements.toast')
@endsection
