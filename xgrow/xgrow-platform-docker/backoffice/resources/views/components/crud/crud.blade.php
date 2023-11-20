<div id="{{ $id }}" name="{{ $name }}" class="{{ $class }}" {{ $attributes->merge($defaultAttributes) }}>
    <div class="">{!! $readTable !!}</div>
    <div class="">{!! $createForm !!}</div>
    <div class="">{!! $updateForm !!}</div>
    <div class="">{!! $deleteForm !!}</div>
</div>
