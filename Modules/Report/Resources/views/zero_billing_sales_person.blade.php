@extends('layouts.app')
@section('content')
<div class="nk-block-head nk-block-head-sm">
    <div class="nk-block-between">
        <div class="nk-block-head-content">
            <h3 class="nk-block-title page-title">Zero Billing Sales Person</h3>
        </div><!-- .nk-block-head-content -->
        <div class="nk-block-head-content">
            <div class="toggle-wrap nk-block-tools-toggle">
                <a href="#" class="btn btn-icon btn-trigger toggle-expand mr-n1" data-target="more-options"><em class="icon ni ni-more-v"></em></a>
                <div class="toggle-expand-content" data-content="more-options">
                    <ul class="nk-block-tools g-3">
                        <li>
                            <a href="#" class="btn btn-trigger btn-icon dropdown-toggle" data-toggle="modal" title="filter" data-target="#modalFilterorder">
                                <div class="dot dot-primary"></div>
                                <em class="icon ni ni-filter-alt"></em>
                            </a>
                        </li>
                        <li>
                            <div class="dropdown">
                                <a href="#" class="btn btn-trigger btn-icon dropdown-toggle" data-toggle="dropdown">
                                    <em class="icon ni ni-setting"></em>
                                </a>
                                <div class="dropdown-menu dropdown-menu-xs dropdown-menu-right">
                                    <ul class="link-check">
                                        <li><span>Actions</span></li>
                                        <li><a href="{{ url('report/export-zero-billing-sales-person') }}"><em class="icon ni ni-download m-r10"></em> Export</a></li>
                                        {{-- <li><a href="#"><em class="icon ni ni-upload m-r10"></em> Import</a></li> --}}
                                        </ul>
                                </div>
                            </div>
                        </li>
                        
                    </ul>
                </div>
            </div>
        </div>
    </div><!-- .nk-block-between -->
</div><!-- .nk-block-head -->

<!--  Filter Tag List -->
<div id="filter_tag_list" class="filter-tag-list"></div>

<div class="nk-block table-compact">
    <div class="nk-tb-list is-separate is-medium mb-3">
        <table id="zero_billing_SP" class="products-init nowrap nk-tb-list is-separate" data-auto-responsive="false">
            <thead>
                <tr class="nk-tb-item nk-tb-head">
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">User</span></th>
                    <th class="nk-tb-col tb-col-md"><span class="sub-text">Contact Number</span></th>
                    <th class="nk-tb-col tb-col-md nk-tb-action-col text-center" nowrap="true"><span class="sub-text">Status</span></th>
                    <th class="nk-tb-col tb-col-md nk-tb-action-col" nowrap="true"><span class="sub-text">Created At</span></th>
                    <th class="nk-tb-col tb-col-md nk-tb-action-col" nowrap="true"><span class="sub-text">Updated At</span></th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div><!-- .nk-tb-list -->
</div><!-- .nk-block -->
<div class="modal fade zoom" tabindex="-1" id="modalFilterorder">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Filter</h5>
                <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
            </div>
            <form role="form" class="mb-0" method="get" action="#">
                @csrf
                <div class="modal-body modal-body-lg">
                    <div class="gy-3">
                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <x-inputs.verticalFormLabel label="Date Range" for="Create" suggestion="Specify the range." />
                            </div>
                            <div class="col-lg-7">
                                <div class="row">
                                    <div class="col-md-6">
                                        <x-inputs.datePicker for="dateFrom" size="sm" placeholder="From date" name="dateFrom" icon="calendar"/>
                                    </div>
                                    <div class="col-md-6">
                                        <x-inputs.datePicker for="dateTo" size="sm" placeholder="To date" name="dateTo" icon="calendar"/>
                                    </div>
                                </div>
                                <div class="row p-1 align-center">
                                    <div class="col-lg-12 text-center fw-bold">
                                        Or
                                    </div>
                                </div>
                                <div>
                                    <x-inputs.select  size="sm" name="range" for="range" placeholder="Select">
                                        <option value="">Select</option>
                                        <option value="Last 7 Days">Last 7 Days</option>
                                        <option value="Last 30 Days">Last 30 Days</option>
                                        <option value="Current Quarter">Current Quarter</option>
                                        <option value="Last Quarter">Last Quarter</option>
                                        <option value="Current Month">Current Month</option>
                                        <option value="Last Month">Last Month</option>
                                    </x-inputs.select>
                                </div>
                            </div>
                        </div>
                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <x-inputs.verticalFormLabel label="Sale Person" for="sp" suggestion="Select the sales person." />
                            </div>
                            <div class="col-lg-7">
                                <x-inputs.select  size="sm" name="sp" for="sp" placeholder="Sales Person">
                                    <option value="">Select</option>
                                    @forelse ($users as $user)
                                    <option value="{{ $user->id }}">{{ ucfirst($user->username) }}</option>
                                        {{-- expr --}}
                                    @empty
                                        {{-- empty expr --}}
                                    @endforelse
                                </x-inputs.select>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" id="userId" name="user_id" value="0">
                <div class="modal-footer bg-light">
                    <div class="row">
                        <div class="col-lg-12 p-0 text-right">
                            <button class="btn btn-outline-light" data-dismiss="modal" aria-label="Close">Cancel</button>
                            <button class="btn btn-danger resetFilter" data-dismiss="modal" aria-label="Close">Clear Filter</button>
                            <button class="btn btn-primary submitBtn" type="button">Submit</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<div id="table_pagination"></div>

@endsection
@push('footerScripts')
<script src="{{url('js/APIDataTable.js')}}"></script>
<script src="{{url('js/dayjs.min.js?t='.time())}}"></script>
<script type="text/javascript">
    
    var token = '{{ \Session::get('token') }}';//get logged in user session.
    var organizationId = "{{ \Session::get('currentOrganization') }}";

    var dataTable = new APIDataTable({
        tableElem: '#zero_billing_SP',
        pageinationElem: '#table_pagination',
        api: "{{ url('api/v1/report/zero-billing-sales-person') }}/"+organizationId,
        authToken: 'Bearer '+token,
        filterIds: [
            '#dateFrom',
            '#dateTo',
            '#range',
            '#sp'
        ],
        filterSubmit: '.submitBtn',
        filterSubmitCallback: function(){
            $('#modalFilterorder').modal('toggle');
        },
        filterClearSubmit: '.resetFilter',
        filterModalId: '#modalFilterorder',
        tagId: '#filter_tag_list',
        columns: [{
                data: "name",
            },
            {
                data: "user_mobile",
            },
            {
                "class": "nk-tb-col tb-col-mb text-center",
                data: "status",
            },
            {
                data: "created_at",
                render: function(row) {
                    return dayjs(row.created_at).format(
                        "DD MMM YYYY"
                    );
                },
            },
            {
                data: "updated_at",
                render: function(row) {
                    return dayjs(row.updated_at).format(
                        "DD MMM YYYY"
                    );
                },
            }
        ]
    });
</script>
@endpush