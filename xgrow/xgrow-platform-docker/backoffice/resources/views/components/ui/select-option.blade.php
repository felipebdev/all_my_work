@php
if(!isset($id)) $id = '';
if(!isset($name)) $name = '';
@endphp
<option id="{{ $id }}" name="{{ $name }}" class="{{ $class }}" value="{{ $value }}" {{ $attributes->merge($defaultAttributes) }} {{ $selected ? 'selected="selected"' : '' }}>
    {!! $slot !!}
</option>
