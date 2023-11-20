@if(@isset($append) && !@empty($append))
    <div class="input-group-prepend">
        <span class="input-group-text">
            {!! $append !!}
        </span>
    </div>
@endif

@if(@isset($appendIcon) && !@empty($appendIcon))
    <div class="input-group-prepend">
        <span class="input-group-text">
            {!! $appendIcon !!}
        </span>
    </div>
@endif
