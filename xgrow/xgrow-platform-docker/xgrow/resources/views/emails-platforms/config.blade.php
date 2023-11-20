@extends('templates.xgrow.main')

@push('jquery')
    <script src="{{ asset('xgrow-vendor/assets/js/toast-config.js') }}"></script>
    <script>
        function validate() {
            var password = $("#server_password").val()
            var server_password_confirm = $("#server_password_confirm").val()

            if (password.length > 0 || server_password_confirm.length > 0) {
                if (password !== server_password_confirm) {
                    errorToast("Senhas diferentes!", "As senhas devem ser iguais.");
                    return false
                }
            }
            return true;
        }

        function mailTest() {
            $.ajax({
                type: 'GET',
                url: "/emails/test",
                dataType: 'json',
                success: function(data) {
                    successToast("Teste bem sucedido!", `${data.message}`);
                },
                error: function() {
                    errorToast("Algum erro aconteceu!", "Houve um erro no envio do e-mail.");
                }
            });
        }

    </script>
@endpush

@section('content')
    <nav class="xgrow-breadcrumb my-3 mb-0" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Início</a></li>
            <li class="breadcrumb-item"><a href="/platform-config">Configurações</a></li>
            <li class="breadcrumb-item"><a href="/emails">E-mails</a></li>
            <li class="breadcrumb-item active mx-2"><span>Configurações do e-mail</span></li>
        </ol>
    </nav>

    <div class="xgrow-card card-dark">
        <div class="xgrow-card-header">
            <p class="xgrow-card-title">Configurações do e-mail</p>
        </div>
        <form class="mui-form" action="{{ url('/emails/conf/store') }}" method="post" onsubmit="return validate()">
            @csrf
            <div class="xgrow-card-body">
                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                            <input id="from_name" name="from_name" autocomplete="off" type="text" spellcheck="false"
                                tabindex="1" value="{{ $email->from_name ?? '' }}" required>
                            <label>Nome from</label>
                            <span onclick="document.getElementById('from_name').value = ''"></span>
                        </div>
                        <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                            <input id="server_name" name="server_name" autocomplete="off" type="text" spellcheck="false"
                                tabindex="3" value="{{ $email->server_name ?? '' }}" required>
                            <label>Servidor de e-mail (SMTP)</label>
                            <span onclick="document.getElementById('server_name').value = ''"></span>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12">
                        <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                            <input id="from_address" name="from_address" autocomplete="off" type="text" spellcheck="false"
                                tabindex="2" value="{{ $email->from_address ?? '' }}" required>
                            <label>E-mail from</label>
                            <span onclick="document.getElementById('from_address').value = ''"></span>
                        </div>
                        <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                            <input id="server_port" name="server_port" autocomplete="off" type="text" spellcheck="false"
                                tabindex="4" value="{{ $email->server_port ?? '' }}" required>
                            <label>Porta (SMTP)</label>
                            <span onclick="document.getElementById('server_port').value = ''"></span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 col-sm-12">
                        <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                            <input id="server_user" name="server_user" autocomplete="off" type="text" spellcheck="false"
                                tabindex="5" value="{{ $email->server_user ?? '' }}" required>
                            <label>Usuário</label>
                            <span onclick="document.getElementById('server_user').value = ''"></span>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12">
                        <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                            <input id="server_password" name="server_password" autocomplete="off" type="password"
                                spellcheck="false" tabindex="6" value="{{ $email->server_password ?? '' }}" required>
                            <label>Senha</label>
                            <span onclick="document.getElementById('server_password').value = ''"></span>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12">
                        <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                            <input id="server_password_confirm" name="server_password_confirm" autocomplete="off"
                                type="password" spellcheck="false" tabindex="7"
                                value="{{ $email->server_password ?? '' }}" required>
                            <label>Confirme a senha</label>
                            <span onclick="document.getElementById('server_password_confirm').value = ''"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="xgrow-card-footer">
                <button type="button" class="xgrow-button-secondary w-auto px-3 me-2" tabindex="8"
                    onclick="mailTest()">Testar envio de e-mail</button>
                <button type="submit" class="xgrow-button" tabindex="9">Salvar alterações</button>
            </div>
        </form>
    </div>
    @include('elements.toast')
@endsection
