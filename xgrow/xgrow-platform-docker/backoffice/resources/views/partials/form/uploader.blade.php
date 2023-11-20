@if(!@isset($classes))
    @php
        $classes = 'uploader';
    @endphp
@endif
@if(!@isset($attributes))
    @php
        $attributes = '';
    @endphp
@endif

<div class="uploader-container">

    <div id="{{$id}}" class="uploader {{$classes}}" {{$attributes}}></div>

</div>
