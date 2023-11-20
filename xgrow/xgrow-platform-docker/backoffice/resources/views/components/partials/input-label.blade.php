@php
if(!isset($separator)) $separator = ':';
@endphp
@if(@isset($label) && !@empty($label))
    <label for="{{ $name }}">{{ $label }}{{ $separator }}{!!  $required ? ' <span class="required-signal">*</span>' : '' !!}</label>
@endif
