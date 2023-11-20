<form id="{{ $id }}" name="{{ $name }}" class="{{ $class }}" action="{{ $action }}" method="{{ $method }}" {{ $attributes->merge($defaultAttributes) }} novalidate>
    {!! $slot !!}
    @csrf
</form>
