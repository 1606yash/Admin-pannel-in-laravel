@extends('layouts.app')

@section('content')
<div class="nk-block-head nk-block-head-sm">
    <div class="nk-block-between">
        <div class="nk-block-head-content">
            <h3 class="nk-block-title page-title">Contact Us</h3>
            @php 
                if($contacts->total() > 1) 
                    $count = 'contacts';
                else
                    $count = 'contact';                       
            @endphp
            <p>You have total {{ $contacts->total() }} {{ $count }}.</p>
        </div><!-- .nk-block-head-content -->
        <div class="nk-block-head-content">
            <div class="toggle-wrap nk-block-tools-toggle">
                <a href="#" class="btn btn-icon btn-trigger toggle-expand mr-n1" data-target="more-options"><em class="icon ni ni-more-v"></em></a>
                <div class="toggle-expand-content" data-content="more-options">
                    <ul class="nk-block-tools g-3">
                        <li class="nk-block-tools-opt">
                            <a href="#" class="btn btn-trigger btn-icon dropdown-toggle" data-toggle="modal" title="filter" data-target="#modalFilterProduct">
                                <div class="dot dot-primary"></div>
                                <em class="icon ni ni-filter-alt"></em>
                            </a>
                        </li>
                        <li class="nk-block-tools-opt">
                            <div class="dropdown">
                                <a href="#" class="btn btn-trigger btn-icon dropdown-toggle" data-toggle="dropdown">
                                    <em class="icon ni ni-setting"></em>
                                </a>
                                <div class="dropdown-menu dropdown-menu-xs dropdown-menu-right">
                                    <ul class="link-check">
                                        <li><span>Actions</span></li>
                                        {{-- <li><a href="#"><em class="icon ni ni-download m-r10"></em> Export</a></li> --}}
                                        <li><a href="#"><em class="icon ni ni-upload m-r10"></em> Import</a></li>
                                        </ul>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div><!-- .nk-block-head-content -->
    </div><!-- .nk-block-between -->
</div><!-- .nk-block-between -->
</div><!-- .nk-block-head -->
<!--  Filter Tag List -->
<div id="filter_tag_list" class="filter-tag-list"></div>
<div class="nk-block table-compact">
    <div class="nk-tb-list is-separate mb-3">
        @if($contacts->count() > 0)
        <div class="nk-tb-item nk-tb-head">
            <div class="nk-tb-col fw-bold"><span class="sub-text">Name</span></div>
            <div class="nk-tb-col fw-bold tb-col-md"><span class="sub-text">Subject</span></div>
            <div class="nk-tb-col fw-bold"><span class="sub-text">Email</span></div>
            <div class="nk-tb-col fw-bold"><span class="sub-text">Mobile No.</span></div>
            <div class="nk-tb-col fw-bold tb-col-md"><span class="sub-text">Date</span></div>
            <div class="nk-tb-col fw-bold tb-col-md"><span class="sub-text">Action</span></div>
        </div><!-- .nk-tb-item -->
        @endif
        @forelse($contacts as $key => $contact)
        <div class="nk-tb-item">

            <div class="nk-tb-col tb-col-mb">
                <a href="#" class="text-primary" onclick="getContactDetails({{ $contact->id }})" >
                    <div class="user-card">
                        <div class="user-info">
                            <span class="text-primary">{{ ucfirst($contact->name) }}</span>
                        </div>
                    </div>
                </a>
            </div>
            <div class="nk-tb-col tb-col-lg">
                <span>{{ $contact->subject }}</span>
            </div>
            <div class="nk-tb-col tb-col-lg">
                <span>{{ $contact->email }}</span>
            </div>
            <div class="nk-tb-col tb-col-lg">
                <span>{{ $contact->phone }}</span>
            </div>
            <div class="nk-tb-col tb-col-lg w-1 nowrap">
                <span>{{  date(\Config::get('constants.DATE.DATE_FORMAT') , strtotime($contact->created_at)) }}</span>
            </div>
            <div class="nk-tb-col nk-tb-col-tools w-1 nowrap">
                <ul class='nk-tb-actions gx-1'>
                    <li>
                        <div class='drodown mr-n1'>
                            <a href='#' class='dropdown-toggle btn btn-icon btn-trigger' data-toggle='dropdown'><em class='icon ni ni-more-h'></em></a>
                            <div class='dropdown-menu dropdown-menu-right'>
                                <ul class='link-list-opt no-bdr'>
                                    <li>
                                        <a href="mailto:{{ $contact->email }}">
                                            <em class="icon ni ni-mail"></em> <span>Reply</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="tel:{{ $contact->phone }}">
                                            <em class="icon ni ni-call"></em> <span>Call</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div><!-- .nk-tb-item -->
        @empty
            <div class="nk-content ">
                <div class="nk-block nk-block-middle wide-md mx-auto">
                    <div class="nk-block-content nk-error-ld text-center">
                        <img class="nk-error-gfx pb-4" src="{{url('images/slides/nodata.png')}}" alt="">
                        <div class="wide-xs mx-auto">
                            <h6 class="nk-error-title">No Records Found</h6>
                            
                        </div>
                    </div>
                </div><!-- .nk-block -->
            </div>
            @endforelse
    </div><!-- .nk-tb-list -->
    @if ($contacts->lastPage() > 1)
            <div class="card">
                <div class="card-inner pt-2 pb-2">
                    <div class="nk-block-between-md g-3">
                        <div class="g">
                            <ul class="pagination justify-content-center justify-content-md-start">
                                <li class="page-item {{ ($contacts->currentPage() == 1) ? ' disabled' : '' }}">
                                    <a class="page-link" href="{{ $contacts->url($contacts->currentPage()-1) }}">Previous</a>
                                </li>
                                {{-- @for ($i = 1; $i <= $contacts->lastPage(); $i++)
                                    <li class="page-item {{ ($contacts->currentPage() == $i) ? ' active' : '' }}">
                                        <a class="page-link" href="{{ $contacts->url($i) }}">{{ $i }}</a>
                                    </li>
                                @endfor --}}
                                <li class="page-item {{ ($contacts->currentPage() == $contacts->lastPage()) ? ' disabled' : '' }}">
                                    <a class="page-link" href="{{ $contacts->url($contacts->currentPage()+1) }}" >Next</a>
                                </li>
                            </ul>
                        </div>
                        <div class="g">
                            <div class="pagination-goto d-flex justify-content-center justify-content-md-start gx-3">
                                <div>Page</div>
                                <div>
                                    <select class="form-select form-select-sm paginationGoto" data-search="on" data-dropdown="xs center" onchange="changePage(this.options[this.selectedIndex].value)">
                                        @for ($i = 1; $i <= $contacts->lastPage() ; $i++)
                                            <option @if($contacts->currentPage() == $i) selected @endif value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div>OF {{ $contacts->lastPage() }}</div>
                            </div>
                        </div><!-- .pagination-goto -->
                    </div><!-- .nk-block-between -->
                </div><!-- .card-inner -->
            </div><!-- .card -->
        @endif

</div><!-- .nk-block -->
@php
$filterAction = array(
array(
'label' => 'Clear Filter',
'type' => 'danger resetFilter',
'click' => '',
),
array(
'label' => 'Submit',
'type' => 'primary',
'click' => '',
)
);
@endphp
<x-ui.modal modalId="modalFilterProduct" title="Filter" :footerActions="$filterAction">
    <form role="form" class="mb-0" method="get" action="#" id="filterForm">
        @csrf
        <div class="modal-body modal-body-lg">
            <div class="gy-3">
                <div class="row g-3 align-center">
                    <div class="col-lg-5">
                        <x-inputs.verticalFormLabel label="Name" for="Name" suggestion="Specify the name of the contacts." required="false" />
                    </div>
                    <div class="col-lg-7">
                        <x-inputs.text value="" for="name" icon="user" placeholder="Name" name="name" />
                    </div>
                </div>
                <div class="row g-3 align-center">
                    <div class="col-lg-5">
                        <x-inputs.verticalFormLabel label="Email" for="Email" suggestion="Specify the email of the contacts." required="false" />
                    </div>
                    <div class="col-lg-7">
                        <x-inputs.text value="" for="name" icon="mail" placeholder="Email" name="email" />
                    </div>
                </div>
                <div class="row g-3 align-center">
                    <div class="col-lg-5">
                        <x-inputs.verticalFormLabel label="Phone Number" for="Phone" suggestion="Specify the phone number of the contacts." required="false" />
                    </div>
                    <div class="col-lg-7">
                        <x-inputs.text value="" for="name" icon="call" placeholder="Phone" name="mobile" />
                    </div>
                </div>
                <div class="row g-3 align-center">
                    <div class="col-lg-5">
                        <x-inputs.verticalFormLabel label="Subject" for="Subject" suggestion="Specify the email of the contacts." required="false" />
                    </div>
                    <div class="col-lg-7">
                        <x-inputs.text value="" for="Subject" icon="list" placeholder="Subject" name="subject" />
                    </div>
                </div>
                <div class="row g-3 align-center">
                    <div class="col-lg-5">
                        <x-inputs.verticalFormLabel label="Date" for="date" suggestion="Select the date of the contacts." required="false" />
                    </div>
                    <div class="col-lg-7">
                        <x-inputs.datePicker for="date" size="sm" name="date" icon="calendar" />
                    </div>
                </div>

            </div>
        </div>
        <input type="hidden" id="userId" name="user_id" value="0">
    </form>
</x-ui.modal>
<input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
<x-ui.modal modalId="modalViewContact" title="Contact View">
<form action="{{ url('administration/contactus') }}" role="form" class="mb-0" method="get">
    @csrf

    <div class="modal-body modal-body-lg">
        <div class="gy-3">
            <div class="row g-3 align-center">
                <div class="col-lg-4">
                    <label class="d-block form-label">Name</label>
                    <label id="contact_name"></label>
                </div>
                <div class="col-lg-4">
                    <label class="d-block form-label">Company</label>
                    <label id="contact_company"></label>
                </div>
                <div class="col-lg-4">
                    <label class="d-block form-label">Phone Number</label>
                    <label id="contact_phone"></label>
                </div>
                <div class="col-lg-4">
                    <label class="d-block form-label">Date</label>
                    <label id="contact_date"></label>
                </div>
                <div class="col-lg-4">
                    <label class="d-block form-label">Subject</label>
                    <label id="contact_subject"></label>
                </div>
                <div class="col-lg-12">
                    <label class="d-block form-label">Description</label>
                    <label id="contact_messge"></label>
                </div>
            </div>
        </div>
    </div>
</x-ui.modal>

<script type="text/javascript">
    function changePage (page) {
        var root_url = "<?php echo Request::root(); ?>";
        var goto = root_url + '/administration/contactus?page='+page;
        window.location = goto;
    }

    var root_url = "<?php echo Request::root(); ?>";
    function getContactDetails (id) {
        $.ajax({
            url: root_url + '/administration/contactus/details',
            data: {'id':id,"_token": $('#token').val()},
            method: "POST",
            cache: false,
            success: function (data) {
                if(data.success){
                    console.log(data);
                    $('#contact_name').text(data.contact.name);
                    $('#contact_company').text(data.contact.company);
                    $('#contact_date').text(data.contact.contact_date);
                    $('#contact_phone').text(data.contact.phone);
                    $('#contact_subject').text(data.contact.subject);
                    $('#contact_messge').text(data.contact.message);

                    $('#modalViewContact').modal('show');

                    /*var items = [
                        '#contact_name',
                        '#contact_company',
                        '#contact_date',
                        '#contact_phone',
                        '#contact_subject',
                        '#contact_messge'
                    ];
                    NioApp.filterTag(items, order_table['.products-init'], '#filter_tag_list');*/

                }else{
                    Swal.fire('Details not found!')
                }
            }
        });
    }

    $('.submitBtn').click(function(){
        $('#filterForm').submit();
    });


    //NioApp.resetModalForm('#modalFilterProduct', productsTable['.products-init'], '.resetFilter');
</script>
@endsection