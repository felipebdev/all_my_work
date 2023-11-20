<button id="{{ $id }}" name="{{ $name }}" class="{{ $class }}" type="{{ $type }}" {{ $attributes->merge($defaultAttributes) }}>
    <span class="button-content">{!! $slot !!}</span>
    <i class="fas fa-circle-notch fa-spin spinner"></i>
</button>
