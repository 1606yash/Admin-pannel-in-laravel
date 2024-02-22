@extends('layouts.app')
<meta name="csrf-token" content="{{ csrf_token() }}" />

@section('content')
<style>
    .attendanceData tbody tr {
        background-color: white;
    }

    select {
        margin-bottom: 10px;
    }
</style>
<div class="nk-block-head nk-block-head-sm">
    <div class="nk-block-between">
        <div class="nk-block-head-content">
            <h3 class="nk-block-title page-title">Attendance</h3>
        </div><!-- .nk-block-head-content -->
        <div class="nk-block-head-content">
            <div class="toggle-wrap nk-block-tools-toggle">
                <a href="#" class="btn btn-icon btn-trigger toggle-expand mr-n1" data-target="more-options"><em class="icon ni ni-more-v"></em></a>
                <div class="toggle-expand-content" data-content="more-options">
                    <ul class="nk-block-tools g-3">
                        <div class="row g-3 align-center">
                            <a class="nav-link btn btn-primary d-none d-md-inline-flex export" href="#" style="position: relative;letter-spacing: 0.02em;display: inline-flex;align-items: center;color: #fff; float: right; background-color: #1849ba;border-color: #1849ba;"><em class="icon ni ni-file-download" style="color: #fff;"></em><span> Export</span></a>
                        </div>
                        <li>
                            <a href="#" class="btn btn-trigger btn-icon toggle" title="filter" data-target="fiterAttendance">
                                <div class="dot dot-primary"></div>
                                <em class="icon ni ni-filter-alt"></em>
                            </a>
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
        <table id="brand_init" class="attendanceData brand-init nowrap nk-tb-list is-separate" data-auto-responsive="false">
            <thead>
                <tr class="nk-tb-item nk-tb-head">
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Employee ID</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Date</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Employee Name</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Role</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">District</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Status</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Action</span></th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div><!-- .nk-tb-list -->

</div><!-- .nk-block -->

<div class="nk-add-product toggle-slide toggle-slide-right" tabindex="-1" data-content="fiterAttendance" data-toggle-screen="any" data-toggle-overlay="true" data-toggle-body="true" data-simplebar id="fiterAttendance">
    <div class="nk-block-head">
        <div class="nk-block-head-content">
            <h5 class="nk-block-title modalTitle">Attendance Filter</h5>
        </div>
    </div>
    <form action="#" role="form" class="mb-0" method="get" id="AttendanceFilterForm">
        @csrf
        <div class="nk-block">
            <div class="row g-3">
                <div class="col-mb-12">
                    <x-inputs.select size="md" name="district_id" for="district_id" data-search="on" placeholder='Select District'>
                        <option value="" selected disabled>Select District</option>
                        @foreach ($districts as $key => $district)
                        <option value="{{ $district->id }}">{{ $district->district_name }}</option>
                        @endforeach
                    </x-inputs.select>
                </div>

                <div class="col-mb-12">
                    <x-inputs.select size="md" name="role_id" for="role_id" data-search="on" placeholder='Select Role'>
                        <option value="" selected disabled>Select Role</option>
                        @php
                        $roles = Helpers::getAllRoles();
                        @endphp
                        @foreach ($roles as $role)
                        <option value="{{ $role->id }}">{{ $role->role_name }}</option>
                        @endforeach
                    </x-inputs.select>
                </div>
                <div class="col-mb-12">
                    <x-inputs.select size="md" name="user_id" for="user_id" data-search="on" placeholder='Select User'>
                        <option value="" selected disabled>Select User</option>
                        @php
                        $users = Helpers::getAllUsers();
                        @endphp
                        @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->first_name.' '.$user->last_name }}</option>
                        @endforeach
                    </x-inputs.select>
                </div>
                <div class="col-mb-12">
                    <x-inputs.select size="md" name="attendance_date_range" for="attendance_date_range" id="attendance_date_range" data-search="on" placeholder='Select Creation Date'>
                        <option value="" selected disabled>Select Creation Date</option>
                        <option value="LastThreeMonth">Last Three Months</option>
                        <option value="LastSixMonth">Last Six Months</option>
                        <option value="CurrentYear">Current Year</option>
                        <option value="LastYear">Last Year</option>
                        <option value="LastThreeYear">Last Three Year</option>
                        <option value="selectDate">Select Date Range</option>
                    </x-inputs.select>
                    </select>
                </div>

                <div class="d-none" id="dateRangeBlock">
                    <div class="row g-3">
                        <div class="col-mb-6">
                            <input type="text" placeholder="Select From date" class="form-control" name="fromDate" id="fromDate" readonly />
                        </div>
                        <div class="col-mb-6">
                            <input type="text" placeholder="Select To date" name="toDate" class="form-control" id="toDate" readonly />
                        </div>
                    </div>
                </div>

                <div class="col-mb-12">
                    <x-inputs.select size="md" name="attendance_filter_status" for="attendance_filter_status" data-search="on" placeholder='Select Status'>
                        <option value="" selected disabled>Select Status</option>
                        <option value="Absent">Absent</option>
                        <option value="Present">Present</option>
                        <option value="Leave">Leave</option>
                        <option value="WeeklyOff">Weekly Off</option>
                        <option value="Holiday">Holiday</option>
                    </x-inputs.select>
                </div>
            </div>

            <div class="col-12 text-center mt-5">
                <a class="btn btn-outline-light cancel" data-target='fiterAttendance'>Cancel</a>
                <a class="btn btn-danger resetFilter" data-target='fiterAttendance' style="color:#fff">Clear
                    Filter</a>
                <a class="btn btn-primary submitBtnFilter" style="color:#fff">Submit</a>
            </div>
        </div>
    </form>
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
                                    <span id="attendance_checkin_location"></span>
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
@endsection
@push('footerScripts')
<script src="{{ url('js/tableFlow.js') }}">
</script>
<script src="{{ url('js/moment.min.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function() {
        // var dt = $('.attendanceData').DataTable();

        // // Destroy the existing DataTable instance before reinitializing
        // if (dt) {
        //     dt.destroy();
        // }
        $('.attendanceData').DataTable().destroy();
        $('.roleStatus').css('display', 'none');
        //$('.resetFilter').click();
        loadAllAttendanceRecords();

        $('.brand-init').on('click', '.view-user-attendance', function(e) {

            // check user role hide role based fields 

            var role = $(this).data('role-name');
            // alert(role);
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
                    //date: date,
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
            $('#updateAttendanceForm')[0].reset(); // Reset the form
            $('#updateAttendanceForm input, #updateAttendanceForm select, #updateAttendanceForm textarea').prop('disabled', true); // Disable form fields
            $('#updateAttandanceBtn').show(); // Show the submit button
            $('#updateAttendanceInfo').hide(); // Hide any info elements
            $('#updateAttendanceForm input, #updateAttendanceForm select, #updateAttendanceForm textarea').removeClass('error'); // Remove error classes from form elements

            // Reset validation messages (if jQuery Validation plugin is used)
            if ($('#updateAttendanceForm').validate && typeof $('#updateAttendanceForm').validate === 'function') {
                var validator = $('#updateAttendanceForm').validate(); // Get the validator object
                validator.resetForm(); // Reset the form validation
            }
        });


        $("#fromDate").datepicker({
            todayBtn: true,
            orientation: "auto",
            todayHighlight: true,
            autoclose: true,
            format: "dd/mm/yyyy",
            startView: "days",
            endDate: "today"
        });

        $('#fromDate').each(function() {
            $(this).on('changeDate', function(e) {
                $('body').addClass('toggle-shown');
                $('.nk-add-product').addClass('content-active');
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
            format: "dd/mm/yyyy",
            startView: "days",
            minViewDate: 0,
            maxViewDate: 0,
            endDate: "today"
        });

        $('#toDate').each(function() {
            $(this).on('changeDate', function(e) {
                $('body').addClass('toggle-shown');
                $('.nk-add-product').addClass('content-active');
                fromDate = $(this).datepicker('getDate');
                $('#toDate').datepicker('update', fromDate);
                var fromDate = document.getElementById('fromDate').value;
                var toDate = document.getElementById('toDate').value;
                if (fromDate == null || fromDate == '') {
                    $('#fromDate').datepicker('update', toDate);
                }
            });
        });

        $('#attendance_date_range').on('change', function() {
            var currentDate = new Date();
            var day = currentDate.getDate();
            var month = currentDate.getMonth() + 1; // Months are zero-based
            var year = currentDate.getFullYear();

            // Format the current date to dd/mm/yyyy
            var formattedCurrentDate = (day < 10 ? '0' : '') + day + '/' + (month < 10 ? '0' : '') + month + '/' + year;

            $('#toDate').val(formattedCurrentDate);
            $('#dateRangeBlock').addClass('d-none');
            if ($('#attendance_date_range').val() == 'LastThreeMonth') {
                // Calculate the date three months ago
                currentDate.setMonth(currentDate.getMonth() - 3);
                $('#dateRangeBlock').addClass('d-none');
            } else if ($('#attendance_date_range').val() == 'LastSixMonth') {
                // Calculate the date six months ago
                currentDate.setMonth(currentDate.getMonth() - 6);
                $('#dateRangeBlock').addClass('d-none');
            } else if ($('#attendance_date_range').val() == 'CurrentYear') {
                // Set the date to the beginning of the current year
                currentDate.setMonth(0, 1);
                $('#dateRangeBlock').addClass('d-none');
            } else if ($('#attendance_date_range').val() == 'LastYear') {
                // Set the date to the beginning of the last year
                currentDate.setFullYear(currentDate.getFullYear() - 1, 0, 1);
                var lastYearEndDate = new Date(currentDate.getFullYear(), 11, 31);
                $('#toDate').val(formatDate(lastYearEndDate));
                $('#dateRangeBlock').addClass('d-none');
            } else if ($('#attendance_date_range').val() == 'LastThreeYear') {
                // Set the date to the beginning of three years ago
                currentDate.setFullYear(currentDate.getFullYear() - 3, 0, 1);
                var lastThreeYearEndDate = new Date(currentDate.getFullYear() + 2, 11, 31); // Set to the last day of December of the third year
                $('#toDate').val(formatDate(lastThreeYearEndDate));
                $('#dateRangeBlock').addClass('d-none');
            } else if ($('#attendance_date_range').val() == 'selectDate') {
                $('#dateRangeBlock').removeClass('d-none');
            }

            // Format the calculated date to dd/mm/yyyy
            var formattedFromDate = (currentDate.getDate() < 10 ? '0' : '') + currentDate.getDate() + '/' +
                ((currentDate.getMonth() + 1) < 10 ? '0' : '') + (currentDate.getMonth() + 1) + '/' +
                currentDate.getFullYear();

            $('#fromDate').val(formattedFromDate);
        });

        function formatDate(date) {
            var day = date.getDate();
            var month = date.getMonth() + 1;
            var year = date.getFullYear();
            return (day < 10 ? '0' : '') + day + '/' + (month < 10 ? '0' : '') + month + '/' + year;
        }

    });

    function loadAllAttendanceRecords() {
        var items = [
            '#district_id', '#role_id', '#user_id', '#attendance_date_range', '#attendance_filter_status', '#fromDate', '#toDate'
        ];


        dt = new CustomDataTable({
            tableElem: '.attendanceData',
            option: {
                processing: true,
                serverSide: true,
                ajax: {
                    type: "GET",
                    url: "{{ url('/human-resource/attendance/get-attendance-records') }}",
                },
                columns: [{
                        "class": "nk-tb-col tb-col-lg",
                        data: 'employee_id',
                        name: 'employee_id',
                        orderable: true,
                        render: function(data, type, row, meta) {
                            return data || 'NA';
                        }
                    },
                    {
                        "class": "nk-tb-col tb-col-lg",
                        data: 'attendance_date',
                        name: 'attendance_date',
                        orderable: true,
                        render: function(data, type, row, meta) {
                            var dateObj = new Date(data);
                            return moment(dateObj).format('DD/MM/YYYY');
                        }
                    },
                    {
                        "class": "nk-tb-col tb-col-lg",
                        data: 'user_name',
                        name: 'user_name',
                        orderable: true,
                        render: function(data, type, row, meta) {
                            return data || 'NA';
                        }
                    },
                    {
                        "class": "nk-tb-col tb-col-lg",
                        data: 'role_name',
                        name: 'role_name',
                        orderable: true,
                        render: function(data, type, row, meta) {
                            return data || 'NA';
                        }
                    },
                    {
                        "class": "nk-tb-col tb-col-lg",
                        data: 'district_name',
                        name: 'district_name',
                        orderable: true,
                        render: function(data, type, row, meta) {
                            return data || 'NA';
                        }
                    },
                    {
                        "class": "nk-tb-col tb-col-lg",
                        data: 'attendance_status',
                        name: 'attendance_status',
                        orderable: true,
                        render: function(data, type, row, meta) {
                            return data || 'NA';
                        }
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
                    [1, 'asc'] // 1 is the index of the 'attendance_date' column
                ],
            },
            filterSubmit: '.submitBtnFilter',
            filterSubmitCallback: function() {
                NioApp.Toggle.collapseDrawer('fiterAttendance')
            },
            filterClearSubmit: '.resetFilter',
            filterModalId: '#fiterAttendance',
            filterItems: items,
            tagId: '#filter_tag_list',
        });
    }

    $('.cancel').on('click', function() {
        NioApp.Toggle.collapseDrawer('fiterAttendance')
    });

    $(document).on("click", function(event) {
        var overlay = $("#fiterAttendance");
        if (!overlay.is(event.target) && overlay.has(event.target).length === 0) {
            //resetForm();
        }
    });

    $('.export').on('click', function(e) {
        var fromDate = $('#fromDate').val();
        var toDate = $('#toDate').val();
        var role_id = $('#role_id').val();
        var district_id = $('#district_id').val();
        var user_id = $('#user_id').val();
        var attendance_filter_status = $('#attendance_filter_status').val();

        $.ajax({
            url: "{{url('/human-resource/attendance/export-attendance-records')}}", // Replace with your actual AJAX endpoint
            type: 'GET', // Adjust the HTTP method as needed
            data: {
                fromDate: fromDate,
                toDate: toDate,
                role_id: role_id,
                district_id: district_id,
                user_id: user_id,
                attendance_filter_status: attendance_filter_status,
            },
            xhrFields: {
                responseType: 'blob'
            },
            success: function(response) {
                // Create a Blob from the response
                var blob = new Blob([response]);

                // Create a link element to trigger the download
                var link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                link.download = "attendance_report.xlsx";

                // Append the link to the document and trigger the click event
                document.body.appendChild(link);
                link.click();

                // Remove the link from the document
                document.body.removeChild(link);
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    });
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
    $("#attendance_leave_type").change(function() {
        $("#updateAttendanceForm").validate().element("#attendance_leave_type");
    });
    // $("#attendance_status").change(function() {
    //     $("#updateAttendanceForm").validate().element("#attendance_status");
    // });
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
</script>

@endpush