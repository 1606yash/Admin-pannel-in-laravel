@extends('layouts.app')
@push('headerScripts')
<meta name="csrf-token" content="{{ csrf_token() }}" />
<link href="{{ url('css/mdtimepicker.css') }} " rel="stylesheet" type="text/css">
<link rel="stylesheet" href="{{url('css/calendar.css?t='.time())}}">
<script src="https://unpkg.com/popper.js/dist/umd/popper.min.js"></script>
<script src="https://unpkg.com/tooltip.js/dist/umd/tooltip.min.js"></script>
@endpush
@section('content')
<style>
    .expenseDetails tbody tr {
        background-color: white;
    }

    .inventoryDetails tbody tr {
        background-color: white;
    }
</style>
<a class="btn" style="font-size: 18px;margin-bottom: 10px;" class="btn" href="javascript:history.back()"><em class='icon ni ni-arrow-left'></em>Back</a>
<div class="container" id="ambulanceDetails">
    @if(!empty($ambulanceDetails))
    <input type="hidden" name="ambulance_id" id="ambulance_id" value="{{ $ambulanceDetails->id }}">
    <div class="card mt-3 mb-2">
        <div class="card-body">
            <div class="form-row">
                <div class="form-group col-md-6 d-flex">
                    <div class="ml-3">
                        <p class="mb-2">
                            <span class="font-weight-bold" style="font-size: 20px">{{ $ambulanceDetails->ambulance_no ? 'Ambulance Number:' : ($ambulanceDetails->chassis_no ? 'Chassis Number:' : 'Ambulance Number') }}
                            </span>
                            <span class="" style="margin-left: 10px;font-size: 20px">{{ $ambulanceDetails->ambulance_no ? $ambulanceDetails->ambulance_no : ($ambulanceDetails->chassis_no ? $ambulanceDetails->chassis_no : '') }}
                            </span>
                        </p>
                        <p class="mb-2">
                            <span class="font-weight-bold" style="font-size: 20px">District: </span>
                            <span class="mt-2" style="margin-left: 10px;font-size: 20px">{{ $ambulanceDetails->district_name ?? '' }}</span>
                        </p>
                        <p class="mb-2">
                            <button type="button" class="btn btn-primary mb-1" id="updateBtn" style="height: 30px;width: 120px">Update Info</button>
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div class="toggle-bar">
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#ambulance_details" data-tab="ambulance_details">Ambulance Details</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#staff_details" data-tab="staff_details">Staff</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#inventory_details" data-tab="inventory_details">Inventory</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#expenses_details" data-tab="expenses_details">Expenses</a>
            </li>
        </ul>
    </div>
    <div class="tab-content">
        <div class="tab-pane fade show active mt-5" id="ambulance_details">
            <form role="form" method="post" id="editAmbulanceDetailsForm" enctype="multipart/form-data">
                @csrf
                <fieldset class="border rounded-3 p-3 card  mt-3 ">
                    <legend class="float-none w-auto px-3">
                        <h5>Vehicle Information</h5>
                    </legend>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label class="font-weight-bold">Ambulance Number:</label>
                            </div>
                            <div class="form-group col-md-3">
                                <span class="not-editable">{{ $ambulanceDetails->ambulance_no ?? '' }}</span>
                                <input type="text" class="form-control editable d-none" name="ambulance_no" id="ambulance_no" placeholder="Enter Ambulance Number" value="{{ $ambulanceDetails->ambulance_no ?? '' }}" maxlength="15" onkeypress="return onlyAlphanumerics(event)">
                            </div>

                            <div class="form-group col-md-3">
                                <label class="font-weight-bold">Make:</label>
                            </div>
                            <div class="form-group col-md-3">
                                <span class="not-editable">{{ $ambulanceDetails->make ?? '' }}</span>
                                <div class="editable d-none">
                                    <select name="ambulance_make" id="ambulance_make" class="form-control form-select" placeholder='Select' data-search="on" data-parsley-excluded="true">
                                        <option disabled>Select</option>
                                        <option value="Mahindra" {{ ($ambulanceDetails->make ?? '') == 'Mahindra' ? 'selected' : '' }}>Mahindra</option>
                                        <option value="TATA" {{ ($ambulanceDetails->make ?? '') == 'TATA' ? 'selected' : '' }}>TATA</option>
                                        <option value="Force" {{ ($ambulanceDetails->make ?? '') == 'Force' ? 'selected' : '' }}>Force</option>
                                        <option value="Maruti" {{ ($ambulanceDetails->make ?? '') == 'Maruti' ? 'selected' : '' }}>Maruti</option>
                                        <option value="SML" {{ ($ambulanceDetails->make ?? '') == 'SML' ? 'selected' : '' }}>SML</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label class="font-weight-bold">Chassis Number:</label>
                            </div>
                            <div class="form-group col-md-3">
                                <span class="not-editable">{{ $ambulanceDetails->chassis_no ?? '' }}</span>
                                <input type="text" class="form-control editable d-none" id="chassis_number" name="chassis_number" placeholder="Enter Chassis Number" data-parsley-excluded="true" value="{{ $ambulanceDetails->chassis_no ?? '' }}" maxlength="16" onkeypress="return onlyAlphanumerics(event)">
                            </div>
                            <div class="form-group col-md-3">
                                <label class="font-weight-bold">Status:</label>
                            </div>
                            <div class="form-group col-md-3">
                                <span class="not-editable">{{ $ambulanceDetails->status ?? '' }}</span>
                                <div class="editable d-none">
                                    <select name="ambulance_status" id="ambulance_status" class="form-control form-select" placeholder='Select Status' data-search="on" data-parsley-excluded="true">
                                        <option disabled>Select Ambulance Status</option>
                                        <option value="Running" {{ ($ambulanceDetails->status ?? '') == 'Running' ? 'selected' : '' }}>Running</option>
                                        <option value="Not Running" {{ ($ambulanceDetails->status ?? '') == 'Not Running' ? 'selected' : '' }}>Not Running</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label class="font-weight-bold">District:</label>
                            </div>
                            <div class="form-group col-md-3">
                                <span class="not-editable">{{ $ambulanceDetails->district_name ?? '' }}</span>
                                <div class="editable d-none">
                                    <select name="ambulance_district_id" id="ambulance_district_id" class="form-control form-select" placeholder='Select District' data-search="on" data-parsley-excluded="true">
                                        <option disabled>Select District</option>
                                        @if(!empty($districts))
                                        @foreach ($districts as $key => $district)
                                        <option value="{{ $district->id }}" {{ ($ambulanceDetails->district_name ?? '') == $district->district_name ? 'selected' : '' }}>
                                            {{ $district->district_name }}
                                        </option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>

                            <div class="form-group col-md-3">
                                <label class="font-weight-bold">Inaugration Date:</label>
                            </div>
                            <div class="form-group col-md-3">
                                <span class="not-editable">{{ $ambulanceDetails->inauguration_date ?  date('d/m/Y', strtotime($ambulanceDetails->inauguration_date)) : ''}}</span>
                                <input type="text" class="form-control editable d-none" id="inaugration_date" name="inaugration_date" readonly placeholder="Select Inaugration Date" data-parsley-excluded="true" value="{{$ambulanceDetails->inauguration_date ?  date('d/m/Y', strtotime($ambulanceDetails->inauguration_date)) : ''}}">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label class="font-weight-bold">Number Plate:</label>
                            </div>
                            <div class="form-group col-md-3">
                                <span class="not-editable">{{ $ambulanceDetails->number_plate_available ?? '' }}</span>
                                <div class="editable d-none">
                                    <select name="number_plate_availability" id="number_plate_availability" class="form-control form-select" placeholder='Select' data-search="on" data-parsley-excluded="true">
                                        <option disabled>Select</option>
                                        <option value="Yes" {{ ($ambulanceDetails->number_plate_available ?? '') == 'Yes' ? 'selected' : '' }}>Yes</option>
                                        <option value="Not Available" {{ ($ambulanceDetails->number_plate_available ?? '') == 'Not Available' ? 'selected' : '' }}>Not Available</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group col-md-3 d-none editable">
                                <label class="font-weight-bold" for="number_plate_doc">Upload Number Plate:</label>
                            </div>
                            <div class="form-group col-md-3 d-none editable">
                                <div class="custom-file mb-3">
                                    <input type="file" class="custom-file-input" id="number_plate_doc" name="number_plate_doc" onchange="updateFileName(this)" data-parsley-excluded="true" accept=".jpg, .jpeg, .png, .gif, .pdf">
                                    <label class="custom-file-label" for="attach_file">Tap to attach a file</label>
                                </div>
                            </div>
                            @if($ambulanceDetails->number_plate_image_path)

                            <label><a href="{{ $ambulanceDetails->number_plate_image_path }}" style="text-decoration: none; cursor: pointer;" target="_blank">View Number Plate</a></label>

                            @endif
                        </div>
                    </div>
                </fieldset>
                <fieldset class="border rounded-3 p-3 card mt-3">
                    <legend class="float-none w-auto px-3">
                        <h5>Invoice and Registration</h5>
                    </legend>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label class="font-weight-bold">Registration Certificate:</label>
                            </div>
                            <div class="form-group col-md-3">
                                <span class="not-editable">{{ $ambulanceDetails->registration_certificate_available ?? '' }}</span>
                                <div class="d-none editable">
                                    <select name="registration_certificate_availability" id="registration_certificate_availability" class="form-control form-select" placeholder='Select' data-search="on" data-parsley-excluded="true">
                                        <option disabled>Select</option>
                                        <option value="Yes" {{ ($ambulanceDetails->registration_certificate_available ?? '') == 'Yes' ? 'selected' : '' }}>Yes</option>
                                        <option value="Not Available" {{ ($ambulanceDetails->registration_certificate_available ?? '') == 'Not Available' ? 'selected' : '' }}>Not Available</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group col-md-3 d-none editable">
                                <label class="font-weight-bold" for="registration_certificate_doc">Upload Registration Certificate: </label>
                            </div>
                            <div class="form-group col-md-3 d-none editable">
                                <div class="custom-file mb-3">
                                    <input type="file" class="custom-file-input" id="registration_certificate_doc" name="registration_certificate_doc" onchange="updateFileName(this)" data-parsley-excluded="true" accept=".jpg, .jpeg, .png, .gif, .pdf">
                                    <label class="custom-file-label" for="attach_file">Tap to attach a file</label>
                                </div>
                            </div>
                            @if($ambulanceDetails->registration_certificate_path)
                            <label><a href="{{ $ambulanceDetails->registration_certificate_path }}" style="text-decoration: none; cursor: pointer;" target="_blank">View Registration Certificate</a></label>
                            @endif
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label class="font-weight-bold">Purchase Papers:</label>
                            </div>
                            <div class="form-group col-md-3">
                                <span class="not-editable">{{ $ambulanceDetails->purchase_paper_available ?? '' }}</span>
                                <div class="editable d-none">
                                    <select name="purchase_paper_availability" id="purchase_paper_availability" class="form-control form-select" placeholder='Select' data-search="on" data-parsley-excluded="true">
                                        <option disabled>Select</option>
                                        <option value="Yes" {{ ($ambulanceDetails->purchase_paper_available ?? '') == 'Yes' ? 'selected' : '' }}>Yes</option>
                                        <option value="Not Available" {{ ($ambulanceDetails->purchase_paper_available ?? '') == 'Not Available' ? 'selected' : '' }}>Not Available</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-md-3 d-none editable">
                                <label class="font-weight-bold" for="purchase_paper_doc">Upload Purchase Paper:</label>
                            </div>
                            <div class="form-group col-md-3 d-none editable">
                                <div class="custom-file mb-3">
                                    <input type="file" class="custom-file-input" id="purchase_paper_doc" name="purchase_paper_doc" onchange="updateFileName(this)" data-parsley-excluded="true" accept=".jpg, .jpeg, .png, .gif, .pdf">
                                    <label class="custom-file-label" for="attach_file">Tap to attach a file</label>
                                </div>
                            </div>
                            @if($ambulanceDetails->purchase_paper_path)

                            <label><a href="{{ $ambulanceDetails->purchase_paper_path }}" style="text-decoration: none; cursor: pointer;" target="_blank">View Purchase Paper</a></label>

                            @endif
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label class="font-weight-bold">Fastag:</label>
                            </div>
                            <div class="form-group col-md-3">
                                <span class="not-editable">{{ $ambulanceDetails->fastags_available ?? '' }}</span>
                                <div class="d-none editable">
                                    <select name="fastag_availability" id="fastag_availability" class="form-control form-select" placeholder='Select' data-search="on" data-parsley-excluded="true">
                                        <option disabled>Select</option>
                                        <option value="Yes" {{ ($ambulanceDetails->fastags_available ?? '') == 'Yes' ? 'selected' : '' }}>Yes</option>
                                        <option value="Not Available" {{ ($ambulanceDetails->fastags_available ?? '') == 'Not Available' ? 'selected' : '' }}>Not Available</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-md-3 d-none editable">
                                <label class="font-weight-bold" for="fastag_doc">Upload FASTag:</label>
                            </div>
                            <div class="form-group col-md-3 d-none editable">
                                <div class="custom-file mb-3">
                                    <input type="file" class="custom-file-input" id="fastag_doc" name="fastag_doc" onchange="updateFileName(this)" data-parsley-excluded="true" accept=".jpg, .jpeg, .png, .gif, .pdf">
                                    <label class="custom-file-label" for="attach_file">Tap to attach a file</label>
                                </div>
                            </div>
                            @if($ambulanceDetails->fastags_image_path)

                            <label><a href="{{ $ambulanceDetails->fastags_image_path }}" style="text-decoration: none; cursor: pointer;" target="_blank">View Fastag</a></label>

                            @endif
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label class="font-weight-bold">Sponsor Name:</label>
                            </div>
                            <div class="form-group col-md-3">
                                <span class="not-editable">{{ $ambulanceDetails->sponsor_name ?? '-' }}</span>
                                <input type="text" class="form-control editable d-none" id="sponsor_name" name="sponsor_name" placeholder="Enter Sponsor Name" data-parsley-excluded="true" value="{{ $ambulanceDetails->sponsor_name ?? '' }}" maxlength="100">
                            </div>
                            <div class="form-group col-md-3">
                                <label class="font-weight-bold">Invoice Number:</label>
                            </div>
                            <div class="form-group col-md-3">
                                <span class="not-editable">{{ $ambulanceDetails->invoice_no ?? '-' }}</span>
                                <input type="text" class="form-control editable d-none" id="invoice_number" name="invoice_number" placeholder="Enter Invoice Number" data-parsley-excluded="true" value="{{ $ambulanceDetails->invoice_no ?? '' }}" onkeypress="return onlyAlphanumerics(event)">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label class="font-weight-bold">Invoice Date:</label>
                            </div>
                            <div class="form-group col-md-3">
                                <span class="not-editable">{{ $ambulanceDetails->invoice_date ?  date('d/m/Y', strtotime($ambulanceDetails->invoice_date)) : ' - '}}</span>
                                <input type="text" class="form-control editable d-none" id="invoice_date" name="invoice_date" readonly placeholder="Select Invoice Date" data-parsley-excluded="true" value="{{ $ambulanceDetails->invoice_date ?  date('d/m/Y', strtotime($ambulanceDetails->invoice_date)) : ''}}">
                            </div>
                            <div class="form-group col-md-3">
                                <label class="font-weight-bold">Date of Delivery:</label>
                            </div>
                            <div class="form-group col-md-3">
                                <span class="not-editable">{{ $ambulanceDetails->date_of_delivery ?  date('d/m/Y', strtotime($ambulanceDetails->date_of_delivery)) : ' - '}}</span>
                                <input type="text" class="form-control editable d-none" id="date_of_delivery" name="date_of_delivery" readonly placeholder="Select Date Of Delivery" data-parsley-excluded="true" value="{{ $ambulanceDetails->date_of_delivery ?  date('d/m/Y', strtotime($ambulanceDetails->date_of_delivery)) : ''}}">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label class="font-weight-bold">Registration Date:</label>
                            </div>
                            <div class="form-group col-md-3">
                                <span class="not-editable">{{ $ambulanceDetails->registration_date ?  date('d/m/Y', strtotime($ambulanceDetails->registration_date)) : ' - '}}</span>
                                <input type="text" class="form-control editable d-none" id="registration_date" name="registration_date" readonly placeholder="Select Registrtaion Date" data-parsley-excluded="true" value="{{ $ambulanceDetails->registration_date ?  date('d/m/Y', strtotime($ambulanceDetails->registration_date)) : ''}}">
                            </div>
                            <div class="form-group col-md-3">
                                <label class="font-weight-bold">Entry Date:</label>
                            </div>
                            <div class="form-group col-md-3">
                                <span class="not-editable">{{ $ambulanceDetails->entry_date ?  date('d/m/Y', strtotime($ambulanceDetails->entry_date)) : ' - '}}</span>
                                <input type="text" class="form-control editable d-none" id="entry_date" name="entry_date" readonly placeholder="Select Entry Date" data-parsley-excluded="true" value="{{ $ambulanceDetails->entry_date ?  date('d/m/Y', strtotime($ambulanceDetails->entry_date)) : ''}}">
                            </div>
                        </div>
                    </div>
                </fieldset>
                <fieldset class="border rounded-3 p-3 card  mt-3 ">
                    <legend class="float-none w-auto px-3">
                        <h5>Insurance Details</h5>
                    </legend>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label class="font-weight-bold">Insurance:</label>
                            </div>
                            <div class="form-group col-md-3">
                                <span class="not-editable">{{ $ambulanceDetails->insurance_available ?? '-' }}</span>
                                <div class="d-none editable">
                                    <select name="insurance_availability" id="insurance_availability" class="form-control form-select" placeholder='Select' data-search="on" data-parsley-excluded="true">
                                        <option disabled>Select</option>
                                        <option value="Yes" {{ ($ambulanceDetails->insurance_available ?? '') == 'Yes' ? 'selected' : '' }}>Yes</option>
                                        <option value="Not Available" {{ ($ambulanceDetails->insurance_available ?? '') == 'Not Available' ? 'selected' : '' }}>Not Available</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-md-3 d-none editable">
                                <label class="font-weight-bold" for="insurance_doc">Upload Insurance:</label>
                            </div>
                            <div class="form-group col-md-3 d-none editable">
                                <div class="custom-file mb-3">
                                    <input type="file" class="custom-file-input" id="insurance_doc" name="insurance_doc" onchange="updateFileName(this)" data-parsley-excluded="true" accept=".jpg, .jpeg, .png, .gif, .pdf">
                                    <label class="custom-file-label" for="attach_file">Tap to attach a file</label>
                                </div>
                            </div>

                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label class="font-weight-bold">Policy Company:</label>
                            </div>
                            <div class="form-group col-md-3">
                                <span class="not-editable">{{ $ambulanceDetails->policy_company ?? '-' }}</span>
                                <input type="text" class="form-control editable d-none" id="policy_company" name="policy_company" placeholder="Enter Policy Company Name" data-parsley-excluded="true" value="{{ $ambulanceDetails->policy_company ?? '' }}" maxlength="100">
                            </div>
                            <div class="form-group col-md-3">
                                <label class="font-weight-bold">Policy Number:</label>
                            </div>
                            <div class="form-group col-md-3">
                                <span class="not-editable">{{ $ambulanceDetails->policy_number ?? '-' }}</span>
                                <input type="text" class="form-control editable d-none" id="policy_number" name="policy_number" placeholder="Enter Policy Number" data-parsley-excluded="true" value="{{ $ambulanceDetails->policy_number ?? '' }}" maxlength="20">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label class="font-weight-bold">Current Insurance Start Date:</label>
                            </div>
                            <div class="form-group col-md-3">
                                <span class="not-editable">{{ $ambulanceDetails->insurance_start_date ?  date('d/m/Y', strtotime($ambulanceDetails->insurance_start_date)) : ' - '}}</span>
                                <input type="text" class="form-control editable d-none" id="current_insurance_start_date" name="current_insurance_start_date" readonly placeholder="Select Current Insurance Start Date" data-parsley-excluded="true" value="{{ $ambulanceDetails->insurance_start_date ?  date('d/m/Y', strtotime($ambulanceDetails->insurance_start_date)) : ''}}">
                            </div>
                            <div class="form-group col-md-3">
                                <label class="font-weight-bold">Insurance Valid Upto:</label>
                            </div>
                            <div class="form-group col-md-3">
                                <span class="not-editable">{{ $ambulanceDetails->insurance_valid_upto ?  date('d/m/Y', strtotime($ambulanceDetails->insurance_valid_upto)) : ' - '}}</span>
                                <input type="text" class="form-control editable d-none" id="insurance_valid_date" name="insurance_valid_date" readonly placeholder="Select Insurance Valid Date" data-parsley-excluded="true" value="{{ $ambulanceDetails->insurance_valid_upto ?  date('d/m/Y', strtotime($ambulanceDetails->insurance_valid_upto)) : ''}}">
                            </div>
                        </div>

                        @if($ambulanceDetails->insurance_upload_path)
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label><a href="{{ $ambulanceDetails->insurance_upload_path }}" style="text-decoration: none; cursor: pointer;" target="_blank">View Insurance</a></label>
                            </div>
                        </div>
                        @endif
                    </div>
                </fieldset>
                <fieldset class="border rounded-3 p-3 card  mt-3 ">
                    <legend class="float-none w-auto px-3">
                        <h5>PUC Details</h5>
                    </legend>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label class="font-weight-bold">PUC:</label>
                            </div>
                            <div class="form-group col-md-3">
                                <span class="not-editable">{{ $ambulanceDetails->puc_available ?? '-' }}</span>
                                <div class="editable d-none">
                                    <select name="puc_availability" id="puc_availability" class="form-control form-select" placeholder='Select' data-search="on" data-parsley-excluded="true">
                                        <option selected disabled>Select</option>
                                        <option value="Yes" {{ ($ambulanceDetails->puc_available ?? '') == 'Yes' ? 'selected' : '' }}>Yes</option>
                                        <option value="Not Available" {{ ($ambulanceDetails->puc_available ?? '') == 'Not Available' ? 'selected' : '' }}>Not Available</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-md-3 d-none editable">
                                <label class="font-weight-bold" for="puc_certificate_doc">Upload PUC Certificate:</label>
                            </div>
                            <div class="form-group col-md-3 d-none editable">
                                <div class="custom-file mb-3">
                                    <input type="file" class="custom-file-input" id="puc_certificate_doc" name="puc_certificate_doc" onchange="updateFileName(this)" data-parsley-excluded="true" accept=".jpg, .jpeg, .png, .gif, .pdf">
                                    <label class="custom-file-label" for="attach_file">Tap to attach a file</label>
                                </div>
                            </div>

                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label class="font-weight-bold">PUC Certificate Validity:</label>
                            </div>
                            <div class="form-group col-md-3">
                                <span class="not-editable">{{ $ambulanceDetails->puc_certificate_validity ?  date('d/m/Y', strtotime($ambulanceDetails->puc_certificate_validity)) : ' - '}}</span>
                                <input type="text" class="form-control editable d-none" id="puc_certificate_valid_date" name="puc_certificate_valid_date" readonly placeholder="Select PUC Certificate Validity" data-parsley-excluded="true" value="{{ $ambulanceDetails->puc_certificate_validity ?  date('d/m/Y', strtotime($ambulanceDetails->puc_certificate_validity)) : ''}}">
                            </div>

                            @if($ambulanceDetails->puc_certificates_path)
                            <div class="form-group col-md-3">
                                <label><a href="{{ $ambulanceDetails->puc_certificates_path }}" style="text-decoration: none; cursor: pointer;" target="_blank">View PUC Certificate</a></label>
                            </div>
                            @endif
                        </div>
                    </div>
                </fieldset>
                <fieldset class=" border rounded-3 p-3 card mt-3 ">
                    <legend class=" float-none w-auto px-3">
                        <h5>Fitness Details</h5>
                    </legend>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label class="font-weight-bold">Fitness Papers:</label>
                            </div>
                            <div class="form-group col-md-3">
                                <span class="not-editable">{{ $ambulanceDetails->fitness_available ?? '-' }}</span>
                                <div class="editable d-none">
                                    <select name="fitness_certificate_availability" id="fitness_certificate_availability" class="form-control form-select" placeholder='Select' data-search="on" data-parsley-excluded="true">
                                        <option disabled>Select</option>
                                        <option value="Yes" {{ ($ambulanceDetails->fitness_available ?? '') == 'Yes' ? 'selected' : '' }}>Yes</option>
                                        <option value="Not Available" {{ ($ambulanceDetails->fitness_available ?? '') == 'Not Available' ? 'selected' : '' }}>Not Available</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-md-3 d-none editable">
                                <label class="font-weight-bold" for="fitness_certificate_doc">Upload Fitness Certificate</label>
                            </div>
                            <div class="form-group col-md-3 d-none editable">
                                <div class="custom-file mb-3">
                                    <input type="file" class="custom-file-input" id="fitness_certificate_doc" name="fitness_certificate_doc" onchange="updateFileName(this)" data-parsley-excluded="true" accept=".jpg, .jpeg, .png, .gif, .pdf">
                                    <label class="custom-file-label" for="attach_file">Tap to attach a file</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label class="font-weight-bold">Fitness Certificate Validity:</label>
                            </div>
                            <div class="form-group col-md-3">
                                <span class="not-editable">{{ $ambulanceDetails->fitness_certificate_validity ?  date('d/m/Y', strtotime($ambulanceDetails->fitness_certificate_validity)) : ' - '}}</span>
                                <input type="text" class="form-control editable d-none" id="fitness_certificate_valid_date" name="fitness_certificate_valid_date" readonly placeholder="Select Fitness Certificate Validity" data-parsley-excluded="true" value="{{ $ambulanceDetails->fitness_certificate_validity ?  date('d/m/Y', strtotime($ambulanceDetails->fitness_certificate_validity)) : ''}}">
                            </div>

                            @if($ambulanceDetails->fitness_certificate_upload_path)
                            <div class="form-group col-md-3">
                                <label><a href="{{ $ambulanceDetails->fitness_certificate_upload_path }}" style="text-decoration: none; cursor: pointer;" target="_blank">View Fitness Certificate</a></label>
                            </div>
                            @endif
                        </div>
                    </div>
                </fieldset>
                <fieldset class="border rounded-3 p-3 card  mt-3 ">
                    <legend class="float-none w-auto px-3">
                        <h5>Supplier and Payment Details</h5>
                    </legend>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label class="font-weight-bold">Supplier Name:</label>
                            </div>
                            <div class="form-group col-md-3">
                                <span class="not-editable">{{ $ambulanceDetails->supplier_name ?? '-' }}</span>
                                <input type="text" class="form-control editable d-none" id="supplier_name" name="supplier_name" placeholder="Enter Supplier Name" data-parsley-excluded="true" value="{{ $ambulanceDetails->supplier_name ?? '' }}" maxlength="100">
                            </div>
                            <div class="form-group col-md-3">
                                <label class="font-weight-bold">Payment Bank:</label>
                            </div>
                            <div class="form-group col-md-3">
                                <span class="not-editable">{{ $ambulanceDetails->bank_name ?? '-' }}</span>
                                <div class="d-none editable">
                                    <select name="payment_bank_id" id="payment_bank_id" class="form-control form-select" placeholder='Select Bank' data-search="on" data-parsley-excluded="true">
                                        <option disabled>Select Bank</option>
                                        @if(!empty($banks))
                                        @foreach ($banks as $key => $bank)
                                        <option value="{{ $bank->id }}" {{ ($ambulanceDetails->bank_name ?? '') == $bank->bank_name ? 'selected' : '' }}>
                                            {{ $bank->bank_name }}
                                        </option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label class="font-weight-bold">Payment Date:</label>
                            </div>
                            <div class="form-group col-md-3">
                                <span class="not-editable">{{ $ambulanceDetails->payment_date ?  date('d/m/Y', strtotime($ambulanceDetails->payment_date)) : ' - '}}</span>
                                <input type="text" class="form-control editable d-none" id="payment_date" name="payment_date" readonly placeholder="Select Payment Date" data-parsley-excluded="true" value="{{ $ambulanceDetails->payment_date ?  date('d/m/Y', strtotime($ambulanceDetails->payment_date)) : ''}}">
                            </div>
                        </div>
                    </div>
                </fieldset>
                <fieldset class="border rounded-3 p-3 card  mt-3 ">
                    <legend class="float-none w-auto px-3">
                        <h5>Notes</h5>
                    </legend>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label class="font-weight-bold">Additional Notes:</label>
                            </div>
                            <div class="form-group col-md-5">
                                <span class="not-editable">{{ $ambulanceDetails->additional_notes ?? '-' }}</span>
                                <textarea class="form-control editable d-none" id="additional_notes" name="additional_notes" rows="4" placeholder="Enter Additional Notes Here ..." data-parsley-excluded="true"></textarea>
                            </div>
                        </div>
                    </div>
                </fieldset>
                <fieldset class="border rounded-3 p-3 card  mt-3 ">
                    <legend class="float-none w-auto px-3">
                        <h5>Other Details</h5>
                    </legend>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label class="font-weight-bold">Ambulance Date of Creation:</label>
                            </div>
                            <div class="form-group col-md-3">
                                <span></span>
                            </div>
                            <div class="form-group col-md-3">
                                <label class="font-weight-bold">Created By:</label>
                            </div>
                            <div class="form-group col-md-3">
                                <span>{{ $ambulanceDetails->ambulance_created_by ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                </fieldset>
                <div class="form-row mt-2 justify-content-end">
                    <button class="btn btn-primary updateAmbulanceDetails d-none" name="submit" type="submit"><span>Update Ambulance Details</span></button>
                </div>
            </form>
        </div>
        <div class="tab-pane fade show mt-5" id="staff_details">
            <div class="d-none update_staff_shift" id="update_staff_shift">
                <a class="btn" style="font-size: 18px;margin-bottom: 10px;" class="btn" href="#" onclick="backToStaffList()"><em class='icon ni ni-arrow-left'></em>Back</a>
                <fieldset class="border rounded-3 p-3 card  mt-3 ">
                    <div class="card-body justify-content-center">
                        <form role="form" id="shiftAssignmentForm" method="post" enctype="multipart/form-data">
                            @csrf

                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label class="font-weight-bold">User Type</label>
                                </div>
                                <div class="form-group col-md-6">
                                    <div class="custom-control custom-control-md custom-radio mb-2 col-md-2">
                                        <input type="radio" id="driver" name="userType" class="custom-control-input radio-btn" value="Driver" checked onchange="getUsersList();">
                                        <label class="custom-control-label" for="driver">Driver</label>
                                    </div>

                                    <div class="custom-control custom-control-md custom-radio mb-2 col-md-2">
                                        <input type="radio" id="attendant" name="userType" class="custom-control-input radio-btn" value="Attendant" onchange="getUsersList();">
                                        <label class="custom-control-label" for="attendant">Attendant</label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label class="font-weight-bold">Shift Type</label>
                                </div>
                                <div class="form-group col-md-6">
                                    <div class="custom-control custom-control-md custom-radio mb-2 col-md-2">
                                        <input type="radio" id="permanent" name="shiftType" class="custom-control-input radio-btn" value="Permanent" checked onchange="getUsersList();">
                                        <label class="custom-control-label" for="permanent">Permanent</label>
                                    </div>

                                    <div class="custom-control custom-control-md custom-radio mb-2 col-md-2">
                                        <input type="radio" id="temporary" name="shiftType" class="custom-control-input radio-btn" value="Temporary" onchange="getUsersList();">
                                        <label class="custom-control-label" for="temporary">Temporary</label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label class="font-weight-bold">Shift</label>
                                </div>
                                <div class="form-group col-md-6">
                                    @if(!empty($shiftTypes))
                                    @foreach ($shiftTypes as $key => $shiftType)
                                    <div class="custom-control custom-control-md custom-radio mb-2">
                                        <input class="custom-control-input radio-btn" type="radio" name="shift_id" id="shift_{{ $shiftType->id }}" value="{{ $shiftType->id }}" {{ $key === 0 ? 'checked' : '' }} onchange="getUsersList()">
                                        <label class="custom-control-label" for="shift_{{ $shiftType->id }}">
                                            {{ $shiftType->shift_name }}
                                        </label>
                                    </div>
                                    @endforeach
                                    @endif
                                </div>
                            </div>


                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label class="font-weight-bold">Shift Start Date</label>
                                </div>
                                <div class="col-md-6">
                                    <div class="col-md-2"></div>
                                    <div class="form-group col-md-4">
                                        <input type="text" class="form-control" name="shift_start_date" id="shift_start_date" readonly placeholder="Select Shift Start Date" data-parsley-excluded="true" onchange="getUsersList()">
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label class="font-weight-bold">Shift End Date</label>
                                </div>
                                <div class="col-md-6">
                                    <div class="col-md-2"></div>
                                    <div class="form-group col-md-4">
                                        <input type="text" class="form-control" name="shift_end_date" id="shift_end_date" readonly placeholder="Select Shift End Date" data-parsley-excluded="true" onchange="getUsersList()">
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label class="font-weight-bold">Shift Date</label>
                                </div>
                                <div class="col-md-6">
                                    <div class="col-md-2"></div>
                                    <div class="form-group col-md-4">
                                        <input type="text" class="form-control" name="shift_date" id="shift_date" readonly placeholder="Select Shift Date" data-parsley-excluded="true" onchange="getUsersList()">
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label class="font-weight-bold" for="service_area_id">Service Area <span class="text-danger">*</span></label>
                                </div>
                                <div class="col-md-6">
                                    <div class="col-md-2"></div>
                                    <div class="form-group col-md-4">
                                        <select name="service_area_id" id="service_area_id" class="form-control form-select" data-search="on" placeholder='Select Service Area' data-parsley-excluded="true">
                                            <option value="" selected>Select Service Area</option>
                                            @if(!empty($serviceAreas))
                                            @foreach ($serviceAreas as $key => $serviceArea)
                                            <option value="{{ $serviceArea->id }}">
                                                {{ $serviceArea->service_area }}
                                            </option>
                                            @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label class="font-weight-bold" for="station_area">Station Area<span class="text-danger">*</span></label>
                                </div>
                                <div class="col-md-6">
                                    <div class="col-md-2"></div>
                                    <div class="form-group col-md-4">
                                        <input type="text" class="form-control" name="station_area" placeholder="Enter Station Area" data-parsley-excluded="true">
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label class="font-weight-bold">Assign<span class="text-danger">*</span></label>
                                </div>
                                <div class="form-group col-md-6">
                                    <div class="col-md-2"></div>
                                    <div class="col-md-4 form-control-required-wrap">
                                        <select name="assigned_user" class="form-control form-select" id="assigned_user" data-search="on" data-parsley-excluded="true">
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-row justify-content-center mt-2">
                                <a class="btn btn- btn-outline-light mr-2" href="#" onclick="backToStaffList();">Cancel</a>
                                <button class="btn btn-primary submitShiftBtn" name="submit"><span>Submit</span></button>
                            </div>
                        </form>
                    </div>
                </fieldset>
            </div>
            <div class="staff_view" id="staff_view">
                <div class="toggle-bar">
                    <ul class="nav" style="display: flex; justify-content: flex-end; list-style: none; padding: 0;">
                        <li class="nav-item">
                            <div class="form-group" style="padding-right: 15px; width: 150px;">
                                <select name="month_id" id="month_id" class="form-control mr-1 form-select" data-search="on" placeholder="Select Month" onchange="getAmbulanceStaffByMonth();">
                                    <option disabled value="">Select Month</option>
                                    @php
                                    // Create options for each month
                                    $months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

                                    $currentMonth = date('n'); // Get the current month (1-based)
                                    $previousMonth = (($currentMonth - 2 + 12) % 12) + 1; // Calculate the previous month

                                    foreach ($months as $index => $month) {
                                    $monthNumber = $index + 1; // Months are 1-based in PHP
                                    $monthNumberPadded = str_pad($monthNumber, 2, '0', STR_PAD_LEFT); // Add leading zero if necessary
                                    $selected = $monthNumber == $currentMonth ? 'selected' : '';
                                    echo "<option value=\"$monthNumberPadded\" $selected>$month</option>";
                                    }
                                    @endphp
                                </select>
                            </div>
                        </li>
                        <li class="nav-item">
                            <div class="form-group" style="padding-right: 15px; width: 150px;">
                                <select name="year_id" id="year_id" class="form-control form-select" data-search="on" placeholder="Select Year" onchange="getAmbulanceStaffByMonth();">
                                    <option disabled value="">Select Year</option>
                                    @php
                                    // Set the range of years you want to allow
                                    $currentYear = date('Y');
                                    $startYear = 2000;

                                    // Create options for each year in the range
                                    for ($year = $currentYear; $year >= $startYear; $year--) {
                                    echo " <option value=\"$year\">$year</option>";
                                    }
                                    @endphp
                                </select>
                            </div>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link btn btn-primary mr-2" href="#" id="update_staff_shift_btn"><em class="icon ni ni-calendar" style="color: #fff;"></em><span>Update Staff</span></a>
                        </li>
                    </ul>
                </div>
                <fieldset class="border rounded-3 p-3 card  mt-3 ">
                    <ul class="nav nav-tabs" id="staffTabs">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#ambulance_staff_list_view" id="list_view">List View</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#ambulance_staff_calendar_view" id="calendar_view">Calendar View</a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="ambulance_staff_list_view">
                            <fieldset class="border rounded-3 p-3 card mt-3">
                                <div class="card-body">
                                    <div class="row" id="staff_card_view">

                                    </div>
                                </div>
                            </fieldset>
                        </div>

                        <div class="tab-pane fade show" id="ambulance_staff_calendar_view">
                            <fieldset class="border rounded-3 p-3 card mt-3">
                                <ul class="calendar-legends">
                                    <li><span class="circle" style="background-color:green;"></span> Assigned Shifts</li>
                                    <li><span class="circle" style="background-color:red;"></span> Unassigned Shifts</li>
                                </ul>
                                <div class="card-body">
                                    <div id="calendar" class="nk-calendar">
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>
        <div class="tab-pane fade show mt-5" id="inventory_details">
            <div class="toggle-bar">
                <ul class="nav" style="display: flex; justify-content: flex-end; list-style: none; padding: 0;">
                    <li class="nav-item">
                        <a class="nav-link btn btn-primary mr-3 dropdown-toggle" href="#" data-toggle="modal" data-target="#addItemModal" id="addItem"><em class="icon ni ni-plus" style="color: #fff;"></em><span>Add an Item</span></a>
                    </li>
                </ul>
            </div>
            <fieldset class="border rounded-3 p-3 card mt-3 " id="inventory_list">
                <div class="card-body">
                    <div class="nk-block table-compact">
                        <div class="nk-tb-list is-separate mt-3">
                            <table id="inventoryDetails" class="inventoryDetails nowrap nk-tb-list is-separate" data-auto-responsive="false">
                                <thead>
                                    <tr class="nk-tb-item nk-tb-head">
                                        <th class="nk-tb-col tb-col-mb"><span class="sub-text">Item</span></th>
                                        <th class="nk-tb-col tb-col-mb"><span class="sub-text">UOM</span></th>
                                        <th class="nk-tb-col tb-col-mb"><span class="sub-text">Capacity</span></th>
                                        <th class="nk-tb-col tb-col-mb"><span class="sub-text">Quantity</span></th>
                                        <th class="nk-tb-col tb-col-mb"><span class="sub-text">Last Updated</span></th>
                                        <th class="nk-tb-col tb-col-mb"><span class="sub-text">Actions</span></th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </fieldset>
            <fieldset class="border rounded-3 p-3 card mt-3" id="update_inventory">
                <input type="hidden" name="inventory_id" id="inventory_id" value="">
                <div class="card-body">
                    <h6 id="inventory_item_name" style="text-align:center;justify-content:space-between;"></h6>
                    <a class="btn" style="font-size: 18px;margin-bottom: 10px;" class="btn" href="#" onclick="backToList()"><em class='icon ni ni-arrow-left'></em>Back</a>
                    <fieldset class="border rounded-3 p-3 card mt-3" style="margin:auto;max-width: 50%;">
                        <legend class="float-none w-auto px-3">
                            <h6 id="inventory_heading">Current Status</h6>
                        </legend>
                        <div class="card-body">
                            <div class="form-row">
                                <div class="form-group col-md-8">
                                    <label class="font-weight-bold">Unit Of Measurement:</label>
                                </div>
                                <div class="form-group col-md-3">
                                    <span id="view_inventory_unit_of_measurement"></span>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-8">
                                    <label class="font-weight-bold">Capacity:</label>
                                </div>
                                <div class="form-group col-md-3">
                                    <span id="view_inventory_capacity"></span>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-8">
                                    <label class="font-weight-bold">Quantity (Current):</label>
                                </div>
                                <div class="form-group col-md-3">
                                    <span id="view_inventory_quantity"></span>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-8">
                                    <label class="font-weight-bold">Last Updated:</label>
                                </div>
                                <div class="form-group col-md-3">
                                    <span id="view_last_updated_inventory"></span>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <a class="nav-link btn btn-primary d-none d-md-inline-flex dropdown-toggle" href="#" data-toggle="modal" data-target="#updateInventoryModal"><em class="icon ni ni-plus" style="color: #fff;"></em><span>Update Inventory</span></a>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset class="border rounded-3 p-3 card mt-3" id="update_inventory_history">
                        <legend class="float-none w-auto px-3">
                            <h6>Previous Updates</h6>
                        </legend>
                        <div class="nk-block table-compact">
                            <div class="nk-tb-list is-separate">
                                <table id="inventoryHistory" class="inventoryHistory nowrap nk-tb-list is-separate table-responsive">
                                    <thead>
                                        <tr class="nk-tb-item nk-tb-head">
                                            <th class="nk-tb-col tb-col-mb"><span class="sub-text">Date</span></th>
                                            <th class="nk-tb-col tb-col-mb"><span class="sub-text">Change Type</span></th>
                                            <th class="nk-tb-col tb-col-mb"><span class="sub-text">UOM</span></th>
                                            <th class="nk-tb-col tb-col-mb"><span class="sub-text">Added/Consumed By</span></th>
                                            <th class="nk-tb-col tb-col-mb"><span class="sub-text">Changed By</span></th>
                                        </tr>
                                    </thead>
                                    <tbody class="mb-2">
                                        <!-- Add your table content here -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </fieldset>
                </div>
            </fieldset>
        </div>
        <div class="tab-pane fade show mt-5" id="expenses_details">
            <div class="toggle-bar">
                <ul class="nav" style="display: flex; justify-content: flex-end; list-style: none; padding: 0;">
                    <li class="nav-item">
                        <div id="filter_tag_list" class="filter-tag-list"></div>
                    </li>
                    <li class="nav-item">
                        <a href="#" data-toggle="modal" data-target="#filterExpense" class="toggle btn btn-trigger btn-icon">
                            <div class="dot dot-primary" style="border-radius: 50%;width: 10px;height: 10px;"></div>
                            <em class="icon ni ni-filter-alt"></em>
                        </a>
                    </li>
                </ul>
            </div>
            <fieldset class="border rounded-3 p-3 card mt-3">
                <div class="card-body">
                    <div class="nk-block table-compact">
                        <div class="nk-tb-list is-separate">
                            <table id="expenseDetails" class="brand-init expenseDetails nowrap nk-tb-list is-separate table-responsive">
                                <thead>
                                    <tr class="nk-tb-item nk-tb-head">
                                        <th class="nk-tb-col tb-col-mb"><span class="sub-text">Created By</span></th>
                                        <th class="nk-tb-col tb-col-mb"><span class="sub-text">Vehicle No.</span></th>
                                        <th class="nk-tb-col tb-col-mb"><span class="sub-text">Entry Type</span></th>
                                        <th class="nk-tb-col tb-col-mb"><span class="sub-text">Expense Type</span></th>
                                        <th class="nk-tb-col tb-col-mb"><span class="sub-text">Amount</span></th>
                                        <th class="nk-tb-col tb-col-mb"><span class="sub-text">Date</span></th>
                                        <th class="nk-tb-col tb-col-mb"><span class="sub-text">Claim Status</span></th>
                                        <th class="nk-tb-col tb-col-mb"><span class="sub-text">Reimbursement Status</span></th>
                                        <th class="nk-tb-col tb-col-mb"><span class="sub-text">Actions</span></th>
                                    </tr>
                                </thead>
                                <tbody class="mb-2">
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </fieldset>
        </div>
    </div>
    @endif
</div>
<div class="modal fade zoom" tabindex="-1" id="addItemModal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add an Item</h5>
                <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
            </div>
            <form role="form" class="mb-0" method="POST" id="addItemForm">
                @csrf
                <div class="modal-body modal-body-lg">
                    <div class="gy-3">
                        <div class="alert alert-info">
                            <p class="mb-0"><strong>NOTE:</strong></p>
                            <ul class="mb-0">
                                <li>UOM = Unit of measurement</li>
                                <li>Gas = PSI MPa/kPa</li>
                                <li>Liquids = Litre/mL</li>
                                <li>Solids = KG/g/pounds</li>
                                <li>Other = Units</li>
                            </ul>
                        </div>

                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <label class="form-label" for="item_name">Item Name<span class="text-danger">*</span></label>
                            </div>
                            <div class="col-lg-7">
                                <input type="text" class="form-control" id="item_name" name="item_name" placeholder="Enter Item Name" data-parsley-excluded="true">
                            </div>
                        </div>
                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <label class="form-label" for="unit_of_measurement">Unit Of Measurement<span class="text-danger">*</span></label>
                            </div>
                            <div class="col-lg-7 form-control-required-wrap">
                                <select class="form-control form-select" name="unit_of_measurement" id="unit_of_measurement" data-search="on" placeholder='Select Unit Of Measurement' data-parsley-excluded="true">
                                    <option selected disabled value="0">Select Unit of Measurement</option>
                                    <option value="Gram">g</option>
                                    <option value="KG">KG</option>
                                    <option value="Litre">L</option>
                                    <option value="ml">ml</option>
                                    <option value="MPa">MPa</option>
                                    <option value="KPa">kPa</option>
                                    <option value="Pounds">pounds</option>
                                    <option value="units">Units</option>
                                </select>

                            </div>
                        </div>
                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <label class="form-label" for="capacity">Capacity<span class="text-danger">*</span></label>
                            </div>
                            <div class="col-lg-7">
                                <input type="text" class="form-control" id="capacity" name="capacity" placeholder="Enter Capacity" data-parsley-excluded="true" onkeypress="return isNumber(event)">
                            </div>
                        </div>
                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <label class="form-label" for="quantity">Quantity (Initial)<span class="text-danger">*</span></label>
                            </div>
                            <div class="col-lg-7">
                                <input type="text" class="form-control" id="quantity" name="quantity" placeholder="Enter Quantity" data-parsley-excluded="true" onkeypress="return isNumber(event)">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <div class="row">
                        <div class="col-lg-12 p-0 text-right">
                            <button class="btn btn-outline-light" data-dismiss="modal" aria-label="Close">Cancel</button>
                            <button class="btn btn-danger" onclick="resetValues()" type="button">Reset</button>
                            <button class="btn btn-primary submitBtn">Submit</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade zoom" tabindex="-1" id="filterExpense">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Filter</h5>
                <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
            </div>
            <form action="#" role="form" class="mb-0" method="get" id="expenseFilterForm">
                @csrf
                <div class="modal-body modal-body-lg">
                    <div class="gy-3">

                        <div class="row g-3">
                            <!-- Dropdown for Entry Type Range -->
                            <div class="col-mb-12">
                                <x-inputs.select size="md" name="entry_type" for="entry_type" id="entry_type" data-search="on" placeholder='Select Entry Type'>
                                    <option value="" selected disabled>Select Entry Type</option>
                                    <option value="Claim">Claim</option>
                                    <option value="Record">Record</option>
                                </x-inputs.select>
                            </div>
                        </div>
                        <div class="row g-3">
                            <!-- Dropdown for Expense Type -->
                            <div class="col-mb-12">
                                <x-inputs.select size="md" name="expense_type_id" for="expense_type_id" id="expense_type_id" data-search="on" placeholder='Select Expense Type'>
                                    <option value="" selected disabled>Select Expense Type</option>
                                    @if(!empty($expenseTypes))
                                    @foreach($expenseTypes as $key => $expenseType)
                                    <option value="{{$expenseType->id}}">{{$expenseType->name ?? ''}}</option>
                                    @endforeach
                                    @endif
                                </x-inputs.select>

                            </div>
                        </div>
                        <div class="row g-3">
                            <!-- Dropdown for Status -->
                            <div class=" col-mb-12">
                                <x-inputs.select size="md" name="status" for="status" id="status" data-search="on" placeholder='Select Status'>
                                    <option value="" selected disabled>Select Status</option>
                                    <option value="Pending">Pending</option>
                                    <option value="Approved">Approved</option>
                                    <option value="Rejected">Rejected</option>
                                </x-inputs.select>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-mb-12">
                                <x-inputs.select size="md" name="reimbursment_status" for="reimbursment_status" id="reimbursment_status" data-search="on" placeholder='Select Reimbursment Status'>
                                    <option value="" selected disabled>Select Reimbursment Status</option>
                                    <option value="Pending">Pending</option>
                                    <option value="Approved">Approved</option>
                                    <option value="Rejected">Rejected</option>
                                </x-inputs.select>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-mb-6">
                                <input type="text" placeholder="Select From date" class="form-control" name="fromDate" id="fromDate" readonly />
                            </div>
                            <div class="col-mb-6">
                                <input type="text" placeholder="Select To date" name="toDate" class="form-control" id="toDate" readonly />
                            </div>
                        </div>



                        <div class="row">
                            <div class="col-lg-12 p-0 text-right">
                                <a data-dismiss="modal" aria-label="Close" class="btn btn-outline-light cancel">Cancel</a>
                                <a class="btn btn-danger resetFilter" data-target='#filterExpense' style="color:#fff">Clear
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

<div class="modal fade zoom" tabindex="-1" id="updateInventoryModal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Quantity</h5>
                <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
            </div>
            <form role="form" class="mb-0" method="POST" id="updateQuantityForm">
                @csrf
                <div class="modal-body modal-body-lg">
                    <div class="gy-3">
                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <label class="form-label" for="item_name">Change Type</label>
                            </div>
                            <div class="col-lg-7">
                                <div class="custom-control custom-control-md custom-radio mb-2">
                                    <input class="custom-control-input radio-btn" type="radio" name="update_inventory_change_type" id="addInventory" value="Added" checked>
                                    <label class="custom-control-label" for="addInventory">Add</label>
                                </div>
                                <div class="custom-control custom-control-md custom-radio mb-2">
                                    <input class="custom-control-input radio-btn" type="radio" name="update_inventory_change_type" id="consumeInventory" value="Consumed">
                                    <label class="custom-control-label" for="consumeInventory">Consume</label>
                                </div>
                            </div>
                        </div>
                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <label class="form-label" for="quantity">Quantity<span class="text-danger">*</span></label>
                            </div>
                            <div class="col-lg-7">
                                <input type="text" class="form-control" id="update_inventory_quantity" name="update_inventory_quantity" placeholder="Enter Quantity" data-parsley-excluded="true" onkeypress="return isNumber(event)">
                            </div>
                        </div>
                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <label class="form-label" for="date_of_change">Date of Change<span class="text-danger">*</span></label>
                            </div>
                            <div class="col-lg-7">
                                <input type="text" placeholder="Select Date of Change" class="form-control" name="date_of_change" id="date_of_change" readonly />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12 p-0 text-right">
                                <button class="btn btn-outline-light" data-dismiss="modal" aria-label="Close">Cancel</button>
                                <button class="btn btn-primary updateInventory">Submit</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" id="previewEventPopup" aria-modal="false" role="dialog">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div id="preview-event-header" class="modal-header custom-modal-header">
                <h5 id="preview-event-title" class="modal-title">-</h5> <a href="#" class="close" data-dismiss="modal" aria-label="Close"> <em class="icon ni ni-cross"></em> </a>
            </div>
            <div class="modal-body">
                <div class="row gy-3 py-1">
                    <div class="col-sm-4">
                        <h6 class="overline-title">From Date</h6>
                        <p id="preview-from-date">-</p>
                    </div>
                    <div class="col-sm-4">
                        <h6 class="overline-title">To Date</h6>
                        <p id="preview-to-date">-</p>
                    </div>

                    <div class="col-sm-4 chassis_id">
                        <h6 class="overline-title">Chassis Id</h6>
                        <p id="preview-event-chassis_id">-</p>
                    </div>

                    <div class="col-sm-4">
                        <h6 class="overline-title">Type</h6>
                        <p id="preview-to-type">-</p>
                    </div>
                    <div class="col-sm-4 model_name">
                        <h6 class="overline-title">Model Name</h6>
                        <p id="preview-event-model_name">-</p>
                    </div>

                    <div class="col-sm-4 customer_name">
                        <h6 class="overline-title">Customer Name</h6>
                        <p id="preview-event-customer_name">-</p>
                    </div>

                    <div class="col-sm-4 workshop_name">
                        <h6 class="overline-title">Workshop Name</h6>
                        <p id="preview-event-workshop_name">-</p>
                    </div>

                    <div class="col-sm-4 area_name">
                        <h6 class="overline-title">Area</h6>
                        <p id="preview-event-area_name">-</p>
                    </div>

                    <div class="col-sm-4 event-region_name">
                        <h6 class="overline-title">Region</h6>
                        <p id="preview-event-region_name">-</p>
                    </div>

                    <div class="col-sm-12" id="preview-event-description-wrap">
                        <h6 class="overline-title">Description</h6>
                        <p id="preview-event-description">-</p>
                    </div>

                    <div class="col-sm-12" id="preview-event-description-check" style="display: none">
                        <h6 class="overline-title">Plan Comment</h6>
                        <p id="preview-event-description"></p>
                    </div>

                </div>
            </div>
            <div class="modal-footer bg-light custom-modal-footer" id="footer_createChecklistBtn" style="display: flex; justify-content: space-between;">
                <div class="left">
                    <button id="reAssignBtn" class="btn btn-info" data-toggle="modal" data-target="#reAssignPopup">Re Assign</button>
                    <button id="reScheduleBtn" class="btn btn-warning" data-toggle="modal" data-target="#reSchedulePopup">Re Schedule</button>
                </div>
                <div class="right">
                    <button id="createChecklistBtn" class="btn btn-primary createChecklistBtn">Fill Checklist</button>
                    <button data-eventid='' id="markAsDoneBtn" class="btn btn-success markAsDoneBtn">Mark as Done</button>
                </div>

            </div>
            <div class="alert alert-warning alert-icon" id="reAssignStatus" style="display: none; width: 100%;">
                <em class="icon ni ni-alert-circle"></em> Approval request is pending to <span id="requestType"></span> this event.
            </div>
        </div>
    </div>
</div>
<!-- Re Assign Modal -->
<div class="modal fade" tabindex="-1" id="reAssignPopup" aria-modal="false" role="dialog">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div id="preview-event-header" class="modal-header custom-modal-header" style="background-color: #09c2de;">
                <h5 id="preview-event-title" class="modal-title">Re-Assign </h5> <a href="#" class="close" id="reAssignPopupClose" data-dismiss="modal" aria-label="Close"> <em class="icon ni ni-cross"></em> </a>
            </div>
            <form method="post" action="{{ url('event/event-update-request') }}" id="reAssignForm">
                @csrf
                <div class="modal-body">
                    <div class="row gy-3 py-1">
                        <input type="hidden" name="event_id" id="event_id" class="event_id" value="" />
                        <input type="hidden" name="type" id="type" value="Reassign" />
                        <input type="hidden" name="current_workshop" class="current_workshop" id="current_workshop" />
                        <input type="hidden" name="current_from_time" class="currentFromDateTime">
                        <input type="hidden" class="currentToDateTime" id="currentToDateTime" name="current_to_time">

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label">Current Workshop</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="current_workshop_name" readonly />
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="requested_workshop">Workshops <span class="text-danger">*</span></label>
                                <div class="form-control-wrap form-control-required-wrap">
                                    <select class="form-select form-control" data-placeholder="Select" name="requested_workshop" id="requested_workshop" data-search="on" required>
                                        <option value="">Select</option>
                                        @if(!empty($workshops))
                                        @foreach($workshops as $wrkshp)
                                        @if($wrkshp->workshop_id != $workshop[0]->ID)
                                        <option value="{{$wrkshp->workshop_id}}">{{$wrkshp->workshop_name}}</option>
                                        @endif
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light custom-modal-footer">
                    <button id="handleReAssignForm" type="submit" data-form="reAssignForm" data-type="ReAssign" class="btn btn-primary eventUpdateBtn">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Re Schedule Modal -->
<div class="modal fade" tabindex="-1" id="reSchedulePopup" aria-modal="false" role="dialog">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div id="preview-event-header" class="modal-header custom-modal-header" style="background-color: #f4bd0e;">
                <h5 id="preview-event-title" class="modal-title">Re-Schedule </h5> <a href="#" class="close" id="reSchedulePopupClose" data-dismiss="modal" aria-label="Close"> <em class="icon ni ni-cross"></em> </a>
            </div>
            <form method="post" action="{{ url('event/event-update-request') }}" id="reScheduleForm">
                @csrf
                <div class="modal-body">
                    <div class="row gy-3 py-1">
                        <input type="hidden" name="event_id" id="event_id" class="event_id" value="" />
                        <input type="hidden" name="type" id="type" value="Reschedule" />
                        <input type="hidden" name="current_workshop" class="current_workshop" />
                        <input type="hidden" name="area_id" id="area_id" />
                        <div class="col-sm-12">
                            <div class="card">
                                <div class="card-header border-bottom">
                                    <h6>Current Date Time</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row gy-3 py-1">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label class="form-label" for="currentFromDateTime">Start Date Time</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control currentFromDateTime" id="currentFromDateTime" name="current_from_time" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label class="form-label" for="currentToDateTime">End Date Time</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control currentToDateTime" value="" id="currentToDateTime" name="current_to_time" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="card">
                                <div class="card-header border-bottom">
                                    <h6>Requested Date Time</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row gy-3 py-1">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label class="form-label" for="requestFromDate">Event Start Date</label>
                                                <div class="form-control-wrap">
                                                    <div class="form-icon form-icon-right">
                                                        <em class="icon ni ni-calendar-alt"></em>
                                                    </div>
                                                    <input type="text" class="form-control date-picker" data-date-start-date="{{ date('m/d/Y',strtotime("0 days")) }}" id="requestFromDate" name="requested_from_date" required readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label" for="requestFromTime">Event Start Time</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control clockTimePicker" id="requestFromTime" name="requested_from_time" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label class="form-label" for="requestToDate">Event End Date</label>
                                                <div class="form-control-wrap">
                                                    <div class="form-icon form-icon-right">
                                                        <em class="icon ni ni-calendar-alt"></em>
                                                    </div>
                                                    <input type="text" class="form-control date-picker" data-date-start-date="{{ date('m/d/Y',strtotime("0 days")) }}" id="requestToDate" name="requested_to_date" required readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label" for="requestToTime">Event End Time</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control clockTimePicker" id="requestToTime" name="requested_to_time" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light custom-modal-footer">
                    <button id="handleReScheduleForm" data-form="reScheduleForm" type="submit" data-type="ReSchedule" class="btn btn-primary eventUpdateBtn">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade zoom" tabindex="-1" id="viewCalendar">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewSelectedCalendarMonth"></h5>
                <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
            </div>
            <div class="modal-body modal-body-lg">
                <ul class="calendar-legends">
                    <li><span class="dot oil-change-color"></span> Assigned Shifts</li>
                    <li><span class="dot internal-event-color"></span> Unassigned Shifts</li>
                </ul>
                <div id="calendar-single" class="nk-calendar">
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('footerScripts')
<script src="{{url('js/tableFlow.js')}}"></script>

<!-- scripts for calendar starts -->
<script src="{{ url('/js/mdtimepicker.js') }}"></script>
<script src="https://unpkg.com/dayjs@1.8.21/dayjs.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.5/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@6.1.5/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/list@6.1.5/index.global.min.js"></script>
<script src="{{url('js/calendar-view.js')}}"></script>
<script>
    $('.clockTimePicker').mdtimepicker({


        // format of the time value (data-time attribute)
        timeFormat: 'hh:mm:ss.000',

        // format of the input value
        format: 'h:mm tt',

        // theme of the timepicker
        // 'red', 'purple', 'indigo', 'teal', 'green', 'dark'
        theme: 'blue',

        // determines if input is readonly
        readOnly: true,

        // determines if display value has zero padding for hour value less than 10 (i.e. 05:30 PM); 24-hour format has padding by default
        hourPadding: true,

        // determines if clear button is visible  
        clearBtn: false

    });
</script>
<!-- scripts for calendar ends -->
<script>
    function onlyAlphanumerics(e) {
        var regex = new RegExp("^[a-zA-Z0-9]+$");
        var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
        if (regex.test(str)) {
            return true;
        }
        e.preventDefault();
        return false;
    }
    var root_url = "<?php echo Request::root(); ?>";
    $(document).ready(function() {
        getExpenseDetails();
        getInventoryDetails();
        // getAmbulanceStaffByMonth();
        getAmbulanceListStaffByMonth();
        $('#update_inventory').addClass('d-none');
        $('#inventory_list').removeClass('d-none');
        $('#update_inventory_history').addClass('d-none');
        $('.not-editable').removeClass('d-none');
        $('.editable').addClass('d-none');
        $('.updateAmbulanceDetails').addClass('d-none');
        $('#updateBtn').on('click', function() {
            updateAmbulanceDetails();
        });
    });
    $("#fromDate").datepicker({
        todayBtn: true,
        orientation: "auto",
        todayHighlight: true,
        autoclose: true,
        format: "mm/dd/yyyy",
        startView: "days",
        endDate: "today"
    });
    $('#fromDate').each(function() {
        $(this).on('changeDate', function(e) {
            fromDate = $(this).datepicker('getDate');
            $('#fromDate').datepicker('update', fromDate);
            $('#toDate').datepicker('update', fromDate);
            startDate = $("#fromDate").val();
            $('#toDate').datepicker('setStartDate', startDate);
            var fromDate = document.getElementById('fromDate').value;
            var toDate = document.getElementById('toDate').value;
            if (toDate == null || toDate == '') {
                $('#toDate').datepicker('update', fromDate);
            }
        });
    });
    $('#toDate').datepicker({
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
    $('#toDate').each(function() {
        $(this).on('changeDate', function(e) {
            fromDate = $(this).datepicker('getDate');
            $('#toDate').datepicker('update', fromDate);
            var fromDate = document.getElementById('fromDate').value;
            var toDate = document.getElementById('toDate').value;
            if (fromDate == null || fromDate == '') {
                $('#fromDate').datepicker('update', toDate);
            }
        });
    });
    $("#date_of_change").datepicker({
        todayBtn: true,
        orientation: "auto",
        todayHighlight: true,
        autoclose: true,
        format: "dd/mm/yyyy",
        startView: "days",
        endDate: "today"
    });
    $('#date_of_change').each(function() {
        $(this).on('changeDate', function(e) {
            date_of_change = $(this).datepicker('getDate');
            $('#date_of_change').datepicker('update', date_of_change);
        });
    });

    $("#inaugration_date").datepicker({
        todayBtn: true,
        orientation: "auto",
        todayHighlight: true,
        autoclose: true,
        format: "dd/mm/yyyy",
        startView: "days",
        endDate: "today"
    });
    $('#inaugration_date').each(function() {
        $(this).on('changeDate', function(e) {
            inaugration_date = $(this).datepicker('getDate');
            $('#inaugration_date').datepicker('update', inaugration_date);
        });
    });
    $("#invoice_date").datepicker({
        todayBtn: true,
        orientation: "auto",
        todayHighlight: true,
        autoclose: true,
        format: "dd/mm/yyyy",
        startView: "days",
        //endDate: "today"
    });
    $('#invoice_date').each(function() {
        $(this).on('changeDate', function(e) {
            invoice_date = $(this).datepicker('getDate');
            $('#invoice_date').datepicker('update', invoice_date);
        });
    });

    $("#date_of_delivery").datepicker({
        todayBtn: true,
        orientation: "auto",
        todayHighlight: true,
        autoclose: true,
        format: "dd/mm/yyyy",
        startView: "days",
        //endDate: "today"
    });
    $('#date_of_delivery').each(function() {
        $(this).on('changeDate', function(e) {
            date_of_delivery = $(this).datepicker('getDate');
            $('#date_of_delivery').datepicker('update', date_of_delivery);
        });
    });

    $("#registration_date").datepicker({
        todayBtn: true,
        orientation: "auto",
        todayHighlight: true,
        autoclose: true,
        format: "dd/mm/yyyy",
        startView: "days",
        endDate: "today"
    });
    $('#registration_date').each(function() {
        $(this).on('changeDate', function(e) {
            registration_date = $(this).datepicker('getDate');
            $('#registration_date').datepicker('update', registration_date);
        });
    });

    $("#entry_date").datepicker({
        todayBtn: true,
        orientation: "auto",
        todayHighlight: true,
        autoclose: true,
        format: "dd/mm/yyyy",
        startView: "days",
        endDate: "today"
    });
    $('#entry_date').each(function() {
        $(this).on('changeDate', function(e) {
            entry_date = $(this).datepicker('getDate');
            $('#entry_date').datepicker('update', entry_date);
        });
    });
    $("#current_insurance_start_date").datepicker({
        todayBtn: true,
        orientation: "auto",
        todayHighlight: true,
        autoclose: true,
        format: "dd/mm/yyyy",
        startView: "days",
        endDate: "today"
    });
    $('#current_insurance_start_date').each(function() {
        $(this).on('changeDate', function(e) {
            current_insurance_start_date = $(this).datepicker('getDate');
            $('#current_insurance_start_date').datepicker('update', current_insurance_start_date);
        });
    });

    $("#insurance_valid_date").datepicker({
        todayBtn: true,
        orientation: "auto",
        todayHighlight: true,
        autoclose: true,
        format: "dd/mm/yyyy",
        startView: "days",
        //endDate: "today"
    });
    $('#insurance_valid_date').each(function() {
        $(this).on('changeDate', function(e) {
            insurance_valid_date = $(this).datepicker('getDate');
            $('#insurance_valid_date').datepicker('update', insurance_valid_date);
        });
    });

    $("#puc_certificate_valid_date").datepicker({
        todayBtn: true,
        orientation: "auto",
        todayHighlight: true,
        autoclose: true,
        format: "dd/mm/yyyy",
        startView: "days",
        //endDate: "today"
    });
    $('#puc_certificate_valid_date').each(function() {
        $(this).on('changeDate', function(e) {
            puc_certificate_valid_date = $(this).datepicker('getDate');
            $('#puc_certificate_valid_date').datepicker('update', puc_certificate_valid_date);
        });
    });

    $("#fitness_certificate_valid_date").datepicker({
        todayBtn: true,
        orientation: "auto",
        todayHighlight: true,
        autoclose: true,
        format: "dd/mm/yyyy",
        startView: "days",
        //endDate: "today"
    });
    $('#fitness_certificate_valid_date').each(function() {
        $(this).on('changeDate', function(e) {
            fitness_certificate_valid_date = $(this).datepicker('getDate');
            $('#fitness_certificate_valid_date').datepicker('update', fitness_certificate_valid_date);
        });
    });

    $("#payment_date").datepicker({
        todayBtn: true,
        orientation: "auto",
        todayHighlight: true,
        autoclose: true,
        format: "dd/mm/yyyy",
        startView: "days",
        endDate: "today"
    });
    $('#payment_date').each(function() {
        $(this).on('changeDate', function(e) {
            payment_date = $(this).datepicker('getDate');
            $('#payment_date').datepicker('update', payment_date);
        });
    });

    $('#filterExpense').on('hidden.bs.modal', function(e) {
        // Reset the values of input fields
        $('#expenseFilterForm')[0].reset();
    });

    $('#updateInventoryModal').on('hidden.bs.modal', function(e) {
        // Reset the values of input fields
        $('#updateQuantityForm')[0].reset();
        $('#updateQuantityForm').validate().resetForm();
        $('#updateQuantityForm .error').removeClass('error');
    });

    $('#addItemModal').on('hidden.bs.modal', function(e) {
        // Reset the values of input fields
        $('#addItemForm')[0].reset();
        $('#addItemForm').validate().resetForm();
        $('#addItemForm .error').removeClass('error');
        resetValues();

    });

    $("#unit_of_measurement").change(function() {
        $("#addItemForm").validate().element("#unit_of_measurement");
    });

    if ($("#addItemForm").length > 0) {
        $("#addItemForm").validate({
            rules: {
                item_name: {
                    required: true,
                },
                unit_of_measurement: {
                    required: true,
                },
                capacity: {
                    required: true,
                },
                quantity: {
                    required: true,
                },
            },
            messages: {
                item_name: {
                    required: "Please enter item name.",
                },
                unit_of_measurement: {
                    required: "Please select unit of measurement.",
                },
                capacity: {
                    required: "Please enter capacity.",
                },
                quantity: {
                    required: "Please enter quantity.",
                },
            },
            submitHandler: function(form, event) {
                event.preventDefault();
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                var ambulance_id = $('#ambulance_id').val();

                // disabled submit button during process running status
                $(".submitBtn").attr("disabled", true);
                var formData = new FormData($('#addItemForm')[0]);
                formData.append('ambulance_id', ambulance_id);
                $.ajax({
                    type: "POST",
                    url: "{{ url('ambulance/store-inventory') }}",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        $(".submitBtn").attr("disabled", false);
                        if (response.status == 'success') {
                            var table = $('.inventoryDetails').DataTable();
                            table.ajax.reload();
                            getInventoryDetails();
                            $('#addItemModal').modal('hide');
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

    function resetValues() {
        // alert('HI');
        $('#item_name').val('');
        $('#unit_of_measurement').val(0).trigger('change');
        $('#capacity').val('');
        $('#quantity').val('');

    }

    function updateInventory(inventoryId) {
        $('#update_inventory').removeClass('d-none');
        $('#inventory_list').addClass('d-none');
        $('#update_inventory_history').removeClass('d-none');
        $('#addItem').addClass('d-none');
        $('.inventoryHistory').DataTable().destroy();
        $('#inventory_id').val(inventoryId);

        var inventoryHistoryDatatable = new CustomDataTable({
            tableElem: '.inventoryHistory',
            option: {
                processing: true,
                serverSide: true,
                ajax: {
                    type: "GET",
                    url: root_url + "/ambulance/view-inventory-history/" + inventoryId,
                },
                columns: [{
                        "class": "nk-tb-col tb-col-lg",
                        data: 'formatted_updated_date',
                        name: 'formatted_updated_date',
                        orderable: true
                    },
                    {
                        "class": "nk-tb-col tb-col-lg",
                        data: 'type',
                        name: 'type',
                        orderable: true
                    },
                    {
                        "class": "nk-tb-col tb-col-lg",
                        data: 'unit_of_measurement',
                        name: 'unit_of_measurement',
                        orderable: true
                    },
                    {
                        "class": "nk-tb-col tb-col-lg",
                        data: 'quantity',
                        name: 'quantity',
                        orderable: true
                    },
                    {
                        "class": "nk-tb-col tb-col-lg",
                        data: 'changed_by',
                        name: 'changed_by',
                        orderable: true
                    },
                ],
            }
        });
    }

    function getExpenseDetails() {
        // Get values

        var dataTable;
        var ambulance_id = $('#ambulance_id').val();
        var items = [
            '#entry_type', '#status', '#expense_type_id', '#reimbursment_status', '#fromDate', '#toDate'
        ];

        $('.brand-init').DataTable().destroy();
        // Initialize DataTable
        var dataTable = new CustomDataTable({
            tableElem: '.brand-init',
            option: {
                processing: true,
                serverSide: true,
                ajax: {
                    type: "GET",
                    url: root_url + "/ambulance/view-expense-details/" + ambulance_id,
                },
                columns: [{
                        "class": "nk-tb-col tb-col-lg",
                        data: 'created_by_name',
                        name: 'created_by_name',
                        orderable: true
                    },
                    {
                        "class": "nk-tb-col tb-col-lg",
                        data: 'vechicle_no',
                        name: 'vechicle_no',
                        orderable: true
                    },
                    {
                        "class": "nk-tb-col tb-col-lg",
                        data: 'entry_type',
                        name: 'entry_type',
                        orderable: true
                    },
                    {
                        "class": "nk-tb-col tb-col-lg",
                        data: 'expense_type_name',
                        name: 'expense_type_name',
                        orderable: true
                    },
                    {
                        "class": "nk-tb-col tb-col-lg",
                        data: 'amount',
                        name: 'amount',
                        orderable: true
                    },
                    {
                        "class": "nk-tb-col tb-col-lg",
                        data: 'expense_date',
                        name: 'expense_date',
                        orderable: true
                    },
                    {
                        "class": "nk-tb-col tb-col-lg",
                        data: 'claim_status',
                        name: 'claim_status',
                        orderable: true
                    },
                    {
                        "class": "nk-tb-col tb-col-lg",
                        data: 'reimbursement_status',
                        name: 'reimbursement_status',
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
            },
            filterSubmit: '.submitBtnFilter',
            filterSubmitCallback: function() {
                $('#filterExpense').modal('toggle');
            },
            filterClearSubmit: '.resetFilter',
            filterModalId: '#filterExpense',
            filterItems: items,
            tagId: '#filter_tag_list',
        });
    }

    function getInventoryDetails() {
        // Destroy existing DataTable instance
        $('.inventoryDetails').DataTable().destroy();

        // Get values
        var ambulance_id = $('#ambulance_id').val();

        // Initialize DataTable
        var inventoryDataTable = new CustomDataTable({
            tableElem: '.inventoryDetails',
            option: {
                processing: true,
                serverSide: true,
                ajax: {
                    type: "GET",
                    url: root_url + "/ambulance/view-inventory-details/" + ambulance_id,
                },
                columns: [{
                        "class": "nk-tb-col tb-col-lg",
                        data: 'name',
                        name: 'name',
                        orderable: true
                    },
                    {
                        "class": "nk-tb-col tb-col-lg",
                        data: 'unit_of_measurement',
                        name: 'unit_of_measurement',
                        orderable: true
                    },
                    {
                        "class": "nk-tb-col tb-col-lg",
                        data: 'capacity',
                        name: 'capacity',
                        orderable: true
                    },
                    {
                        "class": "nk-tb-col tb-col-lg",
                        data: 'quantity',
                        name: 'quantity',
                        orderable: true
                    },
                    {
                        "class": "nk-tb-col tb-col-lg",
                        data: 'formatted_updated_at',
                        name: 'formatted_updated_at',
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
            }
        });
    }

    function updateInventoryItemInfo(inventoryId) {
        $.ajax({
            url: "{{url('ambulance/get-inventory-item-info')}}",
            type: 'GET',
            dataType: 'json',
            data: {
                inventoryId: inventoryId
            },
            success: function(response) {
                if (response.status == 'success') {
                    $('#inventory_item_name').text(response.inventoryData.name);
                    $('#view_inventory_unit_of_measurement').text(response.inventoryData.unit_of_measurement);
                    $('#view_inventory_capacity').text(response.inventoryData.capacity);
                    $('#view_inventory_quantity').text(response.inventoryData.quantity);
                    $('#view_last_updated_inventory').text(response.inventoryData.formatted_updated_at);
                    $('#update_inventory_quantity').attr('max', response.inventoryData.capacity);
                    $('#update_inventory_quantity').attr('min', 0);
                } else {
                    toastr.error(response.message);
                }
            }
        });
    }

    function backToList() {
        $('#update_inventory').addClass('d-none');
        $('#inventory_list').removeClass('d-none');
        $('#update_inventory_history').addClass('d-none');
        $('#addItem').removeClass('d-none');
    }

    function backToStaffList() {
        $('#update_staff_shift').addClass('d-none');
        $('#staff_view').removeClass('d-none');
    }

    $('#update_staff_shift_btn').on('click', function() {
        $('#update_staff_shift').removeClass('d-none');
        $('#staff_view').addClass('d-none');
        // getUsersList();
    });

    if ($("#updateQuantityForm").length > 0) {
        $("#updateQuantityForm").validate({
            rules: {
                update_inventory_quantity: {
                    required: true,
                },
                date_of_change: {
                    required: true,
                },
            },
            messages: {
                update_inventory_quantity: {
                    required: "Please enter quantity.",
                    max: "You can't exceed quantity more than its capacity."
                },
                date_of_change: {
                    required: "Please select date of change.",
                },
            },
            submitHandler: function(form, event) {
                event.preventDefault();
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                var inventory_id = $('#inventory_id').val();

                // disabled submit button during process running status
                $(".updateInventory").attr("disabled", true);
                var formData = new FormData($('#updateQuantityForm')[0]);
                formData.append('inventory_id', inventory_id);
                $.ajax({
                    type: "POST",
                    url: "{{ url('ambulance/update-inventory') }}",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        $(".updateInventory").attr("disabled", false);
                        if (response.status == 'success') {
                            backToList();
                            getInventoryDetails();
                            $('#updateInventoryModal').modal('hide');
                            toastr.success(response.message);
                        } else {
                            toastr.error(response.message);
                        }
                    }
                });
                $(".updateInventory").attr("disabled", false);
            }
        });
    }

    if ($("#editAmbulanceDetailsForm").length > 0) {
        $("#editAmbulanceDetailsForm").validate({
            rules: {
                registration_number_availability: {
                    required: true,
                },
                make: {
                    required: true,
                },
                chassis_number: {
                    required: true,
                },
                district_id: {
                    required: true,
                },
                status: {
                    required: true,
                },
                inaugration_date: {
                    required: true,
                },
                number_plate_availability: {
                    required: true,
                },
                // number_plate_doc: {
                //     required: true,
                // },
                registration_certificate_availability: {
                    required: true,
                },
                purchase_paper_availability: {
                    required: true,
                },
                fastag_availability: {
                    required: true,
                },
                insurance_availability: {
                    required: true,
                },
                puc_availability: {
                    required: true,
                },
                fitness_certificate_availability: {
                    required: true,
                }
            },
            messages: {
                registration_number_availability: {
                    required: "Please select registration number availability.",
                },
                ambulance_number: {
                    required: "Please enter ambulance number.",
                },
                make: {
                    required: "Please select manufacturing company of ambulance.",
                },
                chassis_number: {
                    required: "Please enter ambulance chassis number.",
                },
                district_id: {
                    required: "Please select district.",
                },
                status: {
                    required: "Please select ambulance status.",
                },
                inaugration_date: {
                    required: "Please select ambulance inaugration date.",
                },
                number_plate_availability: {
                    required: "Please select number plate availability.",
                },
                number_plate_doc: {
                    required: "Please upload number plate photo.",
                },
                registration_certificate_availability: {
                    required: "Please select registration certificate availability status.",
                },
                purchase_paper_availability: {
                    required: "Please select purchase paper availability status.",
                },
                fastag_availability: {
                    required: "Please select FASTag availability status.",
                },
                insurance_availability: {
                    required: "Please select insurance availability status.",
                },
                puc_availability: {
                    required: "Please select PUC availability status.",
                },
                fitness_certificate_availability: {
                    required: "Please select fitness certificate availability status.",
                }
            },
            submitHandler: function(form, event) {
                event.preventDefault();
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                // disabled submit button during process running status
                $(".updateAmbulanceDetails").attr("disabled", true);
                var formData = new FormData($('#editAmbulanceDetailsForm')[0]);
                formData.append('ambulance_id', $('#ambulance_id').val());

                $.ajax({
                    type: "POST",
                    url: "{{ url('ambulance/update-ambulance-info') }}",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        $(".updateAmbulanceDetails").attr("disabled", false);
                        if (response.status == 'success') {
                            window.location.reload();
                            toastr.success(response.message);
                        } else {
                            toastr.error(response.message);
                        }
                    }
                });

                $(".updateAmbulanceDetails").attr("disabled", false);
            }
        });
    }

    function updateAmbulanceDetails() {
        $('.editable').removeClass('d-none');
        $('.not-editable').addClass('d-none');
        $('#updateBtn').addClass('d-none');
        $('.updateAmbulanceDetails').removeClass('d-none');
    }

    function updateFileName(input) {
        var fileName = input.files[0].name;
        var label = input.parentNode.querySelector('.custom-file-label');
        label.textContent = fileName;
    }

    $(document).ready(function() {
        // Function to toggle the visibility of the updateBtn based on the tab's active state
        function toggleUpdateBtnVisibility() {
            var isActive = $('#ambulance_details').hasClass('active');
            $('#updateBtn').toggle(isActive);
        }

        // Initial visibility check on document ready
        toggleUpdateBtnVisibility();

        // Listen for Bootstrap tab show event
        $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
            toggleUpdateBtnVisibility();
        });
    });

    var todayDate = new Date();

    $('#shift_start_date').datepicker({
        todayBtn: true,
        orientation: "auto",
        todayHighlight: true,
        autoclose: true,
        format: "dd/mm/yyyy",
        startView: "days",
        //endDate: "today"
    }).datepicker('update', todayDate);
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
    }).datepicker('update', todayDate);
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

    $("#shift_date").datepicker({
        todayBtn: true,
        orientation: "auto",
        todayHighlight: true,
        autoclose: true,
        format: "dd/mm/yyyy",
        startView: "days",
        startDate: "today"
        // endDate: "today"
    }).datepicker('update', todayDate);
    $('#shift_date').each(function() {
        $(this).on('changeDate', function(e) {
            shift_date = $(this).datepicker('getDate');
            $('#shift_date').datepicker('update', shift_date);
        });
    });

    $(document).ready(function() {

        $('#shift_date').closest('.form-row').hide();
        $('#shift_start_date, #shift_end_date').closest('.form-row').show();
        // Add a change event listener to the radio buttons
        $('input[name="shiftType"]').change(function() {
            if ($(this).val() === 'Temporary') {
                // If temporary is selected, show shift date and hide start/end date
                $('#shift_date').closest('.form-row').show();
                $('#shift_start_date, #shift_end_date').closest('.form-row').hide();
            } else {
                // If permanent is selected, show start/end date and hide shift date
                $('#shift_date').closest('.form-row').hide();
                $('#shift_start_date, #shift_end_date').closest('.form-row').show();
            }
        });
    });

    function getUsersList() {
        var userType = $('input[name="userType"]:checked').val();
        var shiftType = $('input[name="shiftType"]:checked').val();
        var shift_id = $('input[name="shift_id"]:checked').val();
        var shiftStartDate = $('#shift_start_date').val();
        var shiftEndDate = $('#shift_end_date').val();
        var shiftDate = $('#shift_date').val();

        $.ajax({
            url: "{{ url('ambulance/get-users-list') }}",
            type: 'GET',
            data: {
                userType: userType,
                shiftType: shiftType,
                shift_id: shift_id,
                shift_start_date: shiftStartDate,
                shift_end_date: shiftEndDate,
                shift_date: shiftDate,
            },
            success: function(response) {
                $('#assigned_user').empty();

                // Add a default option
                $('#assigned_user').append('<option selected disabled>Select User</option>');

                // Loop through the usersList array and append options
                $.each(response.usersList, function(index, user) {
                    $('#assigned_user').append('<option value="' + user.id + '">' + user.user_name + '</option>');
                });

            }
        });
    }

    $("#assigned_user").change(function() {
        $("#shiftAssignmentForm").validate().element("#assigned_user");
    });

    $("#date_of_change").change(function() {
        $("#updateQuantityForm").validate().element("#date_of_change");
    });


    if ($("#shiftAssignmentForm").length > 0) {
        $("#shiftAssignmentForm").validate({
            rules: {
                service_area_id: {
                    required: true,
                },
                station_area: {
                    required: true,
                },
                assigned_user: {
                    required: true,
                },
            },
            messages: {
                service_area_id: {
                    required: "Please enter service area.",
                },
                station_area: {
                    required: "Please enter station area.",
                },
                assigned_user: {
                    required: "Please select assign user.",
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
                $(".submitShiftBtn").attr("disabled", true);
                var formData = new FormData($('#shiftAssignmentForm')[0]);
                formData.append('ambulance_id', $('#ambulance_id').val());
                $.ajax({
                    type: "POST",
                    url: "{{ url('ambulance/assign-shift') }}",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        $(".submitShiftBtn").attr("disabled", false);
                        if (response.status == 'success') {
                            backToStaffList();
                            getAmbulanceStaffByMonth();
                            toastr.success(response.message);
                        } else {
                            toastr.error(response.message);
                        }
                    }
                });
                $(".submitShiftBtn").attr("disabled", false);
            }
        });
    }

    function getAmbulanceStaffByMonth() {
        $('.overlay').show();
        var month = $('#month_id').val();
        var year = $('#year_id').val();
        var ambulance = $('#ambulance_id').val();
        $.ajax({
            url: "{{ url('ambulance/get-assign-staff-list') }}",
            type: 'GET',
            data: {
                month_id: month,
                year_id: year,
                ambulance_id: ambulance,
            },
            success: function(response) {
                $.ajax({
                    url: "{{url('ambulance/get-assign-staff-for-events')}}",
                    type: 'GET',
                    data: {
                        month_id: month,
                        year_id: year,
                        ambulance_id: ambulance,
                    },
                    success: function(responseData) {
                        initializeCalendar(responseData, month, year);
                    }
                });
                $('#staff_card_view').empty();
                if (response.staffList.length === 0) {
                    $("#staff_card_view").html('<div class="col-md-12" style="display: flex;justify-content: center">' +
                        '<img width="300" src="https://parivaar.siplsolutions.com/images/empty_item.svg">' +
                        '</div>' +
                        '<h5 class="col-md-12" style="display: flex;justify-content: center;">No records found.</h5>');
                } else {
                    $('#viewSelectedCalendarMonth').text(response.date);
                    $.each(response.staffList, function(index, user) {
                        $('#staff_card_view').append(`
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="form-row">
                                        <div class="form-group">
                                            <span class="data-value data-value-text">
                                                <img src="https://cdn4.iconfinder.com/data/icons/small-n-flat/24/user-alt-512.png" width="60" class="rounded-circle z-depth-2" />
                                            </span>
                                        </div>
                                        <span style="margin-left: 20px">
                                            <span class="user-name-text" style="font-size: 15px; font-weight: bold">Name: ${user.user_name}</span><br>
                                            <span class="employee-id" style="font-size: 15px; font-weight: bold">Employee ID: ${user.employee_id}</span><br>
                                            <span class="user-role-text" style="font-size: 15px; font-weight: bold">Role: ${user.user_type}</span>
                                        </span>
                                    </div>
                                    <div class="form-row mt-2">
                                        <div class="form-group col-md-6">
                                            <label class="font-weight-bold">Type</label>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <span>${user.type}</span>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label class="font-weight-bold">Added By</label>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <span>${user.created_by_user_name}</span>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label class="font-weight-bold">Shift</label>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <span>${user.shift_name}</span>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label class="font-weight-bold">Total Days</label>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <span>${user.day_count}</span>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label class="font-weight-bold">Total Shifts</label>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <span>${user.shift_count}</span>
                                        </div>
                                    </div>
                                     <div class="form-row">
                                     <a href="#" style="text-decoration: none; cursor: pointer;" data-toggle="modal" data-target="#viewCalendar" class="toggle" data-user-id="${user.user_id}" data-shift-id="${user.shift_type_id}">View Dates & Shifts</a>
                                     </div>
                                </div>
                            </div>
                        </div>
                    `);
                    });
                }
                $('.overlay').hide();
            }
        });

    }

    $('#viewCalendar').on('shown.bs.modal', function(event) {
        $('.overlay').show();
        var monthId = $('#month_id').val();
        var yearId = $('#year_id').val();
        var ambulanceId = $('#ambulance_id').val();
        var button = $(event.relatedTarget); // Button that triggered the modal
        var userId = button.data('user-id');
        var shiftId = button.data('shift-id');
        $.ajax({
            url: "{{url('ambulance/get-assign-staff-for-events')}}",
            type: 'GET',
            data: {
                month_id: monthId,
                year_id: yearId,
                ambulance_id: ambulanceId,
                user_id: userId,
                shift_type_id: shiftId
            },
            success: function(response) {
                $('.overlay').hide();
                initializeCalendarInModal(response, monthId, yearId);
            }
        });
    });
    $("#calendar_view").click(function() {
        getAmbulanceStaffByMonth();
    });
    $("#list_view").click(function() {
        getAmbulanceStaffByMonth();
    });

    function getAmbulanceListStaffByMonth() {
        $('.overlay').show();
        var month = $('#month_id').val();
        var year = $('#year_id').val();
        var ambulance = $('#ambulance_id').val();
        $.ajax({
            url: "{{ url('ambulance/get-assign-staff-list') }}",
            type: 'GET',
            data: {
                month_id: month,
                year_id: year,
                ambulance_id: ambulance,
            },
            success: function(response) {
                $('#staff_card_view').empty();
                if (response.staffList.length === 0) {
                    $("#staff_card_view").html('<div class="col-md-12" style="display: flex;justify-content: center">' +
                        '<img width="300" src="https://parivaar.siplsolutions.com/images/empty_item.svg">' +
                        '</div>' +
                        '<h5 class="col-md-12" style="display: flex;justify-content: center;">No records found.</h5>');
                } else {
                    $('#viewSelectedCalendarMonth').text(response.date);
                    $.each(response.staffList, function(index, user) {
                        $('#staff_card_view').append(`
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="form-row">
                                        <div class="form-group">
                                            <span class="data-value data-value-text">
                                                <img src="https://cdn4.iconfinder.com/data/icons/small-n-flat/24/user-alt-512.png" width="60" class="rounded-circle z-depth-2" />
                                            </span>
                                        </div>
                                        <span style="margin-left: 20px">
                                            <span class="user-name-text" style="font-size: 15px; font-weight: bold">Name: ${user.user_name}</span><br>
                                            <span class="employee-id" style="font-size: 15px; font-weight: bold">Employee ID: ${user.employee_id}</span><br>
                                            <span class="user-role-text" style="font-size: 15px; font-weight: bold">Role: ${user.user_type}</span>
                                        </span>
                                    </div>
                                    <div class="form-row mt-2">
                                        <div class="form-group col-md-6">
                                            <label class="font-weight-bold">Type</label>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <span>${user.type}</span>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label class="font-weight-bold">Added By</label>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <span>${user.created_by_user_name}</span>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label class="font-weight-bold">Shift</label>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <span>${user.shift_name}</span>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label class="font-weight-bold">Total Days</label>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <span>${user.day_count}</span>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label class="font-weight-bold">Total Shifts</label>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <span>${user.shift_count}</span>
                                        </div>
                                    </div>
                                     <div class="form-row">
                                     <a href="#" style="text-decoration: none; cursor: pointer;" data-toggle="modal" data-target="#viewCalendar" class="toggle" data-user-id="${user.user_id}" data-shift-id="${user.shift_type_id}">View Dates & Shifts</a>
                                     </div>
                                </div>
                            </div>
                        </div>
                    `);
                    });
                }

            }
        });
        $('.overlay').hide();
    }
</script>
@endpush