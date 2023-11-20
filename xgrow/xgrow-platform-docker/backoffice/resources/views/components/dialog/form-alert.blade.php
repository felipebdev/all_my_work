@php
$icon = '';
switch($type)
{
    case 'success':

        $icon = 'fas fa-check';

        break;

    case 'warning':

        $icon = 'fas fa-times';

        break;

    case 'info':

        $icon = 'fas fa-info';

        break;
}


if(!isset($params)) $params = [];

$params = array_map(static fn(string $param) => trim($param), explode('|', $params));

if(!isset($params[0])) $params[0] = 'Documento';

@endphp
<li data-name="{{ $name }}" class="{{ $type }} {{ $class }}" data-type="{{ $type }}" {{ $attributes->merge($defaultAttributes) }} data-alert>
    <i class="{{ $icon }}"></i>
    {{ vsprintf($slot, $params) }}
</li>
