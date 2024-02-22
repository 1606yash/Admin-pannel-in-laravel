@props([
'for',
'value' => '',
'size',
'name',
'label' => '',
'checked'=>''
])
<div class="custom-control custom-control-{{$size}} custom-radio">
    <input type="radio" id="{{ $for }}" name="{{ $name }}" class="custom-control-input radio-btn" value="{{ $value }}" @if ($checked != '') checked="{{ $checked }}" @endif >
    <label class="custom-control-label" for="{{ $for }}">{{ $label }}</label>
</div>