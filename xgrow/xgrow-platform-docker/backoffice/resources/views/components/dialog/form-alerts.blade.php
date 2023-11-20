@php
if(!isset($title) || empty($title)) $title = 'Informações';
@endphp
<div id="{{ $id }}" name="{{ $name }}" class="form-alerts shadow-sm {{ $class }}" {{ $attributes->merge($defaultAttributes) }} data-target="{{ $target }}">
    <h3>{{ $title }}:</h3>
    <hr />
    <ul>
        {!! $slot !!}
    </ul>
</div>
