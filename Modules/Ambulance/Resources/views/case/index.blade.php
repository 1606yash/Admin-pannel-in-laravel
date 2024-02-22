@extends('layouts.app')
<meta name="csrf-token" content="{{ csrf_token() }}" />
@section('content')
<div class="nk-block-head nk-block-head-sm">
    <div class="nk-block-between">
        <div class="nk-block-head-content">
            <h3 class="nk-block-title page-title">Call Center</h3>
        </div><!-- .nk-block-head-content -->
        <div class="nk-block-head-content">
            <div class="toggle-wrap nk-block-tools-toggle">
                <a href="#" class="btn btn-icon btn-trigger toggle-expand mr-n1" data-target="more-options"><em class="icon ni ni-more-v"></em></a>
                <div class="toggle-expand-content" data-content="more-options">
                    <ul class="nk-block-tools g-3">
                        <li class="nav-item">
                            <a href="#" class="btn btn-trigger btn-icon toggle" data-target="filterCase">
                                <div class="dot dot-primary"></div>
                                <em class="icon ni ni-filter-alt"></em>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link btn btn-primary mr-2" href="{{ url('/call-center/create-case/') }}"><em class="icon ni ni-plus" style="color: #fff;"></em><span>Add Case</span></a>
                        </li>

                    </ul>
                </div>
            </div>
        </div>
    </div><!-- .nk-block-between -->
    <div class="container justify-content-center mx-auto" style="margin: 10px;padding:15px;">
        <div class="card-deck">
            <div class="card" style="margin: 0 10px 20px 0;box-sizing: border-box;float: left;width: calc(100% / 3 - 20px); ">
                <div class="card-body" style="text-align: center;">
                    <div class="card-title">Total Cases</div>
                    <div style="font-weight: bold; text-align: center;">{{$counts['total_cases'] ?? ''}}</div>
                </div>
            </div>
            <div class="card" style="margin: 0 10px 20px 0;box-sizing: border-box; float: left;width: calc(100% / 3 - 20px);">
                <div class="card-body" style="text-align: center;">
                    <div class="card-title">Pending Cases</div>
                    <div style="font-weight: bold; text-align: center;">{{$counts['pending_cases'] ?? ''}}</div>
                </div>
            </div>
            <div class="card" style="margin: 0 10px 20px 0; box-sizing: border-box;float: left;width: calc(100% / 3 - 20px);">
                <div class="card-body" style="text-align: center;">
                    <div class="card-title">Active Cases</div>
                    <div style="font-weight: bold; text-align: center;">{{$counts['active_cases'] ?? ''}}</div>
                </div>
            </div>
            <div class="card" style="margin: 0 10px 20px 0;box-sizing: border-box;float: left;width: calc(100% / 3 - 20px); ">
                <div class="card-body" style="text-align: center;">
                    <div class="card-title">Completed Cases</div>
                    <div style="font-weight: bold; text-align: center;">{{$counts['completed_cases'] ?? ''}}</div>
                </div>
            </div>
            <div class="card" style="margin: 0 10px 20px 0; box-sizing: border-box;float: left;width: calc(100% / 3 - 20px);">
                <div class="card-body" style="text-align: center;">
                    <div class="card-title">Cancelled Cases</div>
                    <div style="font-weight: bold; text-align: center;">{{$counts['cancelled_cases'] ?? ''}}</div>
                </div>
            </div>
        </div>
    </div>
</div><!-- .nk-block-head -->
<!--  Filter Tag List -->
<div id="filter_tag_list" class="filter-tag-list"></div>
<div class="nk-block table-compact">
    <div class="nk-tb-list is-separate mb-3">
        <table id="brand_init" class="brand-init nowrap nk-tb-list is-separate" data-auto-responsive="false">
            <thead>
                <tr class="nk-tb-item nk-tb-head">
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Request ID</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Date</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">District</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Ambulance No.</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Service Area</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Requestor</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Mobile Number</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Pickup</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Drop</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Case Status</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Request Status</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Action</span></th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div><!-- .nk-tb-list -->

</div><!-- .nk-block -->
<div class="nk-add-product toggle-slide toggle-slide-right" tabindex="-1" data-content="filterCase" data-toggle-screen="any" data-toggle-overlay="true" data-toggle-body="true" data-simplebar id="filterCase">
    <form action="#" role="form" class="mb-0" method="get" id="caseFilterForm">
        @csrf
        <div class="nk-block-head">
            <div class="nk-block-head-content">
                <h5 class="nk-block-title modalTitle">Case Filter</h5>
            </div>
        </div>
        <div class="nk-block">
            <div class="row g-3">
                <div class="col-mb-12">
                    <x-inputs.select size="md" name="vehicle_id" for="vehicle_id" id="vehicle_id" data-search="on" placeholder='Select Vehicle Number'>
                        <option value="" selected disabled>Select Vehicle Number</option>
                        @if(!empty($ambulances))
                        @foreach($ambulances as $key => $ambulance)
                        <option value="{{$ambulance->id}}">{{$ambulance->ambulance_no ?? $ambulance->chassis_no }}</option>
                        @endforeach
                        @endif
                    </x-inputs.select>
                </div>

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

                <div class="col-mb-12">
                    <x-inputs.select size="md" name="creation_date" for="creation_date" id="creation_date" data-search="on" placeholder='Select Creation Date'>
                        <option value="" selected disabled>Select Creation Date</option>
                        <option value="LastThreeMonth">Last Three Months</option>
                        <option value="LastSixMonth">Last Six Months</option>
                        <option value="CurrentYear">Current Year</option>
                        <option value="LastYear">Last Year</option>
                        <option value="LastThreeYear">Last Three Year</option>
                    </x-inputs.select>
                    </select>
                </div>

                <div class="col-mb-12">
                    <x-inputs.select size="md" name="case_status" for="case_status" id="case_status" data-search="on" placeholder='Select Case Status'>
                        <option value="" selected disabled>Select Case Status</option>
                        <option value="pending">Pending</option>
                        <option value="ongoing">Ongoing</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </x-inputs.select>
                </div>

                <div class="col-mb-12">
                    <x-inputs.select size="md" name="req_status" for="req_status" id="req_status" data-search="on" placeholder='Select Request Status'>
                        <option value="" selected disabled>Select Request Status</option>
                        <option value="pending">Pending</option>
                        <option value="accepted">Accepted</option>
                        <option value="rejected">Rejected</option>
                    </x-inputs.select>
                </div>
            </div>
            <div class="col-12 text-center mt-5">
                <a data-target='filterCase' class="btn btn-outline-light cancel">Cancel</a>
                <a class="btn btn-danger resetFilter" data-target='filterCase' style="color:#fff">Clear Filter</a>
                <a class="btn btn-primary submitBtnFilter" style="color:#fff">Submit</a>
            </div>
        </div>
    </form>
</div>

<div class="modal fade zoom" tabindex="-1" id="caseInfo">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Request Info</h5>
                <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
            </div>
            <div class="modal-body">
                <div class="form-group justify-content-center">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="form-label" for="requestId">Request ID:</label>
                        </div>
                        <div class="form-group col-md-6">
                            <span id="requestId"></span>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="form-label" for="requestorName">Requestor Name:</label>
                        </div>
                        <div class="form-group col-md-6">
                            <span id="requestorName"></span>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="form-label" for="mobileNumber">Mobile Number:</label>
                        </div>
                        <div class="form-group col-md-6">
                            <span id="mobileNumber"></span>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="form-label" for="district">District:</label>
                        </div>
                        <div class="form-group col-md-6">
                            <span id="district"></span>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="form-label" for="serviceArea">Service Area:</label>
                        </div>
                        <div class="form-group col-md-6">
                            <span id="serviceArea"></span>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="form-label" for="pickupLocation">Pickup Location:</label>
                        </div>
                        <div class="form-group col-md-6">
                            <span id="pickupLocation"></span>
                        </div>
                    </div>
                    <div class="form-row justify-content-center">
                        <div class="form-group text-center my-2">
                            <a class="btn btn-primary" href="#" id="assignDriver">Assign Driver</a>
                        </div>
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
    $(document).ready(function() {
        loadAllCasesDataTable();
    });

    function loadAllCasesDataTable() {
        // alert('hi');
        $('.brand-init').DataTable().destroy();
        var items = [
            '#district_id',
            '#case_status',
            '#creation_date',
            '#vehicle_id',
            '#req_status'
        ];
        var dt = '';
        dt = new CustomDataTable({
            tableElem: '.brand-init',
            option: {
                processing: true,
                serverSide: true,
                ajax: {
                    type: "GET",
                    url: "{{ url('/call-center/get-all-cases') }}",
                },
                columns: [{
                        "class": "nk-tb-col tb-col-lg",
                        data: 'id',
                        name: 'id',
                        orderable: false
                    },
                    {
                        "class": "nk-tb-col tb-col-lg",
                        data: 'case_date',
                        name: 'case_date',
                        orderable: false
                    }, {
                        "class": "nk-tb-col tb-col-lg",
                        data: 'district_name',
                        name: 'district_name',
                        orderable: false
                    },
                    {
                        "class": "nk-tb-col tb-col-lg",
                        data: 'vechicle_no',
                        name: 'vechicle_no',
                        orderable: false
                    },
                    {
                        "class": "nk-tb-col tb-col-lg",
                        data: 'service_area',
                        name: 'service_area',
                        orderable: false
                    },
                    {
                        "class": "nk-tb-col tb-col-lg",
                        data: 'requester_name',
                        name: 'requester_name',
                        orderable: false
                    },
                    {
                        "class": "nk-tb-col tb-col-lg",
                        data: 'mobile_number',
                        name: 'mobile_number',
                        orderable: false
                    },
                    {
                        "class": "nk-tb-col tb-col-lg",
                        data: 'pickup_address',
                        name: 'pickup_address',
                        orderable: false
                    },
                    {
                        "class": "nk-tb-col tb-col-lg",
                        data: 'drop_address',
                        name: 'drop_address',
                        orderable: false
                    },
                    {
                        "class": "nk-tb-col tb-col-lg",
                        data: 'case_status',
                        name: 'case_status',
                        orderable: false
                    },
                    {
                        "class": "nk-tb-col tb-col-lg",
                        data: 'request_status',
                        name: 'request_status',
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
            },
            filterSubmit: '.submitBtnFilter',
            filterSubmitCallback: function() {
                NioApp.Toggle.collapseDrawer('filterCase')
            },
            filterClearSubmit: '.resetFilter',
            filterModalId: '#filterCase',
            filterItems: items,
            tagId: '#filter_tag_list',
        });
    }

    function resetForm() {
        $('#vehicle_id').val(0).trigger("change");
        $('#district_id').val(0).trigger("change");
        $('#case_status').val(0).trigger("change");
        $('#creation_date').val(0).trigger("change");
    }

    $(document).on("click", function(event) {
        var overlay = $("#filterCase");
        if (!overlay.is(event.target) && overlay.has(event.target).length === 0) {
            //resetForm();
        }
    });

    $('.cancel').on('click', function() {
        NioApp.Toggle.collapseDrawer('filterCase')
    });

    function getCaseInfo(caseId) {
        $.ajax({
            url: "{{ url('/call-center/get-case-info') }}",
            type: 'GET',
            dataType: 'json',
            data: {
                case_id: caseId
            },
            success: function(response) {
                if (response.status == 'success') {
                    $('#requestId').text(response.case_details.id);
                    $('#requestorName').text(response.case_details.requester_name);
                    $('#mobileNumber').text(response.case_details.mobile_number);
                    $('#district').text(response.case_details.district_name);
                    $('#serviceArea').text(response.case_details.service_area);
                    $('#pickupLocation').text(response.case_details.pickup_address);
                    if (response.case_details.request_status == 'pending') {
                        $('#assignDriver').text('Assign Driver')
                    } else {
                        $('#assignDriver').text('Update')
                    }
                    $('#assignDriver').attr('href', "{{ url('call-center/get-case-details/') }}/" + response.case_details.id);
                    $('#caseInfo').modal('show');
                } else {
                    toastr.error(response.message);
                }
            }
        });
    }
</script>
@endpush