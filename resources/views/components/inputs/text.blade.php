@props([
    'for'=> '',
    'label' => '',
    'value'=> '',
    'placeholder'=> '',
    'id'=> '',
    'name' => '',
    'icon'=>'',
    'formNote' => '',
    'required' =>'false',
    'minlength'=>'',
    'maxlength'=>'',
    'autocomplete'=>'',
    'class' =>'',
    'readonly' => ''
])
<div class="form-group">
    @if (isset($label) && $label != '')
        <label class="form-label" for="{{ $for }}">{{ $label }}@if($required !='' && $required=='true' )<span class="text-danger">*</span>@endif</label>
    @endif
    <div class="form-control-wrap">
        @if ($icon != '')
            <div class="form-icon form-icon-left "><em class="icon ni ni-{{$icon}}"></em></div>
        @endif

        @if (!isset($class))
            @php
                $class = '';
            @endphp
        @endif

        @if (!isset($required))
            @php
                $required = 'false';
            @endphp
        @endif

        <input type="text" @if($required !='' && $required=='true' ) required="{{$required}}" @endif data-parsley-errors-container=".parsley-container-{{ $name }}" id="{{$for}}" value="{{$value}}" placeholder="{{ $placeholder }}" name="{{ $name }}"  {{ $attributes }} {{ $attributes->merge(['class' => 'form-control ' . $class]) }} autocomplete="off"/>
    </div>

    @if (isset($formNote) && $formNote != '')
        <span class="form-note mt-0">{{ $formNote }}</span>
    @endif    

    @if(isset($required) && $required != 'false')
        <div class="parsley-container-{{ $name }}"></div>
    @endif
</div>