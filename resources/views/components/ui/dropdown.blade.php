@props([
    'actionList' => '',
])
<div class="dropdown">
    {{ $slot }}

    <div class="dropdown-menu dropdown-menu-right">
        <ul class="link-list-opt no-bdr">
            @if(is_array($actionList))
                @foreach($actionList as $key => $value)
                    <li>
                        <a 
                            @if(isset($value['link'])) href="{{ $value['link'] }}" @endif
                            @if(isset($value['click'])) onclick="{{ $value['click'] }}" @endif
                        >
                            @if($value['icon'])
                                <em class="icon ni ni-{{ $value['icon'] }}"></em>
                            @endif
                            <span>{{ $value['label'] }}</span>
                        </a>
                    </li>    
                @endforeach
            @endif
        </ul>
    </div>
</div>