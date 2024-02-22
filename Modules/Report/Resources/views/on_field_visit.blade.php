@extends('layouts.app')
@section('content')
<div class="nk-block-head nk-block-head-sm">
    <div class="nk-block-between">
        <div class="nk-block-head-content">
            <h3 class="nk-block-title page-title">On-Field Visits</h3>
        </div><!-- .nk-block-head-content -->
        <div class="nk-block-head-content">
            <div class="toggle-wrap nk-block-tools-toggle">
                <a href="#" class="btn btn-icon btn-trigger toggle-expand mr-n1" data-target="more-options"><em class="icon ni ni-more-v"></em></a>
                <div class="toggle-expand-content" data-content="more-options">
                    <ul class="nk-block-tools g-3">
                        <li>
                            <a href="#" class="btn btn-trigger btn-icon dropdown-toggle" data-toggle="modal" title="filter" data-target="#modalFilterVisit">
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
                                        <li><a href=""><em class="icon ni ni-download m-r10"></em> Export</a></li>
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
        <table id="sales_SP" class="products-init nowrap nk-tb-list is-separate" data-auto-responsive="false">
            <thead>
                <tr class="nk-tb-item nk-tb-head">
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Buyer</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Sale Person</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Planned Date</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Checked In At</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Check Out At</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Canceled At</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Comment</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Status</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Checkout By</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Canceled By</span></th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div><!-- .nk-tb-list -->
</div><!-- .nk-block -->
<div id="table_pagination"></div>


<div class="modal fade zoom" tabindex="-1" id="modalFilterVisit">
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
                                <x-inputs.verticalFormLabel label="Status" for="status" suggestion="Select the status." />
                            </div>
                            <div class="col-7">
                                <x-inputs.select for="status" size="sm"  name="status">
                                    <option value=""></option>
                                    <option value="pending">Pending</option>
                                    <option value="completed">Completed</option>
                                    <option value="checkedIn">Checked In</option>
                                    <option value="cancelled">Cancelled</option>
                                </x-inputs.select>
                            </div>
                        </div>

                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <x-inputs.verticalFormLabel label="Buyer" for="Buyer" suggestion="Select the buyer." />
                            </div>
                            <div class="col-7">
                                <x-inputs.select for="buyers" size="sm"  name="buyer">
                                    <option></option>
                                    @forelse($buyers as $key  => $buyer)
                                    <option value="{{ $buyer->buyer }}">{{ ucfirst($buyer->buyerName) }}</option>
                                    @empty
                                        <option></option>
                                    @endforelse
                                </x-inputs.select>
                            </div>
                        </div>
                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <x-inputs.verticalFormLabel label="Sales Person" for="Dsp" suggestion="Select the sales person." />
                            </div>
                            <div class="col-7">
                                <x-inputs.select for="dsps" size="sm" name="dsp">
                                    <option></option>
                                    @forelse($dsps as $key  => $dsp)
                                    <option value="{{ $dsp->dsp }}">{{ ucfirst($dsp->dspName) }}</option>
                                    @empty
                                        <option></option>
                                    @endforelse
                                </x-inputs.select>
                            </div>
                        </div>

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
                                        <option value="today">Today</option>
                                        <option value="this_week">This Week</option>
                                        <option value="this_month">This Month</option>
                                    </x-inputs.select>
                                </div>
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
@endsection
@push('footerScripts')
<script src="{{url('js/APIDataTable.js')}}"></script>
<script src="{{url('js/dayjs.min.js?t='.time())}}"></script>
<script type="text/javascript">
    
    var token = '{{ \Session::get('token') }}';//get logged in user session.
    var organizationId = "{{ \Session::get('currentOrganization') }}";

    var dataTable = new APIDataTable({
        tableElem: '#sales_SP',
        pageinationElem: '#table_pagination',
        api: "{{ url('api/v1/report/onfield-visits') }}/"+organizationId,
        authToken: 'Bearer '+token,
        filterIds: [
            '#buyers',
            '#status',
            '#dsps',
            '#dateFrom',
            '#dateTo',
            '#range'
        ],
        
        filterSubmit: '.submitBtn',
        filterSubmitCallback: function(){
            $('#modalFilterVisit').modal('toggle');
        },
        filterClearSubmit: '.resetFilter',
        filterModalId: '#modalFilterVisit',
        tagId: '#filter_tag_list',
        columns: [{
                data: "retailerName",
            },
            {
                data: "dspName",
                render: function(row) {
                    return row.dspName.charAt(0).toUpperCase() + row.dspName.slice(1)
                },
            },
            {
                data: "planned_date",
                render: function(row) {
                    return row.planned_date ? dayjs(row.planned_date).format(
                        "DD MMM YYYY"
                    ) : "-";
                },
            },
            {
                data: "checked_in_at",
                render: function(row) {
                    return row.checked_in_at ? dayjs(row.checked_in_at).format(
                        "DD MMM YYYY"
                    ) : "-";
                },
            },
            {
                data: "checked_out_at",
                render: function(row) {
                    return row.checked_out_at ? dayjs(row.checked_out_at).format(
                        "DD MMM YYYY"
                    ) : "-";
                },
            },
            {
                data: "cancelled_at",
                render: function(row) {
                    return row.cancelled_at ? dayjs(row.cancelled_at).format(
                        "DD MMM YYYY"
                    ) : "-";
                },
            },
            {
                data: "comment",
            },
            {
                data: "status",
            },
            {
                data: "checked_out_by",
            },
            {
                data: "cancelled_by",
            }
        ]
    });
</script>
@endpush