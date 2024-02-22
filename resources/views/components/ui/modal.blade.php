@props([
'title'=> '',
'footerActions' => '',
'modalId'=> '',
'click'=> '',
])

<div class="modal fade zoom" tabindex="-1" id="{{ $modalId }}">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ $title }}</h5>
                <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
            </div>
            {{ $slot }}
            <div class="modal-footer bg-light">
                <div class="row">
                    <div class="col-lg-12 p-0 text-right">
                        <button class="btn btn-outline-light" data-dismiss="modal" aria-label="Close">Close</button>
                        @if(is_array($footerActions))
                            @foreach($footerActions as $key => $value)
                                <button class="btn btn-{{ $value['type'] }} submitBtn" onclick="{{ $value['click'] }}" type="submit">{{ $value['label'] }}</button>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>