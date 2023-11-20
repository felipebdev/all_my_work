@if(!@empty($prepend))
    <div class="input-group-prepend">
                <span class="input-group-text">
                    {!! $prepend !!}
                </span>
    </div>
@endif

@if(!@empty($prependIcon))
    <div class="input-group-prepend">
            <span class="input-group-text">
                {!! $prependIcon !!}
            </span>
    </div>
@endif
