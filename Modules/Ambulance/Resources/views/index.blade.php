@extends('layouts.app')
<meta name="csrf-token" content="{{ csrf_token() }}" />

@section('content')
<div class="nk-block-head nk-block-head-sm">
    <div class="nk-block-between">
        <div class="nk-block-head-content">
            <h3 class="nk-block-title page-title">Ambulance</h3>
        </div><!-- .nk-block-head-content -->
        <div class="nk-block-head-content">
            <div class="toggle-wrap nk-block-tools-toggle">
                <a href="#" class="btn btn-icon btn-trigger toggle-expand mr-n1" data-target="more-options"><em class="icon ni ni-more-v"></em></a>
                <div class="toggle-expand-content" data-content="more-options">
                    <ul class="nk-block-tools g-3">
                        <li class="nav-item">
                            <a href="#" class="btn btn-trigger btn-icon toggle" title="filter" data-target="filterAmbulance">
                                <div class="dot dot-primary"></div>
                                <em class="icon ni ni-filter-alt"></em>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link btn btn-primary d-none d-md-inline-flex" href="{{ url('/ambulance/add-ambulance') }}"><em class="icon ni ni-plus" style="color: #fff;"></em><span> Add Ambulance</span></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div><!-- .nk-block-between -->
</div>

<!--  Filter Tag List -->
<div id="filter_tag_list" class="filter-tag-list"></div>
<div class="nk-block table-compact">
    <div class="nk-tb-list is-separate mb-3">
        <table id="brand_init" class="brand-init nowrap nk-tb-list is-separate" data-auto-responsive="false">
            <thead>
                <tr class="nk-tb-item nk-tb-head">
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Ambulance Number</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Chassis Number</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">District</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Date of Inaugration</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Status</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Action</span></th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div><!-- .nk-tb-list -->
</div><!-- .nk-block -->

<div class="nk-add-product toggle-slide toggle-slide-right" tabindex="-1" data-content="filterAmbulance" data-toggle-screen="any" data-toggle-overlay="true" data-toggle-body="true" data-simplebar id="filterAmbulance">
    <form action="#" role="form" class="mb-0" method="get" id="filterAmbulanceForm">
        @csrf
        <div class="nk-block-head">
            <div class="nk-block-head-content">
                <h5 class="nk-block-title modalTitle">Ambulance Filter</h5>
            </div>
        </div>
        <div class="nk-block">
            <div class="row g-3">
                <!-- Dropdown for District -->

                <div class="col-mb-12">
                    <x-inputs.select size="md" name="district_id" for="district_id" id="district_id" data-search="on" placeholder='Select District'>
                        <option value="" selected disabled>Select District</option>
                        @if(!empty($districts))
                        @foreach ($districts as $key => $district)
                        <option value="{{ $district->id }}">{{ $district->district_name }}</option>
                        @endforeach
                        @endif
                    </x-inputs.select>
                </div>

                <!-- Dropdown for Inaugration Date Range -->
                <div class="col-mb-12">

                    <x-inputs.select size="md" name="inaugration_date" for="inaugration_date" id="inaugration_date" data-search="on" placeholder='Select Inaugration Date'>
                        <option value="" selected disabled>Select Inaugration Date</option>
                        <option value="LastThreeMonth">Last Three Months</option>
                        <option value="LastSixMonth">Last Six Months</option>
                        <option value="CurrentYear">Current Year</option>
                        <option value="LastYear">Last Year</option>
                        <option value="LastThreeYear">Last Three Year</option>
                    </x-inputs.select>
                    </select>
                </div>
                <!-- Dropdown for Chassis Number -->
                <div class="col-mb-12">
                    <x-inputs.select size="md" name="chassis_no" for="chassis_no" id="chassis_no" data-search="on" placeholder='Select Chassis Number'>
                        <option value="" selected disabled>Select Chassis Number</option>
                        @if(!empty($chassisNumbers))
                        @foreach ($chassisNumbers as $key => $chassisNumber)
                        <option value="{{ $chassisNumber->chassis_no }}">
                            {{ $chassisNumber->chassis_no}}
                        </option>
                        @endforeach
                        @endif
                    </x-inputs.select>
                    <!-- Add your Chassis Number options here -->
                    </select>
                </div>
                <!-- Dropdown for Status -->
                <div class="col-mb-12">
                    <x-inputs.select size="md" name="status" for="status" id="status" data-search="on" placeholder='Select Status'>
                        <option value="" selected disabled>Select Status</option>
                        <option value="Running">Active</option>
                        <option value="Not Running">Inactive</option>
                    </x-inputs.select>
                </div>
            </div>
            <div class="col-12 text-center mt-5">
                <a data-target='filterAmbulance' class="btn btn-outline-light cancel">Cancel</a>
                <a class="btn btn-danger resetFilter" data-target='filterAmbulance'>Clear
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
    $(document).ready(function() {
        loadAmbulanceDataTable();
        $('.cancel').on('click', function() {
            resetValues();
            NioApp.Toggle.collapseDrawer('filterAmbulance')
        })

        $('.brand-init').on('click', '.eg-swal-av3', function(e) {
            var id = $(this).data('id');
            var active_status = $(this).data('status');

            // Toggle the active_status between "Running" and "Not Running"
            var new_status = (active_status === "Running") ? "Not Running" : "Running";
            $.ajax({
                url: root_url + '/ambulance/update-ambulance-status',
                data: {
                    'id': id,
                    'status': new_status
                },
                method: "GET",
                cache: false,
                success: function(data) {
                    if (data.success) {
                        Swal.fire('Ambulance ' + new_status, '', 'success');
                        loadAmbulanceDataTable();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: data.msg,
                            showCancelButton: true,
                            confirmButtonText: data.info,
                            cancelButtonText: 'Cancel'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Redirect to unassign shift URL
                                window.location.href = data.url;
                            }
                        });
                    }
                }
            });
            e.preventDefault();
        });

    });

    $('.brand-init').on('click', '.delete-ambulance', function(e) {
        var id = $(this).attr('data-id');
        Swal.fire({
            title: 'Are you sure, you want to delete this ambulance?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!'
        }).then(function(result) {
            if (result.value) {
                $.ajax({
                    url: root_url + '/ambulance/delete-ambulance',
                    data: {
                        'id': id
                    },
                    method: "GET",
                    success: function(data) {
                        console.log(data);
                        if (data.success) {
                            Swal.fire('Deleted!', 'Ambulance has been deleted.',
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

    $(document).on("click", function(event) {
        var overlay = $("#filterAmbulance");
        if (!overlay.is(event.target) && overlay.has(event.target).length === 0) {
            //resetValues();
        }
    });

    function resetValues() {
        $('#district_id').val(0).trigger("change");
        $('#status').val(0).trigger("change");
        $('#chassis_no').val(0).trigger("change");
        $('#inaugration_date').val(0).trigger("change");
        $('#filterAmbulanceForm')[0].reset();
        $('#filterAmbulanceForm').parsley().reset();
    }

    function loadAmbulanceDataTable() {
        $('.brand-init').DataTable().destroy();
        var items = [
            '#district_id',
            '#status',
            '#inaugration_date',
            '#chassis_no'
        ];
        var dt = '';
        dt = new CustomDataTable({
            tableElem: '.brand-init',
            option: {
                processing: true,
                serverSide: true,
                ajax: {
                    type: "GET",
                    url: "{{ url('/ambulance/get-all-ambulances') }}",
                },
                columns: [{
                        "class": "nk-tb-col tb-col-lg",
                        data: 'ambulance_no',
                        name: 'ambulance_no',
                        render: function(data, type, row) {
                            var ambulance_no = row['ambulance_no'];

                            return ambulance_no || 'NA';
                        },
                        orderable: false
                    },
                    {
                        "class": "nk-tb-col tb-col-lg",
                        data: 'chassis_no',
                        name: 'chassis_no',
                        render: function(data, type, row) {
                            var chassis_no = row['chassis_no'];

                            return chassis_no || 'NA';
                        },
                        orderable: false
                    }, {
                        "class": "nk-tb-col tb-col-lg",
                        data: 'district_name',
                        name: 'district_name',
                        render: function(data, type, row) {
                            var district_name = row['district_name'];

                            return district_name || 'NA';
                        },
                        orderable: false
                    },
                    {
                        "class": "nk-tb-col tb-col-lg",
                        data: 'inauguration_date',
                        name: 'inauguration_date',
                        orderable: true,
                        render: function(data, type, row, meta) {
                            // Assuming 'data' contains a date in ISO format, e.g., '2023-09-22T12:34:56'
                            var dateObj = new Date(data);
                            var formattedDate = ('0' + dateObj.getDate()).slice(
                                    -2) + '/' + (
                                    '0' + (dateObj.getMonth() + 1)).slice(-2) +
                                '/' + dateObj
                                .getFullYear().toString();
                            return formattedDate;
                        }
                    },
                    {
                        "class": "nk-tb-col tb-col-lg",
                        data: 'status',
                        name: 'status',
                        render: function(data, type, row) {
                            var status = row['status'];

                            return status || 'NA';
                        },
                        orderable: false,
                    },
                    {
                        "class": "nk-tb-col tb-col-lg text-right nk-tb-col-tools",
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
            },
            filterSubmit: '.submitBtnFilter',
            filterSubmitCallback: function() {
                // resetValues();
                NioApp.Toggle.collapseDrawer('filterAmbulance')
            },
            filterClearSubmit: '.resetFilter',
            filterModalId: '#filterAmbulance',
            filterItems: items,
            tagId: '#filter_tag_list',
        });
    }
</script>
@endpush