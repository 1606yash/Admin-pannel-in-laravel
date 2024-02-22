<button {{ $attributes }} {{ $attributes->merge(['class' => 'btn btn-'.$size.' btn-'.$buttonType.' '. $className ]) }}>
    @if($icon != null)
    <em class="icon ni ni-{{ $icon }}"></em>
    @endif
    <span>{{ $slot }}</span>
</button>