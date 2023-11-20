<!-- ----------------------------------------------------------------------------------------------------------------------------------------------------------------- -->
@include('partials.libraries')
@push('after-styles')

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

@endpush
@push('after-scripts')
    <script>
		window.APP_URL_LEARNING_AREA = `<?= env('APP_URL_LEARNING_AREA') ?>`;
		window.existsClient = <?= ($customers->count() !== 0 ? 'true' : 'false'); ?>;
    </script>
    <script type="module" src="{{ asset('/js/platforms.js') }}"></script>
@endpush

@php

    $type = (!isset($user->id) || $user->id === 0) ? 'create' : 'update';
    if(!isset($platformsIDs)) $platformsIDs = [];

   if($type === 'create')
   {
       $action = '/platforms';
       $method = 'post';
       $required = true;
   }
   else
   {
       $action = "/platforms/{$user->id}";
       $method = 'put';
       $required = false;
   }

   if(!isset($platforms)) $platforms = [];

    $platformsIDs = implode('|', $platformsIDs);

    if(!isset($formMessages)) $formMessages = [];

@endphp

<h1>{{ $type == 'create' ? 'Cadastrar nova plataforma' : 'Atualizar plataforma existente' }}</h1>
@if(!@empty($formMessages) || $type !== 'create')
    <x-form-alerts id="create-form-info" target="#create-form">

        @foreach($formMessages as $formMessageName => $formMessage)

            <x-form-alert type="{{ $formMessage['type'] }}" name="{{ $formMessageName }}" params="{{ $formMessage['params'] }}">{{ $formMessage['message'] }}</x-form-alert>

        @endforeach

        <x-form-alert type="info" name="password">Para manter a mesma senha, deixe o campo "senha" em branco</x-form-alert>

    </x-form-alerts>
@endif

<x-form id="create-form" action="{{ $action }}" method="{{ $method }}" class="clearfix">

    <fieldset class="user-data">

        <legend>Dados pessoais</legend>

        <div class="row align-items-center h-100">

            <div class="col-xs-12 col-sm-12 col-lg-6">

                <x-input-name id="input-name" value="{{ $client->first_name ?? '' }}" label="nome" validation="minchars:3" required />

            </div>

            <div class="col-xs-12 col-sm-12 col-lg-6">

                <x-select2 id="input-client" label="Cliente" name="clients[]" required multiple prependIcon="company" validation="atleast:1" serverValidation="atleast:1">

                    @foreach ($customers as $customer)
                        <x-select-option value="{{ $customer->id }}" selected="{{ isset($platform) && ($platform->customer_id === $customer->id) }}">
                            {{ $customer->first_name . ' ' . $customer->last_name}}
                        </x-select-option>
                    @endforeach

                </x-select2>

            </div>

        </div>

    </fieldset>

    <fieldset class="options-data">

        <legend>Opções</legend>

        <div class="row">

            <div class="col-xs-12 col-sm-12 col-lg-6">

                <x-input-url id="input-url" name="platform_url" type="slug" maxlength="200" prepend="https://" label="URL" value="{{ str_replace(['http://', 'https://'], ['', ''], ($platform->url ?? '')) }}" />

            </div>

            <div class="col-xs-12 col-sm-12 col-lg-6">

                <x-input-url id="input-slug" name="slug" type="slug" maxlength="20" label="Endereço amigável" value="{{ str_replace(['http://', 'https://'], ['', ''], ($platform->slug ?? '')) }}" />

            </div>

        </div>

        <div class="row">

            <div class="col-xs-12 col-sm-12 col-lg-12">

                <x-input-check-box id="input-default-discount" type="checkbox" value="" label="Restringir acesso por ip" toggle />

            </div>

        </div>

        <label>Modelos:</label>

        <div class="row">

            @foreach ($templates as $template)

            <div class="col-xs-12 col-sm-12 col-lg-12">

                <x-input-radio id="template-{{ $template->id }}" label="{{ $template->name }}" value="{{ $template->id }}" name="template[]" checked="{{ isset($platform) && ($platform->template_id === $template->id )}}" />

                @if(isset($template->thumb->filename))
                    <img class="card-img-top"
                         src="{{ asset(config('constants.imgTemplatesDir') . $template->thumb->filename) }}"
                         alt="Card image cap">
                @endif

            </div>

            @endforeach

            @if(!@isset($templates) || !@count($templates))
                <p>Não há modelos cadastrados</p>
            @endif

        </div>

    </fieldset>

    <x-submit id="submit-test" class="big-button btn-block btn {{ $type === 'create' ? 'btn-primary' : 'btn-info' }}">{!! $type === 'create' ? '<i class="fas fa-plus-circle"></i> criar' : '<i class="fas fa-edit"></i> atualizar' !!} plataforma</x-submit>

</x-form>
<!-- ----------------------------------------------------------------------------------------------------------------------------------------------------------------- -->
