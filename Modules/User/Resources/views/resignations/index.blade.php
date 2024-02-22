@extends('layouts.app')

@section('content')
<div class="nk-block-head nk-block-head-sm">
    <div class="nk-block-between">
        <div class="nk-block-head-content">
            <h3 class="nk-block-title page-title">Resignations</h3>
        </div><!-- .nk-block-head-content -->
        <div class="nk-block-head-content">
            <div class="toggle-wrap nk-block-tools-toggle">
                <a href="#" class="btn btn-icon btn-trigger toggle-expand mr-n1" data-target="more-options"><em class="icon ni ni-more-v"></em></a>
                <div class="toggle-expand-content" data-content="more-options">
                    <ul class="nk-block-tools g-3">
                        <li>
                            <a href="#" class="btn btn-trigger btn-icon toggle" data-target="filterResignation">
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
        <table id="brand_init" class="attendanceData brand-init nowrap nk-tb-list is-separate" data-auto-responsive="false">
            <thead>
                <tr class="nk-tb-item nk-tb-head">
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Name</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Role</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">District</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Reason</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Date</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Applied To</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Status</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Action</span></th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div><!-- .nk-tb-list -->

</div><!-- .nk-block -->

<div class="nk-add-product toggle-slide toggle-slide-right" tabindex="-1" data-content="filterResignation" data-toggle-screen="any" data-toggle-overlay="true" data-toggle-body="true" data-simplebar id="filterResignation">
    <div class="nk-block-head">
        <div class="nk-block-head-content">
            <h5 class="nk-block-title modalTitle">Resignation Filter</h5>
        </div>
    </div>
    <form action="#" role="form" class="mb-0" method="get" id="ResignationFilterForm">
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
                    <x-inputs.select size="md" name="district_id" for="district_id" id="district_id" data-search="on" placeholder='Select District'>
                        <option value="" selected disabled>Select District</option>
                        @foreach ($districts as $key => $district)
                        <option value="{{ $district->id }}">{{ $district->district_name }}</option>
                        @endforeach
                    </x-inputs.select>
                </div>
                <div class="col-mb-12">
                    <x-inputs.select size="md" name="date_range" for="date_range" id="date_range" data-search="on" placeholder='Select Resignation Date'>
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
                <a data-target='filterResignation' class="btn btn-outline-light cancel">Cancel</a>
                <a class="btn btn-danger resetFilter" data-target='filterResignation' style="color:#fff">Clear Filter</a>
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
        var items = [
            '#district_id', '#role_id', '#date_range'
        ];
        var dt = '';

        dt = new CustomDataTable({
            tableElem: '.brand-init',
            option: {
                processing: true,
                serverSide: true,
                ajax: {
                    type: "GET",
                    url: "{{ url('human-resource/resignations/get-resignation-list') }}",
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
                            var reason = row['reason'];

                            return reason || 'NA';
                        },
                        name: 'reason',
                        orderable: false
                    },
                    {
                        "class": "nk-tb-col tb-col-lg",
                        data: null,
                        render: function(data, type, row) {
                            var resignationDate = row['resignation_date'];

                            return resignationDate || 'NA';
                        },
                        name: 'resignation_date',
                        orderable: true
                    },
                    {
                        "class": "nk-tb-col tb-col-lg",
                        data: null,
                        render: function(data, type, row) {
                            var appliedTo = row['applied_to'];

                            return appliedTo || 'NA';
                        },
                        name: 'applied_to',
                        orderable: false
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
                    [4, 'desc']
                ],
            },
            filterSubmit: '.submitBtnFilter',
            filterSubmitCallback: function() {
                NioApp.Toggle.collapseDrawer('filterResignation')
            },
            filterClearSubmit: '.resetFilter',
            filterModalId: '#filterResignation',
            filterItems: items,
            tagId: '#filter_tag_list',
        });

        $('.cancel').on('click', function() {
            NioApp.Toggle.collapseDrawer('filterResignation')
        });

        $(document).on("click", function(event) {
            var overlay = $("#filterResignation");
            if (!overlay.is(event.target) && overlay.has(event.target).length === 0) {
                //resetForm();
            }
        });

        function resetForm() {
            $('#role_id').val(0).trigger("change");
            $('#district_id').val(0).trigger("change");
            $('#date_range').val(0).trigger("change");
        }
    });
</script>
@endpush