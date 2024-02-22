@extends('layouts.app')
<meta name="csrf-token" content="{{ csrf_token() }}" />
@section('content')
<div class="nk-block-head nk-block-head-sm">
    <div class="nk-block-between">
        <div class="nk-block-head-content">
            <h3 class="nk-block-title page-title">User Roles</h3>
        </div><!-- .nk-block-head-content -->
        <div class="nk-block-head-content">
            <div class="toggle-wrap nk-block-tools-toggle">
                <a href="#" class="btn btn-icon btn-trigger toggle-expand mr-n1" data-target="more-options"><em class="icon ni ni-more-v"></em></a>
                <div class="toggle-expand-content" data-content="more-options">
                    <ul class="nk-block-tools g-3">
                        <li>
                            {{-- <a href="#" class="btn btn-trigger btn-icon dropdown-toggle" data-toggle="modal"
                                    title="filter" data-target="#modalFilterBrand">
                                    <div class="dot dot-primary"></div>
                                    <em class="icon ni ni-filter-alt"></em>
                                </a> --}}
                        </li>

                        <li class="nk-block-tools-opt">
                            <a href="#" data-target="addRole" class="toggle btn btn-primary d-none d-md-inline-flex" id="add"><em class="icon ni ni-plus"></em><span>Add Role</span></a>
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
    <div class="nk-tb-list is-separate mb-3">
        <table id="brand_init" class="brand-init nowrap nk-tb-list is-separate" data-auto-responsive="false">
            <thead>
                <tr class="nk-tb-item nk-tb-head">
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">User Roles</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">No of Staff</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Last Updated On</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Description</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Action</span></th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div><!-- .nk-tb-list -->

    <div class="nk-add-product toggle-slide toggle-slide-right" data-content="addRole" data-toggle-screen="any" data-toggle-overlay="true" data-toggle-body="true" data-simplebar id="modal">
        <form role="form" method="post" id="addRoleForm" enctype="multipart/form-data">
            @csrf
            <div class="nk-block-head">
                <div class="nk-block-head-content">
                    <h5 class="nk-block-title modalTitle">Add Role</h5>
                </div>
            </div><!-- .nk-block-head -->
            <input type="hidden" value="0" name="id" id="itemId">
            <div class="nk-block">
                <div class="row g-3">
                    <div class="col-12">
                        <span class="form-label">Role Name<span class="text-danger">*</span></span>
                        <input type="text" class="form-control" id="role_name" name="role_name" placeholder="Enter Role Name" onkeypress="return isCharSpace(event)" maxlength="50" />
                    </div>
                    <div class="col-mb-12">
                        <span class="form-label">Role Description<span class="text-danger">*</span></span>
                        <input type="text" class="form-control" for="roleDescription" name="role_description" id="role_description" placeholder="Enter Role Description" onkeypress="return isCharSpace(event)" maxlength="50" />
                    </div>

                    <div class="col-12 text-right">
                        <a data-target='addRole' class="btn btn-outline-light cancel">Cancel</a>
                        <button class="btn btn-primary submitBtn" name="submit"><span>Submit</span></button>
                    </div>
                </div>
            </div><!-- .nk-block -->
        </form>
    </div>
</div><!-- .nk-block -->


<div class="modal fade zoom" tabindex="-1" id="modalFilterBrand">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Filter</h5>
                <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
            </div>
            <form action="#" role="form" class="mb-0" method="get">
                @csrf
                <div class="modal-body modal-body-lg">
                    <div class="gy-3">

                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <x-inputs.verticalFormLabel label="Roles Name" for="rolename" suggestion="Specify the name of the role." />
                            </div>
                            <div class="col-lg-7">
                                <x-inputs.text value="" for="rolename" icon="sign-dash" placeholder="Role Name" name="rolename" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <div class="row">
                        <div class="col-lg-12 p-0 text-right">
                            <button class="btn btn-outline-light" data-dismiss="modal" aria-label="Close">Cancel</button>
                            <button class="btn btn-danger resetFilter" data-dismiss="modal" aria-label="Close">Clear
                                Filter</button>
                            <button class="btn btn-primary submitBtnFilter" type="button">Submit</button>
                        </div>
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
    function resetValues() {
        $('.submitBtn').text('Submit');
        $('.modalTitle').text('Add Role');
        $('#itemId').val(0);
        $('#rolename').val('');
        $('#role_description').val('');
        $('#role_name').val('');
        $('#role_description').val('');
        $('#addRoleForm')[0].reset();
        $('#addRoleForm').parsley().reset();
        $('#addRoleForm').validate().resetForm();
    }

    $(document).on("click", function(event) {
        var overlay = $("#modal"); // Change this to the actual ID or class of your overlay

        // Check if the click target is outside the overlay
        if (!overlay.is(event.target) && overlay.has(event.target).length === 0) {
            resetValues(); // Replace this with your own code to handle the click outside the overlay
        }
    });

    $('.cancel').on('click', function() {
        resetValues();
        NioApp.Toggle.collapseDrawer('addRole')
    })


    $(function() {
        var root_url = "<?php echo Request::root(); ?>";
        $('.brand-init').on('click', '.editItem', function() {
            var id = $(this).attr('data-id');
            $.ajax({
                url: root_url + '/role/get-role',
                data: {
                    'id': id,
                },
                method: "GET",
                success: function(data) {
                    if (data.success) {
                        $('#itemId').val(id);
                        $('#role_name').val(data.role.role_name);
                        $('#no_of_staff').val(data.role.no_of_staff);
                        $('#role_description').val(data.role.role_description);

                        $('.submitBtn').text('Update');
                        $('.modalTitle').text('Edit Role');
                    } else {
                        Swal.fire('Details not found!')
                    }
                }
            });
        });

        $('.brand-init').on('click', '.eg-swal-av3', function(e) {
            var id = $(this).attr('data-id');
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!'
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        url: root_url + '/role/delete',
                        data: {
                            'id': id
                        },
                        method: "GET",
                        success: function(data) {
                            console.log(data);
                            if (data.success) {
                                Swal.fire('Deleted!', 'Role has been deleted.',
                                    'success');
                                var table = $('.brand-init').DataTable();
                                table.ajax.reload();
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
        });

        var items = [
            '#rolename',
        ];
        var dt = '';

        dt = new CustomDataTable({
            tableElem: '.brand-init',
            option: {
                processing: true,
                serverSide: true,
                ajax: {
                    type: "GET",
                    url: "{{ url('role/get-role-list') }}",
                },
                columns: [{
                        "class": "nk-tb-col tb-col-lg",
                        data: 'role_name',
                        name: 'role_name',
                        orderable: true
                    },
                    {
                        "class": "nk-tb-col tb-col-lg",
                        data: 'no_of_staff',
                        name: 'no_of_staff',
                        orderable: true
                    },
                    {
                        "class": "nk-tb-col tb-col-lg",
                        data: 'updated_at',
                        name: 'updated_at',
                        orderable: true
                    },
                    {
                        "class": "nk-tb-col tb-col-lg",
                        data: 'role_description',
                        name: 'role_description',
                        orderable: true
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
                $('#modalFilterBrand').modal('toggle');
            },
            filterClearSubmit: '.resetFilter',
            filterModalId: '#modalFilterBrand',
            filterItems: items,
            tagId: '#filter_tag_list',
        });
    });

    // add/edit role form with ajax 
    $(document).ready(function() {
        if ($("#addRoleForm").length > 0) {
            $("#addRoleForm").validate({
                rules: {
                    role_name: {
                        required: true,
                        maxlength: 50
                    },
                    role_description: {
                        required: true,
                        maxlength: 50,
                    },
                },
                messages: {
                    role_name: {
                        required: "Please enter role name",
                        maxlength: "Your role name maxlength should be 50 characters long."
                    },
                    role_description: {
                        required: "Please enter role description",
                        maxlength: "Your role description maxlength should be 50 characters long."
                    },
                },
                submitHandler: function(form) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    // disabled submit button during process running status
                    $(".submitBtn").attr("disabled", true);

                    $.ajax({
                        type: "POST",
                        url: "{{ url('role/add') }}",
                        data: $('#addRoleForm').serialize(),
                        success: function(response) {
                            $(".submitBtn").attr("disabled", false);
                            if (response.status == 'success') {
                                // after success response add/edit  hide sidebar form
                                if ($('body').hasClass('toggle-shown')) {

                                    //Close the sidebar
                                    $('body').removeClass('toggle-shown');
                                }
                                $('#modal').removeClass('content-active');
                                $('#add').removeClass('active');
                                $('.toggle-overlay[data-target="addRole"]').remove();

                                // refresh datatable without page reload
                                var table = $('.brand-init').DataTable();
                                table.ajax.reload();
                                resetValues();
                                toastr.success(response.message);
                            } else {
                                toastr.error(response.message);
                            }
                        }
                    });
                }
            });
        }
    });
</script>
@endpush