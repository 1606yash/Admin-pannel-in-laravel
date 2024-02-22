@extends('layouts.app')
@section('content')
<div class="nk-block-between">
    <div class="nk-block-head-content">
        <h5 class="nk-block-title page-title">Super Admin</h5>
    </div>
</div>
<div class="tab-content">
    <div class="tab-pane fade show active mt-5" id="all-sub-admins">
        <div class="nk-block table-compact">
            <div id="filter_tag_list" class="filter-tag-list"></div>
            <div class="nk-tb-list is-separate mt-3">
                <table id="brand_init" class="brand-init nowrap nk-tb-list is-separate" data-auto-responsive="false">
                    <thead>
                        <tr class="nk-tb-item nk-tb-head">
                            <th class="nk-tb-col tb-col-mb"><span class="sub-text">Name</span></th>
                            <th class="nk-tb-col tb-col-mb"><span class="sub-text">Email</span></th>
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
                        <h5 class="nk-block-title modalTitle">Super Admin Filter</h5>
                    </div>
                </div>
                <div class="nk-block">
                    <div class="row g-3">
                        <div class="col-mb-12">

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
</div>
@endsection
@push('footerScripts')
<script src="{{ url('js/tableFlow.js') }}"></script>
<script type="text/javascript">
    var root_url = "<?php echo Request::root(); ?>";

    function loadAllSuperAdminDataTable() {
        $('.brand-init').DataTable().destroy();
        var items = [];
        var dt = '';
        dt = new CustomDataTable({
            tableElem: '.brand-init',
            option: {
                processing: true,
                serverSide: true,
                ajax: {
                    type: "GET",
                    url: "{{ url('/human-resource/employees/super-admin') }}",
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
                            if (data) {
                                var dateObj = new Date(data);
                                var formattedDate = ('0' + dateObj.getDate()).slice(-2) + '/' + ('0' + (dateObj.getMonth() + 1)).slice(-2) + '/' + dateObj.getFullYear().toString();
                                return formattedDate;
                            } else {
                                return 'NA';
                            }
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
                        //onCloseCallback: resetValues
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
        window.location.href = root_url + `/human-resource/employees/super-admin/view-user/${id}`;
    });


    $(document).ready(function() {
        var root_url = "<?php echo Request::root(); ?>";
        loadAllSuperAdminDataTable();
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
                        loadAllSuperAdminDataTable();
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
                loadAllSuperAdminDataTable();
            }
        });

    });
</script>
@endpush