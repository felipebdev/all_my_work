<div id="{{ $id }}" data-name="{{ $name }}" data-visible="{{ $visible }}" {{ $attributes->merge($defaultAttributes) }} class="alert alert-{{ $type }} {{ $class }}" role="alert" >
    {!! $slot !!}
</div>
