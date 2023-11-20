<fieldset class="{{ $class }}" data-type="input-group" id="{{ $id }}" data-name="{{ $name }}" {{ $attributes->merge($defaultAttributes) }} data-form-group data-horizontal="{{ $horizontal }}">
    @if($label)
        <legend>{{ $label }}</legend>
    @endif
    {!! $slot !!}
</fieldset>
