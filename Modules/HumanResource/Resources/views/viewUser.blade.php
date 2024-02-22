@extends('layouts.app')
<meta name="csrf-token" content="{{ csrf_token() }}" />
@section('content')
<style>
    .card-checkbox.custom-control-input:not(:checked)~.custom-control-label::before {
        background-color: #c8c8d3;
        /* Change the background color of the toggle container  when it is not checked */
        border-color: #ffffff;
        /* Change the border color of the toggle container when it is not checked */
    }

    .card-checkbox.custom-control-input:checked~.custom-control-label::before {
        background-color: #06D6A0;
        /* Change the color of the toggle container when it is checked */
        border-color: #ffffff;
        /* Change the border color of the toggle container when it is checked */
    }

    /* toggle knob */
    .card-checkbox.checkbox-outside-card.custom-control-input:not(:checked)~.custom-control-label::after {
        background-color: #c8c8d3;
        /* Change the background color of the knob when it is checked */
        border: #ffffff;
        /* Add a border if you prefer */
    }

    .card-checkbox.checkbox-outside-card.custom-control-input:checked~.custom-control-label::after {
        background-color: #06D6A0;
        /* Change the background color of the knob when it is checked */
        border: #ffffff;
        /* Add a border if you prefer */
    }

    .attendanceDetails tbody tr {
        background-color: white;
    }
</style>
<a class="btn" style="font-size: 18px;margin-bottom: 10px;" href="javascript:history.back()"><em class='icon ni ni-arrow-left'></em>Back</a>
<input type="hidden" name="view_user_id" id="view_user_id" value="{{ $data->user_id ?? '' }}">

@if($data)
<div class="container" id="profileDetails">
    <div class="card mt-3 mb-2">
        <div class="card-body">
            <div class="form-row">
                <div class="form-group col-md-6" style="display: flex">
                    @if($data->profile_path)
                    <span class="data-value data-value-text">
                        <img src="{{ $data->profile_path }}" width="90" height="90" class="z-depth-2 profile-view-image" />
                    </span>
                    @else
                    <span class="data-value data-value-text">
                        <img src="https://cdn4.iconfinder.com/data/icons/small-n-flat/24/user-alt-512.png" width="60" class="rounded-circle z-depth-2" />
                    </span>
                    @endif
                    <span style="margin-left: 20px">
                        <span class="user-name-text" style="font-size: 20px ;font-weight: bold">{{ ucwords($data->user_name) ?: '-'}}</span>
                        <br>
                        <span class="employee-id" style="font-size: 15px;font-weight: bold">Employee ID: {{ $data->employee_id ?: '-' }}</span>

                        <br>
                        <span class="user-role-text" style="font-size: 20px;font-weight: bold"> {{ $data->role_name ?: '-' }}</span>
                    </span>
                </div>
                <div class="col-md-6">
                    <span style="float: right;">

                        <button type="button" class="btn btn-primary mb-1 edit_user" id="updateBtn" style="height: 30px;width: 120px">Update Info</button>
                        <br>

                        <span class="active_status " style="font-weight: 500;margin-left: 10px">Profile Status
                        </span>
                        <div class="custom-control custom-switch" style="margin-left: 5px;margin-bottom:15px">
                            <input type="checkbox" class="custom-control-input card-checkbox" id="active_status" {{ $data->is_active == 1 ? 'checked' : '' }}>
                            <label class="custom-control-label" for="active_status" style="margin-top: 8px;">
                            </label>
                        </div>
                    </span>
                </div>
            </div>
        </div>
    </div>

    @if($roleSlug == 'driver' || $roleSlug == 'attendant' || $roleSlug == 'district-anchor')
    <div class="toggle-bar">
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#personal" data-tab="personal">Personal</a>
            </li>
            <li class="nav-item">
                <a class="nav-link " data-toggle="tab" href="#employement-record" data-tab="employement-record">Employement
                    Record</a>
            </li>
            <li class="nav-item">
                <a class="nav-link " data-toggle="tab" href="#salary" data-tab="salary">Salary</a>
            </li>
            <li class="nav-item">
                <a class="nav-link " data-toggle="tab" href="#attendance" data-tab="attendance">Attendance</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#leaves" data-tab="leaves">Leaves</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#tasks" data-tab="tasks">Tasks</a>
            </li>
            @if($roleSlug == 'driver' || $roleSlug == 'attendant' )
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#shift_details" data-tab="shift_details">Shift Details</a>
            </li>
            @endif
        </ul>
    </div>
    @endif

    <form role="form" method="post" id="editProfileForm" enctype="multipart/form-data">
        <div class="tab-content">
            <input type="hidden" class="form-control" id="role_id" name="role_id" value="{{ $data->role_id ?? '' }}">
            <input type="hidden" class="form-control" id="profile_user_id" name="user_id" value="{{ $data->user_id ?? '' }}">
            <input type="hidden" name="role" id="role" value="{{ $roleSlug ?? '' }}">
            <div class="tab-pane fade mt-5" id="personal">
                <fieldset class="border rounded-3 p-3 card  mt-3 ">
                    <legend class="float-none w-auto px-3">
                        <h5>Personal Information</h5>
                    </legend>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group col-md-2">
                                <label class="font-weight-bold">First Name</label>
                            </div>
                            <div class="form-group col-md-2">
                                <span class="not-editable">{{ $data->first_name ?? '' }}</span>
                                <input type="text" class="form-control edit-profile d-none" id="first_name" name="first_name" value="{{ $data->first_name ?? '' }}" placeholder="Enter First Name" onkeypress="return isCharSpace(event)" maxlength="50">
                            </div>
                            <div class="form-group col-md-2">
                                <label class="font-weight-bold">Middle Name</label>
                            </div>
                            <div class="form-group col-md-2">
                                <span class="not-editable">{{ $data->middle_name ?? '' }}</span>
                                <input type="text" class="form-control edit-profile d-none" id="middle_name" name="middle_name" value="{{ $data->middle_name ?? '' }}" placeholder="Enter Middle Name" onkeypress="return isCharSpace(event)" maxlength="50">
                            </div>
                            <div class="form-group col-md-2">
                                <label class="font-weight-bold">Last Name</label>
                            </div>
                            <div class="form-group col-md-2">
                                <span class="not-editable">{{ $data->last_name ?? '' }}</span>
                                <input type="text" class="form-control edit-profile d-none" id="last_name" name="last_name" value="{{ $data->last_name ?? '' }}" placeholder="Enter Last Name" onkeypress="return isCharSpace(event)" maxlength="50">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-2">
                                <label class="font-weight-bold">Gender</label>
                            </div>
                            <div class="form-group col-md-2">
                                <span class="not-editable">{{ ucwords($data->gender) ?? '' }}</span>
                                <div class="edit-profile d-none">
                                    <select name="gender" id="gender" class="form-control form-select" data-search="on" placeholder='Select Gender'>
                                        <option value="" disabled>Select Gender</option>
                                        <option value="male" @if($data->gender == 'male') selected @endif>Male</option>
                                        <option value="female" @if($data->gender == 'female') selected @endif>Female</option>
                                        <option value="other" @if($data->gender == 'other') selected @endif>Other</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-md-2">
                                <label class="font-weight-bold">Date of Birth</label>
                            </div>
                            <div class="form-group col-md-2">
                                <span class="not-editable">{{ $data->dob ?  date('d/m/Y', strtotime($data->dob)) : ''}}</span>
                                <input type="text" class="form-control edit-profile d-none" id="dob" name="dob" value="{{ $data->dob ?  date('d/m/Y', strtotime($data->dob)) : ''}}" readonly placeholder="Select Date of Birth">
                            </div>
                            <div class="form-group col-md-2">
                                <label class="font-weight-bold">Mobile Number</label>
                            </div>
                            <div class="form-group col-md-2">
                                <span class="not-editable">{{ $data->phone_no ?? ''}}</span>
                                <input type="text" class="form-control edit-profile d-none" id="phone_no" name="phone_no" value="{{ $data->phone_no ?? ''}}" placeholder="Enter Mobile Number" onkeypress="return isNumber(event)" maxlength="10">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-2">
                                <label class="font-weight-bold">Email ID</label>
                            </div>
                            <div class="form-group col-md-2">
                                <span class="not-editable">{{ $data->email ?? ''}}</span>
                                <input type="text" class="form-control edit-profile d-none" id="email" name="email" value="{{ $data->email ?? ''}}" placeholder="Enter Email ID " maxlength="255">
                            </div>
                            @if($roleSlug == 'driver' || $roleSlug == 'attendant' || $roleSlug == 'district-anchor')
                            <div class="form-group col-md-2">
                                <label class="font-weight-bold">PAN Number</label>
                            </div>
                            <div class="form-group col-md-2">
                                <span class="not-editable">{{ $data->pan_card_number ?? ''}}</span>
                                <input type="text" class="form-control edit-profile d-none" id="pan_no" name="pan_no" value="{{ $data->pan_card_number ?? '' }}" placeholder="Enter PAN Number" onkeypress="return onlyAlphanumerics(event)" maxlength="10">
                            </div>
                            <div class="form-group col-md-2">
                                <label class="font-weight-bold">Aadhar Number</label>
                            </div>
                            <div class="form-group col-md-2">
                                <span class="not-editable">{{ $data->adhar_number ?? ''}}</span>
                                <input type="text" class="form-control edit-profile d-none" id="aadhar_no" name="aadhar_no" value="{{ $data->adhar_number ?? ''}}" placeholder="Enter Aadhar Number" onkeypress="return isNumber(event)" maxlength="12">
                            </div>
                            @endif
                        </div>
                        @if($roleSlug == 'driver' || $roleSlug == 'attendant' || $roleSlug == 'district-anchor')
                        <div class="form-row">
                            <div class="form-group col-md-2">
                                <label class="font-weight-bold">Address</label>
                            </div>
                            <div class="form-group col-md-10">
                                <span class="not-editable">{{ ucwords($data->address) ?? ''}}</span>
                                <textarea class="form-control edit-profile d-none" id="address" name="address" rows="4" placeholder="Enter Address Here ...">{{ old('address', $data->address ?? '') }}</textarea>
                            </div>
                        </div>
                        @endif
                    </div>
                </fieldset>

                @if($roleSlug == 'driver' || $roleSlug == 'attendant' || $roleSlug == 'district-anchor')
                <fieldset class="border rounded-3 p-3 card  mt-3 ">
                    <legend class="float-none w-auto px-3">
                        <h5>Location & Reporting</h5>
                    </legend>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group col-md-2">
                                <label class="font-weight-bold">District</label>
                            </div>
                            <div class="form-group col-md-2">
                                <span class="not-editable">{{ $data->district_name ?? '' }}</span>
                                <div class="edit-profile d-none">
                                    <select name="district_id" id="district_id" class="form-control form-select" data-search="on" style="display: none;">
                                        <option value="" disabled>Select District</option>
                                        @if(!empty($districts))
                                        @foreach ($districts as $key => $district)
                                        <option value="{{ $district->id }}" {{ $district->id == $data->district_id ? 'selected' : '' }}>
                                            {{ $district->district_name ?? '' }}
                                        </option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-md-2">
                                <label class="font-weight-bold">State</label>
                            </div>
                            <div class="form-group col-md-2">
                                <span class="not-editable">{{ $data->state_name ?? '' }}</span>
                                <input type="text" class="form-control edit-profile d-none" id="state_name" name="state_name" value="{{ $data->state_name ?? '' }}" readonly>
                                <input type="hidden" class="form-control" id="state_id" name="state_id" value="{{ $data->state_id ?? ''}}">
                            </div>
                            <div class="form-group col-md-2">
                                <label class="font-weight-bold">Reporting Manager</label>
                            </div>
                            <div class="form-group col-md-2">
                                <span class="not-editable">{{ $data->reporting_manager_name ?? '' }}</span>
                                <div class="edit-profile d-none">
                                    <input type="hidden" name="reportingManager" id="reportingManager" value="{{ $data->reporting_manager_id ?? '' }}">
                                    <select name="reporting_manager_id" id="reporting_manager_id" class="form-control form-select" placeholder='Select Reporting Manager' data-search="on" disabled>
                                        <option {{ $data->reporting_manager_id ? 'selected' : '' }}>Select Reporting Manager</option>
                                        <option value="{{ $data->reporting_manager_id ?? '' }}" selected>{{ $data->reporting_manager_name ?? ''}}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </fieldset>

                <fieldset class="border rounded-3 p-3 card  mt-3 ">
                    <legend class="float-none w-auto px-3">
                        <h5>Educational Qualifcation</h5>

                    </legend>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group col-md-2">
                                <label class="font-weight-bold">Highest Qualifcation</label>
                            </div>
                            <div class="form-group col-md-2">
                                <span class="not-editable">{{ $data->highest_qualification_type ?? '' }}</span>
                                <div class="edit-profile d-none">
                                    <select name="highest_qualification_id" id="highest_qualification_id" class="form-control form-select" data-search="on" placeholder='Select Highest Qualification'>
                                        <option selected disabled>Select Highest Qualification</option>
                                        @if(!empty($highestQualifications))
                                        @foreach ($highestQualifications as $key => $highestQualification)
                                        <option value="{{ $highestQualification->id }}" @if($data->highest_qualification_id == $highestQualification->id) selected @endif>
                                            {{ $highestQualification->type ?? '' }}
                                        </option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-md-2">
                                <label class="font-weight-bold">Year of Completion</label>
                            </div>
                            <div class="form-group col-md-2">
                                <span class="not-editable">{{ $data->year_of_completion ?? '' }}</span>
                                <div class="edit-profile d-none">
                                    <select name="year_of_completion" id="year_of_completion" class="form-control form-select" data-search="on" placeholder='Select Year of Completion'>
                                        <option selected disabled>Select Year</option>

                                        @php
                                        // Set the range of years you want to allow
                                        $currentYear = date('Y');
                                        $startYear = 1900;

                                        // Create options for each year in the range
                                        for ($year = $currentYear; $year >= $startYear; $year--) {
                                        $selected = ($year == $data->year_of_completion) ? 'selected' : '';
                                        echo " <option value=\"$year\" $selected>$year</option>";
                                        }
                                        @endphp
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-md-2">
                                <label class="font-weight-bold">Field of Study</label>
                            </div>
                            <div class="form-group col-md-2">
                                <span class="not-editable">{{ $data->field_of_study_type ?? '' }}</span>
                                <div class="edit-profile d-none">
                                    <select name="field_of_study_id" id="field_of_study_id" class="form-control form-select" data-search="on" placeholder='Select Field of Study'>
                                        <option selected disabled>Select Field of Study</option>
                                        @if(!empty($fieldOfStudys))
                                        @foreach ($fieldOfStudys as $key => $fieldOfStudy)
                                        <option value="{{ $fieldOfStudy->id }}" @if($data->field_of_study_type_id == $fieldOfStudy->id) selected @endif>
                                            {{ $fieldOfStudy->type ?? '' }}
                                        </option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </fieldset>

                <fieldset class="border rounded-3 p-3 card  mt-3 ">
                    <legend class="float-none w-auto px-3">
                        <h5>Bank Account Details</h5>
                    </legend>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group col-md-2">
                                <label class="font-weight-bold">Bank Name</label>
                            </div>
                            <div class="form-group col-md-2">
                                <span class="not-editable">{{ $data->bank_name ?? '' }}</span>
                                <div class="edit-profile d-none">
                                    <select name="bank_id" id="bank_id" class="form-control form-select" data-search="on" placeholder='Select Bank Name'>
                                        <option selected disabled>Select Bank</option>
                                        @if(!empty($banks))
                                        @foreach ($banks as $bank)
                                        <option value="{{ $bank->id }}" {{ $data->bank_id == $bank->id ? 'selected' : '' }}>
                                            {{ $bank->bank_name ?? ''}}
                                        </option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-md-2">
                                <label class="font-weight-bold">Account Number</label>
                            </div>
                            <div class="form-group col-md-2">
                                <span class="not-editable">{{ $data->account_number ?? '' }}</span>
                                <input type="text" class="form-control edit-profile d-none" id="account_number" name="account_number" value="{{ $data->account_number ?? ''}}" placeholder="Enter Account Number" onkeypress="return isNumber(event)" maxlength="20">
                            </div>
                            <div class="form-group col-md-2">
                                <label class="font-weight-bold">IFSC Code</label>
                            </div>
                            <div class="form-group col-md-2">
                                <span class="not-editable">{{ $data->ifsc_code ?? '' }}</span>
                                <input type="text" class="form-control edit-profile d-none" id="ifsc_code" name="ifsc_code" value="{{ $data->ifsc_code ?? '' }}" placeholder="Enter IFSC Code" onkeypress="return onlyAlphanumerics(event)" maxlength="11">
                            </div>
                        </div>
                    </div>
                </fieldset>
                @endif
                <fieldset class="border rounded-3 p-3 card  mt-3 ">
                    <legend class="float-none w-auto px-3">
                        <h5>Other Details</h5>
                    </legend>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group col-md-2">
                                <label class="font-weight-bold">Account created on</label>
                            </div>
                            <div class="form-group col-md-2">
                                <span class="not-editable">{{ $data->account_created_date ? date('d/m/Y', strtotime($data->account_created_date)) : ''}}</span>
                                <input type="text" class="form-control edit-profile d-none" id="account_created_date" name="account_created_date" value="{{ $data->account_created_date ? date('d/m/Y', strtotime($data->account_created_date)) : ''}}" readonly>
                            </div>
                            <div class="form-group col-md-2">
                                <label class="font-weight-bold">Account created by</label>
                            </div>
                            <div class="form-group col-md-2">
                                <span class="not-editable">{{$data->account_created_by_name ?? ''}}</span>
                                <input type="text" class="form-control edit-profile d-none" id="account_created_by" name="account_created_by" value="{{$data->account_created_by_name ?? ''}}" readonly>
                            </div>
                        </div>
                    </div>
                </fieldset>

                @if(($roleSlug == 'driver') || ($roleSlug == 'attendant'))
                <fieldset class="border rounded-3 p-3 card  mt-3 ">
                    <legend class="float-none w-auto px-3">
                        <h5>Driving License Details</h5>
                    </legend>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group col-md-2">
                                <label class="font-weight-bold">Driver License Number</label>
                            </div>
                            <div class="form-group col-md-2">
                                <span class="not-editable">{{$data->license_number ?? ''}}</span>
                                <input type="text" class="form-control edit-profile d-none" id="license_number" name="license_number" value="{{$data->license_number ?? ''}}" placeholder="Enter Driver License Number" maxlength="16">
                            </div>
                            <div class="form-group col-md-2">
                                <label class="font-weight-bold">DL Type</label>
                            </div>
                            <div class="form-group col-md-2">
                                <span class="not-editable">{{$data->license_type_name ?? ''}}</span>
                                <div class="edit-profile d-none">
                                    <select name="license_type_id" id="license_type_id" class="form-control form-select" data-search="on" placeholder='Select DL Type'>
                                        <option selected disabled>Select DL Type</option>
                                        @if(!empty($drivingLicenceTypes))
                                        @foreach ($drivingLicenceTypes as $key => $drivingLicenceType)
                                        <option value="{{ $drivingLicenceType->id }}" {{ $data->dl_type_id == $drivingLicenceType->id ? 'selected' : '' }}>
                                            {{ $drivingLicenceType->type ?? ''}}
                                        </option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-md-2">
                                <label class="font-weight-bold">License Expiry Date</label>
                            </div>
                            <div class="form-group col-md-2">
                                <span class="not-editable">{{ $data->license_expiry_date ? date('d/m/Y', strtotime($data->license_expiry_date)) : ''}}</span>
                                <input type="text" class="form-control edit-profile d-none" id="license_expiry_date" name="license_expiry_date" value="{{ $data->license_expiry_date ? date('d/m/Y', strtotime($data->license_expiry_date)) : ''}}" readonly placeholder="Select License Expiry Date">
                            </div>
                        </div>
                    </div>
                </fieldset>

                @endif

                @if($roleSlug == 'driver' || $roleSlug == 'attendant' || $roleSlug == 'district-anchor')
                <fieldset class="border rounded-3 p-3 card  mt-3 ">
                    <legend class="float-none w-auto px-3">
                        <h5>Documents</h5>
                    </legend>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group col-md-2 d-none edit-profile">
                                <label class="font-weight-bold" for="aadhar_doc">Upload Aadhar Card:</label>
                            </div>
                            <div class="form-group col-md-4 d-none edit-profile">
                                <div class="custom-file mb-3">
                                    <input type="file" class="custom-file-input" id="aadhar_doc" name="aadhar_doc" onchange="updateFileName(this)" data-parsley-excluded="true" accept=".jpg, .jpeg, .png, .gif, .pdf">
                                    <label class="custom-file-label" for="attach_file">Tap to attach a file</label>
                                </div>
                            </div>
                            @if($data->aadhar_image_path)
                            <label><a href="{{ $data->aadhar_image_path }}" style="text-decoration: none; cursor: pointer;" target="_blank">View Aadhar Card</a></label>
                            @endif
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-2 d-none edit-profile">
                                <label class="font-weight-bold" for="pan_doc">Upload PAN Card:</label>
                            </div>
                            <div class="form-group col-md-4 d-none edit-profile">
                                <div class="custom-file mb-3">
                                    <input type="file" class="custom-file-input" id="pan_doc" name="pan_doc" onchange="updateFileName(this)" data-parsley-excluded="true" accept=".jpg, .jpeg, .png, .gif, .pdf">
                                    <label class="custom-file-label" for="attach_file">Tap to attach a file</label>
                                </div>
                            </div>
                            @if($data->pan_image_path)
                            <label><a href="{{ $data->pan_image_path }}" style="text-decoration: none; cursor: pointer;" target="_blank">View PAN Card</a></label>
                            @endif
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-2 d-none edit-profile">
                                <label class="font-weight-bold" for="bank_doc">Upload Bank Proof:</label>
                            </div>
                            <div class="form-group col-md-4 d-none edit-profile">
                                <div class="custom-file mb-3">
                                    <input type="file" class="custom-file-input" id="bank_doc" name="bank_doc" onchange="updateFileName(this)" data-parsley-excluded="true" accept=".jpg, .jpeg, .png, .gif, .pdf">
                                    <label class="custom-file-label" for="attach_file">Tap to attach a file</label>
                                </div>
                            </div>
                            @if($data->bank_proof_image_path)
                            <label><a href="{{ $data->bank_proof_image_path }}" style="text-decoration: none; cursor: pointer;" target="_blank">View Bank Proof</a></label>
                            @endif
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-2 d-none edit-profile">
                                <label class="font-weight-bold" for="mark_doc">Upload Marksheet:</label>
                            </div>
                            <div class="form-group col-md-4 d-none edit-profile">
                                <div class="custom-file mb-3">
                                    <input type="file" class="custom-file-input" id="mark_doc" name="mark_doc" onchange="updateFileName(this)" data-parsley-excluded="true" accept=".jpg, .jpeg, .png, .gif, .pdf">
                                    <label class="custom-file-label" for="attach_file">Tap to attach a file</label>
                                </div>
                            </div>
                            @if($data->marksheet_file_path)
                            <label><a href="{{ $data->marksheet_file_path }}" style="text-decoration: none; cursor: pointer;" target="_blank">View Marksheet</a></label>
                            @endif
                        </div>
                        @if(($roleSlug == 'driver') || ($roleSlug == 'attendant'))
                        <div class="form-row">
                            <div class="form-group col-md-2 d-none edit-profile">
                                <label class="font-weight-bold" for="dl_doc">Upload Driving License:</label>
                            </div>
                            <div class="form-group col-md-4 d-none edit-profile">
                                <div class="custom-file mb-3">
                                    <input type="file" class="custom-file-input" id="dl_doc" name="dl_doc" onchange="updateFileName(this)" data-parsley-excluded="true" accept=".jpg, .jpeg, .png, .gif, .pdf">
                                    <label class="custom-file-label" for="attach_file">Tap to attach a file</label>
                                </div>
                            </div>
                            @if($data->license_image_path)
                            <label><a href="{{ $data->license_image_path }}" style="text-decoration: none; cursor: pointer;" target="_blank">View Driving License</a></label>
                            @endif
                        </div>
                        @endif
                    </div>
                </fieldset>
                @endif
            </div>
            @if($roleSlug == 'driver' || $roleSlug == 'attendant' || $roleSlug == 'district-anchor')
            <div class="tab-pane fade show  mt-5" id="employement-record">
                <fieldset class="border rounded-3 p-3 card  mt-3 ">
                    <legend class="float-none w-auto px-3">
                        <h5>Current Employment</h5>
                    </legend>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group col-md-2">
                                <label class="font-weight-bold">Company</label>
                            </div>
                            <div class="form-group col-md-2">
                                <span>Parivaar</span>
                            </div>
                            <div class="form-group col-md-2">
                                <label class="font-weight-bold">Department</label>
                            </div>
                            <div class="form-group col-md-2">
                                <span>Headquarter</span>
                            </div>
                            <div class="form-group col-md-2">
                                <label class="font-weight-bold">Designation</label>
                            </div>
                            <div class="form-group col-md-2">
                                <span>{{ $data->role_name ?: '-' }}</span>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-2">
                                <label class="font-weight-bold">Location</label>
                            </div>
                            <div class="form-group col-md-2">
                                <span>{{ $data->district_name ?: '-' }}</span>
                            </div>
                            <div class="form-group col-md-2">
                                <label class="font-weight-bold">Employment Start</label>
                            </div>
                            <div class="form-group col-md-2">
                                <span class="not-editable">{{ $data->joining_date ? date('d/m/Y', strtotime($data->joining_date)) : '' }}</span>
                                <input type="text" class="form-control d-none edit-profile" id="joining_date" name="joining_date" value="{{ $data->joining_date ? date('d/m/Y', strtotime($data->joining_date)) : '' }}" readonly placeholder="Select Joining Date">
                            </div>
                            <div class="form-group col-md-2">
                                <label class="font-weight-bold">Employment End</label>
                            </div>
                            <div class="form-group col-md-2">
                                <span>Still Associated</span>
                            </div>
                        </div>
                    </div>
                </fieldset>

                <fieldset class="border rounded-3 p-3 card  mt-3 ">
                    <legend class="float-none w-auto px-3">
                        <h5>Past Experience</h5>
                    </legend>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group col-md-2">
                                <label class="font-weight-bold">Last Company Name</label>
                            </div>
                            <div class="form-group col-md-2">
                                <span class="not-editable">{{ $data->last_company_name ?? '' }}</span>
                                <input type="text" class="form-control d-none edit-profile" id="last_company_name" name="last_company_name" value="{{ $data->last_company_name ?? '' }}" placeholder="Enter Last Company Name" maxlength="100">
                            </div>
                            <div class="form-group col-md-2">
                                <label class="font-weight-bold">Designation</label>
                            </div>
                            <div class="form-group col-md-2">
                                <span class="not-editable">{{ $data->last_company_designation ?? '' }}</span>
                                <input type="text" class="form-control d-none edit-profile" id="designation" name="designation" value="{{ $data->last_company_designation ?? '' }}" placeholder="Enter Designation" maxlength="50">
                            </div>
                            <div class="form-group col-md-2">
                                <label class="font-weight-bold">Location</label>
                            </div>
                            <div class="form-group col-md-2">
                                <span class="not-editable">{{ $data->last_company_location ?? '' }}</span>
                                <input type="text" class="form-control d-none edit-profile" id="past_experience_location" name="past_experience_location" value="{{ $data->last_company_location ?? '' }}" placeholder="Enter Location">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-2">
                                <label class="font-weight-bold">Start Date</label>
                            </div>
                            <div class="form-group col-md-2">
                                <span class="not-editable">{{ $data->last_company_start_date ? date('d/m/Y', strtotime($data->last_company_start_date)) : '' }}</span>
                                <input type="text" class="form-control d-none edit-profile" id="start_date" name="start_date" value="{{ $data->last_company_start_date ? date('d/m/Y', strtotime($data->last_company_start_date)) : '' }}" readonly placeholder="Select Start Date">
                            </div>
                            <div class="form-group col-md-2">
                                <label class="font-weight-bold">End Date</label>
                            </div>
                            <div class="form-group col-md-2">
                                <span class="not-editable">{{ $data->last_company_end_date ? date('d/m/y', strtotime($data->last_company_end_date)) : ''}}</span>
                                <input type="text" class="form-control d-none edit-profile" id="end_date" name="end_date" value="{{ $data->last_company_end_date ? date('d/m/y', strtotime($data->last_company_end_date)) : ''}}" readonly placeholder="Select End Date">
                            </div>

                            <div class="form-group col-md-2 d-none edit-profile">
                                <label class="font-weight-bold" for="past_experience_document">Upload Document:</label>
                            </div>
                            <div class="form-group col-md-2 d-none edit-profile">
                                <div class="custom-file mb-3">
                                    <input type="file" class="custom-file-input" id="past_experience_document" name="past_experience_document" onchange="updateFileName(this)" data-parsley-excluded="true" accept=".jpg, .jpeg, .png, .gif, .pdf">
                                    <label class="custom-file-label" for="attach_file">Tap to attach a file</label>
                                </div>
                            </div>
                            @if($data->work_experience_document)
                            <label><a href="{{ $data->work_experience_document }}" style="text-decoration: none; cursor: pointer;" target="_blank">View Document</a></label>
                            @endif

                        </div>
                    </div>
                </fieldset>
            </div>

            <div class="tab-pane fade show  mt-5" id="salary">
                <div class="toggle-bar">
                    <ul class="nav" style="display: flex; justify-content: flex-end; list-style: none; padding: 0;">
                        <li class="nav-item">
                            <a class="nav-link active" href="#salary" id="addPayslip"><em class='icon ni ni-plus'></em><span>Add</span></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#salary" id="updatePayslip"><em class='icon ni ni-edit'></em><span>Update</span></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link " href="#salary" id="deletePayslip" data-user-id="{{$data->user_id ?? ''}}"><em class='icon ni ni-trash'></em><span>Delete</span></a>
                        </li>
                        <li class="nav-item">
                            <div class="form-group" style="padding-right: 15px; width: 150px;">
                                <select name="year_id" id="year_id" class="form-control form-select" data-search="on" placeholder="Select Year" onchange="getSalaryDetails();toggleViewMode();">
                                    <option disabled value="">Select Year</option>
                                    @php
                                    // Set the range of years you want to allow
                                    $currentYear = date('Y');
                                    $startYear = 2000;

                                    // Calculate the previous month and year
                                    $previousMonth = date('n') - 1;
                                    $previousYear = date('Y');

                                    // Adjust the year and month if the previous month is December
                                    if ($previousMonth == 0) {
                                    $previousMonth = 12;
                                    $previousYear--;
                                    }

                                    // Create options for each year in the range, starting from the previous month's year
                                    for ($year = $previousYear; $year >= $startYear; $year--) {
                                    echo "<option value=\"$year\">$year</option>";
                                    }
                                    @endphp

                                </select>
                            </div>

                        </li>
                        <li class="nav-item">
                            <div class="form-group" style="padding-right: 15px; width: 150px;">
                                <select name="month_id" id="month_id" class="form-control form-select" data-search="on" placeholder="Select Month" onchange="getSalaryDetails();toggleViewMode();">
                                    <option disabled value="">Select Month</option>
                                    @php
                                    // Create options for each month
                                    $months = [
                                    'January', 'February', 'March', 'April', 'May', 'June',
                                    'July', 'August', 'September', 'October', 'November', 'December'
                                    ];

                                    $currentMonth = date('n'); // Get the current month (1-based)
                                    $previousMonth = ($currentMonth - 2 + 12) % 12 + 1; // Calculate the previous month

                                    foreach ($months as $index => $month) {
                                    $monthNumber = $index + 1; // Months are 1-based in PHP
                                    $selected = ($monthNumber == $previousMonth) ? 'selected' : '';
                                    echo "<option value=\"$monthNumber\" $selected>$month</option>";
                                    }
                                    @endphp
                                </select>
                            </div>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link downloadSalarySlip" id="downloadSalarySlip" href="#"><em class='icon ni ni-download'></em><span>Download</span></a>
                        </li>
                    </ul>
                </div>
                <div id="salary-block" class="card mt-3">
                    <div class="card-body">
                        <div id="salaryDetailsContainer"></div>
                        <div id="salaryDetailTemplate">
                            <form id="updateSalarySlipForm" method="post" enctype="multipart/form-data">
                                @csrf
                                <fieldset class="border rounded-3 p-3 card  mt-3 ">
                                    <legend class="float-none w-auto px-3">
                                        <h5>Earnings</h5>
                                    </legend>
                                    <div class="card-body">
                                        <div class="form-row">
                                            <table class="table table-borderless">
                                                <thead>
                                                    <th></th>
                                                    <th>Amount in Rs</th>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>Basic</td>
                                                        <td><span id="basic_salary" class="editable"></span></td>
                                                    </tr>
                                                    <tr>
                                                        <td>HRA</td>
                                                        <td><span id="hra" class="editable"></span></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Conveyance</td>
                                                        <td><span id="conveyance" class="editable"></span></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Special Allowance</td>
                                                        <td><span id="special_allowance" class="editable"></span></td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="2">
                                                            <hr>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Total</strong></td>
                                                        <td id="total_earnings"><strong></strong></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </fieldset>
                                <fieldset class="border rounded-3 p-3 card  mt-3 ">
                                    <legend class="float-none w-auto px-3">
                                        <h5>Deductions</h5>
                                    </legend>
                                    <div class="card-body">
                                        <div class="form-row">
                                            <table class="table table-borderless">
                                                <thead>
                                                    <th></th>
                                                    <th>Amount in Rs</th>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>PF</td>
                                                        <td><span id="pf"></span></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Professional Tax</td>
                                                        <td><span id="professional_tax" class="editable"></span></td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="2">
                                                            <hr>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Total</strong></td>
                                                        <td><span id="total_deductions"><strong></strong></span></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </fieldset>

                                <fieldset class="border rounded-3 p-3 card  mt-3 ">
                                    <legend class="float-none w-auto px-3">
                                        <h5>Net payment for <span id="slipMonthYear"></span> </h5>
                                    </legend>
                                    <div class="card-body">
                                        <div class="form-row">
                                            <table class="table table-borderless">
                                                <tr>
                                                    <td id="net_payment"></td>
                                                </tr>
                                                <tr>
                                                    <td id="net_payment_in_words"></td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </fieldset>
                                <button class="btn btn-primary updateSlip" style="color:#fff" id="updateSlip" type="button">Submit</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade show  mt-5" id="attendance">
                <div class="toggle-bar">
                    <ul class="nav" style="display: flex; justify-content: flex-end; list-style: none; padding: 0;">
                        <li class="nav-item">
                            <div class="form-group" style="padding-right: 15px; width: 150px;">
                                <select name="attendance_year_id" id="attendance_year_id" class="form-control form-select" data-search="on" placeholder="Select Year" onchange="getAttendanceDetails();">
                                    <option disabled value="">Select Year</option>
                                    @php
                                    // Set the range of years you want to allow
                                    $currentYear = date('Y');
                                    $startYear = 2000;

                                    // Create options for each year in the range
                                    for ($year = $currentYear; $year >= $startYear; $year--) {
                                    echo "<option value=\"$year\">$year</option>";
                                    }
                                    @endphp
                                </select>
                            </div>
                        </li>
                        <li class="nav-item">
                            <div class="form-group" style="padding-right: 15px; width: 150px;">
                                <select name="attendance_month_id" id="attendance_month_id" class="form-control form-select" data-search="on" placeholder="Select Month" onchange="getAttendanceDetails();">
                                    <option disabled value="">Select Month</option>
                                    @php
                                    // Create options for each month
                                    $months = [
                                    'January', 'February', 'March', 'April', 'May', 'June',
                                    'July', 'August', 'September', 'October', 'November', 'December'
                                    ];

                                    $currentMonth = date('n'); // Get the current month (1-based)


                                    foreach ($months as $index => $month) {
                                    $monthNumber = $index + 1; // Months are 1-based in PHP
                                    $selected = ($monthNumber == $currentMonth) ? 'selected' : '';
                                    echo "<option value=\"$monthNumber\" $selected>$month</option>";
                                    }
                                    @endphp
                                </select>
                            </div>
                        </li>
                    </ul>
                </div>

                <fieldset class="border rounded-3 p-3 card mt-3 ">
                    @if ($data->is_verified)
                    <div class="card-body">

                        <div class="nk-block table-compact">
                            <div class="nk-tb-list is-separate mt-3">
                                <table id="attendanceDetails" class="attendanceDetails nowrap nk-tb-list is-separate" data-auto-responsive="false">
                                    <thead>
                                        <tr class="nk-tb-item nk-tb-head">
                                            <th class="nk-tb-col tb-col-mb"><span class="sub-text">Date</span></th>
                                            <th class="nk-tb-col tb-col-mb"><span class="sub-text">Shift</span></th>
                                            <th class="nk-tb-col tb-col-mb"><span class="sub-text">Attendance</span></th>
                                            <th class="nk-tb-col tb-col-mb"><span class="sub-text">Actions</span></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                    @else
                    <div class="col-md-12" style="display: flex;justify-content: center">
                        <img width="300" src="https://parivaar.siplsolutions.com/images/empty_item.svg">
                    </div>

                    <h5 class="col-md-12" style="display: flex;justify-content: center;">No records found.</h5>
                    @endif
                </fieldset>
            </div>
            <div class="tab-pane fade show  mt-5" id="leaves">
                <div class="toggle-bar">
                    <ul class="nav" style="display: flex; justify-content: flex-end; list-style: none; padding: 0;">
                        <li class="nav-item">
                            <div class="form-group" style="padding-right: 15px; width: 150px;">
                                <select name="leave_year_id" id="leave_year_id" class="form-control form-select" data-search="on" placeholder="Select Year" onchange="getLeaveDetails();">
                                    <option disabled value="">Select Year</option>
                                    @php
                                    // Set the range of years you want to allow
                                    $currentYear = date('Y');
                                    $startYear = 2000;

                                    // Create options for each year in the range
                                    for ($year = $currentYear; $year >= $startYear; $year--) {
                                    echo "<option value=\"$year\">$year</option>";
                                    }
                                    @endphp
                                </select>
                            </div>
                        </li>
                        <li class="nav-item">
                            <div class="form-group" style="padding-right: 15px; width: 150px;">
                                <select name="leave_month_id" id="leave_month_id" class="form-control form-select" data-search="on" placeholder="Select Month" onchange="getLeaveDetails();">
                                    <option disabled value="">Select Month</option>
                                    @php
                                    // Create options for each month
                                    $months = [
                                    'January', 'February', 'March', 'April', 'May', 'June',
                                    'July', 'August', 'September', 'October', 'November', 'December'
                                    ];

                                    $currentMonth = date('n'); // Get the current month (1-based)


                                    foreach ($months as $index => $month) {
                                    $monthNumber = $index + 1; // Months are 1-based in PHP
                                    $selected = ($monthNumber == $currentMonth) ? 'selected' : '';
                                    echo "<option value=\"$monthNumber\" $selected>$month</option>";
                                    }
                                    @endphp
                                </select>
                            </div>
                        </li>
                    </ul>
                </div>
                <fieldset class="border rounded-3 p-3 card mt-3">
                    <div class="card-body">
                        <div id="leaveDetailsContainer"></div>
                        <div id="leaveDetailTemplate" style="display: none;">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-lg-6">
                                            <div class="row g-3 align-items-center">
                                                <div class="col-lg-5">
                                                    <span class="form-label">Status</span>
                                                </div>
                                                <div class="col-lg-7">
                                                    <span class="leave_status"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="row g-3 align-items-center">
                                                <div class="col-lg-5">
                                                    <span class="form-label">Leave Type</span>
                                                </div>
                                                <div class="col-lg-7">
                                                    <span class="leave_type"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-lg-6">
                                            <div class="row g-3 align-items-center">
                                                <div class="col-lg-5">
                                                    <span class="form-label">From Date</span>
                                                </div>
                                                <div class="col-lg-7">
                                                    <span class="leave_from_date"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="row g-3 align-items-center">
                                                <div class="col-lg-5">
                                                    <span class="form-label">To Date</span>
                                                </div>
                                                <div class="col-lg-7">
                                                    <span class="leave_to_date"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-lg-6">
                                            <div class="row g-3 align-items-center">
                                                <div class="col-lg-5">
                                                    <span class="form-label">Amount</span>
                                                </div>
                                                <div class="col-lg-7">
                                                    <span class="leave_amount"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="row g-3 align-items-center">
                                                <div class="col-lg-5">
                                                    <span class="form-label">Applied To</span>
                                                </div>
                                                <div class="col-lg-7">
                                                    <span class="leave_applied_to"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-lg-6">
                                            <div class="row g-3 align-items-center">
                                                <div class="col-lg-5">
                                                    <span class="form-label">Applied Date</span>
                                                </div>
                                                <div class="col-lg-7">
                                                    <span class="leave_applied_date"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="row g-3 align-items-center">
                                                <div class="col-lg-5">
                                                    <span class="form-label">Approved On</span>
                                                </div>
                                                <div class="col-lg-7">
                                                    <span class="leave_approved_date"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-lg-6">
                                            <div class="row g-3 align-items-center">
                                                <div class="col-lg-5">
                                                    <span class="form-label">Reason</span>
                                                </div>
                                                <div class="col-lg-7">
                                                    <span class="leave_reason"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="row g-3 align-items-center">
                                                <div class="col-lg-5">
                                                    <span class="form-label">Attachment</span>
                                                </div>
                                                <div class="col-lg-7">
                                                    <span class="leave_attachment">
                                                        <a href="#" style="text-decoration: none;">-</a>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row g-3" id="reject_reason" style="display: none;">
                                        <div class="col-lg-6">
                                            <div class="row g-3 align-items-center">
                                                <div class="col-lg-5">
                                                    <span class="form-label">Remark</span>
                                                </div>
                                                <div class="col-lg-7">
                                                    <span class="leave_reason"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row g-3" id="leaves_action_btn">
                                        <div class="col-lg-12 p-0 text-right" style="display:flex;justify-content:right;">
                                            <button class="btn btn-outline-light rejectLeave mr-2" type="button">Reject</button>
                                            <button class="btn btn-primary approveLeave" type="button">Approve</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </fieldset>

            </div>
            <div class="tab-pane fade show  mt-5" id="tasks">
                <div class="toggle-bar">
                    <ul class="nav" style="display: flex; justify-content: flex-end; list-style: none; padding: 0;">
                        <li class="nav-item">
                            <div class="form-group" style="padding-right: 15px; width: 150px;">
                                <select name="task_year_id" id="task_year_id" class="form-control form-select" data-search="on" placeholder="Select Year" onchange="getTaskDetails();">
                                    <option disabled value="">Select Year</option>
                                    @php
                                    // Set the range of years you want to allow
                                    $currentYear = date('Y');
                                    $startYear = 2000;

                                    // Create options for each year in the range
                                    for ($year = $currentYear; $year >= $startYear; $year--) {
                                    echo "<option value=\"$year\">$year</option>";
                                    }
                                    @endphp
                                </select>
                            </div>
                        </li>
                        <li class="nav-item">
                            <div class="form-group" style="padding-right: 15px; width: 150px;">
                                <select name="task_month_id" id="task_month_id" class="form-control form-select" data-search="on" placeholder="Select Month" onchange="getTaskDetails();">
                                    <option disabled value="">Select Month</option>
                                    @php
                                    // Create options for each month
                                    $months = [
                                    'January', 'February', 'March', 'April', 'May', 'June',
                                    'July', 'August', 'September', 'October', 'November', 'December'
                                    ];

                                    $currentMonth = date('n'); // Get the current month (1-based)


                                    foreach ($months as $index => $month) {
                                    $monthNumber = $index + 1; // Months are 1-based in PHP
                                    $selected = ($monthNumber == $currentMonth) ? 'selected' : '';
                                    echo "<option value=\"$monthNumber\" $selected>$month</option>";
                                    }
                                    @endphp
                                </select>
                            </div>
                        </li>
                    </ul>
                </div>
                <div id="task-block">
                    <fieldset class="border rounded-3 p-3 card mt-3">
                        <div class="card-body">
                            <div id="taskDetailsContainer"></div>
                            <div id="taskDetailTemplate" style="display: none;">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-lg-6">
                                                <div class="row g-3 align-items-center">
                                                    <div class="col-lg-5">
                                                        <span class="form-label">Task Title</span>
                                                    </div>
                                                    <div class="col-lg-7">
                                                        <span class="task_title"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="row g-3 align-items-center">
                                                    <div class="col-lg-5">
                                                        <span class="form-label">Priority</span>
                                                    </div>
                                                    <div class="col-lg-7">
                                                        <span class="task_priority"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row g-3">
                                            <div class="col-lg-6">
                                                <div class="row g-3 align-items-center">
                                                    <div class="col-lg-5">
                                                        <span class="form-label">Assigned To </span>
                                                    </div>
                                                    <div class="col-lg-7">
                                                        <span class="task_assigned_to"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="row g-3 align-items-center">
                                                    <div class="col-lg-5">
                                                        <span class="form-label">Creation Date</span>
                                                    </div>
                                                    <div class="col-lg-7">
                                                        <span class="task_created_date"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-lg-6">
                                                <div class="row g-3 align-items-center">
                                                    <div class="col-lg-5">
                                                        <span class="form-label">Created By</span>
                                                    </div>
                                                    <div class="col-lg-7">
                                                        <span class="task_created_by"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="row g-3 align-items-center">
                                                    <div class="col-lg-5">
                                                        <span class="form-label">Status</span>
                                                    </div>
                                                    <div class="col-lg-7">
                                                        <span class="task_status"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row g-3">
                                            <div class="col-lg-6">
                                                <div class="row g-3 align-items-center">
                                                    <div class="col-lg-5">
                                                        <span class="form-label">Last Updated Date</span>
                                                    </div>
                                                    <div class="col-lg-7">
                                                        <span class="task_updated_date"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="row g-3 align-items-center">
                                                    <div class="col-lg-5">
                                                        <span class="form-label">Remarks</span>
                                                    </div>
                                                    <div class="col-lg-7">
                                                        <span class="task_remarks"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row g-3">
                                            <div class="col-lg-6">
                                                <div class="row g-3 align-items-center">
                                                    <div class="col-lg-5">
                                                        <span class="form-label">Task Description</span>
                                                    </div>
                                                    <div class="col-lg-7">
                                                        <span class="task_description"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="row g-3 align-items-center">
                                                    <div class="col-lg-5">
                                                        <span class="form-label">Attachment</span>
                                                    </div>
                                                    <div class="col-lg-7">
                                                        <span class="task_attachment">
                                                            <a href="#" style="text-decoration: none; cursor: pointer;">View</a>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row g-3" id="task_action_btn">
                                            <div class="col-lg-12 p-0 text-right" style="display:flex;justify-content:right;">
                                                <button class="btn btn-outline-light cancelTask mr-2" data-target='#cancelTaskModal' data-toggle='modal' type="button">Cancel Task</button>
                                                <button class="btn btn-primary reassignTask" data-target='#assignTaskModal' data-toggle='modal' type="button">Re Assign Task</button>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                </div>
            </div>
            <div class="tab-pane fade show mt-5" id="shift_details">
                <div class="toggle-bar">
                    <div id="filter_tag_list" class="filter-tag-list"></div>
                    <ul class="nav" style="display: flex; justify-content: flex-end; list-style: none; padding: 0;">
                        <li class="nav-item">
                            <p id="addShift" style="padding: 0.5rem 1rem;color:navy;cursor:pointer;"><em class='icon ni ni-plus'></em><span style="margin-left: 0.75rem">Add</span></p>
                        </li>
                        <li class="nav-item">
                            <p id="deleteShift" style="padding: 0.5rem 1rem;color:navy;cursor:pointer;"><em class='icon ni ni-trash'></em><span style="margin-left: 0.75rem">Unassign Shift</span></p>
                        </li>
                        <li class="nav-item">
                            <div class="form-group" style="padding-right: 15px; width: 150px;">
                                <select name="shift_year_id" id="shift_year_id" class="form-control" data-search="on" placeholder="Select Year" onchange="getShiftDetails();">
                                    <option disabled value="">Select Year</option>
                                    @php
                                    // Set the range of years you want to allow
                                    $currentYear = date('Y');
                                    $startYear = 2000;

                                    // Create options for each year in the range
                                    for ($year = $currentYear; $year >= $startYear; $year--) {
                                    echo "<option value=\"$year\">$year</option>";
                                    }
                                    @endphp
                                </select>
                            </div>
                        </li>
                        <li class="nav-item">
                            <div class="form-group" style="padding-right: 15px; width: 150px;">
                                <select name="shift_month_id" id="shift_month_id" class="form-control" data-search="on" placeholder="Select Month" onchange="getShiftDetails();">
                                    <option disabled value="">Select Month</option>
                                    @php
                                    // Create options for each month
                                    $months = [
                                    'January', 'February', 'March', 'April', 'May', 'June',
                                    'July', 'August', 'September', 'October', 'November', 'December'
                                    ];

                                    $currentMonth = date('n'); // Get the current month (1-based)


                                    foreach ($months as $index => $month) {
                                    $monthNumber = $index + 1; // Months are 1-based in PHP
                                    $selected = ($monthNumber == $currentMonth) ? 'selected' : '';
                                    echo "<option value=\"$monthNumber\" $selected>$month</option>";
                                    }
                                    @endphp
                                </select>
                            </div>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="dropdown-toggle mr-3" data-toggle="modal" data-target="#filterShift" class="toggle btn btn-trigger btn-icon">
                                <div class="dot dot-primary"></div>
                                <em class="icon ni ni-filter-alt"></em>
                            </a>
                        </li>
                    </ul>
                </div>

                <fieldset class="border rounded-3 p-3 card mt-3 ">
                    <div class="card-body">
                        <div class="nk-block table-compact">
                            <div class="nk-tb-list is-separate mt-3">
                                <table id="shiftDetails" class="shiftDetails nowrap nk-tb-list is-separate" data-auto-responsive="false">
                                    <thead>
                                        <tr class="nk-tb-item nk-tb-head">
                                            <th class="nk-tb-col tb-col-mb"><span class="sub-text"><input type="checkbox" id="toggle-all-checkboxes"></span></th>
                                            <th class="nk-tb-col tb-col-mb"><span class="sub-text">Date</span></th>
                                            <th class="nk-tb-col tb-col-mb"><span class="sub-text">Ambulance No</span></th>
                                            <th class="nk-tb-col tb-col-mb"><span class="sub-text">Shift</span></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </fieldset>
            </div>
            @endif
        </div>
    </form>

    <div style="float: right;margin-top: 10px">
        <button class="btn btn-primary mb-1 d-none" style="height: 30px;" type="button" id="updateProfile" style="height: 30px;width: 120px">Update Profile</button>
        @if (empty($data->is_verified))
        <button type="button" class="btn btn-primary mb-1 approve_user" id="{{ $data->user_id ?? '' }}" style="height: 30px; width: 120px">Approve User</button>
        <button type="button" class="btn btn-primary mb-1 reject_user" id="{{ $data->user_id ?? '' }}" style="height: 30px; width: 120px">Reject User</button>
        @endif

    </div>
</div>


<div class="modal fade zoom" tabindex="-1" id="viewUserAttendanceInfo">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Attendance Details</h5>
                <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
            </div>
            <div class="modal-body modal-body-md">
                <div class="form-row">
                    <div class="col-md-6"></div>
                    <div class="col-md-6">
                        <span style="float: right;">
                            <button type="button" class="btn btn-primary mb-1 edit_user" id="updateAttandanceBtn" style="height: 30px;width: 100px;justify-content:center;">Update</button>
                        </span>
                    </div>
                </div>
                <form role="form" class="mb-0" method="post" id="updateAttendanceForm">
                    @csrf
                    <div class="modal-body modal-body-md">
                        <div class="gy-3">
                            <div class="alert alert-info d-none" id="noteInfo">
                                <p class="mb-0"><strong>NOTE:</strong> Please fill CheckIn & CheckOut time according to 24 hours format.</p>
                            </div>
                            <div class="row g-3 align-center">
                                <div class="col-lg-5">
                                    <label for="attendance_date"><em class='icon ni ni-calendar'></em> Date: </label>
                                </div>
                                <div class="col-lg-7">
                                    <span id="attendance_date"></span>
                                </div>
                            </div>
                            <div class="row g-3 align-center">
                                <div class="col-lg-5">
                                    <label for="attendance_status"><em class='icon ni ni-calendar'></em> Status: </label>
                                </div>
                                <div class="col-lg-7">
                                    <select name="attendance_status" id="attendance_status" class="form-control form-select" disabled>
                                        <option value="" selected disabled>Select Status</option>
                                        <option value="Present">Present</option>
                                        <option value="Absent">Absent</option>
                                        <option value="Leave">Leave</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row g-3 align-center">
                                <div class="col-lg-5 leaveStatus d-none">
                                    <label for="attendance_leave_type"><em class='icon ni ni-calendar'></em> Leave Type <span class="text-danger">*</span></label>
                                </div>
                                <div class="col-lg-7 leaveStatus d-none form-control-required-wrap">
                                    <select name="attendance_leave_type" id="attendance_leave_type" class="form-control form-select" data-parsley-excluded="true" disabled>

                                    </select>
                                </div>
                            </div>
                            <div class="row g-3 align-center">
                                <div class="col-lg-5 leaveStatus d-none">
                                    <label for="attendance_leave_reason"><em class='icon ni ni-location'></em> Leave Reason <span class="text-danger">*</span></label>
                                </div>
                                <div class="col-lg-7 leaveStatus d-none">
                                    <textarea class="form-control" id="attendance_leave_reason" name="attendance_leave_reason" rows="4" placeholder="Enter Leave Reason Here ..." data-parsley-excluded="true" disabled></textarea>
                                </div>
                            </div>
                            <div class="row g-3 align-center">
                                <div class="col-lg-5 presentStatus d-none">
                                    <label for="checkIn_time"><em class='icon ni ni-clock'></em> Check In Time <span class="text-danger">*</span></label>
                                </div>
                                <div class="col-lg-7 presentStatus d-none">
                                    <input type="time" name="checkIn_time" id="checkIn_time" class="form-control" disabled placeholder="Enter check in time here">
                                </div>
                            </div>
                            <div class="row g-3 roleStatus">
                                <div class="align-center">
                                    <div class="col-lg-5 presentStatus d-none">
                                        <label for="checkIn_meter_reading"><em class='icon ni ni-meter'></em> Check IN Meter Reading<span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-lg-7 presentStatus d-none">
                                        <input type="text" name="checkIn_meter_reading" id="checkIn_meter_reading" class="form-control" onkeypress="return isNumber(event)" disabled placeholder="Enter check in meter reading here">
                                    </div>
                                </div>
                            </div>
                            <div class="row g-3 align-center">
                                <div class="col-lg-5 presentStatus d-none">
                                    <label for="checkIn_location"><em class='icon ni ni-location'></em> Check IN Location <span class="text-danger">*</span></label>
                                </div>
                                <div class="col-lg-7 presentStatus d-none">
                                    <span id="attendance_checkin_location"></span>
                                    <textarea class="form-control" id="checkIn_location" name="checkIn_location" rows="4" placeholder="Enter Check In Location Here ..." data-parsley-excluded="true"></textarea>
                                </div>
                            </div>
                            <div class="row g-3 align-center">

                                <div class="col-lg-5 presentStatus d-none">
                                    <label for="checkOut_time"><em class='icon ni ni-clock'></em> Check Out Time <span class="text-danger">*</span></label>
                                </div>
                                <div class="col-lg-7 presentStatus d-none">
                                    <input type="time" name="checkOut_time" id="checkOut_time" class="form-control" disabled placeholder="Enter check out location here">
                                </div>

                            </div>
                            <div class="row g-3 roleStatus">
                                <div class="align-center">
                                    <div class="col-lg-5 presentStatus d-none">
                                        <label for="checkOut_meter_reading"><em class='icon ni ni-meter'></em> Check OUT Meter Reading <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-lg-7 presentStatus d-none">
                                        <input type="text" name="checkOut_meter_reading" id="checkOut_meter_reading" class="form-control" onkeypress="return isNumber(event)" disabled placeholder="Enter check out meter reading here">
                                    </div>
                                </div>
                            </div>
                            <div class="row g-3 align-center">
                                <div class="col-lg-5 presentStatus d-none">
                                    <label for="checkOut_location"><em class='icon ni ni-location'></em> Check OUT Location <span class="text-danger">*</span></label>
                                </div>
                                <div class="col-lg-7 presentStatus d-none">
                                    <span id="attendance_checkout_location"></span>
                                    <textarea class="form-control" id="checkOut_location" name="checkOut_location" rows="4" placeholder="Enter Check OUT Location Here ..." data-parsley-excluded="true"></textarea>
                                </div>
                            </div>
                            <div class="row g-3 align-center">
                                <div class="col-lg-5 presentStatus d-none">
                                    <label for="duration"><em class='icon ni ni-clock'></em> Duration <span class="text-danger">*</span></label>
                                </div>
                                <div class="col-lg-7 presentStatus d-none">
                                    <div class="input-group mb-3">
                                        <input type="time" name="duration" id="duration" class="form-control" onkeypress="return isNumber(event)" disabled placeholder="Enter duration here">
                                        <span class="input-group-text">hours</span>
                                    </div>
                                    <label id="duration-error" class="error" for="duration" style="color: #e85347;font-size: 11px;font-style: italic;"></label>
                                </div>
                            </div>
                            <div class="row g-3 roleStatus">
                                <div class="align-center">
                                    <div class="col-lg-5 presentStatus d-none">
                                        <label for="km_run"><em class='icon ni ni-meter'></em> KM Run <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-lg-7 presentStatus d-none">
                                        <div class="input-group mb-3">
                                            <input type="text" name="km_run" id="km_run" class="form-control" onkeypress="return isNumber(event)" disabled placeholder="Enter km run here">
                                            <span class="input-group-text">KM</span>
                                        </div>
                                        <label id="km_run-error" class="error" for="km_run" style="color: #e85347;font-size: 11px;font-style: italic;"></label>
                                    </div>
                                </div>
                            </div>
                            <!-- <div class="row g-3 align-center">
                                <div class="col-lg-5 presentStatus d-none">
                                    <label for=""><em class='icon ni ni-bed'></em> Case served on the date: </label>
                                </div>
                                <div class="col-lg-7 presentStatus d-none">
                                    <span id="caseServedCount"></span>
                                </div>
                            </div> -->
                            <input type="hidden" name="attendance_shift_id" id="attendance_shift_id">
                            <input type="hidden" name="attendance_details_date" id="attendance_details_date">
                            <input type="hidden" name="attendance_user_id" id="attendance_user_id">
                        </div>
                    </div>
                    <!-- <div class="modal-footer bg-light"> -->
                    <div class="row">
                        <div class="col-lg-12 p-0 text-right">
                            <button class="btn btn-outline-light" data-dismiss="modal" aria-label="Close">Cancel</button>
                            <button class="btn btn-primary" id="updateAttendanceInfo" type="submit">Submit</button>
                        </div>
                    </div>
                    <!-- </div> -->
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade zoom" tabindex="-1" id="cancelTaskModal" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cancel Task</h5>
                <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
            </div>
            <form action="#" role="form" class="mb-0" method="post" id="cancelTaskForm">
                @csrf
                <div class="modal-body modal-body-lg">
                    <div class="gy-3">
                        <div class="form-row">
                            <div class="form-group col-md-1">
                            </div>
                            <div class="form-group col-md-10">
                                <label for="reasonForCancellation" class="form-label">Reason For Task Cancellation <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="reasonForCancellation" name="reasonForCancellation" rows="4" placeholder="Enter Reason For Task Cancellation Here ..."></textarea>
                                <input type="hidden" name="task_id" id="task_id" value="">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <div class="row">
                        <div class="col-lg-12 p-0 text-right">
                            <button class="btn btn-outline-light" data-dismiss="modal" aria-label="Close">Cancel</button>
                            <button class="btn btn-primary submitBtnCancelTask" type="submit">Submit</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade zoom" tabindex="-1" id="assignTaskModal" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Re Assign Task</h5>
                <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
            </div>
            <form action="#" role="form" class="mb-0" method="post" id="assignTaskForm">
                @csrf
                <div class="modal-body modal-body-lg">
                    <div class="gy-3">
                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <label for="assign_role_id" class="form-label mr-2">Role<span class="text-danger">*</span></label>
                            </div>
                            <div class="col-lg-7">
                                <select name="assign_role_id" id="assign_role_id" class="form-control form-select" placeholder='Select Role' data-search="on">
                                    <option value="" selected disabled>Select Role</option>
                                    @php
                                    $roles = Helpers::getAllRoles();
                                    @endphp
                                    @if(!empty($roles))
                                    @foreach($roles as $role)
                                    <option value="{{$role->id}}">{{$role->role_name}}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>

                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <label for="user_id" class="form-label mr-2">User<span class="text-danger">*</span></label>
                            </div>
                            <div class="col-lg-7">
                                <select name="user_id" id="user_id" class="form-control form-select" placeholder='Select User' data-search="on" disabled>
                                    <option selected disabled>Select Users</option>
                                </select>
                                <input type="hidden" name="assign_task_id" id="assign_task_id">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <div class="row">
                        <div class="col-lg-12 p-0 text-right">
                            <button class="btn btn-outline-light" data-dismiss="modal" aria-label="Close">Cancel</button>
                            <button class="btn btn-primary submitBtnAssignTask" type="submit">Submit</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade zoom" tabindex="-1" id="addSalarySlipModal" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Salary Slip</h5>
                <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
            </div>
            <form action="#" role="form" class="mb-0" method="post" id="addSalarySlipForm">
                @csrf
                <div class="modal-body modal-body-lg">
                    <div class="gy-3">
                        <div class="row g-3 align-center">
                            <span id="payslip_month" style="font-weight: 700;"></span>
                            <input type="hidden" name="payslip_month_id" id="payslip_month_id">
                            <input type="hidden" name="payslip_year_id" id="payslip_year_id">
                            <input type="hidden" name="salary_date" id="salary_date">
                            <input type="hidden" name="salary_user_id" id="salary_user_id" value="{{ $data->user_id ?? '' }}">
                        </div>
                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <label for="add_basic_salary" class="form-label mr-2">Basic<span class="text-danger">*</span></label>
                            </div>
                            <div class="col-lg-7">
                                <div class="input-group mb-3">
                                    <input type="text" name="add_basic_salary" id="add_basic_salary" class="form-control" onkeypress="return isNumber(event)">
                                    <span class="input-group-text">Rs</span>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <label for="add_hra" class="form-label mr-2">HRA<span class="text-danger">*</span></label>
                            </div>
                            <div class="col-lg-7">
                                <div class="input-group mb-3">
                                    <input type="text" name="add_hra" id="add_hra" class="form-control" onkeypress="return isNumber(event)">
                                    <span class="input-group-text">Rs</span>
                                </div>

                            </div>
                        </div>

                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <label for="add_conveyance" class="form-label mr-2">Conveyance<span class="text-danger">*</span></label>
                            </div>
                            <div class="col-lg-7">
                                <div class="input-group mb-3">
                                    <input type="text" name="add_conveyance" id="add_conveyance" class="form-control" onkeypress="return isNumber(event)">
                                    <span class="input-group-text">Rs</span>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <label for="add_special_allowance" class="form-label mr-2">Special Allowance<span class="text-danger">*</span></label>
                            </div>
                            <div class="col-lg-7">
                                <div class="input-group mb-3">
                                    <input type="text" name="add_special_allowance" id="add_special_allowance" class="form-control" onkeypress="return isNumber(event)">
                                    <span class="input-group-text">Rs</span>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <label for="add_professional_tax" class="form-label mr-2">Professional Tax<span class="text-danger">*</span></label>
                            </div>
                            <div class="col-lg-7">
                                <div class="input-group mb-3">
                                    <input type="text" name="add_professional_tax" id="add_professional_tax" class="form-control" onkeypress="return isNumber(event)">
                                    <span class="input-group-text">Rs</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <div class="row">
                        <div class="col-lg-12 p-0 text-right">
                            <button class="btn btn-outline-light" data-dismiss="modal" aria-label="Close">Cancel</button>
                            <button class="btn btn-primary submitBtnAddSalarySlip" type="button">Submit</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@else
<div>
    <div class="tab-pane fade show  mt-5" id="leaves">
        <div class="col-md-12" style="display: flex;justify-content: center">
            <img width="300" src="https://parivaar.siplsolutions.com/images/empty_item.svg">
        </div>

        <h5 class="col-md-12" style="display: flex;justify-content: center;">No records found.</h5>
    </div>
</div>
@endif

<div class="modal fade zoom" tabindex="-1" id="filterShift">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Filter</h5>
                <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
            </div>
            <form action="#" role="form" class="mb-0" method="get" id="shiftFilterForm">
                @csrf
                <div class="modal-body modal-body-lg">
                    <div class="gy-3">
                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <x-inputs.verticalFormLabel label="Ambulance No" for="ambulance_id" />
                            </div>
                            <div class="col-lg-7">
                                <x-inputs.select size="md" name="ambulance_id" for="ambulance_id" id="ambulance_id" data-search="on" placeholder='Select Ambulance Number'>
                                    <option value="" selected disabled>Select Ambulance Number</option>
                                    @if(!empty($ambulances))
                                    @foreach($ambulances as $key => $ambulance)
                                    <option value="{{$ambulance->id}}">{{!empty($ambulance->ambulance_no) ? $ambulance->ambulance_no : $ambulance->chassis_no; }}</option>
                                    @endforeach
                                    @endif
                                </x-inputs.select>
                            </div>
                        </div>
                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <x-inputs.verticalFormLabel label="Shift" for="shift_type" />
                            </div>
                            <div class="col-lg-7">
                                <x-inputs.select size="md" name="shift_type" for="shift_type" id="shift_type" data-search="on" placeholder='Select Shift Type'>
                                    <option value="" selected disabled>Select Shift Type</option>
                                    @if(!empty($shiftTypes))
                                    @foreach($shiftTypes as $key => $shiftType)
                                    <option value="{{$shiftType->id}}">{{$shiftType->shift_name ?? '' }}</option>
                                    @endforeach
                                    @endif
                                </x-inputs.select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12 p-0 text-right">
                                <a data-dismiss="modal" aria-label="Close" class="btn btn-outline-light cancel">Cancel</a>
                                <a class="btn btn-danger resetFilter" data-dismiss="modal" data-target='#filterShift' style="color:#fff">Clear
                                    Filter</a>
                                <a class="btn btn-primary submitBtnFilter" style="color:#fff">Submit</a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade zoom" tabindex="-1" id="addShiftModal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Shift</h5>
                <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
            </div>
            <form role="form" class="mb-0" method="POST" id="addShiftForm">
                @csrf
                <div class="modal-body modal-body-lg">
                    <div class="gy-3">
                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <label for="shift_district_id" class="form-label">District <span class="text-danger">*</span></label>
                            </div>
                            <div class="col-lg-7">
                                <div class="form-group form-control-required-wrap">
                                    <select name="shift_district_id" id="shift_district_id" class="form-control form-select" data-search="on" placeholder='Select District' data-parsley-excluded="true">
                                        <option selected disabled>Select District</option>
                                        @foreach ($districts as $key => $district)
                                        <option value="{{ $district->id }}">
                                            {{ $district->district_name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <label for="ambulance_id" class="form-label">Ambulance Number <span class="text-danger">*</span></label>
                            </div>
                            <div class="col-lg-7">
                                <div class="form-group form-control-required-wrap">
                                    <select name="shift_ambulance_id" id="shift_ambulance_id" class="form-control form-select" data-search="on" placeholder='Select Ambulance' disabled data-parsley-excluded="true">
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <label for="shift_id" class="form-label">Shift <span class="text-danger">*</span></label>
                            </div>
                            <div class="col-lg-7">
                                <div class="form-group form-control-required-wrap">
                                    <select name="shift_id[]" id="shift_id" class="form-control form-select" data-search="on" placeholder='Select Shift' data-parsley-excluded="true" size="5" multiple>
                                        @if(!empty($shiftTypes))
                                        @foreach($shiftTypes as $key => $shiftType)
                                        <option value="{{$shiftType->id}}">{{$shiftType->shift_name ?? '' }}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <label class="form-label" for="service_area">Service Area <span class="text-danger">*</span></label>
                            </div>

                            <div class="col-lg-7">
                                <div class="form-group form-control-required-wrap">
                                    <select name="service_area_id" id="service_area_id" class="form-control form-select" data-search="on" placeholder='Select Service Area' disabled data-parsley-excluded="true">
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <label class="form-label" for="station_area">Station Area <span class="text-danger">*</span></label>
                            </div>
                            <div class="col-lg-7">
                                <input type="text" class="form-control" name="station_area" placeholder="Enter Station Area" data-parsley-excluded="true">
                            </div>
                        </div>
                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <label class="form-label" for="shift_start_date">Shift Start Date <span class="text-danger">*</span></label>
                            </div>
                            <div class="col-lg-7">
                                <input type="text" class="form-control" name="shift_start_date" id="shift_start_date" placeholder="Select Shift Start Date" data-parsley-excluded="true" readonly>
                            </div>
                        </div>
                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <label class="form-label" for="shift_end_date">Shift End Date <span class="text-danger">*</span></label>
                            </div>
                            <div class="col-lg-7">
                                <input type="text" class="form-control" name="shift_end_date" id="shift_end_date" placeholder="Select Shift End Date" readonly data-parsley-excluded="true">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12 p-0 text-right">
                                <button class="btn btn-outline-light" data-dismiss="modal" aria-label="Close">Cancel</button>
                                <button class="btn btn-primary submitBtn" type="submit"><span>Submit</span></button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@php
$joiningDate = $data->joining_date ?? $data->account_created_date;

// Ensure $joiningDate is a DateTime object
if (is_string($joiningDate)) {
$joiningDate = new DateTime($joiningDate);
$joiningDate = $joiningDate->format('Y-m-d');
}
@endphp

@endsection
@push('footerScripts')
<!-- <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script> -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/parsley.js/2.9.2/parsley.min.js"></script>
<script src="{{ url('js/tableFlow.js') }}"></script>
<script type="text/javascript">
    var root_url = "<?php echo Request::root(); ?>";
    $(document).ready(function() {
        getSalaryDetails();
        getAttendanceDetails();
        getLeaveDetails();
        getTaskDetails();

        $("#shift_year_id").select2();
        $("#shift_month_id").select2();
    });

    function onlyAlphanumerics(e) {
        var regex = new RegExp("^[a-zA-Z0-9]+$");
        var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
        if (regex.test(str)) {
            return true;
        }
        e.preventDefault();
        return false;
    }

    function updateFileName(input) {
        var fileName = input.files[0].name;
        var label = input.parentNode.querySelector('.custom-file-label');
        label.textContent = fileName;
    }

    $("#addPayslip").click(function() {
        $('#addSalarySlipModal').modal('show');
        var monthNumber = $("#month_id").val();
        var year = $("#year_id").val();
        var monthName = new Date(year, monthNumber - 1, 1).toLocaleString('default', {
            month: 'long'
        });
        $("#payslip_month_id").val(monthNumber);
        $("#payslip_year_id").val(year);
        $("#salary_date").val(year + '-' + (monthNumber < 10 ? '0' : '') + monthNumber + '-01');
        $("#payslip_month").text('Payslip of ' + monthName + ' ' + year);
    });


    $("#start_date").datepicker({
        todayBtn: true,
        orientation: "auto",
        todayHighlight: true,
        autoclose: true,
        format: "mm/dd/yyyy",
        startView: "days",
        endDate: "today"
    });
    $('#start_date').each(function() {
        $(this).on('changeDate', function(e) {
            start_date = $(this).datepicker('getDate');
            $('#start_date').datepicker('update', start_date);
            $('#end_date').datepicker('update', start_date);
            startDate = $("#start_date").val();
            $('#end_date').datepicker('setStartDate', startDate);
            var start_date = document.getElementById('start_date').value;
            var end_date = document.getElementById('end_date').value;
            if (end_date == null || end_date == '') {
                $('#end_date').datepicker('update', start_date);
            }
        });
    });

    $('#end_date').datepicker({
        todayBtn: true,
        orientation: "auto",
        todayHighlight: true,
        autoclose: true,
        format: "mm/dd/yyyy",
        startView: "days",
        minViewDate: 0,
        maxViewDate: 0,
        endDate: "today"
    });
    $('#end_date').each(function() {
        $(this).on('changeDate', function(e) {
            start_date = $(this).datepicker('getDate');
            $('#end_date').datepicker('update', start_date);
            var start_date = document.getElementById('start_date').value;
            var end_date = document.getElementById('end_date').value;
            if (start_date == null || start_date == '') {
                $('#start_date').datepicker('update', end_date);
            }
        });
    });

    $('#shift_start_date').datepicker({
        todayBtn: true,
        orientation: "auto",
        todayHighlight: true,
        autoclose: true,
        format: "dd/mm/yyyy",
        startView: "days",
        // endDate: "today"
    });
    $('#shift_start_date').each(function() {
        $(this).on('changeDate', function(e) {
            shift_start_date = $(this).datepicker('getDate');
            $('#shift_start_date').datepicker('update', shift_start_date);
            $('#shift_end_date').datepicker('update', shift_start_date);
            shiftStartDate = $('#shift_start_date').val();
            $('#shift_end_date').datepicker('setStartDate', shiftStartDate);
            var shift_start_date = document.getElementById('shift_start_date').value;
            var shift_end_date = document.getElementById('shift_end_date').value;
            if (shift_end_date == null || shift_end_date == '') {
                $('#shift_end_date').datepicker('update', shift_start_date);
            }
        });
    });

    $('#shift_end_date').datepicker({
        todayBtn: true,
        orientation: "auto",
        todayHighlight: true,
        autoclose: true,
        format: "dd/mm/yyyy",
        startView: "days",
        minViewDate: 0,
        maxViewDate: 0,
        // endDate: "today"
    });
    $('#shift_end_date').each(function() {
        $(this).on('changeDate', function(e) {
            shift_start_date = $(this).datepicker('getDate');
            $('#shift_end_date').datepicker('update', shift_start_date);
            var shift_start_date = document.getElementById('shift_start_date').value;
            var shift_end_date = document.getElementById('shift_end_date').value;
            if (shift_start_date == null || shift_start_date == '') {
                $('#shift_start_date').datepicker('update', shift_end_date);
            }
        });
    });

    $(document).ready(function() {
        // Initial state: Show span and hide button
        $('#updateBtn').on('click', function() {
            updateProfileDetails();
            updateReportingManager();
        });

        $('#addShiftModal').on('hidden.bs.modal', function(e) {
            // Reset form fields
            $('#addShiftForm')[0].reset();
            $('#shift_id').val('').trigger('change');
            $('#shift_district_id').val('').trigger("change");
            $('#shift_ambulance_id').val('').trigger("change");
            $("#shift_ambulance_id").attr("disabled", true);
            $("#service_area_id").attr("disabled", true);
            $('#addShiftForm input, #addShiftForm select').removeClass('error');
            // Reset validate.js validations
            if ($('#addShiftForm').validate && typeof $('#addShiftForm').validate === 'function') {
                $('#addShiftForm').validate().resetForm();
            }
        });

    });

    $(document).ready(function() {
        $('#updateAttandanceBtn').show();
        $('#updateAttendanceInfo').hide();
        $('#updateAttendanceForm input, #updateAttendanceForm select,#updateAttendanceForm textarea').prop('disabled', true);

        $('#updateAttandanceBtn').click(function() {
            $('#updateAttendanceForm input, #updateAttendanceForm select,#updateAttendanceForm textarea').prop('disabled', function(_, prop) {
                return !prop;
            });
            $('#updateAttendanceInfo').toggle();
            $('#updateAttandanceBtn').toggle();
        });
        $('#viewUserAttendanceInfo').on('hidden.bs.modal', function(e) {
            $('#updateAttendanceForm')[0].reset();
            $('#updateAttendanceForm input, #updateAttendanceForm select,#updateAttendanceForm textarea').prop('disabled', true);
            $('#updateAttandanceBtn').show();
            $('#updateAttendanceInfo').hide();
            $('#updateAttendanceForm input, #updateAttendanceForm select, #updateAttendanceForm textarea').removeClass('error');
            // Reset validation messages (if jQuery Validation plugin is used)
            if ($('#updateAttendanceForm').validate && typeof $('#updateAttendanceForm').validate === 'function') {
                var validator = $('#updateAttendanceForm').validate(); // Get the validator object
                validator.resetForm(); // Reset the form validation
            }
        });
    });

    $('#district_id').on('change', function() {
        // Get the selected value
        var selectedDistrictId = $(this).val();

        // Perform AJAX call
        $.ajax({
            url: "{{ url('/get-state') }}", // Replace with your actual AJAX endpoint
            type: 'GET', // Adjust the HTTP method as needed
            data: {
                district_id: selectedDistrictId,
                // Add any other data you want to send in the AJAX request
            },
            success: function(response) {
                // Handle the AJAX success response
                //console.log(response.state);
                $('#state_name').val(response.state);
                $('#state_id').val(response.stateId);
            },
        });
    });


    function updateReportingManager() {
        var selectedDistrictId = $('#district_id').val();
        var selectedRole = $('#role_id').val();
        $('#reporting_manager_id').attr('disabled', false);


        $.ajax({
            url: "{{url('/get-reporting-manager')}}",
            type: 'GET',
            contentType: 'JSON', // Fix the contentType value
            data: {
                district_id: selectedDistrictId,
                role_id: selectedRole,
            },
            success: function(response) {
                $('#reporting_manager_id').empty();
                $('#reporting_manager_id').append('<option value="{{ $data->reporting_manager_id }}" selected disabled>{{ $data->reporting_manager_name }}</option>');

                for (var i = 0; i < response.reportingManager.length; i++) {
                    $('#reporting_manager_id').append('<option value="' + response.reportingManager[i].user_id + '">' + response.reportingManager[i].employee_name + '</option>');
                }

                var selectedMangerId = $('#reportingManager').val();
                $('#reporting_manager_id').val(selectedMangerId).trigger('change');
            },
        });
    }



    $('#district_id').on('change', function() {
        updateReportingManager();
    });


    $("#dob").datepicker({
        todayBtn: true,
        orientation: "auto",
        todayHighlight: true,
        autoclose: true,
        format: "dd/mm/yyyy",
        startView: "days",
        endDate: "-18Y"
    });
    $('#dob').each(function() {
        $(this).on('changeDate', function(e) {
            dob = $(this).datepicker('getDate');
            $('#dob').datepicker('update', dob);
        });
    });

    $("#joining_date").datepicker({
        todayBtn: true,
        orientation: "auto",
        todayHighlight: true,
        autoclose: true,
        format: "dd/mm/yyyy",
        startView: "days",
        endDate: "today"
    });
    $('#joining_date').each(function() {
        $(this).on('changeDate', function(e) {
            joining_date = $(this).datepicker('getDate');
            $('#joining_date').datepicker('update', joining_date);
        });
    });

    $("#license_expiry_date").datepicker({
        todayBtn: true,
        orientation: "auto",
        todayHighlight: true,
        autoclose: true,
        format: "dd/mm/yyyy",
        startView: "days",
        //endDate: "today"
    });
    $('#license_expiry_date').each(function() {
        $(this).on('changeDate', function(e) {
            license_expiry_date = $(this).datepicker('getDate');
            $('#license_expiry_date').datepicker('update', license_expiry_date);
        });
    });


    $(document).on('click', '.approveLeave', function(e) {
        var leaveId = $(this).data('id');
        var userId = $(this).data('user-id');

        console.log(leaveId);

        Swal.fire({
            title: 'Confirmation',
            text: "Do you want to approve this Leave Request?",
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'No'
        }).then(function(result) {
            if (result.value) {
                $.ajax({
                    url: root_url + '/human-resource/employees/approve-leave',
                    data: {
                        'leave_id': leaveId,
                        'user_id': userId
                    },
                    method: "GET", // Change the method to POST
                    dataType: "json", // Expect JSON response
                    cache: false,
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('', 'Leave Approved', 'success');
                            location.reload();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: response.message,
                            });
                        }
                    },
                });
            }
        });

        e.preventDefault();
    });

    $(document).on('click', '.rejectLeave', function(e) {
        var leaveId = $(this).data('id');
        var userId = $(this).data('user-id');

        console.log(leaveId);
        Swal.fire({
            title: 'Confirmation',
            text: "Do you want to reject this Leave Request?",
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
                    $.ajax({
                        url: root_url + '/human-resource/employees/reject-leave',
                        data: {
                            'leave_id': leaveId,
                            'user_id': userId,
                            'reason': reason
                        },
                        //dataType: "html",
                        method: "GET",
                        cache: false,
                        success: function(response) {
                            console.log(response);
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Leave Rejected',
                                    //text: "Feel free to update the account information & approve this account again if needed.",
                                })
                                location.reload();
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: data.message,
                                    //footer: '<a href>Why do I have this issue?</a>'
                                })
                            }
                        }
                    });
                }
            })

        });
        e.preventDefault();
    });
    $(document).on('click', '.cancelTask', function(e) {
        var id = $(this).data('task-id');
        $('#task_id').val(id);
    });

    $(document).on('click', '.reassignTask', function(e) {
        var id = $(this).data('task-id');
        $('#task_id').val(id);
    });
    $(document).on('click', '.approveLeave', function(e) {
        var id = $(this).data('id');
        $('#leave_id').val(id);
    });

    $(document).on('click', '.rejectLeave', function(e) {
        var id = $(this).data('id');
        $('#leave_id').val(id);
    });

    $('#updateProfile').on('click', function(e) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // disabled submit button during process running status
        $("#updateProfile").attr("disabled", true);

        $.ajax({
            type: "POST",
            url: "{{ url('human-resource/employees/update-user') }}",
            // data: $('#add_user_form').serialize(),
            data: new FormData($('#editProfileForm')[0]),
            contentType: false,
            processData: false,
            success: function(response) {
                $("#updateProfile").attr("disabled", false);
                if (response.status == 'success') {
                    // $('#profileDetails').load(window.location.href + ' #profileDetails');
                    toastr.success(response.message);
                    window.location.reload();
                } else {
                    toastr.error(response.message);
                }
            }
        });
        $("#updateProfile").attr("disabled", false);
    });
    $("#attendance_leave_type").change(function() {
        $("#updateAttendanceForm").validate().element("#attendance_leave_type");
    });
    if ($("#updateAttendanceForm").length > 0) {
        $("#updateAttendanceForm").validate({
            rules: {
                checkIn_time: {
                    required: true,
                },
                checkIn_location: {
                    required: true,
                },
                checkOut_time: {
                    required: true,
                },
                checkOut_location: {
                    required: true,
                },
                duration: {
                    required: true,
                },

            },
            messages: {
                checkIn_time: {
                    required: "Please enter check in time.",
                },
                checkIn_meter_reading: {
                    required: "Please enter check in meter reading.",
                },
                checkIn_location: {
                    required: "Please enter check in location.",
                },
                checkOut_time: {
                    required: "Please enter check out time.",
                },
                checkOut_meter_reading: {
                    required: "Please enter checkout meter reading.",
                },
                checkOut_location: {
                    required: "Please enter checkout location.",
                },
                duration: {
                    required: "Please enter duration.",
                },
                km_run: {
                    required: "Please enter km run.",
                },
                attendance_leave_type: {
                    required: "Please select leave type.",
                },
                attendance_leave_reason: {
                    required: "Please enter leave reason.",
                },
            },
            submitHandler: function(form, event) {
                event.preventDefault();
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                // disabled submit button during process running status
                $(".updateAttendanceInfo").attr("disabled", true);

                $.ajax({
                    type: "POST",
                    url: "{{ url('human-resource/employees/update-attendance-by-user') }}",
                    data: new FormData($('#updateAttendanceForm')[0]),
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        $(".updateAttendanceInfo").attr("disabled", false);
                        if (response.status == 'success') {
                            window.location.reload();
                            toastr.success(response.message);
                        } else {
                            toastr.error(response.message);
                        }
                    }
                });

                $(".updateAttendanceInfo").attr("disabled", false);
            }
        });
    }

    $('.submitBtnAddSalarySlip').on('click', function(e) {
        var id = $('#profile_user_id').val();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // disabled submit button during process running status
        $(".submitBtnAddSalarySlip").attr("disabled", true);

        $.ajax({
            type: "POST",
            url: "{{ url('human-resource/employees/add-payslip-by-month-id') }}",
            data: new FormData($('#addSalarySlipForm')[0]),
            contentType: false,
            processData: false,
            success: function(response) {
                $(".submitBtnAddSalarySlip").attr("disabled", false);
                if (response.status == 'success') {
                    toastr.success(response.message);
                    window.location.reload();
                    // $('#addSalarySlipModal').modal('hide');
                    window.location.href = root_url + '/human-resource/employees/driver/view-user/' + id + '#salary';
                } else {
                    toastr.error(response.message);
                }
            }
        });
    });

    $('#deletePayslip').on('click', function(e) {

        var monthNumber = $("#month_id").val();
        var year = $("#year_id").val();
        var user_id = $(this).data("user-id");

        $.ajax({
            type: "GET",
            url: "{{ url('human-resource/employees/delete-payslip-by-month-id') }}",
            data: {
                month_id: monthNumber,
                year_id: year,
                user_id: user_id
                // Add any other data you want to send in the AJAX request
            },
            success: function(response) {
                if (response.status == 'success') {
                    toastr.success(response.message);
                    window.location.reload();
                } else {
                    toastr.error(response.message);
                }
            }
        });
    });

    // Assuming your checkbox has an id of 'active_status'
    $('#active_status').on('change', function() {
        // Get the selected value
        var selectedStatus = $(this).is(':checked') ? 1 : 0;
        // Assuming the status is a boolean (1 for true, 0 for false)

        // Perform AJAX call
        $.ajax({
            url: "{{ url('human-resource/employees/update-user-status') }}", // Replace with your actual AJAX endpoint
            type: 'GET', // Change to POST since you are updating data
            data: {
                id: '{{ $data->user_id }}', // Replace with the actual user ID
                active_status: selectedStatus,
                // Add any other data you want to send in the AJAX request
            },
            success: function(response) {
                // Handle the AJAX success response
                if (response.success) {
                    var spanText = selectedStatus ? 'Activated' : 'Deactivated';
                    toastr.success('Status ' + spanText + ' Successfully');
                } else {
                    toastr.error(response.msg);
                    setTimeout(function() {
                        window.location.reload();
                    }, 5000);
                }
            },
        });
    });

    $('.approve_user').on('click', function(e) {
        var id = $(this).attr('id');
        console.log(id);
        Swal.fire({
            title: 'Confirmation',
            text: "Do you want to approve this User Account?",
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
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('', 'User Account Approved',
                                'success');
                            location.reload()
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: response.message,
                            })
                        }
                    }
                });

            }
        });
        e.preventDefault();
    });
    $('.reject_user').on('click', function(e) {
        var id = $(this).attr('id');
        Swal.fire({
            title: 'Confirmation',
            text: "Do you want to reject this User Account Approval Request?",
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
                                Swal.fire({
                                    icon: 'error',
                                    title: 'User Account Rejected',
                                    text: "Feel free to update the account information & approve this account again if needed.",
                                })
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

    function getLeaveDetails() {
        var month_id = $('#leave_month_id').val();
        var year_id = $('#leave_year_id').val();
        var user_id = $('#view_user_id').val();
        $.ajax({
            url: "{{url('/human-resource/employees/get-leave-info-by-date')}}", // Replace with your actual AJAX endpoint
            type: 'GET', // Adjust the HTTP method as needed
            dataType: 'json',
            data: {
                month_id: month_id,
                year_id: year_id,
                user_id: user_id
                // Add any other data you want to send in the AJAX request
            },
            success: function(response) {
                // Assuming response.leaveDetails is an array of leave details

                // Clear any existing content in the container
                $('#leaveDetailsContainer').empty();
                if (response.leaveDetails == '' || response.leaveDetails == null) {
                    $("#leaveDetailsContainer").html('<div class="col-md-12" style="display: flex;justify-content: center">' +
                        '<img width="300" src="https://parivaar.siplsolutions.com/images/empty_item.svg">' +
                        '</div>' +
                        '<h5 class="col-md-12" style="display: flex;justify-content: center;">No records found.</h5>');
                } else {
                    // Iterate through each leave detail
                    response.leaveDetails.forEach(function(leaveDetail) {
                        var showActionButton = false;
                        // Clone the template structure
                        var leaveDetailTemplate = $('#leaveDetailTemplate').clone();

                        // Populate the cloned template with data from the current leaveDetail
                        leaveDetailTemplate.find('.leave_status').text(leaveDetail.status);
                        leaveDetailTemplate.find('.leave_type').text(leaveDetail.leave_type);
                        leaveDetailTemplate.find('.leave_from_date').text(leaveDetail.leave_from_date);
                        leaveDetailTemplate.find('.leave_to_date').text(leaveDetail.leave_to_date);
                        leaveDetailTemplate.find('.leave_amount').text(leaveDetail.duration_in_days);
                        leaveDetailTemplate.find('.leave_applied_to').text(leaveDetail.applying_to_name + ' (' + leaveDetail.role_name + ')');

                        leaveDetailTemplate.find('.leave_applied_date').text(leaveDetail.leave_created_at);
                        leaveDetailTemplate.find('.leave_approved_date').text(leaveDetail.leave_approved_on);
                        leaveDetailTemplate.find('.leave_reason').text(leaveDetail.leave_reason);
                        var $anchor = leaveDetailTemplate.find('.leave_attachment a');
                        $anchor.attr('href', leaveDetail.attachment);

                        // Check if leaveDetail.attachment is not empty
                        if (leaveDetail.attachment) {
                            $anchor.text('View'); // Set the text if the condition is true
                            $anchor.css('cursor', 'pointer');
                        }
                        if (leaveDetail.reject_reason) {
                            leaveDetailTemplate.find('.leave_reason').text(leaveDetail.reject_reason);
                            $('#reject_reason').css('display', 'block');
                        }
                        // alert(leaveDetail.status);
                        if (leaveDetail.status != 'pending') {
                            $('#leaves_action_btn').css('display', 'block');
                        }

                        leaveDetailTemplate.find('.approveLeave').attr('data-id', leaveDetail.id);
                        leaveDetailTemplate.find('.approveLeave').attr('data-user-id', leaveDetail.user_id);
                        leaveDetailTemplate.find('.rejectLeave').attr('data-id', leaveDetail.id);
                        leaveDetailTemplate.find('.rejectLeave').attr('data-user-id', leaveDetail.user_id);
                        // Repeat this process for other fields...
                        leaveDetailTemplate.find('.approveLeave').attr('id', 'approveLeave' + leaveDetail.id);
                        leaveDetailTemplate.find('.rejectLeave').attr('id', 'rejectLeave' + leaveDetail.id);

                        if (leaveDetail.status === 'pending') {
                            showActionButton = true;
                        }



                        // Append the populated template to the container
                        $('#leaveDetailsContainer').append(leaveDetailTemplate.html());

                        if (showActionButton === true) {
                            $('#approveLeave' + leaveDetail.id).css('display', 'block');
                            $('#rejectLeave' + leaveDetail.id).css('display', 'block');
                        } else {
                            $('#approveLeave' + leaveDetail.id).css('display', 'none');
                            $('#rejectLeave' + leaveDetail.id).css('display', 'none');
                        }
                    });
                }
            },
        });
    }



    function getTaskDetails() {
        $('.overlay').show();
        var month_id = $('#task_month_id').val();
        var year_id = $('#task_year_id').val();
        var user_id = $('#view_user_id').val();

        $.ajax({
            url: "{{url('/human-resource/employees/get-task-info-by-date')}}", // Replace with your actual AJAX endpoint
            type: 'GET', // Adjust the HTTP method as needed
            dataType: 'json',
            data: {
                month_id: month_id,
                year_id: year_id,
                user_id: user_id
                // Add any other data you want to send in the AJAX request
            },
            success: function(response) {



                // Clear any existing content in the container
                $('#taskDetailsContainer').empty();
                if (response.taskDetails == '' || response.taskDetails == null) {
                    $("#taskDetailsContainer").html('<div class="col-md-12" style="display: flex;justify-content: center">' +
                        '<img width="300" src="https://parivaar.siplsolutions.com/images/empty_item.svg">' +
                        '</div>' +
                        '<h5 class="col-md-12" style="display: flex;justify-content: center;">No records found.</h5>');
                } else {

                    // Iterate through each leave detail
                    response.taskDetails.forEach(function(taskDetail) {
                        var showActionButton = false;
                        // Clone the template structure
                        var taskDetailTemplate = $('#taskDetailTemplate').clone();

                        // Populate the cloned template with data from the current taskDetail
                        taskDetailTemplate.find('.task_title').text(taskDetail.title);
                        taskDetailTemplate.find('.task_priority').text(taskDetail.priority);
                        taskDetailTemplate.find('.task_assigned_to').text(taskDetail.task_assigned_to);
                        taskDetailTemplate.find('.task_created_date').text(taskDetail.task_created_date);
                        taskDetailTemplate.find('.task_created_by').text(taskDetail.task_created_by + ' (' + taskDetail.task_created_user_role + ')');
                        taskDetailTemplate.find('.task_status').text(taskDetail.status);

                        taskDetailTemplate.find('.task_updated_date').text(taskDetail.task_updated_date);
                        taskDetailTemplate.find('.task_remarks').text(taskDetail.remark);
                        taskDetailTemplate.find('.task_description').text(taskDetail.description);
                        taskDetailTemplate.find('.task_attachment a').attr('href', taskDetail.attached_file);
                        taskDetailTemplate.find('.reassignTask').attr('data-task-id', taskDetail.id);
                        taskDetailTemplate.find('.reassignTask').attr('data-user-id', taskDetail.user_id);
                        taskDetailTemplate.find('.cancelTask').attr('data-task-id', taskDetail.id);
                        taskDetailTemplate.find('.cancelTask').attr('data-user-id', taskDetail.user_id);

                        taskDetailTemplate.find('.cancelTask').attr('id', 'cancel' + taskDetail.id);
                        taskDetailTemplate.find('.reassignTask').attr('id', 'assign' + taskDetail.id);


                        // alert(taskDetail.status);
                        if (taskDetail.status === 'Created' || taskDetail.status === 'Assigned' || taskDetail.status === 'In Progress') {
                            showActionButton = true;
                        }
                        // if ((taskDetail.status != 'Assigned') || (taskDetail.status != 'In Progress')) {
                        //     $('#task_action_btn').css('display', 'block');
                        // }
                        $('#task_id').val(taskDetail.id);
                        $('#assign_task_id').val(taskDetail.id);
                        $('#assign_role_id').val(taskDetail.role_id).trigger('change');
                        setTimeout(function() {
                            $('#user_id').val(taskDetail.user_id).trigger('change');
                        }, 8000);
                        // Repeat this process for other fields...

                        // Append the populated template to the container
                        $('#taskDetailsContainer').append(taskDetailTemplate.html());

                        if (showActionButton === true) {
                            $('#cancel' + taskDetail.id).css('display', 'block');
                            $('#assign' + taskDetail.id).css('display', 'block');
                        } else {
                            $('#assign' + taskDetail.id).css('display', 'none');
                            $('#cancel' + taskDetail.id).css('display', 'none');
                        }
                    });


                }
            }

        });
        $('.overlay').hide();
    }

    function getSalaryDetails() {
        var month_id = $('#month_id').val();
        var year_id = $('#year_id').val();
        var user_id = $('#view_user_id').val();
        $.ajax({
            url: "{{url('/human-resource/employees/get-payslip')}}", // Replace with your actual AJAX endpoint
            type: 'GET', // Adjust the HTTP method as needed
            dataType: 'json',
            data: {
                month_id: month_id,
                year_id: year_id,
                user_id: user_id
                // Add any other data you want to send in the AJAX request
            },
            // ...
            success: function(response) {
                $('.overlay').show();
                if (
                    response.paySlipDetails.basic_salary === 0 &&
                    response.paySlipDetails.house_rent_allowance === 0 &&
                    response.paySlipDetails.conveyance_allowance === 0 &&
                    response.paySlipDetails.special_allowances === 0 &&
                    response.paySlipDetails.pf_contribution === 0 &&
                    response.paySlipDetails.professional_tax === 0
                ) {
                    $("#salaryDetailsContainer").empty();
                    $("#salaryDetailsContainer").html('<div class="col-md-12" style="display: flex;justify-content: center">' +
                        '<img width="300" src="https://parivaar.siplsolutions.com/images/empty_item.svg">' +
                        '</div>' +
                        '<h5 class="col-md-12" style="display: flex;justify-content: center;">No records found.</h5>');
                    $('#salaryDetailTemplate').addClass('d-none');
                    $('#updatePayslip').addClass('d-none');
                    $('#deletePayslip').addClass('d-none');
                    $('#downloadSalarySlip').addClass('d-none');
                    $('#addPayslip').removeClass('d-none');
                } else {
                    $("#salaryDetailsContainer").empty();
                    $('#salaryDetailTemplate').removeClass('d-none');
                    $('#updatePayslip').removeClass('d-none');
                    $('#deletePayslip').removeClass('d-none');
                    $('#downloadSalarySlip').removeClass('d-none');
                    $('#addPayslip').addClass('d-none');
                    //$('#salaryDetailTemplate').show();
                    //$('#salaryDetailTemplate').css('display', 'block');
                    $('#basic_salary').text(response.paySlipDetails.basic_salary);
                    $('#hra').text(response.paySlipDetails.house_rent_allowance);
                    $('#conveyance').text(response.paySlipDetails.conveyance_allowance);
                    $('#special_allowance').text(response.paySlipDetails.special_allowances);
                    $('#total_earnings').text(response.totalEarning);
                    $('#pf').text(response.paySlipDetails.pf_contribution);
                    $('#professional_tax').text(response.paySlipDetails.professional_tax);
                    $('#total_deductions').text(response.totalDeduction);
                    $('#net_payment').text('Rs ' + response.netPayable);
                    $('#slipMonthYear').text(response.salaryMonthYear);
                    $('#net_payment_in_words').text(response.netPayableInWords);
                }
                $('.overlay').hide();
            },
            // ...

        });
    }

    function getAttendanceDetails() {
        $('.attendanceDetails').DataTable().destroy();
        // Get values
        var month_id = $('#attendance_month_id').val();
        var year_id = $('#attendance_year_id').val();
        var user_id = $('#view_user_id').val();

        // Initialize DataTable
        var dataTable = new CustomDataTable({
            tableElem: '.attendanceDetails',
            option: {
                processing: true,
                serverSide: true,
                ajax: {
                    type: "GET",
                    url: root_url + "/human-resource/employees/get-attendance-by-user-id/" + month_id + "/" + year_id + "/" + user_id,

                },
                columns: [{
                        "class": "nk-tb-col tb-col-lg",
                        data: 'attendance_date',
                        name: 'attendance_date',
                        orderable: false
                    },
                    {
                        "class": "nk-tb-col tb-col-lg",
                        data: 'shift_name',
                        name: 'shift_name',
                        orderable: false
                    },
                    {
                        "class": "nk-tb-col tb-col-lg",
                        data: 'attendance_status',
                        name: 'attendance_status',
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
                order: [
                    [1, 'asc']
                ],
            },
        });
    }
    $(document).ready(function() {
        $('#attendance_status').change(function() {
            var selectedOption = $(this).val();

            // Hide all status-related elements initially
            $('.presentStatus').addClass('d-none');
            $('.leaveStatus').addClass('d-none');
            $('#noteInfo').addClass('d-none');
            $('#attendance_leave_type').removeAttr('required');
            $('#attendance_leave_reason').removeAttr('required');

            // If Present option is selected, show the related elements
            if (selectedOption === 'Present') {
                $('#noteInfo').removeClass('d-none');
                $('.presentStatus').removeClass('d-none');
                $('#attendance_leave_type').removeAttr('required');
                $('#attendance_leave_reason').removeAttr('required');
            } else if (selectedOption === 'Leave') {
                $('.leaveStatus').removeClass('d-none');
                $('#attendance_leave_type').attr('required', true);
                $('#attendance_leave_reason').attr('required', true);
            }
        });
    });
    $(document).ready(function() {
        $('.roleStatus').css('display', 'none');
        $('.attendanceDetails').on('click', '.view-user-attendance', function(e) {

            // check user role hide role based fields
            $('.roleStatus').addClass('d-none');
            var role = $(this).data('role-name');
            if (role === 'driver' || role === 'attendant') {
                $('.roleStatus').css('display', 'block');
                $('#km_run').attr('required', true);
                $('#checkOut_meter_reading').attr('required', true);
                $('#checkIn_meter_reading').attr('required', true);
            } else {
                $('#km_run').removeAttr('required');
                $('#checkOut_meter_reading').removeAttr('required');
                $('#checkIn_meter_reading').removeAttr('required');
                $('.roleStatus').css('display', 'none');

            }
            var id = $(this).data('id');
            var attendanceDate = $(this).data('date');
            var shiftId = $(this).data('shift-id');
            $.ajax({
                url: "{{url('/human-resource/employees/get-attendance-info-by-date')}}", // Replace with your actual AJAX endpoint
                type: 'GET', // Adjust the HTTP method as needed
                dataType: 'json',
                data: {
                    user_id: id,
                    attendanceDate: attendanceDate,
                    shiftId: shiftId
                    // Add any other data you want to send in the AJAX request
                },
                success: function(response) {
                    if (response.attendanceInfo != '' || response.attendanceInfo != null) {
                        $("#attendance_status").val(response.attendanceInfo.attendanceLogs[0]?.attendanceStatus).trigger('change');
                        $("#attendance_date").text(response.attendanceInfo.date);
                        $("#checkIn_time").val(response.attendanceInfo.attendanceLogs[0]?.login_time);
                        $("#checkIn_meter_reading").val(response.attendanceInfo.attendanceLogs[0]?.login_meter_reading);
                        $("#checkIn_location").text(response.attendanceInfo.attendanceLogs[0]?.login_location);
                        $("#checkOut_time").val(response.attendanceInfo.attendanceLogs[0]?.logout_time);
                        $("#checkOut_meter_reading").val(response.attendanceInfo.attendanceLogs[0]?.logout_meter_reading);
                        $("#checkOut_location").text(response.attendanceInfo.attendanceLogs[0]?.logout_location);
                        $("#duration").val(response.attendanceInfo.attendanceLogs[0]?.duration);
                        $("#km_run").val(response.attendanceInfo.attendanceLogs[0]?.km_run);
                        $("#caseServedCount").text();
                        $("#attendance_shift_id").val(response.attendanceInfo.attendance_shift_id);
                        $("#attendance_details_date").val(response.attendanceInfo.attendance_date);
                        $("#attendance_user_id").val(response.attendanceInfo.attendance_user_id);
                        $("#attendance_leave_reason").val(response.attendanceInfo.attendanceLogs[0]?.leave_reason);
                        //$("#attendance_leave_type").val(response.attendanceInfo.attendanceLogs[0]?.leave_type_id);

                        $("#attendance_leave_type").empty();

                        // Add default option
                        $("#attendance_leave_type").append('<option value="" selected disabled>Select Leave Type</option>');

                        $.each(response.leaveType, function(index, value) {
                            // Append option for each value
                            var option = '<option value="' + value.leave_type_id + '"';
                            if (response.attendanceInfo.attendanceLogs[0]?.leave_type_id !== null && value.leave_type_id === response.attendanceInfo.attendanceLogs[0]?.leave_type_id) {
                                option += ' selected';
                            }
                            option += '>' + value.name + '</option>';
                            $("#attendance_leave_type").append(option);
                        });
                    }
                },
            });
        });


        if ($("#cancelTaskForm").length > 0) {
            $("#cancelTaskForm").validate({
                rules: {
                    reasonForCancellation: {
                        required: true,
                    },
                },
                messages: {
                    reasonForCancellation: {
                        required: "Please enter reason for task cancellation.",
                    },
                },
                submitHandler: function(form) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    // disabled submit button during process running status
                    $(".submitBtnCancelTask").attr("disabled", true);

                    $.ajax({
                        type: "POST",
                        url: "{{ url('task/cancel-task') }}",
                        data: $('#cancelTaskForm').serialize(),
                        success: function(response) {
                            $(".submitBtnCancelTask").attr("disabled", false);
                            if (response.status == 'success') {
                                form.reset();
                                $('#cancelTaskModal').modal('toggle');
                                // refresh the data without page reload
                                //$('#tasks').load(window.location.href + ' #tasks');
                                window.location.reload();
                                toastr.success(response.message);
                            } else {
                                toastr.error(response.message);
                            }
                        }
                    });
                }
            });
        }

        $('#cancelTaskModal').on('hidden.bs.modal', function(e) {
            // Reset the values of input fields
            var form = $(this).find('form')[0];
            form.reset();
            $(form).validate().resetForm();
            $(form).find('.form-control').removeClass('error').removeClass('is-invalid');
        });

        $('#assign_role_id').on('change', function() {

            var selectedRole = $('#assign_role_id').val();

            $.ajax({
                url: "{{url('/get-users')}}", // Replace with your actual AJAX endpoint
                type: 'GET', // Adjust the HTTP method as needed
                contentType: JSON,
                data: {
                    role_id: selectedRole,
                    // Add any other data you want to send in the AJAX request
                },
                success: function(response) {
                    // Handle the AJAX success response
                    $('#user_id').attr('disabled', false);
                    // Clear existing options
                    $('#user_id').empty();

                    // Add a default option
                    $('#user_id').append('<option value="" selected disabled>Select Users</option>');

                    // Add new options
                    for (var i = 0; i < response.users.length; i++) {
                        $('#user_id').append('<option value="' + response.users[i].id + '">' + response.users[i].username + '</option>');
                    }
                },
            });
        });

        // Get values
        var month_id = $('#shift_month_id').val();
        var year_id = $('#shift_year_id').val();
        var user_id = $('#view_user_id').val();

        var items = ['#ambulance_id', '#shift_type'];

        // Initialize DataTable
        var dataTable = new CustomDataTable({
            tableElem: '.shiftDetails',
            option: {
                processing: true,
                serverSide: true,
                ajax: {
                    type: "GET",
                    url: root_url + "/human-resource/employees/get-shift-details/" + month_id + "/" + year_id + "/" + user_id,

                },
                columns: [{
                        "class": "nk-tb-col tb-col-lg",
                        data: 'checkbox',
                        name: 'checkbox',
                        orderable: false
                    }, {
                        "class": "nk-tb-col tb-col-lg",
                        data: 'date',
                        name: 'date',
                        orderable: false
                    },
                    {
                        "class": "nk-tb-col tb-col-lg",
                        data: 'vehicle_no',
                        name: 'vehicle_no',
                        orderable: false
                    },
                    {
                        "class": "nk-tb-col tb-col-lg",
                        data: 'shift_name',
                        name: 'shift_name',
                        orderable: false
                    }
                ],
                order: [
                    [1, 'asc']
                ],
            },
            filterSubmit: '.submitBtnFilter',
            filterSubmitCallback: function() {
                $('#filterShift').modal('toggle');
            },
            filterClearSubmit: '.resetFilter',
            filterModalId: '#filterShift',
            filterItems: items,
            tagId: '#filter_tag_list',
        });


    });

    function getShiftDetails() {
        var month_id = $('#shift_month_id').val();
        var year_id = $('#shift_year_id').val();
        var user_id = $('#view_user_id').val();
        var newUrl = root_url + "/human-resource/employees/get-shift-details/" + month_id + "/" + year_id + "/" + user_id;
        $("#shiftDetails").DataTable().settings()[0].ajax.url = newUrl;
        $("#shiftDetails").DataTable().ajax.reload();
    }

    if ($("#assignTaskForm").length > 0) {
        $("#assignTaskForm").validate({
            rules: {
                assign_role_id: {
                    required: true,
                },
                user_id: {
                    required: true,
                },
            },
            messages: {
                assign_role_id: {
                    required: "Please select the role for whom you want to assign this task.",
                },
                user_id: {
                    required: "Please select the user to whom you want to assign this task.",
                },
            },
            submitHandler: function(form) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                // disabled submit button during process running status
                $(".submitBtnAssignTask").attr("disabled", true);

                $.ajax({
                    type: "POST",
                    url: "{{ url('task/assign-task') }}",
                    data: $('#assignTaskForm').serialize(),
                    success: function(response) {
                        $(".submitBtnAssignTask").attr("disabled", false);
                        if (response.status == 'success') {
                            form.reset();
                            $('#assignTaskModal').modal('toggle');
                            // refresh datatable without page reload
                            $('#tasks').load(window.location.href + ' #tasks');
                            getTaskDetails();
                            toastr.success(response.message);
                        } else {
                            toastr.error(response.message);
                        }
                    }
                });
            }
        });
    }


    // Function to toggle between view and edit modes
    function toggleEditMode() {
        $(".editable").each(function() {
            var currentValue = $(this).text();
            var id = $(this).attr("id");
            var inputField = $("<input type='text' class='form-control' name='" + id + "' id='" + id + "' value='" + currentValue + "' />");
            $(this).replaceWith(inputField);
        });

        // Change the "Submit" button to a "Save" button
        $("#updatePayslip").text("Save").prop("disabled", true);

        // Show the "Update Slip" button and hide the "Update Payslip" button
        $("#updateSlip").show();
        $("#updatePayslip").hide();
    }

    // Function to toggle between edit and view modes
    function toggleViewMode() {
        $("#updateSalarySlipForm input.form-control").each(function() {
            var currentValue = $(this).val();
            var id = $(this).attr("id");
            var editableSpan = $("<span class='editable' id='" + id + "'>" + currentValue + "</span>");
            $(this).replaceWith(editableSpan);
        });

        // Change the "Save" button back to "Submit"
        $("#updatePayslip").html('<em class="icon ni ni-edit"></em> Update').prop("disabled", false);
        $("#updatePayslip").css("display", "block");


        // Show the "Update Payslip" button and hide the "Update Slip" button
        $("#updateSlip").hide();
        $("#updatePayslip").show();
    }
    $(document).ready(function() {
        // Initial setup: Show "Update Payslip" button, hide "Update Slip" button
        $("#updateSlip").hide();
        $("#updatePayslip").show();

        // Attach click event to the "Update Payslip" button
        $("#updatePayslip").on("click", function() {
            toggleEditMode();
        });

        // Attach click event to the "Update Slip" button
        $("#updateSlip").on("click", function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var month_id = $('#month_id').val();
            var year_id = $('#year_id').val();
            var user_id = $('#view_user_id').val();
            var basic_salary = $('#basic_salary').val();
            var hra = $('#hra').val();
            var professional_tax = $('#professional_tax').val();
            var conveyance = $('#conveyance').val();
            var special_allowance = $('#special_allowance').val();
            $.ajax({
                type: "POST",
                url: "{{ url('human-resource/employees/update-salary-slip')}}",
                data: {
                    basic_salary: basic_salary,
                    special_allowance: special_allowance,
                    hra: hra,
                    professional_tax: professional_tax,
                    conveyance: conveyance,
                    month_id: month_id,
                    year_id: year_id,
                    user_id: user_id
                },
                success: function(response) {
                    if (response.status == 'success') {
                        // $('#salary').load(window.location.href + ' #salary');
                        // getSalaryDetails();
                        window.location.reload();
                        toastr.success(response.message);
                        toggleViewMode(); // Switch back to view mode after successful AJAX request
                    } else {
                        toastr.error(response.message);
                    }
                }
            });
        });

        $('.downloadSalarySlip').on('click', function(e) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var month_id = $('#month_id').val();
            var year_id = $('#year_id').val();
            var user_id = $('#view_user_id').val();

            $.ajax({
                url: "{{ url('/human-resource/employees/download-payslip') }}",
                type: 'POST',
                data: {
                    month_id: month_id,
                    year_id: year_id,
                    user_id: user_id
                },
                xhrFields: {
                    responseType: 'blob'
                },
                success: function(response, status, xhr) {
                    if (response.type === 'application/pdf') {
                        var blob = new Blob([response], {
                            type: 'application/pdf'
                        });
                        var link = document.createElement('a');
                        link.href = window.URL.createObjectURL(blob);
                        link.download = "Payslip_" + month_id + year_id + ".pdf";
                        link.click();
                    } else {
                        toastr.error('Payslip Not Found.');
                    }
                },
                error: function(xhr, status, error) {
                    toastr.error('Error downloading payslip.');
                }
            });
        });

    });
    $(document).ready(function() {
        // Function to toggle the visibility of the updateBtn based on the tab's active state
        function toggleUpdateBtnVisibility() {
            var isActive = $('#personal,#employement-record').hasClass('active');
            $('#updateBtn').toggle(isActive);
            $('#updateProfile').toggle(isActive);
        }

        // Initial visibility check on document ready
        toggleUpdateBtnVisibility();

        // Listen for Bootstrap tab show event
        $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
            toggleUpdateBtnVisibility();
        });
    });
    $('#shift_district_id').on('change', function() {
        // Show loader
        $('.overlay').show();

        $('#shift_ambulance_id').attr('disabled', true);
        $('#shift_ambulance_id').empty();

        // Get the selected value
        var selectedDistrictId = $(this).val();

        // Perform AJAX call
        $.ajax({
            url: "{{ url('/human-resource/employees/get-ambulance-by-district-id') }}",
            type: 'GET',
            data: {
                district_id: selectedDistrictId,
            },
            beforeSend: function() {},
            success: function(response) {
                // Clear existing options
                $('#shift_ambulance_id').empty();

                // Add a default option
                $('#shift_ambulance_id').append('<option selected disabled>Select Ambulance Number</option>');

                // Add new options
                for (var i = 0; i < response.ambulance.length; i++) {
                    var ambulanceOption = '<option value="' + response.ambulance[i].id + '">';

                    // Check if ambulance_no is empty, use chassis_no as an alternative
                    if (response.ambulance[i].ambulance_no !== null && response.ambulance[i].ambulance_no !== "") {
                        ambulanceOption += response.ambulance[i].ambulance_no;
                    } else if (response.ambulance[i].chassis_no !== null && response.ambulance[i].chassis_no !== "") {
                        ambulanceOption += response.ambulance[i].chassis_no + ' (Chassis No)';
                    } else {
                        ambulanceOption += 'N/A';
                    }

                    ambulanceOption += '</option>';

                    $('#shift_ambulance_id').append(ambulanceOption);
                }
            },
            complete: function() {
                $('.overlay').hide();
                $('#shift_ambulance_id').attr('disabled', false);
            },
        });
    });


    $('#shift_district_id').on('change', function() {
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

    function updateProfileDetails() {
        $('.edit-profile').removeClass('d-none');
        $('.not-editable').addClass('d-none');
        $('#updateBtn').addClass('d-none');
        $('#updateProfile').removeClass('d-none');
    }

    if ($("#addShiftForm").length > 0) {
        $("#addShiftForm").validate({
            rules: {
                shift_district_id: {
                    required: true,
                },
                shift_ambulance_id: {
                    required: true,
                },
                shift_id: {
                    required: true,
                },
                service_area_id: {
                    required: true,
                },
                station_area: {
                    required: true,
                },
                shift_start_date: {
                    required: true,
                },
                shift_end_date: {
                    required: true,
                },

            },
            messages: {
                shift_district_id: {
                    required: "Please select district.",
                },
                shift_ambulance_id: {
                    required: "Please select ambulance number.",
                },
                shift_id: {
                    required: "Please select shift.",
                },
                service_area_id: {
                    required: "Please select service area.",
                },
                station_area: {
                    required: "Please enter station area.",
                },
                shift_start_date: {
                    required: "Please select shift start date.",
                },
                shift_end_date: {
                    required: "Please select shift end date.",
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
                var formData = new FormData($('#addShiftForm')[0]);
                var user_id = $('#view_user_id').val();
                formData.append('user_id', user_id);
                var roleName = "{{ $data->role_name ?? ''}}";
                formData.append('role_name', roleName);

                //alert(roleName);
                $.ajax({
                    type: "POST",
                    url: "{{ url('human-resource/employees/add-shift') }}",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        $(".submitBtn").attr("disabled", false);
                        if (response.status == 'success') {
                            form.reset();
                            $('#shift_id').val('').trigger('change');
                            $('#addShiftModal').modal('toggle');
                            // refresh datatable without page reload
                            var dataTable = $('#shiftDetails').DataTable();
                            dataTable.clear().draw();
                            getShiftDetails();
                            toastr.success(response.message);
                        } else {
                            toastr.error(response.message);
                        }
                    }
                });
                $(".submitBtn").attr("disabled", false);
            }
        });
    }

    $("#addShift").click(function() {
        $('#addShiftModal').modal('show');
    });

    $('.shiftDetails').on('click', '.unassign-shift', function(e) {
        var id = $(this).attr('data-shift-id');
        Swal.fire({
            title: 'Are you sure, you want to unassign this shift?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Unassign it!'
        }).then(function(result) {
            if (result.value) {
                $.ajax({
                    url: root_url + '/human-resource/employees/unassign-shift',
                    data: {
                        'id': id
                    },
                    method: "GET",
                    success: function(response) {
                        // console.log(data);
                        if (response.status == 'success') {
                            Swal.fire('Deleted!', 'Shift Unassigned Successfully.',
                                'success');
                            var dataTable = $('#shiftDetails').DataTable();
                            dataTable.clear().draw();
                            getShiftDetails();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: response.message,
                            })
                        }
                    }
                });
            }
        });
        e.preventDefault();
    });

    $(document).ready(function() {
        $('#toggle-all-checkboxes').change(function() {
            $('.shift-checkbox').prop('checked', this.checked);
        });

        // Add event listener for delete button or other actions
        $('#deleteShift').on('click', function(e) {
            e.preventDefault();
            // Handle deletion of selected checkboxes
            var selectedIds = [];
            $('.shift-checkbox:checked').each(function() {
                selectedIds.push($(this).val());
            });

            // Check if at least one checkbox is checked
            if (selectedIds.length === 0) {
                // Show modal with a message
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: 'Please select at least one shift to unassign.',
                });
                return;
            }

            Swal.fire({
                title: 'Are you sure, you want to unassign this shift?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Unassign it!'
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        url: root_url + '/human-resource/employees/unassign-shift',
                        data: {
                            'id[]': selectedIds
                        },
                        method: "GET",
                        success: function(response) {
                            // console.log(data);
                            if (response.status == 'success') {
                                Swal.fire('Deleted!', 'Shift Unassigned Successfully.',
                                    'success');
                                // Uncheck "Toggle All Checkboxes" after deletion
                                $('#toggle-all-checkboxes').prop('checked', false);
                                var dataTable = $('#shiftDetails').DataTable();
                                dataTable.clear().draw();
                                getShiftDetails();
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: response.message,
                                })
                            }
                        }
                    });
                }
            });

            // Prevent the default form submission
            e.preventDefault();

            // Use selectedIds array for further processing, e.g., deletion
            console.log(selectedIds);
        });
        // Add event listener for DataTable page links
        $('#shift_details').on('click', '.page-link', function() {
            // Uncheck "Toggle All Checkboxes" when a page link is clicked
            $('#toggle-all-checkboxes').prop('checked', false);
        });


    });
    document.addEventListener('DOMContentLoaded', function() {
        const tabs = document.querySelectorAll('.nav-link');
        tabs.forEach(tab => {
            tab.addEventListener('click', function(event) {
                const tabName = event.target.getAttribute('data-tab');
                // Update the URL hash to reflect the clicked tab
                window.location.hash = tabName;
            });
        });
    });
    // Function to activate the tab based on the hash fragment
    function activateTabFromHash() {
        const hash = window.location.hash;
        const tabLinks = document.querySelectorAll('.nav-link');
        if (!hash) {
            // If no hash is present, activate the #personal tab by default
            const personalTab = document.querySelector('[href="#personal"]');
            $('#personal').tab('show');
            if (personalTab) {
                personalTab.classList.add('active');
                //$('#personal').tab('show'); // Show the corresponding tab content
            }
        } else {
            tabLinks.forEach(link => {
                if (link.getAttribute('href') === hash) {
                    if (link) {
                        link.classList.add('active');
                        // Show the corresponding tab content
                        $(hash).tab('show');
                    }
                } else {
                    if (link) {
                        link.classList.remove('active');
                    }
                }
            });
        }
    }


    // Listen for hash changes and activate the corresponding tab
    window.addEventListener('hashchange', activateTabFromHash);

    // Activate tab based on initial hash
    activateTabFromHash();
</script>
@endpush