@php
    if($type === 'create')
	{
		$action = '/admin/store';
		$method = 'post';
		$required = true;
	}
	else
    {
        $action = "/admin/update/{$data['user']['id']}";
		$method = 'put';
		$required = false;
    }

    if(!isset($formMessages)) $formMessages = [];

@endphp

<h1>{{ $type == 'create' ? 'Cadastrar novo administrador' : 'Atualizar administrador existente' }}</h1>
@if(!@empty($formMessages) || $type !== 'create')
<x-dialog.form-alerts id="create-form-info" target="#create-form">

    @foreach($formMessages as $formMessageName => $formMessage)

        <x-dialog.form-alert type="{{ $formMessage['type'] }}" name="{{ $formMessageName }}" params="{{ $formMessage['params'] }}">{{ $formMessage['message'] }}</x-dialog.form-alert>

    @endforeach

    <x-dialog.form-alert type="info" name="password">Para manter a mesma senha, deixe o campo "senha" em branco</x-dialog.form-alert>

</x-dialog.form-alerts>
@endif
<x-ui.form id="create-form" action="{{ $action }}" method="{{ $method }}" class="clearfix">

    <fieldset class="user-data">

        <legend>Dados pessoais</legend>

            <div class="row">

                <div class="col-xs-12 col-sm-12 col-lg-4">

                    <x-ui.input-name id="input-name" type="text" value="{{$data['user']['name'] ?? ''}}" label="nome" required="{{ $required }}" validation="minchars:3" />

                </div>

                <div class="col-xs-12 col-sm-12 col-lg-4">

                    <x-ui.input-email id="input-email" value="{{ $data['user']['email'] ?? '' }}" label="email" readonly="{{ $method === 'put' ? 'readonly' : '' }}" required validation="email" serverValidation="email|unique:email" />

                </div>

                <div class="col-xs-12 col-sm-12 col-lg-4">

                    <x-ui.input-password id="input-password" value="" label="senha" validation="minchars:8|strongpassword" tip="Este campo deverá conter ao menos um número, uma letra maiúscula e minúscula e um caractere especial" required="{{ $method === 'put' ? '' : 'required' }}" />

                </div>

            </div>

    </fieldset>

    <x-ui.submit id="submit-test" class="big-button btn-block btn {{ $type === 'create' ? 'btn-primary' : 'btn-info' }}">{!! $type === 'create' ? '<i class="fas fa-plus-circle"></i> criar' : '<i class="fas fa-edit"></i> atualizar' !!} administrador</x-ui.submit>

</x-ui.form>
