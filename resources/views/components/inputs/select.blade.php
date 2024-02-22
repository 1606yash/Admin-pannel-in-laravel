@props([
'for' => '',
'label' => '',
'value' => '',
'name' => '',
'formNote' => '',
'required' =>'false',
'multiple'=>'false',
'placeholder'=>'',
'class' =>'',

])
<div class="form-group"> 
    @if ($label != '')
        <label class="form-label" for="{{ $for }}">{{ $label }}@if($required !='' && $required=='true' )<span class="text-danger">*</span>@endif</label>
    @endif
    <div class="form-control-wrap">
        <select class="form-select {{ $class }}" value="{{ $value }}"  name="{{ $name }}" data-parent=".toggle-slide" @if($required !='' && $required=='true' ) required="{{$required}}" @endif  data-parsley-errors-container=".parsley-container-{{ $name }}" id="{{$for}}" @if ($multiple == 'true') multiple="" @endif data-placeholder="{{ $placeholder }}" data-search="on">
            {{ $slot }}
        </select>
    </div>
    @if ($formNote != '')
        <span class="form-note mt-0">{{ $formNote }}</span>
    @endif
    <div class="parsley-container-{{ $name }}"></div>
</div>
