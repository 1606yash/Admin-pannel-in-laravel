@extends('layouts.app')
<meta name="csrf-token" content="{{ csrf_token() }}" />
@section('content')
<a class="btn" style="font-size: 18px;margin-bottom: 10px;" class="btn" href="javascript:history.back()"><em class='icon ni ni-arrow-left'></em>Back</a>
<div class="container">
    <form role="form" method="post" id="add_ambulance_form" enctype="multipart/form-data">
        @csrf

        <!-- vehicle identity section starts -->
        <div class="card">
            <div class="card-header">
                <h5>Vehicle Identity</h5>
            </div>
            <div class="card-body">
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="registration_number_availability" class="mr-2">Registration Number Availabilty<span class="text-danger">*</span></label>
                    </div>
                    <div class="form-group form-control-required-wrap col-md-4">
                        <select name="registration_number_availability" id="registration_number_availability" class="form-control form-select" placeholder='Select' data-search="on" data-parsley-excluded="true">
                            <option selected disabled>Select</option>
                            <option value="Yes">Yes</option>
                            <option value="Not Available">Not Available</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <!-- vehicle identity section ends -->

        <!-- vehicle information section starts -->
        <div class="card mt-3">
            <div class="card-header">
                <h5>Vehicle Information</h5>
            </div>
            <div class="card-body">
                <div class="form-row">
                    <div class="form-group col-md-4 form-control-required-wrap">
                        <label for="make" class="mr-2">Make<span class="text-danger">*</span></label>
                        <select name="make" id="make" class="form-control form-select" placeholder='Select' data-search="on" data-parsley-excluded="true">
                            <option selected disabled>Select</option>
                            <option value="Mahindra">Mahindra</option>
                            <option value="TATA">TATA</option>
                            <option value="Force">Force</option>
                            <option value="Maruti">Maruti</option>
                            <option value="SML">SML</option>
                        </select>
                    </div>

                    <div class="form-group col-md-4">
                        <label for="inaugration_date">Inaugration Date <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="inaugration_date" name="inaugration_date" readonly placeholder="Select Inaugration Date" data-parsley-excluded="true">
                    </div>

                    <div class="form-group col-md-4 form-control-required-wrap">
                        <label for="status" class="mr-2">Status<span class="text-danger">*</span></label>
                        <select name="status" id="status" class="form-control form-select" placeholder='Select Status' data-search="on" data-parsley-excluded="true">
                            <option selected disabled>Select Ambulance Status</option>
                            <option value="Running">Running</option>
                            <option value="Not Running">Not Running</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-4 form-control-required-wrap">
                        <label for="number_plate_availability" class="mr-2">Number Plate Availabilty<span class="text-danger">*</span></label>
                        <select name="number_plate_availability" id="number_plate_availability" class="form-control form-select" placeholder='Select' data-search="on" data-parsley-excluded="true">
                            <option selected disabled>Select</option>
                            <option value="Yes">Yes</option>
                            <option value="Not Available">Not Available</option>
                        </select>
                    </div>

                    <div class="form-group col-md-4">
                        <label for="number_plate_doc">Upload Number Plate <span class="text-danger">*</span></label>
                        <div class="custom-file mb-3">
                            <input type="file" class="custom-file-input" id="number_plate_doc" name="number_plate_doc" onchange="updateFileName(this)" data-parsley-excluded="true" accept=".jpg, .jpeg, .png, .gif, .pdf">
                            <label class="custom-file-label" for="attach_file">Tap to attach a file</label>
                        </div>
                    </div>

                    <div class="form-group col-md-4">
                        <label for="chassis_number" class="mr-2">Chassis Number<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="chassis_number" name="chassis_number" placeholder="Enter Chassis Number" data-parsley-excluded="true">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-4 form-control-required-wrap">
                        <label for="district_id" class="mr-2">District<span class="text-danger">*</span></label>
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
                    <div class="form-group col-md-4" id="ambulance_no_block">
                        <label for="ambulance_number" class="mr-2">Ambulance Number<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="ambulance_number" name="ambulance_number" placeholder="Enter Ambulance Number" data-parsley-excluded="true">
                    </div>

                </div>


            </div>
        </div>

        <!-- vehicle information section ends -->

        <!-- fastag information starts -->
        <div class="card mt-3">
            <div class="card-header">
                <h5>Invoice and Registration Information</h5>
            </div>
            <div class="card-body">
                <div class="form-row">
                    <div class="form-group col-md-4 form-control-required-wrap">
                        <label for="registration_certificate" class="mr-2">Registration Certificate Availabilty <span class="text-danger">*</span></label>
                        <select name="registration_certificate_availability" id="registration_certificate_availability" class="form-control form-select" placeholder='Select' data-search="on" data-parsley-excluded="true">
                            <option selected disabled>Select</option>
                            <option value="Yes">Yes</option>
                            <option value="Not Available">Not Available</option>
                        </select>
                    </div>

                    <div class="form-group col-md-4 form-control-required-wrap">
                        <label for="purchase_paper" class="mr-2">Purchase Papers Availabilty <span class="text-danger">*</span></label>
                        <select name="purchase_paper_availability" id="purchase_paper_availability" class="form-control form-select" placeholder='Select' data-search="on" data-parsley-excluded="true">
                            <option selected disabled>Select</option>
                            <option value="Yes">Yes</option>
                            <option value="Not Available">Not Available</option>
                        </select>
                    </div>

                    <div class="form-group col-md-4 form-control-required-wrap">
                        <label for="fastag_availability" class="mr-2">FASTag Availabilty <span class="text-danger">*</span></label>
                        <select name="fastag_availability" id="fastag_availability" class="form-control form-select" placeholder='Select' data-search="on" data-parsley-excluded="true">
                            <option selected disabled>Select</option>
                            <option value="Yes">Yes</option>
                            <option value="Not Available">Not Available</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="registration_certificate_doc">Upload Registration Certificate </label>
                        <div class="custom-file mb-3">
                            <input type="file" class="custom-file-input" id="registration_certificate_doc" name="registration_certificate_doc" onchange="updateFileName(this)" data-parsley-excluded="true" accept=".jpg, .jpeg, .png, .gif, .pdf">
                            <label class="custom-file-label" for="attach_file">Tap to attach a file</label>
                        </div>
                    </div>

                    <div class="form-group col-md-4">
                        <label for="purchase_paper_doc">Upload Purchase Papers</label>
                        <div class="custom-file mb-3">
                            <input type="file" class="custom-file-input" id="purchase_paper_doc" name="purchase_paper_doc" onchange="updateFileName(this)" data-parsley-excluded="true" accept=".jpg, .jpeg, .png, .gif, .pdf">
                            <label class="custom-file-label" for="attach_file">Tap to attach a file</label>
                        </div>
                    </div>

                    <div class="form-group col-md-4">
                        <label for="fastag_doc">Upload FASTag</label>
                        <div class="custom-file mb-3">
                            <input type="file" class="custom-file-input" id="fastag_doc" name="fastag_doc" onchange="updateFileName(this)" data-parsley-excluded="true" accept=".jpg, .jpeg, .png, .gif, .pdf">
                            <label class="custom-file-label" for="attach_file">Tap to attach a file</label>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="sponsor_name" class="mr-2">Sponsor Name</label>
                        <input type="text" class="form-control" id="sponsor_name" name="sponsor_name" placeholder="Enter Sponsor Name" data-parsley-excluded="true">
                    </div>

                    <div class="form-group col-md-4">
                        <label for="invoice_number" class="mr-2">Invoice Number</label>
                        <input type="text" class="form-control" id="invoice_number" name="invoice_number" placeholder="Enter Invoice Number" data-parsley-excluded="true">
                    </div>

                    <div class="form-group col-md-4">
                        <label for="invoice_date">Invoice Date</label>
                        <input type="text" class="form-control" id="invoice_date" name="invoice_date" readonly placeholder="Select Invoice Date" data-parsley-excluded="true">
                    </div>
                </div>

                <div class="form-row mt-2">
                    <div class="form-group col-md-4">
                        <label for="date_of_delivery">Date Of Delivery</label>
                        <input type="text" class="form-control" id="date_of_delivery" name="date_of_delivery" readonly placeholder="Select Date Of Delivery" data-parsley-excluded="true">
                    </div>

                    <div class="form-group col-md-4">
                        <label for="registration_date">Registration Date</label>
                        <input type="text" class="form-control" id="registration_date" name="registration_date" readonly placeholder="Select Registrtaion Date" data-parsley-excluded="true">
                    </div>

                    <div class="form-group col-md-4">
                        <label for="entry_date">Entry Date</label>
                        <input type="text" class="form-control" id="entry_date" name="entry_date" readonly placeholder="Select Entry Date" data-parsley-excluded="true">
                    </div>
                </div>
            </div>
        </div>
        <!-- fastag information ends -->

        <!-- Insurance information starts -->
        <div class="card mt-3">
            <div class="card-header">
                <h5>Insurance Details</h5>
            </div>
            <div class="card-body">
                <div class="form-row">
                    <div class="form-group col-md-4 form-control-required-wrap">
                        <label for="insurance_availability" class="mr-2">Insurance Availabilty <span class="text-danger">*</span></label>
                        <select name="insurance_availability" id="insurance_availability" class="form-control form-select" placeholder='Select' data-search="on" data-parsley-excluded="true">
                            <option selected disabled>Select</option>
                            <option value="Yes">Yes</option>
                            <option value="Not Available">Not Available</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="insurance_doc">Upload Insurance</label>
                        <div class="custom-file mb-3">
                            <input type="file" class="custom-file-input" id="insurance_doc" name="insurance_doc" onchange="updateFileName(this)" data-parsley-excluded="true" accept=".jpg, .jpeg, .png, .gif, .pdf">
                            <label class="custom-file-label" for="attach_file">Tap to attach a file</label>
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="policy_company" class="mr-2">Policy Company</label>
                        <input type="text" class="form-control" id="policy_company" name="policy_company" placeholder="Enter Policy Company Name" data-parsley-excluded="true">
                    </div>
                </div>
                <div class="form-row mt-2">
                    <div class="form-group col-md-4">
                        <label for="policy_number" class="mr-2">Policy Number</label>
                        <input type="text" class="form-control" id="policy_number" name="policy_number" placeholder="Enter Policy Number" data-parsley-excluded="true">
                    </div>

                    <div class="form-group col-md-4">
                        <label for="current_insurance_start_date">Current Insurance Start Date</label>
                        <input type="text" class="form-control" id="current_insurance_start_date" name="current_insurance_start_date" readonly placeholder="Select Current Insurance Start Date" data-parsley-excluded="true">
                    </div>

                    <div class="form-group col-md-4">
                        <label for="insurance_valid_date">Insurance Valid Date</label>
                        <input type="text" class="form-control" id="insurance_valid_date" name="insurance_valid_date" readonly placeholder="Select Insurance Valid Date" data-parsley-excluded="true">
                    </div>
                </div>
            </div>
        </div>
        <!-- Insurance information ends -->

        <!-- PUC information starts -->
        <div class="card mt-3">
            <div class="card-header">
                <h5>PUC Details</h5>
            </div>
            <div class="card-body">
                <div class="form-row">
                    <div class="form-group col-md-4 form-control-required-wrap">
                        <label for="puc_availability" class="mr-2">PUC Availabilty <span class="text-danger">*</span></label>
                        <select name="puc_availability" id="puc_availability" class="form-control form-select" placeholder='Select' data-search="on" data-parsley-excluded="true">
                            <option selected disabled>Select</option>
                            <option value="Yes">Yes</option>
                            <option value="Not Available">Not Available</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="puc_certificate_doc">Upload PUC Certificate</label>
                        <div class="custom-file mb-3">
                            <input type="file" class="custom-file-input" id="puc_certificate_doc" name="puc_certificate_doc" onchange="updateFileName(this)" data-parsley-excluded="true" accept=".jpg, .jpeg, .png, .gif, .pdf">
                            <label class="custom-file-label" for="attach_file">Tap to attach a file</label>
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="puc_certificate_valid_date">PUC Certificate Validity</label>
                        <input type="text" class="form-control" id="puc_certificate_valid_date" name="puc_certificate_valid_date" readonly placeholder="Select PUC Certificate Validity" data-parsley-excluded="true">
                    </div>
                </div>
            </div>
        </div>
        <!-- PUC information ends -->

        <!-- Fitness information starts -->
        <div class="card mt-3">
            <div class="card-header">
                <h5>Fitness Details</h5>
            </div>
            <div class="card-body">
                <div class="form-row">
                    <div class="form-group col-md-4 form-control-required-wrap">
                        <label for="fitness_certificate_availability" class="mr-2">Fitness Certificate Availabilty <span class="text-danger">*</span></label>
                        <select name="fitness_certificate_availability" id="fitness_certificate_availability" class="form-control form-select" placeholder='Select' data-search="on" data-parsley-excluded="true">
                            <option selected disabled>Select</option>
                            <option value="Yes">Yes</option>
                            <option value="Not Available">Not Available</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="fitness_certificate_doc">Upload Fitness Certificate</label>
                        <div class="custom-file mb-3">
                            <input type="file" class="custom-file-input" id="fitness_certificate_doc" name="fitness_certificate_doc" onchange="updateFileName(this)" data-parsley-excluded="true" accept=".jpg, .jpeg, .png, .gif, .pdf">
                            <label class="custom-file-label" for="attach_file">Tap to attach a file</label>
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="fitness_certificate_valid_date">Fitness Certificate Validity</label>
                        <input type="text" class="form-control" id="fitness_certificate_valid_date" name="fitness_certificate_valid_date" readonly placeholder="Select Fitness Certificate Validity" data-parsley-excluded="true">
                    </div>
                </div>
            </div>
        </div>
        <!-- Fitness information ends -->

        <!-- Supplier and Payment information starts -->
        <div class="card mt-3">
            <div class="card-header">
                <h5>Supplier and Payment Details</h5>
            </div>
            <div class="card-body">
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="supplier_name" class="mr-2">Supplier Name</label>
                        <input type="text" class="form-control" id="supplier_name" name="supplier_name" placeholder="Enter Supplier Name" data-parsley-excluded="true">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="payment_bank_id" class="mr-2">Payment Bank</label>
                        <select name="payment_bank_id" id="payment_bank_id" class="form-control form-select" placeholder='Select Bank' data-search="on" data-parsley-excluded="true">
                            <option selected disabled>Select Bank</option>
                            @if(!empty($banks))
                            @foreach ($banks as $key => $bank)
                            <option value="{{ $bank->id }}">
                                {{ $bank->bank_name }}
                            </option>
                            @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="payment_date">Payment Date</label>
                        <input type="text" class="form-control" id="payment_date" name="payment_date" readonly placeholder="Select Payment Date" data-parsley-excluded="true">
                    </div>
                </div>
            </div>
        </div>
        <!-- Supplier and Payment information ends -->

        <!-- additional Notes starts -->
        <div class="card mt-3">
            <div class="card-header">
                <h5>Notes</h5>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label for="additional_notes">Additional Notes</label>
                    <textarea class="form-control" id="additional_notes" name="additional_notes" rows="4" placeholder="Enter Additional Notes Here ..." data-parsley-excluded="true"></textarea>
                </div>
            </div>
        </div>
        <!-- additional Notes ends -->
        <div class="mt-3">
            <div class="form-group text-right my-2">
                <a class="btn btn- btn-outline-ligh cancel" href="javascript:history.back()">Cancel</a>
                <a class="btn btn-danger resetFilter cancel" href="">Reset</a>
                <button class="btn btn-primary submitBtn" name="submit" type="submit"><span>Submit</span></button>
            </div>
        </div>
    </form>
</div>
@endsection
@push('footerScripts')
<script src="{{url('js/additional-methods.js')}}"></script>
<script>
    $(document).ready(function() {
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

        $("#registration_number_availability").change(function() {
            $("#add_ambulance_form").validate().element("#registration_number_availability");
        });

        $("#make").change(function() {
            $("#add_ambulance_form").validate().element("#make");
        });

        $("#district_id").change(function() {
            $("#add_ambulance_form").validate().element("#district_id");
        });

        $("#status").change(function() {
            $("#add_ambulance_form").validate().element("#status");
        });

        $("#number_plate_availability").change(function() {
            $("#add_ambulance_form").validate().element("#number_plate_availability");
        });

        $("#inaugration_date").change(function() {
            $("#add_ambulance_form").validate().element("#inaugration_date");
        });

        $("#insurance_availability").change(function() {
            $("#add_ambulance_form").validate().element("#insurance_availability");
        });

        $("#puc_availability").change(function() {
            $("#add_ambulance_form").validate().element("#puc_availability");
        });

        $("#registration_certificate_availability").change(function() {
            $("#add_ambulance_form").validate().element("#registration_certificate_availability");
        });

        $("#purchase_paper_availability").change(function() {
            $("#add_ambulance_form").validate().element("#purchase_paper_availability");
        });

        $("#fastag_availability").change(function() {
            $("#add_ambulance_form").validate().element("#fastag_availability");
        });

        $("#fitness_certificate_availability").change(function() {
            $("#add_ambulance_form").validate().element("#fitness_certificate_availability");
        });

        $('#ambulance_no_block').addClass('d-none');
        $('#registration_number_availability').on('change', function() {
            if ($('#registration_number_availability').val() == 'Yes') {
                $('#ambulance_no_block').removeClass('d-none');
                $('#ambulance_number').attr('required', true);
            } else {
                $('#ambulance_no_block').addClass('d-none');
                $('#ambulance_number').attr('required', false);
            }
        });
    });
    if ($("#add_ambulance_form").length > 0) {
        $("#add_ambulance_form").validate({
            rules: {
                registration_number_availability: {
                    required: true,
                },
                make: {
                    required: true,
                },
                chassis_number: {
                    required: true,
                    minlength: 17,
                    alphanumeric: true
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
                number_plate_doc: {
                    required: true,
                    accept: 'image/*,application/pdf',
                    maxsize: 40 * 1024 * 1024, // 40 MB maximum file size
                },
                registration_certificate_availability: {
                    required: true,
                },
                registration_certificate_doc: {
                    accept: 'image/*,application/pdf',
                    maxsize: 40 * 1024 * 1024, // 40 MB maximum file size
                },
                purchase_paper_doc: {
                    accept: 'image/*,application/pdf',
                    maxsize: 40 * 1024 * 1024, // 40 MB maximum file size
                },
                fastag_doc: {
                    accept: 'image/*,application/pdf',
                    maxsize: 40 * 1024 * 1024, // 40 MB maximum file size
                },
                insurance_doc: {
                    accept: 'image/*,application/pdf',
                    maxsize: 40 * 1024 * 1024, // 40 MB maximum file size
                },
                puc_certificate_doc: {
                    accept: 'image/*,application/pdf',
                    maxsize: 40 * 1024 * 1024, // 40 MB maximum file size
                },
                fitness_certificate_doc: {
                    accept: 'image/*,application/pdf',
                    maxsize: 40 * 1024 * 1024, // 40 MB maximum file size
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
                    alphanumeric: "Chassis number should contain only letters and numbers.",
                    minlength: "Chassis number should be 17 characters long.",
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
                    accept: "Please upload a valid format file.",
                    maxsize: "File size cannot exceed 40 MB.",
                },
                registration_certificate_availability: {
                    required: "Please select registration certificate availability status.",
                },
                registration_certificate_doc: {
                    accept: "Please upload a valid format file.",
                    maxsize: "File size cannot exceed 40 MB.", // 40 MB maximum file size
                },
                purchase_paper_doc: {
                    accept: "Please upload a valid format file.",
                    maxsize: "File size cannot exceed 40 MB.", // 40 MB maximum file size
                },
                fastag_doc: {
                    accept: "Please upload a valid format file.",
                    maxsize: "File size cannot exceed 40 MB.", // 40 MB maximum file size
                },
                insurance_doc: {
                    accept: "Please upload a valid format file.",
                    maxsize: "File size cannot exceed 40 MB.", // 40 MB maximum file size
                },
                puc_certificate_doc: {
                    accept: "Please upload a valid format file.",
                    maxsize: "File size cannot exceed 40 MB.", // 40 MB maximum file size
                },
                fitness_certificate_doc: {
                    accept: "Please upload a valid format file.",
                    maxsize: "File size cannot exceed 40 MB.", // 40 MB maximum file size
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
                $(".submitBtn").attr("disabled", true);

                $.ajax({
                    type: "POST",
                    url: "{{ url('ambulance/store-ambulance') }}",
                    data: new FormData($('#add_ambulance_form')[0]),
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        $(".submitBtn").attr("disabled", false);
                        if (response.status == 'success') {
                            window.location.href = "{{ url('ambulance/') }}";
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


    function updateFileName(input) {
        var fileName = input.files[0].name;
        var label = input.parentNode.querySelector('.custom-file-label');
        label.textContent = fileName;
    }
</script>
@endpush