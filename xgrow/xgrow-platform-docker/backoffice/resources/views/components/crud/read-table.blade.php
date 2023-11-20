<div id="{{ $id }}" name="{{ $name }}" class="{{ $class }}" {{ $attributes->merge($defaultAttributes) }}>
    {!! $slot !!}
</div>
