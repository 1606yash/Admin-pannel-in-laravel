@extends('layouts.app')

@section('content')
<div class="nk-block-head nk-block-head-sm">
    <div class="nk-block-between">
        <div class="nk-block-head-content">
            <h3 class="nk-block-title page-title">Leaves</h3>
        </div><!-- .nk-block-head-content -->
        <div class="nk-block-head-content">
            <div class="toggle-wrap nk-block-tools-toggle">
                <a href="#" class="btn btn-icon btn-trigger toggle-expand mr-n1" data-target="more-options"><em class="icon ni ni-more-v"></em></a>
                <div class="toggle-expand-content" data-content="more-options">
                    <ul class="nk-block-tools g-3">
                        <li class="nav-item">
                            <a href="#" class="btn btn-trigger btn-icon toggle" data-target="filterLeaves">
                                <div class="dot dot-primary"></div>
                                <em class="icon ni ni-filter-alt"></em>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div><!-- .nk-block-between -->
</div>
<div id="filter_tag_list" class="filter-tag-list"></div>
<div class="nk-block table-compact">
    <div class="nk-tb-list is-separate mb-3">
        <table id="brand_init" class="brand-init nowrap nk-tb-list is-separate" data-auto-responsive="false">
            <thead>
                <tr class="nk-tb-item nk-tb-head">
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Name</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Role</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">District</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Leave Type</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">From</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">To</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Amount</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Applied On</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Status</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Action</span></th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div><!-- .nk-tb-list -->

</div><!-- .nk-block -->

<div class="nk-add-product toggle-slide toggle-slide-right" tabindex="-1" data-content="filterLeaves" data-toggle-screen="any" data-toggle-overlay="true" data-toggle-body="true" data-simplebar id="filterLeaves">
    <div class="nk-block-head">
        <div class="nk-block-head-content">
            <h5 class="nk-block-title modalTitle">Leave Filter</h5>
        </div>
    </div>
    <form action="#" role="form" class="mb-0" method="get" id="LeaveFilterForm">
        @csrf
        <div class="nk-block">
            <div class="row g-3">
                <div class="col-mb-12">
                    <x-inputs.select size="md" name="role_id" for="role_id" data-search="on" placeholder='Select Role'>
                        <option value="" selected disabled>Select Role</option>
                        @php
                        $roles = Helpers::getAllRoles();
                        @endphp
                        @foreach ($roles as $role)
                        <option value="{{ $role->id }}">{{ $role->role_name }}</option>
                        @endforeach
                    </x-inputs.select>
                </div>
                <div class="col-mb-12">
                    <x-inputs.select size="md" name="leave_type" for="leave_type" data-search="on" placeholder='Select Leave Type'>
                        <option value="" selected disabled>Select Leave Type</option>
                        @if(!empty($leaveTypes))
                        @foreach($leaveTypes as $leaveType)
                        <option value="{{$leaveType->leave_type_id}}">{{$leaveType->name}}</option>
                        @endforeach
                        @endif
                    </x-inputs.select>
                </div>
                <div class="col-mb-12">
                    <x-inputs.select size="md" name="leave_status" for="leave_status" data-search="on" placeholder='Select Status'>
                        <option value="" selected disabled>Select Status</option>
                        <option value="approved">Approved</option>
                        <option value="pending">Pending</option>
                    </x-inputs.select>
                </div>

                <div class="col-mb-12">
                    <x-inputs.select size="md" name="leave_date_range" for="leave_date_range" data-search="on" placeholder='Select Creation Date'>
                        <option value="" selected disabled>Select Creation Date</option>
                        <option value="LastThreeMonth">Last Three Months</option>
                        <option value="LastSixMonth">Last Six Months</option>
                        <option value="CurrentYear">Current Year</option>
                        <option value="LastYear">Last Year</option>
                        <option value="LastThreeYear">Last Three Year</option>
                    </x-inputs.select>
                    </select>
                </div>
            </div>

            <div class="col-12 text-center mt-5">
                <a class="btn btn-outline-light cancel" data-target='filterLeaves'>Cancel</a>
                <a class="btn btn-danger resetFilter" data-target='filterLeaves' style="color:#fff">Clear Filter</a>
                <a class="btn btn-primary submitBtnFilter" style="color:#fff">Submit</a>
            </div>
        </div>
    </form>
</div>
@endsection
@push('footerScripts')
<script src="{{url('js/tableFlow.js')}}"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $(document).on("click", function(event) {
            var overlay = $("#filterLeaves");
            if (!overlay.is(event.target) && overlay.has(event.target).length === 0) {
                //resetForm();
            }
        });
        $('.cancel').on('click', function() {
            NioApp.Toggle.collapseDrawer('filterLeaves')
        })

        function resetForm() {
            // Assuming the form has the id "myForm"
            $('#LeaveFilterForm')[0].reset();
        }

        var items = [
            '#leave_type', '#role_id', '#leave_status', '#leave_date_range'
        ];
        var dt = '';

        dt = new CustomDataTable({
            tableElem: '.brand-init',
            option: {
                processing: true,
                serverSide: true,
                ajax: {
                    type: "GET",
                    url: "{{ url('human-resource/leaves/get-leaves-list') }}",
                },
                columns: [{
                        "class": "nk-tb-col tb-col-lg",
                        data: null,
                        render: function(data, type, row) {
                            var userName = row['user_name'];

                            return userName || 'NA';
                        },
                        name: 'user_name',
                        orderable: false
                    },
                    {
                        "class": "nk-tb-col tb-col-lg",
                        data: null,
                        render: function(data, type, row) {
                            var roleName = row['role_name'];

                            return roleName || 'NA';
                        },
                        name: 'role_name',
                        orderable: false
                    },
                    {
                        "class": "nk-tb-col tb-col-lg",
                        data: null,
                        render: function(data, type, row) {
                            var districtName = row['district_name'];

                            return districtName || 'NA';
                        },
                        name: 'district_name',
                        orderable: false
                    },
                    {
                        "class": "nk-tb-col tb-col-lg",
                        data: null,
                        render: function(data, type, row) {
                            var leaveName = row['leave_name'];

                            return leaveName || 'NA';
                        },
                        name: 'leave_name',
                        orderable: false
                    },
                    {
                        "class": "nk-tb-col tb-col-lg",
                        data: null,
                        render: function(data, type, row) {
                            var fromDate = row['from_date'];

                            return fromDate || 'NA';
                        },
                        name: 'from_date',
                        orderable: false
                    },
                    {
                        "class": "nk-tb-col tb-col-lg",
                        data: null,
                        render: function(data, type, row) {
                            var toDate = row['to_date'];

                            return toDate || 'NA';
                        },
                        name: 'to_date',
                        orderable: false
                    },
                    {
                        "class": "nk-tb-col tb-col-lg",
                        data: null,
                        render: function(data, type, row) {
                            var amount = row['amount'];

                            return amount || 'NA';
                        },
                        name: 'amount',
                        orderable: false
                    },
                    {
                        "class": "nk-tb-col tb-col-lg",
                        data: null,
                        render: function(data, type, row) {
                            var leaveAppliedDate = row['leave_applied_date'];

                            return leaveAppliedDate || 'NA';
                        },
                        name: 'leave_applied_date',
                        orderable: true
                    },
                    {
                        "class": "nk-tb-col tb-col-lg",
                        data: null,
                        render: function(data, type, row) {
                            var status = row['status'];

                            return status || 'NA';
                        },
                        name: 'status',
                        orderable: false
                    },
                    {
                        "class": "nk-tb-col tb-col-lg text-right nk-tb-col-tools",
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
                order: [
                    [7, 'desc']
                ],
            },
            filterSubmit: '.submitBtnFilter',
            filterSubmitCallback: function() {
                NioApp.Toggle.collapseDrawer('filterLeaves')
            },
            filterClearSubmit: '.resetFilter',
            filterModalId: '#filterLeaves',
            filterItems: items,
            tagId: '#filter_tag_list',
        });
    });
</script>
@endpush