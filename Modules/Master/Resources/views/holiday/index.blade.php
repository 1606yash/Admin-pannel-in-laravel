@extends('layouts.app')
<meta name="csrf-token" content="{{ csrf_token() }}" />
@section('content')
<div class="nk-block-head nk-block-head-sm">
    <div class="nk-block-between">
        <div class="nk-block-head-content">
            <h3 class="nk-block-title page-title">Holidays</h3>
        </div><!-- .nk-block-head-content -->
        <div class="nk-block-head-content">
            <div class="toggle-wrap nk-block-tools-toggle">
                <a href="#" class="btn btn-icon btn-trigger toggle-expand mr-n1" data-target="more-options"><em class="icon ni ni-more-v"></em></a>
                <div class="toggle-expand-content" data-content="more-options">
                    <ul class="nk-block-tools g-3">
                        <li>
                            <div class="form-group" style="padding-right: 15px; width: 150px;">
                                <select name="year_id" id="year_id" class="form-control form-select" data-search="on" placeholder="Select Year" onchange="getHolidayList();">
                                    @php
                                    // Set the range of years you want to allow
                                    $currentYear = date('Y');
                                    $startYear = 2000;

                                    // Create options for each year in the range
                                    for ($year = $currentYear; $year >= $startYear; $year--) {
                                    echo "<option value=\"$year\" ($year===$currentYear) ? 'selected' : ''>$year</option>";
                                    }
                                    @endphp
                                </select>
                            </div>
                        </li>
                        <li class="nk-block-tools-opt">
                            <a href="#" data-target="addHoliday" class="toggle btn btn-primary d-none d-md-inline-flex mr-2" id="updateHoliday"><em class="icon ni ni-plus"></em><span>Add</span></a>
                            <a href="#" class="toggle btn btn-danger d-none d-md-inline-flex" id="deleteHoliday"><em class="icon ni ni-trash"></em><span>Delete</span></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div><!-- .nk-block-between -->
</div>
<div class="container">
    <div class="card mt-3 mb-5">
        <div class="card-header">
            <h5>All Holidays</h5>
        </div>
        <div class="card-body" id="holidayContainer">
            @php
            $months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            @endphp

            @foreach($months as $index => $month)
            @php
            $holidaysForMonth = $holidays->filter(function($holiday) use ($month) {
            return date('F', strtotime($holiday->date)) === $month;
            });
            @endphp

            <fieldset class="border rounded-3 p-3 card mt-3" @if($index>= 4) style="display: none;" @endif>
                <legend class="float-none w-auto px-3">
                    <h5>{{ $month }}</h5>
                </legend>
                <div class="card-body">
                    @if($holidaysForMonth->count() > 0)
                    @foreach($holidaysForMonth as $holiday)
                    <div class="form-row">
                        <div class="col-md-12 mt-3">
                            <div class="row">
                                <div class="col-md-4">
                                    <span class="font-weight-bold">{{ date('d, l', strtotime($holiday->date)) }}</span>
                                </div>
                                <div class="col-md-4">
                                    <span class="ml-5">{{ $holiday->name }}</span>
                                </div>
                                <div class="col-md-4">
                                    <a href="#" class="ml-5 editHolidayDetails" data-toggle='modal' data-id="{{ $holiday->id }}" data-target='#updateHolidayInfo'><em class="icon ni ni-edit " style="font-size:medium;"></em></a>
                                    <a href="#" class="deleteHolidayDetails ml-2" data-id="{{ $holiday->id }}" data-date="{{ $holiday->date }}"><em class="icon ni ni-trash" style="font-size:medium;"></em></a>
                                </div>
                            </div>
                        </div>

                    </div>
                    @endforeach
                    @else
                    <div class="form-row">
                        <div class="col-md-12">
                            <div class="text-muted">No holidays for this month.</div>
                        </div>
                    </div>
                    @endif
                </div>
            </fieldset>
            @endforeach

            @if(count($months) > 4)
            <button id="loadMoreBtn" class="btn btn-primary mt-3" onclick="loadMore()">Load More</button>
            @endif
        </div>
    </div>
</div>

<div class="nk-add-product toggle-slide toggle-slide-right" data-content="addHoliday" data-toggle-screen="any" data-toggle-overlay="true" data-toggle-body="true" data-simplebar id="modal">
    <form role="form" method="post" id="addHolidayForm" enctype="multipart/form-data">
        @csrf
        <div class="nk-block-head">
            <div class="nk-block-head-content">
                <h5 class="nk-block-title modalTitle">Add Holiday</h5>
                <div class="nk-block-des">
                    <p>Upload Excel files for bulk add/update holidays.</p>
                </div>
            </div>
        </div><!-- .nk-block-head -->
        <div class="nk-block">
            <div class="row g-3">
                <div class="col-12">
                    <div class="form-control-required-wrap">
                        <label class="form-label" for="add_holiday">Upload Document <a href="#" class="formatHolidayDoc">(Format for document)</a></label>

                        <div class="custom-file mb-3">
                            <input type="file" class="custom-file-input" id="holiday_doc" name="holiday_doc" accept=".xls, .xlsx, .csv" onchange="updateFileName(this)" data-parsley-excluded="true">
                            <label class="custom-file-label" for="attach_file">Tap to attach a file</label>
                        </div>
                    </div>
                </div>
                <div class="col-12 text-center">
                    <label class="form-label">OR</label>
                </div>
                <div class="col-12">
                    <label class="form-label" for="holiday_name">Holiday</label>
                    <input type="text" class="form-control" id="holiday_name" name="holiday_name" placeholder="Enter Holiday Name" onkeypress="return isCharSpace(event)" data-parsley-excluded="true">
                </div>
                <div class="col-12">
                    <label class="form-label" for="holiday_date">Holiday Date</label>
                    <input type="date" class="form-control" id="holiday_date" name="holiday_date" placeholder="Select Holiday Date" data-parsley-excluded="true">
                </div>

                <div class="col-12">
                    <label class="form-label" for="holiday_description">Holiday Description</label>
                    <textarea class="form-control" id="holiday_description" name="holiday_description" rows="2" placeholder="Enter Holiday Description Here ..." data-parsley-excluded="true"></textarea>
                </div>

                <div class="col-12 text-right">
                    <a data-target='addHoliday' class="btn btn-outline-light cancel">Cancel</a>
                    <button class="btn btn-primary submitBtn" name="submit"><span>Submit</span></button>
                </div>
            </div>
        </div><!-- .nk-block -->
    </form>
</div>

<div class="modal fade zoom" tabindex="-1" id="updateHolidayInfo">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Holiday</h5>
                <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
            </div>
            <div class="modal-body modal-body-md">
                <form role="form" class="mb-0" method="post" id="updateHolidayForm">
                    @csrf
                    <div class="modal-body modal-body-md">
                        <div class="gy-3">

                            <div class="row g-3 align-center">
                                <div class="col-lg-5">
                                    <label for="date"><em class='icon ni ni-calendar'></em> Holiday Date: </label>
                                </div>
                                <div class="col-lg-7">
                                    <span id="holiday_edit_date"></span>
                                </div>
                            </div>
                            <div class="row g-3 align-center">
                                <div class="col-lg-5">
                                    <label for="date"><em class='icon ni ni-calendar'></em> Holiday Name: </label>
                                </div>
                                <div class="col-lg-7">
                                    <input type="text" class="form-control" id="holiday_edit_name" name="holiday_edit_name" placeholder="Enter Holiday Name" onkeypress="return isCharSpace(event)" data-parsley-excluded="true">
                                </div>
                            </div>
                            <div class="row g-3 align-center">
                                <div class="col-lg-5">
                                    <label for="status_id"><em class='icon ni ni-calendar'></em> Holiday Description: </label>
                                </div>
                                <div class="col-lg-7">
                                    <textarea class="form-control" id="holiday_edit_description" name="holiday_edit_description" rows="2" placeholder="Enter Holiday Description Here ..." data-parsley-excluded="true"></textarea>
                                </div>
                            </div>
                            <input type="hidden" name="holiday_edit_id" id="holiday_edit_id" value="">
                        </div>
                    </div>
                    <!-- <div class="modal-footer bg-light"> -->
                    <div class="row">
                        <div class="col-lg-12 p-0 text-right">
                            <button class="btn btn-outline-light" data-dismiss="modal" aria-label="Close">Cancel</button>
                            <button class="btn btn-primary" type="button" id="updateHolidayDetails">Submit</button>
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
<script src="{{ url('js/moment.min.js') }}"></script>

<script>
    var root_url = "<?php echo Request::root(); ?>";
    $(document).ready(function() {
        getHolidayList();
        resetValues();
    });

    $('.cancel').on('click', function() {
        resetValues();
        NioApp.Toggle.collapseDrawer('addHoliday')
    })

    function resetValues() {
        $("#holiday_date").val('');
        $("#holiday_description").val('');
        $("#holiday_name").val('');
        $("#holiday_name").removeClass("error");
        $("#holiday_name-error").text("");
        $("#holiday_doc").val('');
        $("#holiday_doc").removeClass("error");
        $("#holiday_doc-error").text("")
        $("#holiday_date").removeClass("error");
        $("#holiday_date-error").text("")
        var fileInput = $('#holiday_doc');

        // Create a new file input element
        var newFileInput = fileInput.clone();

        // Replace the existing file input with the new one
        fileInput.replaceWith(newFileInput);

        // Optionally, update the custom-file-label text
        var customFileLabel = newFileInput.siblings('.custom-file-label');
        customFileLabel.text('Tap to attach a file');

        var formElement = $("#addHolidayForm");
        if (formElement.length > 0) {
            // Check if the object is a jQuery object
            if (formElement[0] instanceof jQuery) {
                // Reset the form
                formElement[0].reset();
            }
        }
    }

    function loadMore() {
        document.querySelectorAll('#holidayContainer fieldset').forEach(function(fieldset) {
            fieldset.style.display = 'block';
        });
        document.getElementById('loadMoreBtn').style.display = 'none';
    }

    function getHolidayList() {

        var year = $("#year_id").val();
        $.ajax({
            type: "GET",
            url: "{{ url('master/holidays/get-holiday-list') }}",
            data: {
                year: year
            },
            success: function(response) {
                var holidaysByMonth = groupHolidaysByMonth(response.holidays);

                // Clear existing content
                $("#holidayContainer").empty();

                // Update content based on the new data
                for (var i = 0; i < holidaysByMonth.length; i++) {
                    var month = holidaysByMonth[i].month;
                    var holidaysForMonth = holidaysByMonth[i].holidays;

                    var fieldset = $('<fieldset>').addClass('border rounded-3 p-3 card mt-3').attr('style', (i >= 4) ? 'display: none;' : '');
                    var legend = $('<legend>').addClass('float-none w-auto px-3').html('<h5>' + month + '</h5>');
                    var cardBody = $('<div>').addClass('card-body');

                    if (holidaysForMonth.length > 0) {
                        for (var j = 0; j < holidaysForMonth.length; j++) {
                            var holiday = holidaysForMonth[j];
                            var formRow = $('<div>').addClass('form-row');
                            var colMd12 = $('<div>').addClass('col-md-12 mt-3');
                            var colMd3 = $('<div>').addClass('row');
                            // Use JavaScript's Date object to format the date
                            var holidayDate = new Date(holiday.date);

                            // Format the day with leading zeros
                            var day = holidayDate.getDate();
                            var formattedDay = day < 10 ? '0' + day : day.toString();

                            var formattedDate = formattedDay + ' ' + holidayDate.toLocaleDateString('en-US', {
                                weekday: 'long'
                            });

                            colMd3.append('<div class="col-md-4"><span class="font-weight-bold">' + formattedDate + '</span></div>');
                            colMd3.append('<div class="col-md-4"><span class="ml-5">' + holiday.name.charAt(0).toUpperCase() + holiday.name.slice(1).toUpperCase() + '</span></div>');
                            colMd3.append('<div class="col-md-4"><a href="#" class="ml-5 editHolidayDetails" data-toggle="modal" data-target="#updateHolidayInfo" data-id="' + holiday.id + '"><em class="icon ni ni-edit" style="font-size:medium;"></em></a><a href="#" class="deleteHolidayDetails" data-id="' + holiday.id + '" data-date="' + holiday.date + '"><em class="icon ni ni-trash ml-2" style="font-size:medium;"></em></a></div>');
                            colMd3.append('<input type="hidden" name="holiday_id" id="holiday_id" value="' + holiday.id + '">');
                            colMd12.append(colMd3);
                            //colMd12.append('<hr>');
                            formRow.append(colMd12);
                            cardBody.append(formRow);
                        }
                    } else {
                        cardBody.append('<div class="text-muted">No holidays for this month.</div>');
                    }

                    fieldset.append(legend, cardBody);
                    $("#holidayContainer").append(fieldset);
                }

                // Check if there are more than 4 months to display the "Load More" button
                if (holidaysByMonth.length > 4) {
                    $("#holidayContainer").append('<button id="loadMoreBtn" class="btn btn-primary mt-3" onclick="loadMore()">Load More</button>');
                }

            }
        });
    }

    function groupHolidaysByMonth(holidays) {
        var months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        var groupedHolidays = [];

        for (var i = 0; i < months.length; i++) {
            var month = months[i];
            var holidaysForMonth = holidays.filter(function(holiday) {
                return moment(holiday.date).format('MMMM') === month;
            });

            groupedHolidays.push({
                month: month,
                holidays: holidaysForMonth
            });
        }

        return groupedHolidays;
    }

    function updateFileName(input) {
        var fileName = input.files[0].name;
        var label = input.parentNode.querySelector('.custom-file-label');
        label.textContent = fileName;
    }


    $(document).on("click", function(event) {
        var overlay = $("#modal");

        // Check if the click target is outside the overlay
        if (!overlay.is(event.target) && overlay.has(event.target).length === 0) {
            resetValues();
        }
    });

    $(document).ready(function() {

        $('#holiday_name, #holiday_description, #holiday_date').on('input', function() {
            updateRequiredAttributeForAddHoliday();
        });

        function updateRequiredAttributeForAddHoliday() {
            var anyFieldFilled = false;
            $('#holiday_name, #holiday_description, #holiday_date').each(function() {
                if ($(this).val().trim() !== '') {
                    anyFieldFilled = true;
                    return false; // Exit the loop early if any field is filled
                }
            });

            // Add or remove the 'required' attribute based on whether any field is filled
            $('#holiday_name, #holiday_date').prop('required', anyFieldFilled);
            $('#holiday_name, #holiday_date').removeClass('error');
        }
    });

    if ($("#addHolidayForm").length > 0) {
        $("#addHolidayForm").validate({
            rules: {
                holiday_doc: {
                    required: function(element) {
                        // Check if either holiday_name or holiday_date is filled
                        return !($("#holiday_name").val() || $("#holiday_date").val());
                    }
                },
                holiday_name: {
                    required: function(element) {
                        // Check if holiday_doc is not filled
                        return !$("#holiday_doc").val();
                    },
                    maxlength: 50
                },
                holiday_date: {
                    required: function(element) {
                        // Check if holiday_doc is not filled
                        return !$("#holiday_doc").val();
                    }
                }
            },
            messages: {
                holiday_doc: {
                    required: "Please upload document to update holiday calendar."
                },
                holiday_name: {
                    required: "Please enter holiday name.",
                    maxlength: "holiday name should not exceed more than 50 characters."
                },
                holiday_date: {
                    required: "Please enter holiday date."
                }
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
                    url: "{{ url('master/holidays/add') }}",
                    data: new FormData($('#addHolidayForm')[0]),
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        if (response.status == 'success') {
                            if ($('body').hasClass('toggle-shown')) {
                                //Close the sidebar
                                $('body').removeClass('toggle-shown');
                            }
                            $('#modal').removeClass('content-active');
                            $('#updateHoliday').removeClass('active');
                            $('#addHolidayForm')[0].reset();
                            $('.toggle-overlay[data-target="addHoliday"]').remove();
                            $('#holidayContainer').load(window.location.href + ' #holidayContainer');
                            getHolidayList();
                            toastr.success(response.message);
                        } else {
                            //getHolidayList();
                            toastr.error(response.message);
                        }
                        getHolidayList();
                    }
                });
                $(".submitBtn").attr("disabled", false);
            }
        });
    }

    $('#deleteHoliday').on('click', function(e) {
        var year_id = $('#year_id').val();
        Swal.fire({
            title: 'Are you sure you want to delete holidays for the year ' + year_id + '?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!'
        }).then(function(result) {
            if (result.value) {
                $.ajax({
                    url: "{{ url('master/holidays/delete')}}",
                    data: {
                        'year_id': year_id
                    },
                    method: "GET",
                    success: function(response) {
                        if (response.status == 'success') {
                            $('#holidayContainer').load(window.location.href + ' #holidayContainer');
                            getHolidayList();
                            toastr.success(response.message);
                        } else {
                            toastr.error(response.message);
                        }
                    }
                });

            }
        });
        e.preventDefault();
    });

    $(document).ready(function() {
        $('#holidayContainer').on('click', '.editHolidayDetails', function(e) {
            // alert('hi');
            $('.overlay').show();
            var id = $(this).data('id');
            $.ajax({
                url: "{{url('master/holidays/get-holiday-info')}}", // Replace with your actual AJAX endpoint
                type: 'GET', // Adjust the HTTP method as needed
                dataType: 'json',
                data: {
                    holiday_id: id,
                },
                success: function(response) {
                    $('.overlay').show();
                    if (response.holidayInfo.date != '' || response.holidayInfo.date != null) {
                        var originalDate = response.holidayInfo.date; // Assuming response.holidayInfo.date is "2024-01-01"
                        var formattedDate = new Date(originalDate).toLocaleDateString('en-GB', {
                            day: '2-digit',
                            month: 'short',
                            year: 'numeric'
                        });
                        $("#holiday_edit_date").text(formattedDate);
                        $("#holiday_edit_description").text(response.holidayInfo.description);
                        $("#holiday_edit_name").val(response.holidayInfo.name);
                        $("#holiday_edit_id").val(response.holidayInfo.id);
                    }
                    $('.overlay').hide();
                },
            });
            $('.overlay').hide();
        });

        function isPastDate(dateString) {
            // Parse the date string and compare it with the current date
            var date = new Date(dateString);
            var currentDate = new Date();
            //alert(dateString)
            return date < currentDate;
        }

        $('#holidayContainer').on('click', '.deleteHolidayDetails', function(e) {
            //alert('hi');
            var id = $(this).attr('data-id');
            var holidayDate = $(this).attr('data-date');
            var title, textMessage;
            // Check the holiday date and set the title and text message accordingly
            if (isPastDate(holidayDate)) { // Assuming isPastDate function checks if the date is in the past
                title = 'Delete Past Holiday?';
                textMessage = "This holiday date has already passed. Are you sure you want to delete it?";
            } else {
                title = 'Are you sure?';
                textMessage = "You won't be able to revert this!";
            }

            Swal.fire({
                title: title,
                text: textMessage,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!'
            }).then(function(result) {
                if (result.value) {
                    console.log('catId-' + id);
                    $.ajax({
                        url: root_url + '/master/holidays/delete-holiday',
                        data: {
                            'holiday_id': id
                        },
                        //dataType: "html",
                        method: "GET",
                        cache: false,
                        success: function(data) {
                            console.log(data);
                            if (data.status == 'success') {
                                Swal.fire('Deleted!', data.message, 'success');
                                getHolidayList();
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: data.message,
                                })
                            }
                        }
                    });

                }
            });
            e.preventDefault();
        });

        $('#updateHolidayDetails').on('click', function(e) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // disabled submit button during process running status
            $("#updateHolidayDetails").attr("disabled", true);

            $.ajax({
                type: "POST",
                url: "{{ url('/master/holidays/update-holiday-info') }}",
                // data: $('#add_user_form').serialize(),
                data: new FormData($('#updateHolidayForm')[0]),
                contentType: false,
                processData: false,
                success: function(response) {

                    if (response.status == 'success') {
                        //$('#attendance').load(window.location.href + ' #attendance');
                        toastr.success(response.message);
                        $('#updateHolidayInfo').modal('hide');
                        getHolidayList();
                    } else {
                        toastr.error(response.message);
                        $('#updateHolidayInfo').modal('hide');
                    }
                }
            });
            $("#updateHolidayDetails").attr("disabled", false);
        });
    });

    $('.formatHolidayDoc').on('click', function(e) {
        e.preventDefault();

        $.ajax({
            url: "{{ url('/master/holidays/export-format-for-bulk-holiday-upload') }}",
            type: 'GET',
            xhrFields: {
                responseType: 'blob'
            },
            success: function(response) {
                // Create a Blob from the response
                var blob = new Blob([response]);

                // Create a link element to trigger the download
                var link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                link.download = "bulk_holiday_file_format.xlsx";

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
</script>
@endpush