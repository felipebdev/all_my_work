@if(!@isset($classes))
    @php
        $classes = '';
    @endphp
@endif

@if(!@isset($valueIdentifier))
    @php
        $valueIdentifier = 'id';
    @endphp
@endif

@if(!@isset($labelIdentifier))
    @php
        $labelIdentifier = 'value';
    @endphp
@endif

@if(!@isset($multiple))
    @php
        $multiple = false;
    @endphp
@endif

@if(!@isset($attributes))
    @php
        $attributes = '';
    @endphp
@endif
<div class="form-group">

    @if($label)
        <label for="{{$name}}">{{$label}}</label>
    @endif

    <div class="input-group">

        <select class="form-control select-2 custom-select {{$classes}}" id="{{$name}}" name="{{$name}}[]" {{$multiple ? 'multiple' : ''}} data-form-input {{$attributes}}>

            @if(is_array($data))
                @foreach ($data as $row)
                    <option value="{{ $row[0] }}">{{ $row[1] }}</option>
                @endforeach
            @else
                @foreach ($data as $element)
                    <option value="{{ $element->{$valueIdentifier} }}">{{ $element->{$labelIdentifier} }}</option>
                @endforeach
            @endif

        </select>

        <div class="input-group-append group-absolute">
            <i class="fas fa-arrow-down"></i>
        </div>

    </div>

</div>
