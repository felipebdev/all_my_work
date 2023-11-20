@php

    $maxlength = null;
    if(!isset($label)) $label = '';
    if(!isset($icon)) $icon = '';
    if(!isset($prepend)) $prepend = '';
    if(!isset($classes)) $classes = '';
    if(!isset($attributes)) $attributes = '';

    if(!isset($data)) $data = '';

    if(is_array($attributes))
    {
		if(array_key_exists('maxlength', $attributes)) $maxlength = (int) $attributes['maxlength'];

		$buffer = [];
		foreach ($attributes as $key => $val) $buffer[] = $key . '=' . $val;

        $attributes = implode(' ', $buffer);
    }

	if(is_array($data))
    {
		$buffer = [];
		foreach ($data as $key => $val) $buffer[] = "data-" . $key . '=' . $val;

        $data = implode(' ', $buffer);
    }

    if(!isset($value)) $value = '';
	if(isset($attributes['value'])) $value = $attributes['value'];
	if(!isset($type)) $type = 'text';
	if($type === 'datetime') $classes .= 'datepicker';

@endphp

<div class="form-group input-parent">
    @if($label)
        <label for="{{$name}}">{{$label}}</label>
    @endif
    <div class="input-group w-100">
        @if($icon || $prepend)
            <div class="input-group-prepend">
                <span class="input-group-text">
                    @if($icon)
                    <i class="{{$icon}}"></i>
                    @endif
                    {{$prepend}}
                </span>
            </div>
        @endif
        @if($maxlength > 0)
            <span class="badge badge-primary maxlength-badge display-none">{{$maxlength}}</span>
        @endif
        <input type="{{$type}}" class="form-control shadow-sm {{$classes}}" id="{{$name}}" name="{{$name}}" value="{{$value}}" data-form-input {{$attributes}} {{$data}} />
    </div>

</div>
