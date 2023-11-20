<div class="form-group input-parent checkbox-parent">

    <div class="form-check">
        @if(!@isset($toggle) || !$toggle)
        <input type="checkbox" class="shadow-sm form-check-input {{ $class }}" id="{{ $id }}" name="{{ $name }}" value="{{ $value }}" data-form-input {{ $attributes->merge($defaultAttributes) }} data-tip="{{ $tip }}" data-tip-behavior="{{ $tipBehavior }}" {{ $required ? 'required' : '' }} {{ $checked ? 'checked' : '' }} />

            @include('components.partials.input-label', ['label' => $label, 'name' => $name, 'required' => $required, 'separator' => ''])

        @else
            <label class="toggle">
                <input type="checkbox" class="shadow-sm toggle-checkbox {{ $class }}" id="{{ $id }}" name="{{ $name }}" value="{{ $value }}" data-form-input {{ $attributes->merge($defaultAttributes) }} data-tip="{{ $tip }}" data-tip-behavior="{{ $tipBehavior }}" {{ $required ? 'required' : '' }} {{ $checked ? 'checked' : '' }} />
                <div class="toggle-switch"></div>
                <span class="toggle-label">{{ $label }}{!! $required ? ' <span class="required-signal">*</span>' : '' !!}</span>
            </label>
        @endif
    </div>

    @include('components.partials.input-validation', ['validation' => $validation, 'serverValidation' => $serverValidation])

</div>
