@if(!$selfClose)
<{{ $tag }} {{ $attributes->merge($defaultAttributes) }}>
    {!! $slot !!}
</{{ $tag }}>
@else
    <{{ $tag }} {{ $attributes->merge($defaultAttributes) }} />
@endif
