@props([
    'for' => '',
    'label' => '',
    'value' => '',
    'placeholder' => '',
    'id' => '',
    'name' => '',
    'required' =>'false',
    'formNote' => 'Note: Maximum character limit is 500 for description.',
    'maxlength'=>'2000',
    'minlength'=>'0'
])
<div class="form-group">
    @if ($label != '')
        <label class="form-label" for="{{ $for }}">{{ $label }}@if($required != '' && $required == 'true' )<span class="text-danger">*</span>@endif</label>
    @endif
    <div class="form-control-wrap">
        <textarea class="form-control" @if($required != '' && $required == 'true' ) required="{{$required}}" @endif id="{{ $for }}" minlength="{{ $minlength }}" maxlength="{{ $maxlength }}"  placeholder="{{ $placeholder }}" name="{{ $name }}" data-parsley-errors-container=".parsley-container-{{ $name }}">{{ $value }}</textarea>
    </div>
    @if (isset($formNote) && $formNote != '')
            <span class="form-note mt-0">{{ $formNote }}</span>
    @endif
    <div class="parsley-container-{{ $name }}"></div>
</div>