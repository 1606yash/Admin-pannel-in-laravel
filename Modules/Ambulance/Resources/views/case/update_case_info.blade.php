@extends('layouts.app')
<meta name="csrf-token" content="{{ csrf_token() }}" />
@section('content')
<a class="btn" style="font-size: 18px; margin-bottom: 10px;" href="javascript:history.back()"><em class='icon ni ni-arrow-left'></em> Back</a>
@if($caseDetails)
@if($caseDetails->case_status != 'pending')
<span style="float: right;">
    <button type="button" class="btn btn-primary mb-1 edit_case" id="updateBtn" style="height: 30px;width: 120px">Update Info</button>
</span>
@endif
<div class="container">
    <div class="card">
        <div class="card-header">
            <h5 id="caseStatus">{{ ucfirst($caseDetails->case_status) ?? '' }} Case</h5>
        </div>
        <div class="card-body">
            <form role="form" method="post" id="editCaseDetailsForm" enctype="multipart/form-data">
                <fieldset class="border rounded-3 p-3 card  mt-3 ">
                    <legend class="float-none w-auto px-3">
                        <h5>Request Information</h5>
                    </legend>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label class="font-weight-bold">Request ID:</label>
                            </div>
                            <div class="form-group col-md-3">
                                <span>{{ $caseDetails->id ?? '' }}</span>
                                <input type="hidden" name="request_id" id="request_id" value="{{ $caseDetails->id ?? '' }}">
                            </div>

                            <div class="form-group col-md-3">
                                <label class="font-weight-bold">Requestor Name:</label>
                            </div>
                            <div class="form-group col-md-3">
                                <span class="not-editable">{{ ucwords($caseDetails->requester_name) ?? '' }}</span>
                                <input type="text" class="form-control editable d-none" name="requestor_name" id="requestor_name" placeholder="Enter Requestor Name" value="{{ ucwords($caseDetails->requester_name) ?? '' }}">
                            </div>
                        </div>

                        <div class="form-row">

                            <div class="form-group col-md-3">
                                <label class="font-weight-bold">Mobile Number:</label>
                            </div>
                            <div class="form-group col-md-3">
                                <span class="not-editable">{{ $caseDetails->mobile_number ?? '' }}</span>
                                <input type="text" class="form-control editable d-none" name="mobile_mumber" id="mobile_mumber" placeholder="Enter Mobile Number" value="{{ $caseDetails->mobile_number ?? '' }}">
                            </div>

                            <div class="form-group col-md-3">
                                <label class="font-weight-bold">Relation:</label>
                            </div>
                            <div class="form-group col-md-3">
                                <span class="not-editable">{{ $caseDetails->relation ?? '' }}</span>
                                <input type="text" class="form-control editable d-none" name="relation" id="relation" placeholder="Enter Pick Up Location" value="{{ $caseDetails->relation ?? '' }}">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label class="font-weight-bold">Case Date:</label>
                            </div>
                            <div class="form-group col-md-3">
                                <span class="not-editable">{{ $caseDetails->case_date ?? '' }}</span>
                                <input type="text" class="form-control editable d-none" name="case_date" id="case_date" placeholder="Select Case Date" value="{{ $caseDetails->case_date ?? '' }}" data-parsley-excluded="true" readonly>
                            </div>
                        </div>
                    </div>
                </fieldset>
                <fieldset class="border rounded-3 p-3 card  mt-3 ">
                    <legend class="float-none w-auto px-3">
                        <h5>Patient Information</h5>
                    </legend>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label class="font-weight-bold">Patient Name:</label>
                            </div>
                            <div class="form-group col-md-3">
                                <span class="not-editable">{{ ucwords($caseDetails->patient_name) ?? '' }}</span>
                                <input type="text" class="form-control editable d-none" name="patient_name" id="patient_name" placeholder="Enter Patient Name" value="{{ ucwords($caseDetails->patient_name) ?? '' }}">
                            </div>

                            <div class="form-group col-md-3">
                                <label class="font-weight-bold">Age:</label>
                            </div>
                            <div class="form-group col-md-3">
                                <span class="not-editable">{{ $caseDetails->age ?? '' }}</span>
                                <input type="text" class="form-control editable d-none" name="patient_age" id="patient_age" placeholder="Enter Patient Age" value="{{ $caseDetails->age ?? '' }}">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label class="font-weight-bold">Gender:</label>
                            </div>
                            <div class="form-group col-md-3">
                                <span class="not-editable">{{ $caseDetails->gender ?? '' }}</span>
                                <div class="editable d-none">
                                    <select name="patient_gender" id="patient_gender" class="form-control form-select" data-search="on" placeholder='Select Gender'>
                                        <option value="" disabled>Select Gender</option>
                                        <option value="Male" @if($caseDetails->gender == 'Male') selected @endif>Male</option>
                                        <option value="Female" @if($caseDetails->gender == 'Female') selected @endif>Female</option>
                                        <option value="Others" @if($caseDetails->gender == 'Others') selected @endif>Other</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group col-md-3">
                                <label class="font-weight-bold">Reason:</label>
                            </div>
                            <div class="form-group col-md-3">
                                <span class="not-editable">{{ ucfirst($caseDetails->reason) ?? '' }}</span>
                                <input type="text" class="form-control editable d-none" name="reason" id="reason" placeholder="Enter Reason" value="{{ ucfirst($caseDetails->reason) ?? '' }}">
                            </div>
                        </div>
                    </div>
                </fieldset>
                <fieldset class="border rounded-3 p-3 card  mt-3 ">
                    <legend class="float-none w-auto px-3">
                        <h5>Pick Up Location</h5>
                    </legend>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label class="font-weight-bold">Pick Up Address:</label>
                            </div>
                            <div class="form-group col-md-3">
                                <span class="not-editable">{{ ucfirst($caseDetails->pickup_address) ?? '' }}</span>
                                <input type="text" class="form-control editable d-none" name="pickup_address" id="pickup_address" placeholder="Enter Pick Up Address" value="{{ $caseDetails->pickup_address ?? '' }}">
                            </div>

                            <div class="form-group col-md-3">
                                <label class="font-weight-bold">Map Location:</label>
                            </div>
                            <div class="form-group col-md-3">
                                <span class="not-editable"></span>

                            </div>
                        </div>
                    </div>
                </fieldset>
                <fieldset class="border rounded-3 p-3 card  mt-3 ">
                    <legend class="float-none w-auto px-3">
                        <h5>Drop Location</h5>
                    </legend>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label class="font-weight-bold">Drop Address:</label>
                            </div>
                            <div class="form-group col-md-3">
                                <span class="not-editable">{{ ucfirst($caseDetails->drop_address) ?? '' }}</span>
                                <input type="text" class="form-control editable d-none" name="drop_address" id="drop_address" placeholder="Enter Drop Address" value="{{ ucfirst($caseDetails->drop_address) ?? '' }}">
                            </div>

                            <div class="form-group col-md-3">
                                <label class="font-weight-bold">Map Location:</label>
                            </div>
                            <div class="form-group col-md-3">
                                <span class="not-editable"></span>

                            </div>
                        </div>
                    </div>
                </fieldset>

                <fieldset class="border rounded-3 p-3 card mt-3 ">
                    <legend class="float-none w-auto px-3">
                        <h5>Ambulance Information</h5>
                    </legend>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label class="font-weight-bold">District:</label>
                            </div>
                            <div class="form-group col-md-3">
                                <span>{{ ucfirst($caseDetails->district_name) ?? '' }}</span>
                                <input type="hidden" name="district_id" id="district_id" value="{{ $caseDetails->district_id ?? '' }}">
                            </div>

                            <div class="form-group col-md-3">
                                <label class="font-weight-bold">Service Area:</label>
                            </div>
                            <div class="form-group col-md-3">
                                <span>{{ ucfirst($caseDetails->service_area) ?? '' }}</span>
                                <input type="hidden" name="service_area_id" id="service_area_id" value="{{ $caseDetails->service_area_id ?? '' }}">
                            </div>
                        </div>

                        <div class=" form-row">
                            <div class="form-group col-md-3">
                                <label class="font-weight-bold">Ambulance Number:</label>
                            </div>
                            <div class="form-group col-md-3">
                                @if($caseDetails->case_status != 'pending')
                                <span @if($caseDetails->case_status != 'pending') class="not-editable" @endif>
                                    {{ ucfirst($caseDetails->ambulance_no ?: $caseDetails->chassis_no ?: 'NA') }}
                                </span>
                                @endif
                                <div @if($caseDetails->case_status != 'pending') class="d-none editable" @endif>
                                    <select name="ambulance_id" id="ambulance_id" class="form-control form-select" placeholder='Select Ambulance' data-search="on" data-parsley-excluded="true">

                                    </select>
                                </div>
                            </div>
                            @if($caseDetails->case_status == 'pending' && $caseDetails->request_status == 'pending')
                            <div class="form-group col-md-3"></div>
                            <div class="form-group col-md-3">
                                <button class="btn btn-primary mb-1" style="height: 30px;" type="button" id="assignAmbulance" style="height: 30px;width: 120px">Assign</button>
                            </div>
                            @endif
                        </div>
                    </div>
                </fieldset>

                <div style="float: right;margin-top: 10px">
                    <button class="btn btn-primary mb-1 d-none" style="height: 30px;" type="button" id="updateCase" style="height: 30px;width: 120px">Update</button>
                </div>
            </form>
        </div>
    </div>

    <!-- map html starts -->
    <div class="card">
        <div id="map" style="width: 100%;height: 600px;"></div>
    </div>
    <!-- map html ends -->
</div>

<div class="modal fade" id="ambulanceModal" tabindex="-1" role="dialog" aria-labelledby="ambulanceModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ambulanceModalLabel">Ambulance Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="ambulanceDetails"></div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
@push('footerScripts')
<script>
    $('#updateBtn').on('click', function() {
        $('.editable').removeClass('d-none');
        $('.not-editable').addClass('d-none');
        $('#updateBtn').addClass('d-none');
        $('#updateCase').removeClass('d-none');
    });

    $('#updateCase').on('click', function(e) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // disabled submit button during process running status
        $("#updateCase").attr("disabled", true);

        $.ajax({
            type: "POST",
            url: "{{ url('call-center/update-case-info') }}",
            data: new FormData($('#editCaseDetailsForm')[0]),
            contentType: false,
            processData: false,
            success: function(response) {
                $("#updateCase").attr("disabled", false);
                if (response.status == 'success') {
                    toastr.success(response.message);
                    window.location.reload();
                } else {
                    toastr.error(response.message);
                }
            }
        });
        $("#updateCase").attr("disabled", false);
    });

    // Trigger change event for #district_id after page loads
    $(document).ready(function() {
        getAmbulance();
        getDrivers();
    });


    // get ambulance according to district id
    function getAmbulance() {
        var selectedDistrictId = $('#district_id').val();
        $.ajax({
            url: "{{ url('/human-resource/employees/get-ambulance-by-district-id') }}",
            type: 'GET',
            data: {
                district_id: selectedDistrictId,
            },
            success: function(response) {
                // Clear existing options
                $('#ambulance_id').empty();

                // Add a default option
                $('#ambulance_id').append('<option selected disabled>Select Ambulance Number</option>');

                // Add new options
                for (var i = 0; i < response.ambulance.length; i++) {
                    var ambulanceOption = '<option value="' + response.ambulance[i].id + '" ' +
                        ((response.ambulance[i].id == "{{$caseDetails->ambulance_id ?? 0}}") ? 'selected' : '') + '>';

                    // Check if ambulance_no is empty, use chassis_no as an alternative
                    if (response.ambulance[i].ambulance_no !== null && response.ambulance[i].ambulance_no !== "") {
                        ambulanceOption += response.ambulance[i].ambulance_no;
                    } else if (response.ambulance[i].chassis_no !== null && response.ambulance[i].chassis_no !== "") {
                        ambulanceOption += response.ambulance[i].chassis_no + ' (Chassis No)';
                    } else {
                        ambulanceOption += 'N/A';
                    }
                    ambulanceOption += '</option>';
                    $('#ambulance_id').append(ambulanceOption);
                }
            },
        });
    }

    function getDrivers() {
        var caseId = $("#request_id").val();
        var serviceAreaId = $('#service_area_id').val();
        var caseDate = $('#case_date').val();
        var districtId = $('#district_id').val();
        $.ajax({
            url: "{{ url('/call-center/get-drivers') }}",
            type: 'GET',
            data: {
                case_id: caseId,
                service_area_id: serviceAreaId,
                case_date: caseDate,
                district_id: districtId
            },
            success: function(response) {

                // Add new options
                for (var i = 0; i < response.availableDrivers.length; i++) {
                    showAmbulanceMarkers(response.availableDrivers);
                }
            },
        });
    }

    $('#assignAmbulance').on('click', function(e) {
        var caseId = $("#request_id").val();
        var district_id = $("#district_id").val();
        var service_area_id = $("#service_area_id").val();
        var ambulance_id = $("#ambulance_id").val();
        var caseDate = $("#case_date").val();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // disabled submit button during process running status
        $("#assignAmbulance").attr("disabled", true);

        $.ajax({
            type: "POST",
            url: "{{ url('call-center/assign-driver') }}",
            dataType: 'json',
            data: {
                case_id: caseId,
                district_id: district_id,
                service_area_id: service_area_id,
                ambulance_id: ambulance_id,
                case_date: caseDate
            },
            success: function(response) {
                $("#assignAmbulance").attr("disabled", false);
                if (response.status == 'success') {
                    toastr.success(response.message);
                    window.location.reload();
                } else {
                    toastr.error(response.message);
                }
            }
        });
        $("#assignAmbulance").attr("disabled", false);
    });

    $("#case_date").datepicker({
        todayBtn: true,
        orientation: "auto",
        todayHighlight: true,
        autoclose: true,
        format: "dd/mm/yyyy",
        startView: "days",
        endDate: "today"
    });
    $('#case_date').each(function() {
        $(this).on('changeDate', function(e) {
            case_date = $(this).datepicker('getDate');
            $('#case_date').datepicker('update', case_date);
        });
    });
</script>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD1HKR-XGhXub3vlTnDO9P3BcrIZArDohA&callback=initializeMap" async defer></script>
<script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
<script>
    // Define map as a global variable
    let map;
    let ambulanceMarkers = [];

    // Define an initialization function that will be called when the Google Maps API is loaded
    function initializeMap() {
        // Get coordinates from PHP (assuming $caseDetails is available in your view)
        var pickupLat = "{{ $caseDetails->pickup_latitude ?? 0 }}";
        var pickupLng = "{{ $caseDetails->pickup_longitude ?? 0 }}";
        var dropLat = "{{ $caseDetails->drop_latitude ?? 0 }}";
        var dropLng = "{{ $caseDetails->drop_longitude ?? 0 }}";

        // Call the function with pickup coordinates
        myMap(pickupLat, pickupLng);

        // Check if the request status is 'completed'
        if ("{{ $caseDetails->case_status }}" === 'completed') {
            // Call the function with drop coordinates
            markDropLocation(dropLat, dropLng);
        }
    }

    function myMap(lat, long) {
        var indiaCenter = new google.maps.LatLng(20.5937, 78.9629);
        var mapProp = {
            center: indiaCenter,
            zoom: 5, // Adjust this zoom level as needed
           // mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        // Initialize the map variable
        map = new google.maps.Map(document.getElementById("map"), mapProp);

        //if ("{{ $caseDetails->case_status }}" !== 'pending') {
        const marker = new google.maps.Marker({
            position: new google.maps.LatLng(parseFloat(lat), parseFloat(long)),
            map: map
        });

        var infowindow = new google.maps.InfoWindow({
            content: "Pickup Location"
        });
        infowindow.open(map, marker);
        //}
    }

    function markDropLocation(lat, long) {
        // Create a new marker for drop location
        const dropMarker = new google.maps.Marker({
            position: new google.maps.LatLng(parseFloat(lat), parseFloat(long)),
            map: map, // Use the global map variable

        });

        var dropInfowindow = new google.maps.InfoWindow({
            content: "Drop Location"
        });
        dropInfowindow.open(map, dropMarker);
    }

    function showAmbulanceMarkers(ambulances) {
        // Clear existing markers
        ambulanceMarkers.forEach(marker => marker.setMap(null));
        ambulanceMarkers = [];

        // Add new ambulance markers
        ambulances.forEach(ambulance => {
            const marker = new google.maps.Marker({
                position: new google.maps.LatLng(parseFloat(ambulance.login_latitude), parseFloat(ambulance.login_longitude)),
                map: map,
                icon: getAmbulanceIcon(ambulance.status), // Set icon based on status
            });

            // Attach click event to each marker
            marker.addListener('click', function() {
                showAmbulanceDetails(ambulance); // Display modal on marker click
            });

            ambulanceMarkers.push(marker);
        });
    }

    function getAmbulanceIcon(status) {
        return status === 'available' ? '{{url("images/green-ambulance.gif")}}' : '{{url("images/red-ambulance.gif")}}';
    }

    function showAmbulanceDetails(ambulance) {
        // Build HTML content for ambulance details
        var ambulanceDetailsHTML = `
            <p><strong>Ambulance Number:</strong> ${ambulance.ambulance_no ? ambulance.ambulance_no : ambulance.chassis_no}</p>
            <p><strong>Driver Name:</strong> ${ambulance.user_name}</p>
            <p><strong>Driver Contact Number:</strong> ${ambulance.phone_no}</p>
            <p><strong>Status:</strong> ${ambulance.status}</p>
        `;

        // Set modal content and show the modal
        $('#ambulanceDetails').html(ambulanceDetailsHTML);
        $('#ambulanceModal').modal('show');
    }
</script>

@endpush