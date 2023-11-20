<button id="{{ $id }}" name="{{ $name }}" type="submit" class="{{ $class }}" {{ $attributes->merge($defaultAttributes) }} data-form-submit>
    <span class="button-content">{!! $slot !!}</span>
    <i class="fas fa-circle-notch fa-spin spinner"></i>
</button>
