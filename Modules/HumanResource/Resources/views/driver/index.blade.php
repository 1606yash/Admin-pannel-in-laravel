@extends('layouts.app')
<meta name="csrf-token" content="{{ csrf_token() }}" />
@section('content')
<div class="nk-block-head nk-block-head-sm">
    <div class="nk-block-between">
        <div class="nk-block-head-content">
            <h5 class="nk-block-title page-title">Drivers </h5>
        </div>
        <div class="nk-block-head-content">
            <div class="toggle-wrap nk-block-tools-toggle">
                <a href="#" class="btn btn-icon btn-trigger toggle-expand mr-n1" data-target="more-options"><em class="icon ni ni-more-v"></em></a>
                <div class="toggle-expand-content" data-content="more-options">
                    <ul class="nk-block-tools g-3">
                        <li class="nav-item">
                            <a class="btn btn-primary nav-link mr-2" href="{{ url('/human-resource/employees/driver/add-user') }}"><em class="icon ni ni-plus" style="color: #fff;"></em><span>Add Driver</span></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="toggle-bar">
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link active" data-toggle="tab" href="#all-drivers" data-tab="all">All Drivers ({{$drivers}})</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#pending-requests" data-tab="pending">Pending Requests ({{$pendingUsers}})</a>
        </li>
    </ul>
</div>
<div class="tab-content">
    <div class="tab-pane fade show active mt-5" id="all-drivers">
        <div class="nk-block-head-content">
            <div class="toggle-wrap nk-block-tools-toggle">
                <div class="toggle-expand-content" data-content="more-options">
                    <ul class="nk-block-tools g-3" style="justify-content:flex-end;">
                        <li class="nav-item">
                            <a href="#" data-target="filterUser" class="toggle btn btn-trigger btn-icon">
                                <div class="dot dot-primary"></div>
                                <em class="icon ni ni-filter-alt"></em>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div id="filter_tag_list" class="filter-tag-list"></div>
        <div class="nk-block table-compact">
            <div class="nk-tb-list is-separate mt-3">
                <table id="brand_init" class="brand-init nowrap nk-tb-list is-separate" data-auto-responsive="false">
                    <thead>
                        <tr class="nk-tb-item nk-tb-head">
                            <th class="nk-tb-col tb-col-mb"><span class="sub-text">Name</span></th>
                            <th class="nk-tb-col tb-col-mb"><span class="sub-text">Mobile Number</span></th>
                            <th class="nk-tb-col tb-col-mb"><span class="sub-text">District</span></th>
                            <th class="nk-tb-col tb-col-mb"><span class="sub-text">Created By </span></th>
                            <th class="nk-tb-col tb-col-mb"><span class="sub-text">Date of Creation </span></th>
                            <th class="nk-tb-col tb-col-mb"><span class="sub-text">Status</span></th>
                            <th class="nk-tb-col tb-col-mb"><span class="sub-text">Action</span></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="nk-add-product toggle-slide toggle-slide-right" tabindex="-1" data-content="filterUser" data-toggle-screen="any" data-toggle-overlay="true" data-toggle-body="true" data-simplebar id="filterUser">
            <form action="#" role="form" class="mb-0" method="get" id="filterUserForm">
                @csrf
                <div class="nk-block-head">
                    <div class="nk-block-head-content">
                        <h5 class="nk-block-title modalTitle">Driver Filter</h5>
                    </div>
                </div>
                <div class="nk-block">
                    <div class="row g-3">
                        <!-- Dropdown for District -->

                        <div class="col-mb-12">
                            <x-inputs.select size="md" name="district_id" for="district_id" id="district_id" data-search="on" placeholder='Select District'>
                                <option value="" selected disabled>Select District</option>
                                @foreach ($districts as $key => $district)
                                <option value="{{ $district->id }}">{{ $district->district_name }}</option>
                                @endforeach
                            </x-inputs.select>
                        </div>

                        <!-- Dropdown for Creation Date Range -->
                        <div class="col-mb-12">

                            <x-inputs.select size="md" name="creation_date" for="creation_date" id="creation_date" data-search="on" placeholder='Select Creation Date'>
                                <option value="" selected disabled>Select Creation Date</option>
                                <option value="LastThreeMonth">Last Three Months</option>
                                <option value="LastSixMonth">Last Six Months</option>
                                <option value="CurrentYear">Current Year</option>
                                <option value="LastYear">Last Year</option>
                                <option value="LastThreeYear">Last Three Year</option>
                            </x-inputs.select>
                        </div>
                        <!-- Dropdown for Created By -->
                        <div class="col-mb-12">
                            <x-inputs.select size="md" name="created_by" for="created_by" id="created_by" data-search="on" placeholder='Select Created By'>
                                <option value="" selected disabled>Select Created By</option>
                                @foreach ($upperUsers as $key => $upperUser)
                                <option value="{{ $upperUser->id }}">
                                    {{ $upperUser->first_name . ' ' . $upperUser->last_name }}
                                </option>
                                @endforeach
                            </x-inputs.select>
                            <!-- Add your created by options here -->
                        </div>
                        <!-- Dropdown for Status -->
                        <div class="col-mb-12">
                            <x-inputs.select size="md" name="status" for="status" id="status" data-search="on" placeholder='Select Status'>
                                <option value="" selected disabled>Select Status</option>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </x-inputs.select>
                        </div>
                    </div>
                    <div class="col-12 text-center mt-5">
                        <a data-target='filterUser' class="btn btn-outline-light cancel">Cancel</a>
                        <a class="btn btn-danger resetFilter cancel" data-target='filterUser'>Clear
                            Filter</a>
                        <a class="btn btn-primary submitBtnFilter" style="color:#fff">Submit</a>
                    </div>
                </div>

            </form>
        </div>
    </div>
    <div class="tab-pane fade" id="pending-requests">
        <div class="nk-block table-compact">
            <div class="nk-tb-list is-separate mt-3">
                <table id="pending_requestsId" class="pending_requestsId nowrap nk-tb-list is-separate" data-auto-responsive="false">
                    <thead>
                        <tr class="nk-tb-item nk-tb-head">
                            <th class="nk-tb-col tb-col-mb"><span class="sub-text">Name</span></th>
                            <th class="nk-tb-col tb-col-mb"><span class="sub-text">Mobile Number</span></th>
                            <th class="nk-tb-col tb-col-mb"><span class="sub-text">District</span></th>
                            <th class="nk-tb-col tb-col-mb"><span class="sub-text">Requested By </span></th>
                            <th class="nk-tb-col tb-col-mb"><span class="sub-text">Date of Creation </span></th>
                            <th class="nk-tb-col tb-col-mb"><span class="sub-text">Action</span></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
@push('footerScripts')
<script src="{{ url('js/tableFlow.js') }}"></script>
<script type="text/javascript">
    var root_url = "<?php echo Request::root(); ?>";

    function resetValues() {
        $('.submitBtn').text('Submit');
        $('.modalTitle').text('Filter');
        $('#district_id').val('');
        $('#creation_date').val('');
        $('#created_by').val('').trigger('change');
        $('#status').val('').trigger('change');
        $('#filterUserForm')[0].reset();
        $('#filterUserForm').parsley().reset();
    }

    $('.cancel').on('click', function() {
        // resetValues();
        NioApp.Toggle.collapseDrawer('filterUser')
    })

    $(document).on("click", function(event) {
        var overlay = $("#filterUser"); // Change this to the actual ID or class of your overlay

        // Check if the click target is outside the overlay
        if (!overlay.is(event.target) && overlay.has(event.target).length === 0) {
            // The click is outside the overlay

            // Add your logic here, such as closing the overlay
           // resetValues(); // Replace this with your own code to handle the click outside the overlay
        }
    });

    function loadAllDriversDataTable() {
        $('.brand-init').DataTable().destroy();
        var items = [
            '#district_id',
            '#status',
            '#creation_date',
            '#created_by'
        ];
        var dt = '';
        dt = new CustomDataTable({
            tableElem: '.brand-init',
            option: {
                processing: true,
                serverSide: true,
                ajax: {
                    type: "GET",
                    url: "{{ url('/human-resource/employees/driver/get-drivers') }}",
                },
                columns: [{
                        "class": "nk-tb-col tb-col-lg",
                        data: 'user_name',
                        name: 'user_name',
                        orderable: false
                    },
                    {
                        "class": "nk-tb-col tb-col-lg",
                        data: 'phone_no',
                        name: 'phone_no',
                        orderable: false
                    }, {
                        "class": "nk-tb-col tb-col-lg",
                        data: 'district_name',
                        name: 'district_name',
                        orderable: false
                    },
                    {
                        "class": "nk-tb-col tb-col-lg",
                        data: 'created_by_name',
                        name: 'created_by_name',
                        orderable: false
                    },
                    {
                        "class": "nk-tb-col tb-col-lg",
                        data: 'date_creation',
                        name: 'date_creation',
                        orderable: true,
                        render: function(data, type, row, meta) {
                            return data || 'NA';
                        }
                    },
                    {
                        "class": "nk-tb-col tb-col-lg",
                        data: 'is_active',
                        name: 'is_active',
                        render: function(data, type, row, meta) {
                            // Assuming 'data' contains a date in ISO format, e.g., '2023-09-22T12:34:56'
                            if (data == 1) {
                                return "Active";
                            } else {
                                return "Inactive";
                            }

                        }
                    },
                    {
                        "class": "nk-tb-col tb-col-lg text-right nk-tb-col-tools",
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
                "fnDrawCallback": function() {
                    NioApp.TGL.content('.editItem', {
                        onCloseCallback: resetValues
                    });
                    NioApp.BS.tooltip('[data-toggle="tooltip"]');
                }
            },
            filterSubmit: '.submitBtnFilter',
            filterSubmitCallback: function() {
                // resetValues();
                NioApp.Toggle.collapseDrawer('filterUser')
            },
            filterClearSubmit: '.resetFilter',
            filterModalId: '#filterUser',
            filterItems: items,
            tagId: '#filter_tag_list',
        });
    }

    function loadPendingRequestDriversDataTable() {
        $('.pending_requestsId').DataTable().destroy();

        var items = [];
        var st = '';
        st = new CustomDataTable({
            tableElem: '.pending_requestsId',
            option: {
                processing: true,
                serverSide: true,
                ajax: {
                    type: "GET",
                    url: "{{ url('/human-resource/employees/driver/get-drivers-pending-request') }}",
                },
                columns: [{
                        "class": "nk-tb-col tb-col-lg",
                        data: 'user_name',
                        name: 'user_name',
                        orderable: false
                    },
                    {
                        "class": "nk-tb-col tb-col-lg",
                        data: 'phone_no',
                        name: 'phone_no',
                        orderable: false
                    }, {
                        "class": "nk-tb-col tb-col-lg",
                        data: 'district_name',
                        name: 'district_name',
                        orderable: false
                    },
                    {
                        "class": "nk-tb-col tb-col-lg",
                        data: 'created_by_name',
                        name: 'created_by_name',
                        orderable: false
                    },
                    {
                        "class": "nk-tb-col tb-col-lg",
                        data: 'date_creation',
                        name: 'date_creation',
                        orderable: true,
                        render: function(data, type, row, meta) {
                            return data || 'NA';
                        }
                    },
                    {
                        "class": "nk-tb-col tb-col-lg text-right nk-tb-col-tools",
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
                "fnDrawCallback": function() {
                    NioApp.TGL.content('.editItem', {
                        onCloseCallback: resetValues
                    });
                    NioApp.BS.tooltip('[data-toggle="tooltip"]');
                }
            },
            filterSubmit: '.submitBtnFilter',
            filterSubmitCallback: function() {
                // resetValues();
                NioApp.Toggle.collapseDrawer('filterUser')
            },
            filterClearSubmit: '.resetFilter',
            filterModalId: '#filterUser',
            filterItems: items,
            tagId: '#filter_tag_list',
        });

    }

    $(document).ready(function() {
        var root_url = "<?php echo Request::root(); ?>";
        loadAllDriversDataTable();

        $('.brand-init').on('click', '.eg-swal-av3', function(e) {
                var data = JSON.parse($(this).attr('data-id'))
                var id = data?.user_id;
                var active_status = data?.is_active;
                console.log(active_status);
                $.ajax({
                        url: root_url + '/human-resource/employees/update-user-status',
                        data: {
                            'id': id,
                            'active_status': active_status ? null : 1
                        },

                        method: "GET",
                        cache: false,
                        success: function(data) {
                            if (data.success) {
                                if (active_status) {
                                    Swal.fire('User In-active', '',
                                        'success');
                                } else {
                                    Swal.fire('User Active', '',
                                        'success');
                                }
                                loadAllDriversDataTable();

                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: data.msg,
                                    showCancelButton: true,
                                    confirmButtonText: 'Unassign Shift',
                                    cancelButtonText: 'Cancel'
                                }).then((result) => {
                                        if (result.isConfirmed) {
                                            // Redirect to unassign shift URL
                                            window.location.href = root_url + '/human-resource/employees/driver/view-user/' + id + '#shift_details';
                                    }
                                });
                        }
                    }
                }); e.preventDefault();
        });

    $('.brand-init').on('click', '.editItem', function(e) {
        var id = $(this).attr('data-id');
        window.location.href = `/human-resource/employees/edit-user/${id}`;
    }); $('.brand-init').on('click', '.view-user', function(e) {
        var id = $(this).attr('data-id');
        window.location.href = root_url + `/human-resource/employees/driver/view-user/${id}`;
    }); $('.pending_requestsId').on('click', '.view-user', function(e) {
        var id = $(this).attr('data-id');
        window.location.href = root_url + `/human-resource/employees/driver/view-user/${id}`;
    });

    $('.pending_requestsId').on('click', '.approveItem', function(e) {
        var id = $(this).attr('data-id');
        Swal.fire({
            title: 'Confirmation',
            text: "Do you want to approve the User Account Approval Request",
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'No'
        }).then(function(result) {
            if (result.value) {
                $.ajax({
                    url: root_url + '/human-resource/employees/approve-user',
                    data: {
                        'id': id
                    },
                    method: "GET",
                    cache: false,
                    success: function(data) {
                        console.log(data);
                        if (data.success) {
                            Swal.fire('', 'User/Account Approved',
                                'success');
                            loadAllDriversDataTable();
                            loadPendingRequestDriversDataTable();
                            location.reload()
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: data.msg,
                            })
                        }
                    }
                });

            }
        });
        e.preventDefault();
    }); $('.pending_requestsId').on('click', '.eg-swal-av3', function(e) {
        var id = $(this).attr('data-id');
        console.log(id);
        Swal.fire({
            title: 'Confirmation',
            text: "Do you want to reject the User Account Approval Request ",
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'No'
        }).then(function(result) {
            Swal.fire({
                title: 'Enter reason',
                input: 'text',
                inputPlaceholder: 'Enter your reason here',
                showCancelButton: true,
                confirmButtonText: 'Reject',
                cancelButtonText: 'Cancel'
            }).then(function(result) {
                if (result.value) {
                    var reason = result.value;
                    console.log('catId-' + reason);
                    $.ajax({
                        url: root_url + '/human-resource/employees/reject-user',
                        data: {
                            'id': id,
                            'reason': reason
                        },
                        method: "GET",
                        cache: false,
                        success: function(data) {
                            console.log(data);
                            if (data.success) {
                                loadAllDriversDataTable();
                                loadPendingRequestDriversDataTable();
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: data.msg,
                                })
                            }
                        }
                    });
                }
            })

        });
        e.preventDefault();
    });

    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
        var targetTab = $(e.target).data('tab');
        if (targetTab === 'all') {
            loadAllDriversDataTable();
        } else if (targetTab === 'pending') {
            loadPendingRequestDriversDataTable();
        }
    });
    });
</script>
@endpush