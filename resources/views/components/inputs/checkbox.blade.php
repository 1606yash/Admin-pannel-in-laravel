@props([
'for',
'value' => '',
'size' => 'sm',
'label' => '',
'name' => '',
'checked' => ''
])
<div class="custom-control mr-2 custom-checkbox custom-control-{{$size}}">
    <input type="checkbox" class="custom-control-input" id="{{ $for }}" value="{{ $value }}" name="{{ $name }}" @if ($checked != '') checked="{{ $checked }}" @endif>
    <label class="custom-control-label" for="{{ $for }}">{{ $label }}
    </label>
</div>