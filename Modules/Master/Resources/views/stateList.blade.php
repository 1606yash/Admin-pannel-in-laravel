@extends('layouts.app')
<meta name="csrf-token" content="{{ csrf_token() }}" />
@section('content')
@php
$userPermission = \Session::get('userPermission');
$organization_type = \Session::get('organization_type');
@endphp
<div class="nk-block-head nk-block-head-sm">
    <div class="nk-block-between">
        <div class="nk-block-head-content">
            <h3 class="nk-block-title page-title">States</h3>
        </div><!-- .nk-block-head-content -->
        <div class="nk-block-head-content">
            <div class="toggle-wrap nk-block-tools-toggle">
                <a href="#" class="btn btn-icon btn-trigger toggle-expand mr-n1" data-target="more-options"><em class="icon ni ni-more-v"></em></a>
                <div class="toggle-expand-content" data-content="more-options">
                    <ul class="nk-block-tools g-3">
                        <li class="nk-block-tools-opt">
                            <a href="#" data-target="addState" class="toggle btn btn-primary d-none d-md-inline-flex" id="add"><em class="icon ni ni-plus"></em><span>Add State</span></a>
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
                    {{-- <th class="nk-tb-col nk-tb-col-check">
                            <div class="custom-control custom-control-sm custom-checkbox notext">
                                <input type="checkbox" class="custom-control-input" id="check-all" name="check_all"><label
                                    class="custom-control-label" for="check-all"></label>
                            </div>
                        </th> --}}
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">State Name</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Created By</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Updated By</span></th>
                    <th class="nk-tb-col nk-tb-col-tools nk-tb-action-col text-right" nowrap="true">
                        <span class="sub-text">Action</span>
                    </th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div><!-- .nk-tb-list -->

    <div class="nk-add-product toggle-slide toggle-slide-right" data-content="addState" data-toggle-screen="any" data-toggle-overlay="true" data-toggle-body="true" data-simplebar id="modal">
        <form role="form" method="post" id="addStateForm" enctype="multipart/form-data">
            @csrf
            <div class="nk-block-head">
                <div class="nk-block-head-content">
                    <h5 class="nk-block-title modalTitle">Add State</h5>
                    <div class="nk-block-des">
                        <p>Add information and add new state.</p>
                    </div>
                </div>
            </div><!-- .nk-block-head -->
            <input type="hidden" value="0" name="id" id="itemId">
            <div class="nk-block">
                <div class="row g-3">
                    <div class="col-12">
                        <label for="state_name">State Name<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="state_name" name="state_name" value="" placeholder="Enter State Name" onkeypress="return isCharSpace(event)" data-parsley-excluded="true">
                    </div>
                    <div class="col-12 text-right">
                        <a data-target='addState' class="btn btn-outline-light cancel">Cancel</a>
                        <button class="btn btn-primary submitBtn" name="submit"><span>Submit</span></button>
                    </div>
                </div>
            </div><!-- .nk-block -->
        </form>
    </div>
</div><!-- .nk-block -->

<div class="modal fade zoom" tabindex="-1" id="modalFilterStates">
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
                                <x-inputs.verticalFormLabel label="State Name" for="state_name" suggestion="Specify the name of the state." />
                            </div>
                            <div class="col-lg-7">
                                <x-inputs.text value="" for="stateName" icon="sign-dash" placeholder="State Name" name="stateName" />
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
        $('.modalTitle').text('Add State');
        $('#itemId').val(0);
        $('#state_name').val('');
        $('#addStateForm')[0].reset();
        $('#addStateForm').parsley().reset();
        $('#addStateForm').validate().resetForm();
    }

    $('.cancel').on('click', function() {
        resetValues();
        NioApp.Toggle.collapseDrawer('addState')
    })

    $(document).on("click", function(event) {
        var overlay = $("#modal"); // Change this to the actual ID or class of your overlay

        // Check if the click target is outside the overlay
        if (!overlay.is(event.target) && overlay.has(event.target).length === 0) {
            // The click is outside the overlay

            // Add your logic here, such as closing the overlay
            resetValues(); // Replace this with your own code to handle the click outside the overlay
        }
    });


    $(function() {
        var root_url = "<?php echo Request::root(); ?>";
        $('.brand-init').on('click', '.editItem', function() {
            var id = $(this).attr('data-id');
            console.log(id);

            $.ajax({
                url: root_url + '/master/state/get-state',
                data: {
                    'id': id
                },
                //dataType: "html",
                method: "GET",
                cache: false,
                success: function(data) {
                    console.log(data);
                    if (data.success) {
                        $('#itemId').val(id);
                        $('#state_name').val(data.state.state_name);
                        $('.submitBtn').text('Update');
                        $('.modalTitle').text('Edit State');
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
                    console.log('catId-' + id);
                    $.ajax({
                        url: root_url + '/master/state/delete',
                        data: {
                            'id': id
                        },
                        //dataType: "html",
                        method: "GET",
                        cache: false,
                        success: function(data) {
                            console.log(data);
                            if (data.success) {
                                Swal.fire('Deleted!', 'State has been deleted.',
                                    'success');
                                var table = $('.brand-init').DataTable();
                                table.ajax.reload();
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

                }
            });
            e.preventDefault();
        });

        var items = [
            "#stateName"
        ];
        var dt = '';

        dt = new CustomDataTable({
            tableElem: '.brand-init',
            option: {
                processing: true,
                serverSide: true,
                ajax: {
                    type: "GET",
                    url: "{{ url('master/state') }}",
                },
                columns: [{
                        "class": "nk-tb-col tb-col-lg",
                        data: 'state_name',
                        name: 'state_name',
                        orderable: true
                    },
                    {
                        "class": "nk-tb-col tb-col-lg",
                        data: 'created_by',
                        name: 'created_by',
                        orderable: true
                    },
                    {
                        "class": "nk-tb-col tb-col-lg",
                        data: 'updated_by',
                        name: 'updated_by',
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
                $('#modalFilterStates').modal('toggle');
            },
            filterClearSubmit: '.resetFilter',
            filterModalId: '#modalFilterStates',
            filterItems: items,
            tagId: '#filter_tag_list',
        });
    });

    // add/edit state form with ajax 
    $(document).ready(function() {

        if ($("#addStateForm").length > 0) {
            $("#addStateForm").validate({
                rules: {
                    state_name: {
                        required: true,
                        maxlength: 50
                    },
                },
                messages: {
                    state_name: {
                        required: "Please enter state name.",
                        maxlength: "Your state name maxlength should be 50 characters long."
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
                        url: "{{ url('master/state/add') }}",
                        data: $('#addStateForm').serialize(),
                        success: function(response) {
                            $(".submitBtn").attr("disabled", false);
                            if (response.status == 'success') {
                                if ($('body').hasClass('toggle-shown')) {
                                    //Close the sidebar
                                    $('body').removeClass('toggle-shown');
                                }
                                $('#modal').removeClass('content-active');
                                $('#add').removeClass('active');
                                $('.toggle-overlay[data-target="addState"]').remove();

                                // refresh datatable without page reload
                                var table = $('.brand-init').DataTable();
                                table.ajax.reload();
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