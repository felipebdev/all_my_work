@php
    $dataType = $defaultAttributes['data-type'];
    $conditionalClasses = '';
	if($prepend || $prependIcon) $conditionalClasses = 'input-with-prepend';
@endphp
<div class="form-group input-parent {{ $conditionalClasses }}">

    @include('components.partials.input-label', ['label' => $label, 'name' => $name, 'required' => $required])
    @include('components.partials.input-tip', ['tip' => $tip, 'tipBehavior' => $tipBehavior])

    <div class="input-group w-100 shadow-sm">

        @include('components.partials.input-prepend', ['prepend' => $prepend ?? '', 'prependIcon' => $prependIcon ?? ''])
        @include('components.partials.input-max-length', ['maxlength' => $maxlength])

        <select class="form-control select-2 custom-select {{ $class }}" id="{{ $id }}" name="{{ $name }}" value="{{ $value }}" {{ $multiple ? 'multiple' : '' }} data-form-input {{ $attributes->merge($defaultAttributes) }} data-tip="{{ $tip }}" data-tip-behavior="{{ $tipBehavior }}" {{ $maxlength > 0 ? 'maxlength=' . $maxlength : '' }} {{ $optional ? 'data-optional' : '' }} {{ $required ? 'required' : '' }} {{ $readonly ? 'readonly' : '' }}>
            @if($dataType !== 'select2')
            <x-ui.select-option></x-ui.select-option>
            @endif
            {!! $slot !!}

        </select>

        @include('components.partials.input-spinner', [])
        @include('components.partials.input-append', ['append' => $append ?? '', 'appendIcon' => $appendIcon ?? ''])

    </div>

    @include('components.partials.input-validation', ['validation' => $validation, 'serverValidation' => $serverValidation])
    @include('components.partials.input-info', ['info' => $info ?? [], 'serverInfo' => $serverInfo ?? []])

</div>
