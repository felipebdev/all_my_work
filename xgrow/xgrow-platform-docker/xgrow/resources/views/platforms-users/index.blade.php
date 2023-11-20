@extends('templates.xgrow.main')

@push('after-styles')
    <style>
        .img_profile {
            width: 120px !important;
            height: 120px !important;
            object-fit: cover;
            object-position: center;
            border-radius: 50%;
            border: 3px solid white;
        }
    </style>
    <link rel="stylesheet" href="{{asset('xgrow-vendor/plugins/password-validator/password-validator.css')}}">
@endpush

@push('after-scripts')
    <script src="{{ asset('xgrow-vendor/assets/js/toast-config.js') }}"></script>
    <script src="{{asset('xgrow-vendor/plugins/password-validator/password-validator.js')}}"></script>
    <script>
        $('#saveButton').on('click', function (e) {
            let password = $('#user_password').val();
            let confirm_password = $('#user_confirmpassword').val();

            if (password.trim() !== confirm_password.trim()) {
                if (password.trim() === '' || password.trim() === null || password.trim() === undefined) {
                    errorToast('Algum erro aconteceu!', 'Preencha o primeiro campo de senha.');
                    return false;
                }

                if (confirm_password.trim() === '' || confirm_password.trim() === null || confirm_password.trim() === undefined) {
                    errorToast('Algum erro aconteceu!', 'Preencha o segundo campo de senha.');
                    return false;
                }

                errorToast('Senhas não coincidem!', 'As senhas não são iguais.');
                return false;
            }

            e.preventDefault();
            const exportModal = $('#exportModal');
            exportModal.modal('show');
        });

        function save() {
            $('#user_form-edit').submit();
        }

    </script>
    <script>
        const setBtnLightMode = () => {
            document.getElementById('theme-slider').checked = 'true';
        };

        const setBtnDarkMode = () => {
            document.getElementById('theme-slider').removeAttribute('checked');
        };

        const currentBtnState = () => {
            if (localStorage.getItem('theme').includes('dark')) {
                setBtnDarkMode();
            } else {
                setBtnLightMode();
            }
        };
        currentBtnState();

    </script>
@endpush

@section('content')
    <nav class="xgrow-breadcrumb my-3 mb-0" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Usuário</a></li>
            <li class="breadcrumb-item active mx-2"><span>Editar</span></li>
        </ol>
    </nav>

    <div class="xgrow-card card-dark">
        <div class="xgrow-card-header">
            <h3>Perfil</h3>
        </div>
        {!! Form::model($user, ['route' => 'user.update', 'method' => 'PUT', 'id' => 'user_form-edit', 'enctype' => "multipart/form-data"]) !!}
        <div class="xgrow-card-body">
            <div class="row flex-wrap-reverse">
                @include('platforms-users._form')

                <div class="modal-sections modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModal"
                     aria-hidden="true"
                     data-bs-backdrop="static">
                    <span id="spnUrl" class="block"></span>
                    <span id="spnFile" class="block"></span>
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" data-bs-dismiss="modal" aria-label="Close">
                                    <i class="fa fa-times"></i>
                                </button>
                            </div>

                            <div class="modal-header">
                                <p class="modal-title" id="exportModalTitle">Atualização dos dados</p>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <p>Digite sua senha para confirmar a alteração dos dados.</p>
                                    </div>
                                    <div class="col-sm-12 mt-3" style="text-align: left">
                                        <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                                            <input autocomplete="off" type="password" spellcheck="false"
                                                   id="confirm_data"
                                                   name="confirm_data" tabindex="6" required>
                                            <label>Digite sua senha</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer justify-content-center">
                                <button type="submit" class="btn btn-success" id="btnExportModal" onclick="save()">
                                    Salvar
                                </button>
                                <button type="button" class="btn btn-outline-success" data-bs-dismiss="modal"
                                        aria-label="Close">
                                    Cancelar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="xgrow-card-footer">
            <button class="xgrow-button" id="saveButton">Salvar alterações</button>
        </div>
        @include('up_image.modal-xgrow')
        {!! Form::close() !!}
    </div>
    @include('elements.toast')
@endsection
