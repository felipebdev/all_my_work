@php
    if(!isset($id)) $id = '';
    if(!isset($name)) $name = '';
    if(!isset($class)) $class = '';
    if(!isset($label)) $label = '';
    if(!isset($tip)) $tip = '';
    if(!isset($tipBehavior)) $tipBehavior = '';
    if(!isset($prepend)) $prepend = '';
    if(!isset($prependIcon)) $prependIcon = '';
    if(!isset($append)) $append = '';
    if(!isset($appendIcon)) $appendIcon = '';
    if(!isset($type)) $type = 'text';
    if(!isset($maxlength)) $maxlength = 0;
    if(!isset($required)) $required = false;
    if(!isset($optional)) $optional = '';
    if(!isset($group)) $group = '';
    if(!isset($readonly)) $readonly = '';
	if(!isset($validation)) $validation = [];
    if(!isset($serverValidation)) $serverValidation = [];
    if(!isset($defaultAttributes)) $defaultAttributes = [];
@endphp
<div class="form-group input-parent">

    @include('components.partials.input-label', ['label' => $label, 'name' => $name, 'required' => $required])
    @include('components.partials.input-tip', ['tip' => $tip, 'tipBehavior' => $tipBehavior])

    <div class="input-group w-100">

        @include('components.partials.input-prepend', ['prepend' => $prepend ?? '', 'prependIcon' => $prependIcon ?? ''])
        @include('components.partials.input-max-length', ['maxlength' => $maxlength])

        <input type="{{ $type }}" class="form-control shadow-sm {{ $class }}" id="{{ $id }}" name="{{ $name }}" value="{{ $value }}" data-form-input {{ $attributes->merge($defaultAttributes) }} data-tip="{{ $tip }}" data-tip-behavior="{{ $tipBehavior }}" {{ $required ? 'required' : '' }} {{ $maxlength > 0 ? 'maxlength=' . $maxlength : '' }} data-group="{{ $group }}" {{ $optional ? 'data-optional' : '' }} {{ $required ? 'required' : '' }} {{ $readonly ? 'readonly' : '' }} />

        @include('components.partials.input-spinner', [])
        @include('components.partials.input-append', ['append' => $append ?? '', 'appendIcon' => $appendIcon ?? ''])

    </div>

    @include('components.partials.input-validation', ['validation' => $validation, 'serverValidation' => $serverValidation])
    @include('components.partials.input-info', ['info' => $info ?? [], 'serverInfo' => $serverInfo ?? []])

</div>
