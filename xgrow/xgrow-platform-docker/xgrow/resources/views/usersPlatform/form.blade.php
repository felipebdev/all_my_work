@push('after-styles')
    <link rel="stylesheet" href="{{asset('xgrow-vendor/plugins/password-validator/password-validator.css')}}">
@endpush

@push('after-scripts')
    <script src="{{asset('xgrow-vendor/plugins/password-validator/password-validator.js')}}"></script>
    <script>
        function validate() {
            const user_verify = 1;

            if (user_verify === 0) {
                newVerifyIsNecessary()
            } else {

                let type = $('#data_type').val();
                let user_name = $('#user_name').val();
                let user_email = $('#user_email').val();
                let user_password = $('#user_password').val();
                let user_password2 = $('#user_password-repeat').val();
                let user_type_access_restrict = $('#type_access_restrict').prop('checked');
                let user_permission_id = $('#permission_id').val();

                if (user_email.trim() === '') {
                    errorToast('Erro', 'Email não pode ficar em branco.');
                    return false;
                }

                if (user_type_access_restrict === true && user_permission_id === '0') {
                    errorToast('Erro', 'Informe a permissão.');
                    return false;
                }

                const total_in_other_platforms = $('#total_in_other_platforms').val();

                if(total_in_other_platforms === 0){

                    if (user_name.trim() === '') {
                        errorToast('Erro', 'Nome não pode ficar em branco.');
                        return false;
                    }

                    if (type === 'create' || type === '' || (type === 'edit' && user_password.trim() !== '')) {
                        if (user_password.trim() === '') {
                            errorToast('Erro', 'Senha não pode ficar em branco.');
                            return false;
                        }
                        if (user_password2.trim() === '') {
                            errorToast('Erro', 'Confirmação de senha não pode ficar em branco.');
                            return false;
                        }
                        if (user_password.trim() !== user_password2.trim()) {
                            errorToast('Erro', 'As senhas não conferem.');
                            return false;
                        }

                        if (!(/[0-9]/.test(user_password)) || !(/[a-zA-Z]/.test(user_password)) || !(/[^A-Za-z0-9]/.test(user_password)) || !(user_password.length > 4)) {
                            errorToast('Erro', 'Obrigatório no mínimo 5 caracteres incluindo: letras, números e pelo menos um caractere especial.');
                            return false;
                        }
                    }

                }

                document.querySelector('#user_form-create').submit();

            }
        }

        $(document).ready(
            function (){
                $('input[name=type_access]').click(
                    function (){
                        if($(this).val() == 'full')
                            $('#div_permission_id').addClass('d-none')
                        else
                            $('#div_permission_id').removeClass('d-none')
                    }
                )
            }
        )

        const elements = document.getElementsByTagName("input");
        for (let element of elements) {
            element.setAttribute("data-lpignore", "true");
            element.setAttribute("autocomplete", "off");
        }
    </script>
@endpush

@if ($user->id == 0)
    {!! Form::model($user, ['method' => 'post', 'id' => 'user_form-create', 'enctype' => 'multipart/form-data', 'route' => ['platforms-users.store'], "onsubmit" => "return validate()"]) !!}
@else
    {!! Form::model($user, ['method' => 'put', 'id' => 'user_form-create', 'enctype' => 'multipart/form-data', 'route' => ['platforms-users.update', $user->id], "onsubmit" => "return validate()"]) !!}
@endif

{{ csrf_field() }}
{!! Form::hidden('data_type', (($user->id == 0) ? 'create': 'edit'), ['id'=>'data_type']) !!}
{!! Form::hidden('total_in_other_platforms', $user->total_in_other_platforms, ['id'=>'total_in_other_platforms']) !!}
<div class="xgrow-card card-dark p-0">
    <div class="xgrow-card-header border-bottom">
        <div class="form-check form-switch mx-3">
            <span id="status-text">Ativo</span>
            {!! Form::checkbox('active', $user->active, $user->active, ['id' => 'chk-active', 'class' => 'form-check-input']) !!}
            {!! Form::label('', '') !!}
        </div>
    </div>
    <div class="xgrow-card-body p-3">

        @include('elements.alert')
        <div class="row">

            @if($user->id == 0 or $total_in_other_platforms == 0)
                <div class="col-sm-12 col-md-6">
                    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                        {!! Form::email('email', null, ['id'=>'user_email','autocomplete'=>'off', 'required']) !!}
                        {!! Form::label('user_email', 'E-mail') !!}
                    </div>
                    @if($user->id == 0)
                        <div style="margin: -20px 0 20px!important">
                            <span id="user_registered" class="d-none"
                                  style="color: #93BC1E"><small>Usuário cadastrado</small></span>
                            <span id="user_not_registered"
                                  class="d-none text text-danger"><small>Usuário não cadastrado</small></span>
                        </div>
                    @endif
                </div>
                <div class="col-sm-12 col-md-6 @if($user->id > 0) d-none @endif">
                    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                        {!! Form::button('Verificar', ['class' => 'xgrow-button', 'id' => 'btn_user_verify']) !!}
                    </div>
                </div>
            @else
                <div class="col-sm-12 col-md-6">
                    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                        {!! Form::email('email', null, ['id'=>'user_email','readonly'=>'readonly', 'required']) !!}
                        {!! Form::label('user_email', 'E-mail') !!}
                    </div>
                </div>
            @endif

        </div>

        <div id="type_access" @if($user->id == 0) class="d-none" @endif>
            <div class="row mt-3">
                <div class="col">
                    Tipo de acesso:
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-sm-12 col-md-6 d-flex justify-content-between">
                    <div class="xgrow-radio d-flex align-items-center my-2">
                        {!! Form::radio('type_access', 'restrict', $user->type_access, ['id'=>'type_access_restrict', 'class' => 'm-2']) !!}
                        {!! Form::label('type_access_restrict', 'Restrito', ['title' => 'Terá acesso somente as atribuições dos grupos em que o usuário for incluido no menu Permissões']) !!}
                    </div>
                    <div class="xgrow-radio d-flex align-items-center my-2">
                        {!! Form::radio('type_access', 'full', $user->type_access, ['id'=>'type_access_full', 'class' => 'm-2']) !!}
                        {!! Form::label('type_access_full', 'Total', ['title' => 'Possui acesso total a plataforma, sem restrições']) !!}
                    </div>
                </div>
            </div>
            <div class="row mb-3 @if($user->type_access == 'full') d-none @endif" id="div_permission_id" >
                <div class="col-sm-12 col-md-6">
                    <div class="xgrow-form-control mui-textfield mui-textfield--float-label">
                        {!! Form::select('permission_id', $permissions->prepend('Selecione', '0'), $permission_id, [
                        'class' => 'xgrow-select',
                        'id' => 'permission_id'
                        ]) !!}
                        {!! Form::label('permission_id', '*Permissão:') !!}
                    </div>
                </div>
            </div>
        </div>

        <div id="data_user" @if($user->id == 0 or $total_in_other_platforms > 0) class="d-none" @endif>

            <div class="row">
                <div class="col col-sm-12">
                    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                        {!! Form::text('name', null, ['id' => 'user_name','required']) !!}
                        {!! Form::label('user_name', '*Nome', ['id' => 'label_user_name']) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-6">
                    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                        {!! Form::password('password', ['id' => 'user_password']) !!}
                        {!! Form::label('user_password', 'Senha') !!}
                    </div>
                    <div class="password-policies">
                        <div class="policy-length">
                            5 caracteres.
                        </div>
                        <div class="policy-number">
                            Contém números.
                        </div>
                        <div class="policy-letter">
                            Contém letras.
                        </div>
                        <div class="policy-special">
                            Contém caracteres especiais.
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-6">
                    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                        {!! Form::password(null, ['id' => 'user_password-repeat']) !!}
                        {!! Form::label('user_password-repeat', 'Repita a senha') !!}
                    </div>
                </div>
                <div id="keep_password" @if($user->id == 0) class="d-none" @endif>
                    <small>Para manter a mesma senha, deixe estes dois campos vazios!</small>
                </div>
            </div>

        </div>

    </div>
    <div class="xgrow-card-footer p-3 border-top d-flex w-100 justify-content-between">
        {!! Form::button('Salvar', ['class' => 'xgrow-button', 'id' => 'save-user', 'type' => 'submit']) !!}
    </div>
</div>

{!! Form::close() !!}
