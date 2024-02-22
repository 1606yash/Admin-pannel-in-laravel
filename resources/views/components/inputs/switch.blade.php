@props([
'for' => '',
'value' => '1',
'size' => 'sm',
'label' => '',
'name' => '',
'checked' => ''
])
<div class="custom-control custom-control-{{$size}} custom-switch">
    <input type="checkbox" id="{{ $for }}" name="{{ $name }}" class="custom-control-input test" value="{{ $value }}" @if($checked != '') checked @endif >
    <label class="custom-control-label" for="{{ $for }}">{{ $label }} </label>
</div>