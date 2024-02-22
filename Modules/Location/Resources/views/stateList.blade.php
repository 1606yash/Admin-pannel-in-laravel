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
            @php
            if(count($states) > 1)
            $count = 'states';
            else
            $count = 'state';
            @endphp
            <p>You have total <span class="record_count">{{ count($states) }}</span> {{ $count }}.</p>
        </div><!-- .nk-block-head-content -->
        <div class="nk-block-head-content">
            <div class="toggle-wrap nk-block-tools-toggle">
                <a href="#" class="btn btn-icon btn-trigger toggle-expand mr-n1" data-target="more-options"><em class="icon ni ni-more-v"></em></a>
                <div class="toggle-expand-content" data-content="more-options">
                    <ul class="nk-block-tools g-3">
                        <li>
                            <div class="form-wrap 50px mr-2">
                                <select class="form-select form-select-sm" data-search="off" data-placeholder="Bulk Action" id="mass-status">
                                    <option value="" selected disabled>Bulk Action</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                            <div class="btn-wrap">
                                <span class="d-none d-md-block"><button class="btn btn-primary" name="submit_btn" id="mass-update" onclick="updateMassItems()">Apply</button></span>
                                <span class="d-md-none"><button class="btn btn-dim btn-outline-light btn-icon disabled"><em class="icon ni ni-arrow-right"></em></button></span>
                            </div>
                        </li>
                        <li>
                            <a href="#" class="btn btn-trigger btn-icon dropdown-toggle" data-toggle="modal" title="filter" data-target="#modalFilterBrand">
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
                                        {{-- <li><a href="#"><em class="icon ni ni-download m-r10"></em> Export</a></li> --}}
                                        <li><a href="{{ url('ecommerce/brands/import') }}"><em class="icon ni ni-upload m-r10"></em> Import</a></li>
                                    </ul>
                                </div>
                            </div>
                        </li>
                        @if(isset($userPermission['brands']) && ($userPermission['brands']['edit_all'] || $userPermission['brands']['edit_own']))
                        <li class="nk-block-tools-opt">
                            <a href="#" data-target="addState" class="toggle btn btn-primary d-none d-md-inline-flex"><em class="icon ni ni-plus"></em><span>Add State</span></a>
                        </li>
                        @endif
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
                    <th class="nk-tb-col nk-tb-col-check">
                        <div class="custom-control custom-control-sm custom-checkbox notext">
                            <input type="checkbox" class="custom-control-input" id="check-all" name="check_all"><label class="custom-control-label" for="check-all"></label>
                        </div>
                    </th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Country Name</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">State Name</span></th>
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
                    @if(isset($organization_type) && $organization_type == 'MULTIPLE')
                    <div class="col-12">
                        <x-inputs.select size="md" name="organization" required="true" for="organization" data-search="on" placeholder='Select Organization'>
                            <option value="" selected disabled>Select Organization</option>
                            @foreach ($organizations as $key => $organization)
                            <option value="{{ $organization->id }}">{{ $organization->name }}</option>
                            @endforeach
                        </x-inputs.select>
                    </div>
                    @endif
                    <div class="col-12">
                        <x-inputs.select size="md" name="country_code" label="Select Country" required="true" for="countryCode" data-search="on" placeholder='Select Country'>
                            <option value="" selected disabled>Select Organization</option>
                            @foreach ($countries as $key => $country)
                            <option value="{{ $country->code }}">{{ $country->name }}</option>
                            @endforeach
                        </x-inputs.select>
                    </div>
                    <div class="col-12">
                        <x-inputs.text for="stateName" icon="sign-dash" label="State Name" required="true" name="name" minlength="3" maxlength="255" />
                    </div>
                    <div class="col-mb-12">
                        <x-inputs.text for="stateCode" icon="tag" label="State Code" name="state_code" minlength="3" maxlength="255" />
                    </div>
                    <div class="col-6">
                        <x-inputs.switch size="md" for="stateStatus" label="Status" name="status" value="on" checked />
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
                        @if(isset($organization_type) && $organization_type == 'MULTIPLE')
                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <x-inputs.verticalFormLabel label="Organization Name" for="name" suggestion="Specify the name of the organization." />
                            </div>
                            <div class="col-lg-7">
                                <x-inputs.select size="md" name="organization" required="true" for="organizationFilter" data-search="on" placeholder='Select Organization'>
                                    <option value="" selected disabled>Select Organization</option>
                                    @foreach ($organizations as $key => $organization)
                                    <option value="{{ $organization->id }}">{{ $organization->name }}</option>
                                    @endforeach
                                </x-inputs.select>
                            </div>
                        </div>
                        @endif
                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <x-inputs.verticalFormLabel label="Brand Name" for="name" suggestion="Specify the name of the brand." />
                            </div>
                            <div class="col-lg-7">
                                <x-inputs.text value="" for="name" icon="sign-dash" placeholder="Brand Name" name="name" />
                            </div>
                        </div>
                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <x-inputs.verticalFormLabel label="Status" for="status" suggestion="Select the status of the brand." />
                            </div>
                            <div class="col-lg-7">
                                <x-inputs.select size="sm" name="status" for="status">
                                    <option value="" selected disabled></option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
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
                            <button class="btn btn-primary submitBtnFilter" type="button">Submit</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade zoom" tabindex="-1" id="modalLogs">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Audit Logs</h5>
                <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
            </div>
            <div class="modal-body modal-body-lg">
                <div class="timeline">
                    <ul id="orderLogs" class="timeline-list">
                    </ul>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <div class="row">
                    <div class="col-lg-12 p-0 text-right">
                        <button class="btn btn-outline-light" data-dismiss="modal" aria-label="Close">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('footerScripts')
<script src="{{url('js/tableFlow.js')}}"></script>
<script type="text/javascript">
    function resetValues() {
        $('.submitBtn').text('Submit');
        $('.modalTitle').text('Add State');
        $('#itemId').val(0);
        $('#stateName').val('');
        $('#country_code').val('');
        $('#addStateForm')[0].reset();
        $('#addStateForm').parsley().reset();
    }

    $('.cancel').on('click', function() {
        resetValues();
        NioApp.Toggle.collapseDrawer('addState')
    })


    function updateMassItems() {
        var arr = [];
        $('input.cb-check:checkbox:checked').each(function() {
            arr.push($(this).val());
        });

        //
        var status = $('#mass-status').find(":selected").val();

        console.log(status);
        console.log(arr.length);

        if (arr.length == 0) {
            Swal.fire("Please select a brand first !");
        } else if (status == 0) {
            Swal.fire("Please select bulk status !");
        } else {
            var root_url = "<?php echo Request::root(); ?>";
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: root_url + '/location/state/mass-update',
                data: {
                    'ids': arr,
                    'status': status
                },
                //dataType: "html",
                method: "POST",
                cache: false,
                success: function(data) {
                    console.log(data);

                    if (data.success) {

                        $('#check-all').prop('checked', false);
                        var table = $('.brand-init').DataTable();
                        table.ajax.reload();

                        Swal.fire(
                            'Good job!',
                            'Status Updated Successfully.',
                            'success'
                        )

                    } else {

                        $('#check-all').prop('checked', false);
                        var table = $('.brand-init').DataTable();
                        table.ajax.reload();

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

        return false;
    }

    $(function() {

        var root_url = "<?php echo Request::root(); ?>";
        var logUrl = root_url + '/ecommerce/brands/logs';

        NioApp.getAuditLogs('.brand-init', '.audit_logs', 'resourceId', logUrl, '#modalLogs');

        $('.brand-init').on("change", "#check-all", function() {
            if (this.checked)
                $('.cb-check').prop('checked', true);
            else
                $('.cb-check').prop('checked', false);
        })

        var root_url = "<?php echo Request::root(); ?>";
        $('.brand-init').on('click', '.editItem', function() {
            var id = $(this).attr('data-id');
            console.log(id);

            $.ajax({
                url: root_url + '/location/state/get-state',
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
                        $('#countryCode').val(data.state.country_id).trigger('change');
                        $('#stateName').val(data.state.name);
                        $('#stateCode').val(data.state.state_code);

                        $('#organization').val(data.state.sub_organization_id).trigger('change');

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
                        url: root_url + '/location/state/delete',
                        data: {
                            'id': id
                        },
                        //dataType: "html",
                        method: "GET",
                        cache: false,
                        success: function(data) {
                            console.log(data);
                            if (data.success) {
                                Swal.fire('Deleted!', 'State has been deleted.', 'success');
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
            '#name',
            '#status',
        ];
        var dt = '';

        dt = new CustomDataTable({
            tableElem: '.brand-init',
            option: {
                processing: true,
                serverSide: true,
                ajax: {
                    type: "GET",
                    url: "{{ url('location/state') }}",
                },
                columns: [{
                        "class": "nk-tb-col tb-col-lg nk-tb-col-check",
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row, meta) {
                            return '<td class="nk-tb-col nk-tb-col-check"><div class="custom-control custom-control-sm custom-checkbox notext"><input type="checkbox" class="custom-control-input cb-check" id="cb-' + row.id + '" value="' + row.id + '" name="checked_items[]"><label class="custom-control-label" for="cb-' + row.id + '"></label></div></td>'
                        }
                    },
                    {
                        "class": "nk-tb-col tb-col-lg",
                        data: 'country_name',
                        name: 'country_name'
                    },
                    {
                        "class": "nk-tb-col tb-col-lg",
                        data: 'name',
                        name: 'name'
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
            // filterItems: items,
            tagId: '#filter_tag_list',
        });
    });

    // add/edit state form with ajax 
    $(document).ready(function() {
        $('.submitBtn').on('click', function(e) {
            var form = $('#addStateForm')[0];
            var data = new FormData(form);
            e.preventDefault();
            $.ajax({
                type: "POST",
                enctype: 'multipart/form-data',
                url: "{{ url('location/state/add') }}",
                data: data,
                processData: false,
                contentType: false,
                cache: false,
                timeout: 800000, // in milliseconds
                success: function(response) {
                    if (response.status == 'success') {
                        // after success response add/edit  hide sidebar form
                        if ($('body').hasClass('toggle-shown')) {
                            //Close the sidebar
                            $('body').removeClass('toggle-shown');
                        }
                        $('#modal').removeClass('content-active');
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
        });
    });
</script>
@endpush