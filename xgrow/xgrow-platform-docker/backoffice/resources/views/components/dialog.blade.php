<div id="{{ $id }}" class="{{ $class }}" {{ $attributes->merge($defaultAttributes) }} data-closable="{{ $closable ? 'true' : 'false' }}" data-visible="{{ $visible ? 'true' : 'false' }}">
    {!! $slot !!}
</div>
