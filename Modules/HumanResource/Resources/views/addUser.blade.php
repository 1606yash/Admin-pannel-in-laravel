@extends('layouts.app')
<meta name="csrf-token" content="{{ csrf_token() }}" />
@section('content')
@if(!empty($roleSlug))
@php
$roleUser = ucwords(str_replace('-', ' ', $roleSlug));
$roleId = Helpers::getRoleIdByRoleName($roleUser);
$subRoles = Helpers::getSubAdminChildRoles();
@endphp
@endif
<a class="btn" style="font-size: 18px;margin-bottom: 10px;" class="btn" href="javascript:history.back()"><em class='icon ni ni-arrow-left'></em>Back</a>
<div class="container">
    <form role="form" method="post" id="add_user_form" enctype="multipart/form-data" autocomplete="off">
        @csrf
        <input type="hidden" value="0" name="is_active" id="is_active">
        <input type="hidden" class="form-control" id="role_id" name="role_id" value="{{ $roleId }}">
        <input type="hidden" class="form-control" id="role_slug" name="role_slug" value="{{ $roleSlug }}">
        @if(Str::lower($roleSlug) === Str::lower('sub-admin'))
        <!-- User Role section for subAdmin starts-->
        <div class="card">
            <div class="card-header">
                <h5>User Role</h5>
            </div>
            <div class="card-body">
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="role_id" class="mr-2">Role<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="role_name" name="role_name" value="{{ $roleUser }}" readonly data-parsley-excluded="true">
                    </div>
                    <div class="col-md-4"></div>
                    <div class="form-group col-md-4 form-control-required-wrap">
                        <label for="sub_role_id" class="mr-2">Sub - Role<span class="text-danger">*</span></label>
                        <select name="sub_role_id" id="sub_role_id" class="form-control form-select" placeholder='Select Sub Role' data-search="on" data-parsley-excluded="true">
                            <option selected disabled>Select Sub Role</option>
                            @foreach($subRoles as $subRole)
                            <option value="{{ $subRole->id }}">{{ $subRole->role_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Role section for subAdmin ends-->

        <!-- Personal Information section for subAdmin starts -->
        <div class="card mt-3">
            <div class="card-header">
                <h5>Personal Information</h5>
            </div>
            <div class="card-body">

                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="first_name">First Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="first_name" name="first_name" placeholder="Enter First Name" onkeypress="return isCharSpace(event)" data-parsley-excluded="true">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="middle_name">Middle Name</label>
                        <input type="text" class="form-control" id="middle_name" name="middle_name" placeholder="Enter Middle Name" onkeypress="return isCharSpace(event)" data-parsley-excluded="true">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="last_name">Last Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Enter Last Name" onkeypress="return isCharSpace(event)" data-parsley-excluded="true">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-4 form-control-required-wrap">
                        <label for="gender">Gender <span class="text-danger">*</span></label>
                        <select name="gender" id="gender" class="form-control form-select" data-search="on" placeholder='Select Gender' data-parsley-excluded="true">
                            <option selected disabled>Select Gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="dob">Date of Birth <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="dob" name="dob" readonly placeholder="Select Date of Birth" data-parsley-excluded="true">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="phone_no">Mobile Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="phone_no" name="phone_no" placeholder="Enter Mobile Number" onkeypress="return isNumber(event)" data-parsley-excluded="true">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="email">Email ID</label>
                        <input type="text" class="form-control" id="email" name="email" placeholder="Enter Email ID" data-parsley-excluded="true">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="profile">Upload User Photo <span class="text-danger">*</span></label>
                        <div class="custom-file mb-3">
                            <input type="file" class="custom-file-input" id="profile" name="profile" data-parsley-excluded="true" accept="image/*" onchange="updateFileName(this)">
                            <label class="custom-file-label" for="attach_file">Tap to attach a file</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Personal Information section for subAdmin ends -->
        @else
        <!-- User Role section for driver, attendant, district-anchor starts-->
        <div class="card">
            <div class="card-header">
                <h5>User Role</h5>
            </div>
            <div class="card-body">
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label for="district_id" class="mr-2">Home District <span class="text-danger">*</span></label>
                    </div>
                    <div class="form-group form-control-required-wrap col-md-2">
                        <select name="district_id" id="district_id" class="form-control form-select" placeholder='Select District' data-search="on" data-parsley-excluded="true">
                            <option selected disabled>Select District</option>
                            @foreach ($districts as $key => $district)
                            <option value="{{ $district->id }}" {{ old('district_id') == $district->id ? 'selected' : '' }}>
                                {{ $district->district_name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="role_id" class="mr-2">User Role<span class="text-danger">*</span></label>
                    </div>
                    <div class="form-group col-md-2">
                        <input type="text" class="form-control" id="role_name" name="role_name" value="{{ $roleUser }}" readonly data-parsley-excluded="true">

                    </div>
                    <div class="form-group col-md-3">
                        <label for="state_id" class="mr-2">State</label>
                    </div>
                    <div class="form-group col-md-2">
                        <input type="text" class="form-control" id="state_name" name="state_name" readonly data-parsley-excluded="true" placeholder="State">
                        <input type="hidden" class="form-control" id="state_id" name="state_id">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="reporting_manager_id" class="mr-2">Reporting Manager</label>
                    </div>
                    <div class="form-group form-control-required-wrap col-md-2">
                        <select name="reporting_manager_id" id="reporting_manager_id" class="form-control form-select" data-search="on" disabled data-parsley-excluded="true">
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <!-- User Role section for driver, attendant, district-anchor ends-->

        <!-- Personal Information section starts -->
        <div class="card mt-3">
            <div class="card-header">
                <h5>Personal Information</h5>
            </div>
            <div class="card-body">

                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="first_name">First Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="first_name" name="first_name" placeholder="Enter First Name" onkeypress="return isCharSpace(event)" data-parsley-excluded="true">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="middle_name">Middle Name</label>
                        <input type="text" class="form-control" id="middle_name" name="middle_name" placeholder="Enter Middle Name" onkeypress="return isCharSpace(event)" data-parsley-excluded="true">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="last_name">Last Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Enter Last Name" onkeypress="return isCharSpace(event)" data-parsley-excluded="true">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group form-control-required-wrap col-md-4">
                        <label for="gender">Gender <span class="text-danger">*</span></label>
                        <select name="gender" id="gender" class="form-control form-select" data-search="on" placeholder='Select Gender' data-parsley-excluded="true">
                            <option selected disabled>Select Gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="dob">Date of Birth <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="dob" name="dob" readonly placeholder="Select Date of Birth" data-parsley-excluded="true">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="phone_no">Mobile Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="phone_no" name="phone_no" placeholder="Enter Mobile Number" onkeypress="return isNumber(event)" data-parsley-excluded="true">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="email">Email ID</label>
                        <input type="text" class="form-control" id="email" name="email" placeholder="Enter Email ID " data-parsley-excluded="true">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="pan_no">PAN Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="pan_no" name="pan_no" placeholder="Enter PAN Number" onkeypress="return onlyAlphanumerics(event)" data-parsley-excluded="true">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="aadhar_no">Aadhar Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="aadhar_no" name="aadhar_no" placeholder="Enter Aadhar Number" onkeypress="return isNumber(event)" data-parsley-excluded="true">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="joining_date">Joining Date <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="joining_date" name="joining_date" readonly placeholder="Select Joining Date" data-parsley-excluded="true">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="aadhar_doc">Upload Aadhar <span class="text-danger">*</span></label>
                        <div class="custom-file mb-3">
                            <input type="file" class="custom-file-input" id="aadhar_doc" name="aadhar_doc" onchange="updateFileName(this)" data-parsley-excluded="true" accept=".jpg, .jpeg, .png, .gif, .pdf">
                            <label class="custom-file-label" for="attach_file">Tap to attach a file</label>
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="pan_doc">Upload PAN <span class="text-danger">*</span></label>
                        <div class="custom-file mb-3">
                            <input type="file" class="custom-file-input" id="pan_doc" name="pan_doc" onchange="updateFileName(this)" data-parsley-excluded="true" accept=".jpg, .jpeg, .png, .gif, .pdf">
                            <label class="custom-file-label" for="attach_file">Tap to attach a file</label>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="profile">Upload User Photo <span class="text-danger">*</span></label>
                        <div class="custom-file mb-3">
                            <input type="file" class="custom-file-input" id="profile" name="profile" onchange="updateFileName(this)" data-parsley-excluded="true" accept="image/*">
                            <label class="custom-file-label" for="attach_file">Tap to attach a file</label>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea class="form-control" id="address" name="address" rows="4" placeholder="Enter Address Here ..." data-parsley-excluded="true"></textarea>
                </div>
            </div>
        </div>
        <!-- Personal Information section ends -->

        <!-- Educational Qualifications section starts -->
        <div class="card mt-3">
            <div class="card-header">
                <h5>Educational Qualifications</h5>
            </div>
            <div class="card-body">
                <div class="form-row">
                    <div class="form-group form-control-required-wrap col-md-4">
                        <label for="highest_qualification_id">Highest Qualification <span class="text-danger">*</span></label>
                        <select name="highest_qualification_id" id="highest_qualification_id" class="form-control form-select" data-search="on" placeholder='Select Highest Qualification' data-parsley-excluded="true">
                            <option selected disabled>Select Highest Qualification</option>
                            @foreach ($highestQualifications as $key => $highestQualification)
                            <option value="{{ $highestQualification->id }}" {{ old('highest_qualification_id') == $highestQualification->id ? 'selected' : '' }}>
                                {{ $highestQualification->type }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group form-control-required-wrap col-md-4">
                        <label for="year_of_completion">Year of Completion</label>
                        <select name="year_of_completion" id="year_of_completion" class="form-control form-select" data-search="on" placeholder='Select Year of Completion' data-parsley-excluded="true">
                            <option selected disabled>Select Year</option>
                            @php
                            // Set the range of years you want to allow
                            $currentYear = date('Y');
                            $startYear = 1900;

                            // Create options for each year in the range
                            for ($year = $currentYear; $year >= $startYear; $year--) {
                            echo " <option value=\"$year\">$year</option>";
                            }
                            @endphp
                        </select>

                    </div>
                    <div class="form-group form-control-required-wrap col-md-4">
                        <label for="field_of_study_id">Field of Study</label>
                        <select name="field_of_study_id" id="field_of_study_id" class="form-control form-select" data-search="on" placeholder='Select Field of Study' data-parsley-excluded="true">
                            <option selected disabled>Select Field of Study</option>
                            @foreach ($fieldOfStudys as $key => $fieldOfStudy)
                            <option value="{{ $fieldOfStudy->id }}" {{ old('field_of_study_id') == $fieldOfStudy->id ? 'selected' : '' }}>
                                {{ $fieldOfStudy->type }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="mark_doc">Upload Marksheet</label>
                        <div class="custom-file mb-3">
                            <input type="file" class="custom-file-input" id="mark_doc" name="mark_doc" onchange="updateFileName(this)" data-parsley-excluded="true" accept=".jpg, .jpeg, .png, .gif, .pdf">
                            <label class="custom-file-label" for="attach_file">Tap to attach a file</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Educational Qualifications section ends -->

        <!-- Past Experience section starts -->
        <div class="card mt-3">
            <div class="card-header">
                <h5>Past Experience</h5>
            </div>
            <div class="card-body">
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="last_company_name">Last Company Name</label>
                        <input type="text" class="form-control" id="last_company_name" name="last_company_name" placeholder="Enter Last Company Name" data-parsley-excluded="true">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="past_experience_document">Upload Document</label>
                        <div class="custom-file mb-3">
                            <input type="file" class="custom-file-input" id="past_experience_document" name="past_experience_document" onchange="updateFileName(this)" data-parsley-excluded="true" accept=".jpg, .jpeg, .png, .gif, .pdf">
                            <label class="custom-file-label" for="attach_file">Tap to attach a file</label>
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="designation">Designation</label>
                        <input type="text" class="form-control" id="designation" name="designation" placeholder="Enter Designation" data-parsley-excluded="true">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="past_experience_location">Location</label>
                        <input type="text" class="form-control" id="past_experience_location" name="past_experience_location" placeholder="Enter Location" data-parsley-excluded="true">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="start_date">Start Date</label>
                        <input type="text" class="form-control" id="start_date" name="start_date" readonly placeholder="Select Start Date" data-parsley-excluded="true">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="end_date">End Date</label>
                        <input type="text" class="form-control" id="end_date" name="end_date" readonly placeholder="Select End Date" data-parsley-excluded="true">
                    </div>
                </div>
            </div>
        </div>
        <!-- Past Experience section ends -->

        <!-- Bank Account Details section starts -->
        <div class="card mt-3">
            <div class="card-header">
                <h5>Bank Account Details</h5>
            </div>
            <div class="card-body">
                <div class="form-row">
                    <div class="form-group form-control-required-wrap col-md-4">
                        <label for="bank_id">Bank Name <span class="text-danger">*</span></label>
                        <select name="bank_id" id="bank_id" class="form-control form-select" data-search="on" placeholder='Select Bank Name' data-parsley-excluded="true">
                            <option value="" selected disabled>Select Bank</option>
                            @foreach ($banks as $key => $bank)
                            <option value="{{ $bank->id }}" {{ old('bank_id') == $bank->id ? 'selected' : '' }}>{{ $bank->bank_name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="account_number">Account Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="account_number" name="account_number" placeholder="Enter Account Number" onkeypress="return isNumber(event)" data-parsley-excluded="true">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="ifsc_code">IFSC Code <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="ifsc_code" name="ifsc_code" placeholder="Enter IFSC Code" onkeypress="return onlyAlphanumerics(event)" data-parsley-excluded="true">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="bank_doc">Upload Bank Proof <span class="text-danger">*</span></label>
                        <div class="custom-file mb-3">
                            <input type="file" class="custom-file-input" id="bank_doc" name="bank_doc" onchange="updateFileName(this)" data-parsley-excluded="true" accept=".jpg, .jpeg, .png, .gif, .pdf">
                            <label class="custom-file-label" for="attach_file">Tap to attach a file</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Bank Account Details section ends -->

        <!-- Driving License Details section starts -->
        <div class="card mt-3" id="driving_license_section" @unless(Str::lower($roleSlug)===Str::lower('driver') || Str::lower($roleSlug)===Str::lower('attendant'))) style="display: none;" @endunless>
            <div class="card-header">
                <h5>Driving License Details</h5>
            </div>
            <div class="card-body">
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="license_number">Driver License Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="license_number" name="license_number" placeholder="Enter Driver License Number" data-parsley-excluded="true">
                    </div>
                    <div class="form-group form-control-required-wrap col-md-4">
                        <label for="license_type_id">DL Type <span class="text-danger">*</span></label>
                        <select name="license_type_id" id="license_type_id" class="form-control form-select" data-search="on" placeholder='Select DL Type' data-parsley-excluded="true">
                            <option selected disabled>Select DL Type</option>
                            @foreach ($drivingLicenceTypes as $key => $drivingLicenceType)
                            <option value="{{ $drivingLicenceType->id }}" {{ old('license_type_id') == $drivingLicenceType->id ? 'selected' : '' }}>
                                {{ $drivingLicenceType->type }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="license_expiry_date">License Expiry Date <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="license_expiry_date" name="license_expiry_date" readonly placeholder="Select License Expiry Date" data-parsley-excluded="true">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="dl_doc">Upload License Photo <span class="text-danger">*</span></label>
                        <div class="custom-file mb-3">
                            <input type="file" class="custom-file-input" id="dl_doc" name="dl_doc" onchange="updateFileName(this)" data-parsley-excluded="true" accept=".jpg, .jpeg, .png, .gif, .pdf">
                            <label class="custom-file-label" for="attach_file">Tap to attach a file</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Driving License Details section ends -->

        <!-- Ambulance Details section starts -->
        <div class="card mt-3" id="ambulance_section" @unless(Str::lower($roleSlug)===Str::lower('driver') || Str::lower($roleSlug)===Str::lower('attendant')) style="display: none;" @endunless>
            <div class="card-header">
                <h5 class="card-title float-left">Ambulance Details</h5>
                <a href="#" class="add-more-link float-right d-none" style="text-decoration: none; cursor: pointer;"><em style="font-size: 30px;" class="icon ni ni-plus-circle"></em></a>
            </div>
            <div class="card-body">
                <div class="dynamic-section">
                    <div class="form-row">
                        <div class="form-group form-control-required-wrap col-md-4">
                            <label for="ambulance_id">Ambulance Number</label>
                            <select name="ambulance_id[]" class="form-control form-select ambulance_id" id="ambulance_id" data-search="on" placeholder='Select Ambulance' disabled data-parsley-excluded="true">
                            </select>
                        </div>
                        <div class="form-group form-control-required-wrap col-md-4">
                            <label for="shift_id">Shift</label>
                            <select name="shift_id[]" class="form-control form-select shift_id" id="shift_id" data-search="on" placeholder='Select Shift' data-parsley-excluded="true" size="5">
                                @foreach ($shiftTypes as $key => $shiftType)
                                <option value="{{ $shiftType->id }}">
                                    {{ $shiftType->shift_name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group form-control-required-wrap col-md-4">
                            <label for="service_area_id">Service Area</label>
                            <select name="service_area_id[]" class="form-control form-select service_area_id" id="service_area_id" data-search="on" placeholder='Select Service Area' disabled data-parsley-excluded="true">
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="station_area">Station Area</label>
                            <input type="text" class="form-control" name="station_area[]" id="station_area" placeholder="Enter Station Area" data-parsley-excluded="true">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="shift_start_date">Shift Start Date</label>
                            <input type="text" class="form-control" name="shift_start_date[]" id="shift_start_date" readonly placeholder="Select Shift Start Date" data-parsley-excluded="true">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="shift_end_date">Shift End Date</label>
                            <input type="text" class="form-control" name="shift_end_date[]" id="shift_end_date" readonly placeholder="Select Shift End Date" data-parsley-excluded="true">
                        </div>
                    </div>
                    <div class="form-row">
                        <a href="#" class="remove-link d-none" style="text-decoration: none; cursor: pointer;"><em class="icon ni ni-trash"></em></a>
                    </div>
                </div>
            </div>
        </div>
        @endif
        <div class="mt-3">
            <div class="form-group text-right my-2">
                <a class="btn btn- btn-outline-ligh cancel" href="javascript:history.back()">Cancel</a>
                <a class="btn btn-danger resetFilter cancel" href="">Reset</a>
                <button class="btn btn-primary submitBtn" name="submit"><span>Submit</span></button>
            </div>
        </div>
    </form>
</div>
@endsection
@push('footerScripts')
<script src="{{ url('js/tableFlow.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/parsley.js/2.9.2/parsley.min.js"></script>
<script src="{{url('js/additional-methods.js')}}"></script>
<script>
    // method1
    $(document).ready(function() {
        var addMoreCounter = 0;
        // Handle "Add More" click event
        $(document).on('click', '.add-more-link', function(e) {
            e.preventDefault();

            // Increment the counter
            addMoreCounter++;

            // Duplicate the original section
            var html = '';
            html += `<div class="dynamic-section">
                    <hr>
                    <div class="form-row">
                        <div class="form-group form-control-required-wrap col-md-4">
                            <label for="ambulance_id">Ambulance Number</label>
                            <select name="ambulance_id[${addMoreCounter}]" class="form-control form-select ambulance_id" data-search="on" placeholder='Select Ambulance' data-parsley-excluded="true" required>`;
            $("#ambulance_id option").each(function() {
                var isDisabled = $(this).prop('disabled') ? 'disabled' : '';
                var selected = $(this).prop('selected') ? 'selected' : '';
                html += '<option value="' + $(this).val() + '" ' + isDisabled + ' ' + selected + '>' + $(this).text() + '</option>';
            });
            html += `</select>
                        </div>
                        <div class="form-group form-control-required-wrap col-md-4">
                            <label for="shift_id">Shift</label>
                            <select name="shift_id[${addMoreCounter}]" class="form-control form-select shift_id" data-search="on" placeholder='Select Shift' data-parsley-excluded="true" size="5" required>
                                @foreach ($shiftTypes as $key => $shiftType)
                                <option value="{{ $shiftType->id }}" {{ in_array($shiftType->id, (array) old('shift_id')) ? 'selected' : '' }}>
                                    {{ $shiftType->shift_name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group form-control-required-wrap col-md-4">
                        <div class="d-flex justify-content-between">
                            <label for="service_area_id">Service Area</label>
                            <a href="#" class="remove-link" style="text-decoration: none; cursor: pointer; font-size:20px;"><em class="icon ni ni-trash"></em></a>
                        </div>
                            <select name="service_area_id[${addMoreCounter}]" class="form-control form-select service_area_id" data-search="on" placeholder='Select Service Area' data-parsley-excluded="true">`;
            $("#service_area_id option").each(function() {
                var isDisabled = $(this).prop('disabled') ? 'disabled' : '';
                var selected = $(this).prop('selected') ? 'selected' : '';
                html += `<option value="${$(this).val()}" ${isDisabled} ${selected}>${$(this).text()}</option>`;
            });
            html += ` </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="station_area">Station Area</label>
                            <input type="text"
                                class="form-control"
                                name="station_area[${addMoreCounter}]"
                                placeholder="Enter Station Area"
                                data-parsley-excluded="true"
                                required>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="shift_start_date">Shift Start Date</label>
                            <input type="text"
                                class="form-control"
                                name="shift_start_date[${addMoreCounter}]"
                                readonly placeholder="Select Shift Start Date"
                                data-parsley-excluded="true"
                                required>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="shift_end_date">Shift End Date</label>
                            <input type="text"
                                class="form-control"
                                name="shift_end_date[${addMoreCounter}]"
                                readonly placeholder="Select Shift End Date"
                                data-parsley-excluded="true"
                                required>
                        </div>
                    </div>
            </div>`;

            // Append the cloned section
            $('#ambulance_section .card-body:last').append(html);
            $('#ambulance_section .dynamic-section:last').find('.form-select').select2();

            function configureDatepicker(element, linkedElement) {
                element.datepicker({
                    todayBtn: true,
                    orientation: "auto",
                    todayHighlight: true,
                    autoclose: true,
                    format: "dd/mm/yyyy",
                    startView: "days",
                    //endDate: "today"
                });

                element.each(function() {
                    $(this).on('changeDate', function(e) {
                        var selectedDate = $(this).datepicker('getDate');
                        element.datepicker('update', selectedDate);
                        linkedElement.datepicker('update', selectedDate);

                        var startDate = element.val();
                        linkedElement.datepicker('setStartDate', startDate);

                        // Corrected the way to get the value of linkedElement
                        var endDate = linkedElement.val();
                        if (endDate == null || endDate == '') {
                            linkedElement.datepicker('update', startDate);
                        }
                    });
                });
            }

            configureDatepicker(
                $('#ambulance_section .dynamic-section:last').find(`[name="shift_start_date[${addMoreCounter}]"]`),
                $('#ambulance_section .dynamic-section:last').find(`[name="shift_end_date[${addMoreCounter}]"]`)
            );
            configureDatepicker(
                $('#ambulance_section .dynamic-section:last').find(`[name="shift_end_date[${addMoreCounter}]"]`),
                $('#ambulance_section .dynamic-section:last').find(`[name="shift_start_date[${addMoreCounter}]"]`)
            );

            $(`[name="shift_end_date[${addMoreCounter}]"]`).change(function() {
                $("#add_user_form").validate().element(`[name="shift_end_date[${addMoreCounter}]"]`);
            });

            $(`[name="shift_start_date[${addMoreCounter}]"]`).change(function() {
                $("#add_user_form").validate().element(`[name="shift_start_date[${addMoreCounter}]"]`);
            });

            $(`[name="shift_id[${addMoreCounter}]"]`).change(function() {
                $("#add_user_form").validate().element(`[name="shift_id[${addMoreCounter}]"]`);
            });

            $(`[name="ambulance_id[${addMoreCounter}]"]`).change(function() {
                $("#add_user_form").validate().element(`[name="ambulance_id[${addMoreCounter}]"]`);
            });

            $(`[name="service_area_id[${addMoreCounter}]"]`).change(function() {
                $("#add_user_form").validate().element(`[name="service_area_id[${addMoreCounter}]"]`);
            });

        });

        // Handle "Remove" click event
        $(document).on('click', '.remove-link', function(e) {
            e.preventDefault();
            // Remove the corresponding ambulance details section
            $(this).closest('.dynamic-section').remove();
        });
    });



    $(document).ready(function() {

        $('#last_company_name, #past_experience_document, #designation, #end_date, #start_date, #past_experience_location').on('input', function() {
            updateRequiredAttributeForPastExperience();
        });

        $('#ambulance_id, #shift_id, #service_area_id, #station_area, #shift_start_date, #shift_end_date').on('change', function() {
            updateRequiredAttributeForAmbulance();
        });

        $('#license_number, #license_type_id, #license_expiry_date, #dl_doc').on('input', function() {
            updateRequiredAttributeForDrivingLicenseDetails();
        });


        function updateRequiredAttributeForDrivingLicenseDetails() {
            // Check if any of the specified fields is filled
            var anyFieldFilled = false;

            // Iterate through each field
            $('#license_number, #license_type_id, #license_expiry_date, #dl_doc').each(function() {
                if ($(this).val() !== '' && $(this).val() !== null) {
                    anyFieldFilled = true;
                    return false; // Exit the loop early if any field is filled
                }
            });

            // Add or remove the 'required' attribute based on whether any field is filled
            $('#license_number, #license_type_id, #license_expiry_date, #dl_doc').prop('required', anyFieldFilled);
            $('#license_number, #license_type_id, #license_expiry_date, #dl_doc').removeClass('error');
            $("#add_user_form").validate().form();
        }

        function updateRequiredAttributeForPastExperience() {
            // Check if any of the specified fields is filled
            var anyFieldFilled = false;

            // Iterate through each field
            $('#last_company_name, #past_experience_document, #designation, #end_date, #start_date, #past_experience_location').each(function() {
                if ($(this).val() !== '' && $(this).val() !== null) {
                    anyFieldFilled = true;
                    return false; // Exit the loop early if any field is filled
                }
            });

            // Add or remove the 'required' attribute based on whether any field is filled
            $('#last_company_name, #past_experience_document, #designation, #end_date, #start_date, #past_experience_location').prop('required', anyFieldFilled);
            $('#last_company_name, #past_experience_document, #designation, #end_date, #start_date, #past_experience_location').removeClass('error');
            $("#add_user_form").validate().form();
        }

        function updateRequiredAttributeForAmbulance() {
            // Check if any of the specified fields is filled for the second set
            var anyFieldFilled = false;

            // Iterate through each field in the second set
            $('#ambulance_id, #shift_id, #service_area_id, #station_area, #shift_start_date, #shift_end_date').each(function() {
                if ($(this).val() !== '' && $(this).val() !== null) {
                    anyFieldFilled = true;
                    return false; // Exit the loop early if any field is filled
                }
            });

            // Add or remove the 'required' attribute based on whether any field is filled for the second set
            $('#ambulance_id, #shift_id, #service_area_id, #station_area, #shift_start_date, #shift_end_date').prop('required', anyFieldFilled);
            $('#ambulance_id, #shift_id, #service_area_id, #station_area, #shift_start_date, #shift_end_date').removeClass('error');
            $("#add_user_form").validate().form();
            // Additional validation or form submission logic for the second set can be added here
        }

        //get state according to district
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
            $("#add_user_form").validate().form();
        });

        $('#district_id').on('change', function() {
            var selectedDistrictId = $(this).val();
            $('.service_area_id').attr('disabled', false);
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

        // get ambulance according to district id
        $('#district_id').on('change', function() {
            // Get the selected value
            var selectedDistrictId = $(this).val();
            $('.add-more-link').removeClass('d-none');

            // Perform AJAX call
            $.ajax({
                url: "{{ url('/human-resource/employees/get-ambulance-by-district-id') }}", // Replace with your actual AJAX endpoint
                type: 'GET', // Adjust the HTTP method as needed
                data: {
                    district_id: selectedDistrictId,
                    // Add any other data you want to send in the AJAX request
                },
                success: function(response) {
                    // Clear existing options
                    $('[name="ambulance_id[]"]').empty();

                    // Add a default option
                    $('[name="ambulance_id[]"]').append('<option selected disabled>Select Ambulance Number</option>');

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

                        $('[name="ambulance_id[]"]').append(ambulanceOption);
                    }
                },
            });
        });


        // get reporting manager according to district
        $('#district_id').on('change', function() {
            var selectedDistrictId = $('#district_id').val();
            var selectedRole = $('#role_id').val();
            $('#reporting_manager_id').attr('disabled', false);
            $('.ambulance_id').attr('disabled', false);
            $.ajax({
                url: "{{url('/get-reporting-manager')}}", // Replace with your actual AJAX endpoint
                type: 'GET', // Adjust the HTTP method as needed
                contentType: JSON,
                data: {
                    district_id: selectedDistrictId,
                    role_id: selectedRole,
                    // Add any other data you want to send in the AJAX request
                },
                success: function(response) {
                    // Handle the AJAX success response

                    // Clear existing options
                    $('#reporting_manager_id').empty();

                    // Add a default option
                    $('#reporting_manager_id').append('<option selected disabled>Select Reporting Manager</option>');

                    // Add new options
                    for (var i = 0; i < response.reportingManager.length; i++) {
                        $('#reporting_manager_id').append('<option value="' + response.reportingManager[i].user_id + '">' + response.reportingManager[i].employee_name + '</option>');
                    }
                },
            });
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

        $("#start_date").datepicker({
            todayBtn: true,
            orientation: "auto",
            todayHighlight: true,
            autoclose: true,
            format: "dd/mm/yyyy",
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
            format: "dd/mm/yyyy",
            startView: "days",
            // endDate: "today"
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
            //endDate: "today"
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
            //endDate: "today"
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
            var $roleUser = "{{ $roleUser }}";

            if ($("#add_user_form").length > 0) {
                var validationRules = {
                    // Common rules for all roles go here
                    role_name: {
                        required: true,
                    },
                    first_name: {
                        required: true,
                        maxlength: 50
                    },
                    middle_name: {
                        maxlength: 50
                    },
                    last_name: {
                        required: true,
                        maxlength: 50
                    },
                    gender: {
                        required: true,
                    },
                    dob: {
                        required: true,
                    },
                    phone_no: {
                        required: true,
                        maxlength: 10,
                        minlength: 10,
                        mobileIN: true,
                    },
                    profile: {
                        required: true,
                        accept: 'image/*',
                        maxsize: 40 * 1024 * 1024, // 40 MB maximum file size
                    },
                    email: {
                        //presence: true,
                        email: true,
                        maxlength: 255,
                    },
                    last_company_name: {
                        maxlength: 50,
                    },
                    designation: {
                        maxlength: 50
                    },
                    past_experience_document: {
                        accept: 'image/*,application/pdf',
                        maxsize: 40 * 1024 * 1024, // 40 MB maximum file size
                    },
                    mark_doc: {
                        accept: 'image/*,application/pdf',
                        maxsize: 40 * 1024 * 1024,
                    }
                    // ... other common rules ...


                };
                if ($roleUser === 'Attendant' || $roleUser === 'Driver' || $roleUser === 'District Anchor') {
                    validationRules.district_id = {
                            required: true,
                        },
                        validationRules.pan_no = {
                            required: true,
                            maxlength: 10,
                            minlength: 10,
                        },
                        validationRules.aadhar_no = {
                            required: true,
                            maxlength: 12,
                            minlength: 12,
                            digits: true,
                        },
                        validationRules.joining_date = {
                            required: true,
                        },
                        validationRules.aadhar_doc = {
                            required: true,
                            accept: 'image/*,application/pdf',
                            maxsize: 40 * 1024 * 1024, // 40 MB maximum file size
                        },
                        validationRules.pan_doc = {
                            required: true,
                            accept: 'image/*,application/pdf',
                            maxsize: 40 * 1024 * 1024, // 40 MB maximum file size
                        },
                        validationRules.highest_qualification_id = {
                            required: true,
                        },
                        validationRules.bank_id = {
                            required: true,
                        },
                        validationRules.account_number = {
                            required: true,
                            digits: true,
                            minlength: 8,
                            maxlength: 20,
                        },
                        validationRules.ifsc_code = {
                            required: true,
                            minlength: 11,
                            maxlength: 11,
                        },
                        validationRules.bank_doc = {
                            required: true,
                            accept: 'image/*,application/pdf',
                            maxsize: 40 * 1024 * 1024, // 40 MB maximum file size

                        }
                }
                // Additional fields for Attendant
                if ($roleUser === 'Attendant' || $roleUser === 'Driver') {
                    validationRules.license_number = {
                            required: true,
                            minlength: 15,
                            maxlength: 16,
                        },
                        validationRules.license_type_id = {
                            required: true,
                        },
                        validationRules.license_expiry_date = {
                            required: true,
                        },
                        validationRules.dl_doc = {
                            required: true,
                            accept: 'image/*,application/pdf',
                            maxsize: 40 * 1024 * 1024, // 40 MB maximum file size
                        }
                    // Add other rules specific to Attendant
                }

                if ($roleUser === 'Sub Admin') {
                    validationRules.sub_role_id = {
                        required: true,
                    }
                }


                // Merge common fields with specific role fields
                $("#add_user_form").validate({
                    rules: validationRules,
                    messages: {
                        district_id: {
                            required: "Please select district.",
                        },
                        first_name: {
                            required: "Please enter first name.",
                            maxlength: "Your first name maxlength should be 50 characters long."
                        },
                        last_name: {
                            required: "Please enter last name.",
                            maxlength: "Your last name maxlength should be 50 characters long."
                        },
                        middle_name: {
                            maxlength: "Your middle name maxlength should be 50 characters long."
                        },
                        email: {
                            email: "Please enter valid email.",
                            maxlength: "Your email should not exceed more than 255 characters."
                        },
                        gender: {
                            required: "Please select gender.",
                        },
                        dob: {
                            required: "Please enter date of birth.",
                        },
                        phone_no: {
                            required: "Please enter mobile number.",
                            maxlength: "Your mobile number should not exceed more than 10 digits",
                            minlength: "Your mobile number should not be less than 10 digits",
                            mobileIN: "Please enter valid Indian phone number. Valid Indian phone number starts with 6,7,8 or 9"
                        },
                        pan_no: {
                            required: "Please enter PAN No.",
                            maxlength: "Your PAN No maxlength should not be more than 10 characters long.",
                            minlength: "Your PAN No minlength should be 10 characters long.",

                        },
                        aadhar_no: {
                            required: "Please enter Aadhaar number.",
                            maxlength: "Your Aadhaar number should not exceed more than 12 digits",
                            minlength: "Your Aadhaar number should not be less than 12 digits",

                        },
                        joining_date: {
                            required: "Please enter joining date.",
                        },
                        aadhar_doc: {
                            required: "Please upload Aadhar card image.",
                            accept: "Please upload a valid format file.",
                            maxsize: "File size cannot exceed 40 MB.",
                        },
                        pan_doc: {
                            required: "Please upload PAN card image.",
                            accept: "Please upload a valid format file.",
                            maxsize: "File size cannot exceed 40 MB.",
                        },
                        past_experience_document: {
                            accept: "Please upload a valid format file.",
                            maxsize: "File size cannot exceed 40 MB.",
                        },
                        mark_doc: {
                            accept: "Please upload a valid format file.",
                            maxsize: "File size cannot exceed 40 MB.",
                        },
                        profile: {
                            required: "Please upload User photo.",
                            accept: "Please upload a valid format file.",
                            maxsize: "File size cannot exceed 40 MB.",
                        },
                        highest_qualification_id: {
                            required: "Please select highest qualification.",
                        },
                        bank_id: {
                            required: "Please select bank name.",
                        },
                        account_number: {
                            required: "Please enter bank account number.",
                            digits: "Bank account number should contain only digits.",
                            minlength: "Bank account number should be at least 8 digits.",
                            maxlength: "Bank account number should not exceed 20 digits.",
                        },
                        ifsc_code: {
                            required: "Please enter your IFSC code.",
                            minlength: "IFSC code should be exactly 11 characters.",
                            maxlength: "IFSC code should be exactly 11 characters.",

                        },
                        bank_doc: {
                            required: "Please upload Bank Passbook image.",
                            accept: "Please upload a valid format file.",
                            maxsize: "File size cannot exceed 40 MB.",
                        },
                        license_number: {
                            required: "Please enter your driver license number",
                            minlength: "driver license number should be at least 15 characters.",
                            maxlength: "driver license number should not exceed 16 characters.",
                        },
                        license_type_id: {
                            required: "Please select driver license type.",
                        },
                        license_expiry_date: {
                            required: "Please select driver license expiry date.",
                        },
                        dl_doc: {
                            required: "Please upload driving license image.",
                            accept: "Please upload a valid format file.",
                            maxsize: "File size cannot exceed 40 MB.",
                        },
                        last_company_name: {
                            required: "Please enter last company name.",
                            maxlength: "Company Name should not exceed more than 50 characters."
                        },
                        designation: {
                            required: "Please enter designation.",
                            maxlength: "Designation should not exceed more than 50 characters."
                        },
                        past_experience_document: {
                            required: "Please upload past experience certificate.",
                            accept: "Please upload a valid format file.",
                            maxsize: "File size cannot exceed 40 MB.",
                        },
                        past_experience_location: {
                            required: "Please enter location."
                        },
                        start_date: {
                            required: "Please select start date."
                        },
                        end_date: {
                            required: "Please select end date."
                        },
                        "ambulance_id[]": {
                            required: "Please select ambulance number."
                        },
                        "shift_id[]": {
                            required: "Please select shift"
                        },
                        "service_area_id[]": {
                            required: "Please enter service area."
                        },
                        "station_area[]": {
                            required: "Please enter station area."
                        },
                        sub_role_id: {
                            required: "Please select sub role for  this role."
                        },
                        "shift_start_date[]": {
                            required: "Please select shift start date."
                        },
                        "shift_end_date[]": {
                            required: "Please select shift end date."
                        },

                    },
                    // submitHandler and other configurations...
                    submitHandler: function(form, event) {
                        // ... your submit handler code ...
                        event.preventDefault();
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });

                        // disabled submit button during process running status
                        $(".submitBtn").attr("disabled", true);

                        $.ajax({
                            type: "POST",
                            url: "{{ url('human-resource/employees/add-user') }}",
                            // data: $('#add_user_form').serialize(),
                            data: new FormData($('#add_user_form')[0]),
                            contentType: false,
                            processData: false,
                            success: function(response) {
                                $(".submitBtn").attr("disabled", false);
                                if (response.status == 'success') {
                                    if (response.role == 'district-anchor') {
                                        window.location.href = "{{ url('human-resource/employees/district-anchor') }}";
                                    } else if (response.role == 'driver') {
                                        window.location.href = "{{ url('human-resource/employees/driver') }}";
                                    } else if (response.role == 'attendant') {
                                        window.location.href = "{{ url('human-resource/employees/attendant') }}";
                                    } else if (response.role == 'sub-admin') {
                                        window.location.href = "{{ url('human-resource/employees/sub-admin') }}";
                                    }
                                    toastr.success(response.message);
                                } else {
                                    $(".submitBtn").attr("disabled", false);
                                    toastr.error(response.message);
                                }
                            }
                        });
                        $(".submitBtn").attr("disabled", false);
                    }
                });
            }
        });

        $("#highest_qualification_id").change(function() {
            $("#add_user_form").validate().element("#highest_qualification_id");
        });

        $("#bank_id").change(function() {
            $("#add_user_form").validate().element("#bank_id");
        });

        $("#gender").change(function() {
            $("#add_user_form").validate().element("#gender");
        });

        $("#dob").change(function() {
            $("#add_user_form").validate().element("#dob");
        });

        $("#joining_date").change(function() {
            $("#add_user_form").validate().element("#joining_date");
        });

        $("#license_type_id").change(function() {
            $("#add_user_form").validate().element("#license_type_id");
        });

        $("#license_expiry_date").change(function() {
            $("#add_user_form").validate().element("#license_expiry_date");
        });
        $("#start_date").change(function() {
            $("#add_user_form").validate().element("#start_date");
        });
        $("#end_date").change(function() {
            $("#add_user_form").validate().element("#end_date");
        });
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
</script>
@endpush