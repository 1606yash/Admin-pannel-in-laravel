@props([
    'for' => '',
    'label' => '',
    'value' => '',
    'placeholder' => '',
    'id' => '',
    'name' => '',
    'icon'=>'',
    'required' =>'false'
])
<div class="form-group">
    @if ($label != '')
        <label class="form-label" for="{{ $for }}">{{ $label }}@if($required != '')<span class="text-danger">*</span>@endif</label>
    @endif
    <div class="form-control-wrap">
        @if ($icon != '')
        <div class="form-icon form-icon-left "><em class="icon ni ni-{{$icon}}"></em></div>
        @endif
        <input type="text" class="form-control date-picker" id="{{$for}}" value="{{$value}}" placeholder="{{ $placeholder }}" name="{{ $name }}">
    </div>
</div>