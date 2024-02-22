@props([
    'for'=> '',
    'label' => '',
    'suggestion' => '',
    'required' => 'false'
])
<div class="form-group">
    <label class="form-label" for="{{ $for }}">{{ $label }}@if($required != 'false')<span class="text-danger">*</span>@endif</label>
    <span class="form-note">{{ $suggestion }}</span>
</div>