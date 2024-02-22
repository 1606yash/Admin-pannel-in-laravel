@extends('layouts.app')
<meta name="csrf-token" content="{{ csrf_token() }}" />
@section('content')
<a class="btn" style="font-size: 18px; margin-bottom: 10px;" href="javascript:history.back()"><em class='icon ni ni-arrow-left'></em> Back</a>

<div class="container">
    <form role="form" method="post" id="addCaseForm" enctype="multipart/form-data">
        @csrf
        <div class="card">
            <div class="card-header">
                <h5>Create Case</h5>
            </div>
            <div class="card-body">
                <div class="form-row">
                    <div class="form-group col-md-1">
                    </div>
                    <div class="form-group col-md-2">
                        <label for="requestor_name">Requestor Name<span class="text-danger">*</span></label>
                    </div>
                    <div class="form-group col-md-2">
                        <input type="text" class="form-control" id="requestor_name" name="requestor_name" placeholder="Enter Requestor Name" onkeypress="return isCharSpace(event)" data-parsley-excluded="true">
                    </div>
                    <div class="form-group col-md-2">
                    </div>
                    <div class="form-group col-md-2">
                        <label for="priority">Mobile Number<span class="text-danger">*</span></label>
                    </div>

                    <div class="form-group col-md-2 form-control-required-wrap">
                        <input type="text" class="form-control" id="phone_no" name="phone_no" placeholder="Enter Mobile Number" onkeypress="return isNumber(event)" data-parsley-excluded="true">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-1">
                    </div>
                    <div class="form-group col-md-2">
                        <label for="district_id">District<span class="text-danger">*</span></label>
                    </div>
                    <div class="form-group col-md-2 form-control-required-wrap">
                        <select name="district_id" id="district_id" class="form-control form-select" placeholder='Select District' data-search="on" data-parsley-excluded="true">
                            <option selected disabled>Select District</option>
                            @if(!empty($districts))
                            @foreach ($districts as $key => $district)
                            <option value="{{ $district->id }}">
                                {{ $district->district_name }}
                            </option>
                            @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="form-group col-md-2">
                    </div>
                    <div class="form-group col-md-2">
                        <label for="service_area_id" class="mr-2">Service Area<span class="text-danger">*</span></label>
                    </div>

                    <div class="form-group col-md-2 form-control-required-wrap">
                        <select name="service_area_id" id="service_area_id" class="form-control form-select" placeholder='Select Service Area' data-search="on" data-parsley-excluded="true" disabled>

                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-1">
                    </div>
                    <div class="form-group col-md-10">
                        <label for="pickup_location">Pickup Location<span class="text-danger">*</span></label>
                        <textarea class="form-control" id="pickup_location" name="pickup_location" rows="4" placeholder="Enter Pickup Location Here ..." data-parsley-excluded="true"></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-3">
            <div class="form-group text-right my-2">
                <a class="btn btn-outline-light" href="javascript:history.back()">Cancel</a>
                <a class="btn btn-danger resetBtn" href="" style="color:#fff">Reset</a>
                <button class="btn btn-primary submitBtn" name="submit"><span>Submit</span></button>
            </div>
        </div>
    </form>
</div>

<div class="modal fade zoom" tabindex="-1" id="caseInfo">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">New Request Generated!</h5>
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
<script>
    $("#district_id").change(function() {
        $("#addCaseForm").validate().element("#district_id");
    });

    $('#district_id').on('change', function() {
        var selectedDistrictId = $(this).val();
        $('#service_area_id').attr('disabled', false);
        $.ajax({
            url: "{{ url('/human-resource/employees/get-service-area-by-district-id') }}",
            type: 'GET',
            data: {
                district_id: selectedDistrictId,
            },
            success: function(response) {
                $('#service_area_id').empty();
                $('#service_area_id').append('<option selected disabled>Select Service Area</option>');
                for (var i = 0; i < response.serviceArea.length; i++) {
                    var serviceArea = '<option value="' + response.serviceArea[i].id + '">';
                    serviceArea += response.serviceArea[i].service_area;
                    serviceArea += '</option>';

                    $('#service_area_id').append(serviceArea);
                }
            },
        });
    });

    if ($("#addCaseForm").length > 0) {
        $("#addCaseForm").validate({
            rules: {
                requestor_name: {
                    required: true,
                    maxlength: 50
                },
                phone_no: {
                    required: true,
                    maxlength: 10,
                    minlength: 10,
                },
                district_id: {
                    required: true,
                },
                pickup_location: {
                    required: true,
                },
            },
            messages: {
                requestor_name: {
                    required: "Please enter requestor name",
                    maxlength: "Your requestor name maxlength should not exceed more than 50 characters long"
                },
                phone_no: {
                    required: "Please enter mobile number",
                    maxlength: "Your mobile number should not exceed more than 10 digits",
                    minlength: "Your mobile number should not be less than 10 digits"
                },
                district_id: {
                    required: "Please select district ",
                },
                pickup_location: {
                    required: "Please enter pickup location",
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
                    url: "{{ url('call-center/store-case') }}",
                    data: new FormData($('#addCaseForm')[0]),
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        $(".submitBtn").attr("disabled", false);
                        if (response.status == 'success') {
                            form.reset();
                            toastr.success(response.message);
                            $('#requestId').text(response.case_details.id);
                            $('#requestorName').text(response.case_details.requester_name);
                            $('#mobileNumber').text(response.case_details.mobile_number);
                            $('#district').text(response.case_details.district_name);
                            $('#serviceArea').text(response.case_details.service_area);
                            $('#pickupLocation').text(response.case_details.pickup_address);
                            $('#assignDriver').attr('href', "{{ url('call-center/get-case-details/') }}/" + response.case_details.id);
                            $('#caseInfo').modal('show');

                        } else {
                            toastr.error(response.message);
                        }
                    }
                });
                $(".submitBtn").attr("disabled", false);
            }
        });
    }
</script>
@endpush