@extends('layouts.app')
@section('content')
<div class="nk-block-head nk-block-head-sm">
    <div class="nk-block-between">
        <div class="nk-block-head-content">
            <h3 class="nk-block-title page-title">Top 10 Buyers</h3>
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
                                        <li><a href="{{ url('report/export-sales-by-sales-person') }}"><em class="icon ni ni-download m-r10"></em> Export</a></li>
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
        <table id="sales_SP" class="products-init nowrap nk-tb-list is-separate" data-auto-responsive="false">
            <thead>
                <tr class="nk-tb-item nk-tb-head">
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Buyer</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Mobile Number</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Total no of Orders</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Order Amount</span></th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div><!-- .nk-tb-list -->
</div><!-- .nk-block -->
<div id="table_pagination"></div>


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
                                <x-inputs.verticalFormLabel label="Month" for="month" suggestion="Select the month." />
                            </div>
                            <div class="col-lg-7">
                                <x-inputs.select  size="sm" name="month" for="month" placeholder="Select Month">
                                    <option value="">Select</option>
                                    @for ($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}">{{ date('F',strtotime('2012-'.$i.'-12')) }}</option>
                                    @endfor
                                </x-inputs.select>
                            </div>
                        </div>
                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <x-inputs.verticalFormLabel label="Year" for="year" suggestion="Select the year." />
                            </div>
                            <div class="col-lg-7">
                                <x-inputs.select  size="sm" name="year" for="year" placeholder="Select Year">
                                    <option value="">Select</option>
                                    @for ($i = 2021; $i <= 2030; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
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
@endsection
@push('footerScripts')
<script src="{{url('js/APIDataTable.js')}}"></script>
<script src="{{url('js/dayjs.min.js?t='.time())}}"></script>
<script type="text/javascript">
    
    var token = '{{ \Session::get('token') }}';//get logged in user session.
    var organizationId = "{{ \Session::get('currentOrganization') }}";

    var dataTable = new APIDataTable({
        tableElem: '#sales_SP',
        //pageinationElem: '#table_pagination',
        api: "{{ url('api/v1/report/top-buyers') }}/"+organizationId,
        authToken: 'Bearer '+token,
        filterIds: [
            '#month',
            '#year'
        ],
        filterSubmit: '.submitBtn',
        filterSubmitCallback: function(){
            $('#modalFilterorder').modal('toggle');
        },
        filterClearSubmit: '.resetFilter',
        filterModalId: '#modalFilterorder',
        tagId: '#filter_tag_list',
        columns: [{
                data: "username",
            },
            {
                data: "user_mobile",
            },
            {
                data: "totalOrders",
            },
            {
                data: "totalAmount",
                render:function(row){
                    return NioApp.formatToCurrency(row.totalAmount);
                }
            },
        ]
    });
</script>
@endpush