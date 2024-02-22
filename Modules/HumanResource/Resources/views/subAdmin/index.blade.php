@extends('layouts.app')
<meta name="csrf-token" content="{{ csrf_token() }}" />
@section('content')
<div class="nk-block-head nk-block-head-sm">
    <div class="nk-block-between">
        <div class="nk-block-head-content">
            <h5 class="nk-block-title page-title">Sub Admin</h5>
        </div>
        <div class="nk-block-head-content">
            <div class="toggle-wrap nk-block-tools-toggle">
                <a href="#" class="btn btn-icon btn-trigger toggle-expand mr-n1" data-target="more-options"><em class="icon ni ni-more-v"></em></a>
                <div class="toggle-expand-content" data-content="more-options">
                    <ul class="nk-block-tools g-3">
                        <li class="nav-item">
                            <a href="#" data-target="filterUser" class="toggle btn btn-trigger btn-icon">
                                <div class="dot dot-primary"></div>
                                <em class="icon ni ni-filter-alt"></em>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-primary nav-link mr-2" href="{{ url('/human-resource/employees/sub-admin/add-user/') }}"><em class="icon ni ni-plus" style="color: #fff;"></em><span>Add Sub Admin</span></a>
                        </li>
                    </ul>
                </div>
            </div>
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
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Email</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Sub-Role</span></th>
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

<div class="nk-add-product toggle-slide toggle-slide-right" data-content="filterUser" data-toggle-screen="any" data-toggle-overlay="true" data-toggle-body="true" data-simplebar id="filterUser">
    <form action="#" role="form" class="mb-0" method="get" id="filterUserForm">
        @csrf
        <div class="nk-block-head">
            <div class="nk-block-head-content">
                <h5 class="nk-block-title modalTitle">Sub Admin Filter</h5>
            </div>
        </div>
        <div class="nk-block">
            <div class="row g-3">
                <div class="col-mb-12">
                    <x-inputs.select size="md" name="sub_role_id" for="sub_role_id" id="sub_role_id" data-search="on" placeholder='Select Role'>
                        <option value="" selected disabled>Select Role</option>
                        @foreach ($subRoles as $key => $subRole)
                        <option value="{{ $subRole->id }}">{{ $subRole->role_name }}</option>
                        @endforeach
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
@endsection
@push('footerScripts')
<script src="{{ url('js/tableFlow.js') }}"></script>
<script type="text/javascript">
    var root_url = "<?php echo Request::root(); ?>";

    function resetValues() {
        $('.submitBtn').text('Submit');
        $('.modalTitle').text('Filter');
        $('#sub_role_id').val('');
        $('#filterUserForm')[0].reset();
        $('#filterUserForm').parsley().reset();
    }

    $('.cancel').on('click', function() {
        //resetValues();
        NioApp.Toggle.collapseDrawer('filterUser')
    })

    $(document).on("click", function(event) {
        var overlay = $("#filterUser"); // Change this to the actual ID or class of your overlay

        // Check if the click target is outside the overlay
        if (!overlay.is(event.target) && overlay.has(event.target).length === 0) {
            // The click is outside the overlay

            // Add your logic here, such as closing the overlay
            //resetValues(); // Replace this with your own code to handle the click outside the overlay
        }
    });

    function loadAllSubAdminDataTable() {
        $('.brand-init').DataTable().destroy();
        var items = [
            '#sub_role_id',
        ];
        var dt = '';
        dt = new CustomDataTable({
            tableElem: '.brand-init',
            option: {
                processing: true,
                serverSide: true,
                ajax: {
                    type: "GET",
                    url: "{{ url('/human-resource/employees/sub-admin/get-sub-admin') }}",
                },
                columns: [{
                        "class": "nk-tb-col tb-col-lg",
                        data: 'user_name',
                        name: 'user_name',
                        orderable: true,
                        render: function(data, type, row, meta) {
                            return data || 'NA';
                        }
                    },
                    {
                        "class": "nk-tb-col tb-col-lg",
                        data: 'email',
                        name: 'email',
                        orderable: true,
                        render: function(data, type, row, meta) {
                            return data || 'NA';
                        }
                    },
                    {
                        "class": "nk-tb-col tb-col-lg",
                        data: 'role_name',
                        name: 'role_name',
                        orderable: true,
                        render: function(data, type, row, meta) {
                            return data || 'NA';
                        }
                    },
                    {
                        "class": "nk-tb-col tb-col-lg",
                        data: 'created_by_name',
                        name: 'created_by_name',
                        orderable: true,
                        render: function(data, type, row, meta) {
                            return data || 'NA';
                        }
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
                        orderable: true,
                        render: function(data, type, row, meta) {
                            return data == 1 ? "Active" : "Inactive";
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
                NioApp.Toggle.collapseDrawer('filterUser')
            },
            filterClearSubmit: '.resetFilter',
            filterModalId: '#filterUser',
            filterItems: items,
            tagId: '#filter_tag_list',
        });
    }


    $('.brand-init').on('click', '.editItem', function() {
        var id = $(this).attr('data-id');
        window.location.href = `/human-resource/employees/edit-user/${id}`;
    });
    $('.brand-init').on('click', '.view-user', function(e) {
        var id = $(this).attr('data-id');
        window.location.href = root_url + `/human-resource/employees/sub-admin/view-user/${id}`;
    });


    $(document).ready(function() {
        var root_url = "<?php echo Request::root(); ?>";
        loadAllSubAdminDataTable();
        $('.brand-init').on('click', '.eg-swal-av3', function(e) {
            var data = JSON.parse($(this).attr('data-id'))
            var id = data?.user_id;
            var active_status = data?.is_active;

            $.ajax({
                url: root_url + '/human-resource/employees/update-user-status',
                data: {
                    'id': id,
                    'active_status': active_status ? null : 1
                },
                //dataType: "html",
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
                        loadAllSubAdminDataTable();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: data.msg,
                            //footer: '<a href>Why do I have this issue?</a>'
                        })
                    }
                }
            });
            e.preventDefault();
        });

        $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
            var targetTab = $(e.target).data('tab');
            if (targetTab === 'all') {
                loadAllSubAdminDataTable();
            }
        });

    });
</script>
@endpush