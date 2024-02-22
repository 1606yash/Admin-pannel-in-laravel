@extends('layouts.app')
@push('headerScripts')
<link href="{{ url('css/mdtimepicker.css') }} " rel="stylesheet" type="text/css">
<link rel="stylesheet" href="{{url('css/calendar.css?t='.time())}}">
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.5/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@6.1.5/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/list@6.1.5/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/timegrid@6.1.5/index.global.min.js"></script>
<script src="https://unpkg.com/popper.js/dist/umd/popper.min.js"></script>
<script src="https://unpkg.com/tooltip.js/dist/umd/tooltip.min.js"></script>
<script>
  var events = <?php echo json_encode($masterEvent)  ?>;
  document.addEventListener('DOMContentLoaded', function() {

    var modelId = "",
      customerId = "",
      zoho_response_id = "";
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {

      slotMinTime: '09:00:00',
      slotMaxTime: '19:00:00',

      dayMaxEventRows: true,
      events: events,

      initialView: 'dayGridMonth',
      headerToolbar: {
        left: 'prev,next',
        center: 'title',
        right: "dayGridMonth,timeGridWeek,timeGridDay,listDay", // user can switch between the two
      },

      eventDidMount: function(info, element) {
        // console.log("event", info);
        var color = "#DE3E3E";
        var textColor = "#FFFFFF";
        switch (info.event._def.extendedProps.event_type) {
          case "Checklist":
            color = "#DE3E3E";
            textColor = "#FFFFFF";
            break;
          case "Weekly Checkup":
            color = "#678C96";
            textColor = "#FFFFFF";
            break;
          case "Monthly Checkup":
            color = "#66B3A6";
            textColor = "#FFFFFF";
            break;
          case "Oil Change":
            color = "#8FC543";
            textColor = "#222222";
            break;
          case "Customer":
            color = "#888B8D";
            textColor = "#FFFFFF";
            break;
          case "Internal":
            color = "#182871";
            textColor = "#FFFFFF";
            break;
          default:
            color = "#212A44";
            textColor = "#FFFFFF";
            break;
        }
        $(info.el).css({
          "background-color": color,
          "color": textColor
        });
      },
      eventClick: function(event, jsEvent, view) {
        console.log(event.event._def.extendedProps);
        var color = "#DE3E3E";
        var textColor = "#FFFFFF";
        switch (event.event._def.extendedProps.event_type) {
          case "Checklist":
            color = "#DE3E3E";
            textColor = "#FFFFFF";
            break;
          case "Weekly Checkup":
            color = "#678C96";
            textColor = "#FFFFFF";
            break;
          case "Monthly Checkup":
            color = "#66B3A6";
            textColor = "#FFFFFF";
            break;
          case "Oil Change":
            color = "#8FC543";
            textColor = "#222222";
            break;
          case "Customer":
            color = "#888B8D";
            textColor = "#FFFFFF";
            break;
          case "Internal":
            color = "#182871";
            textColor = "#FFFFFF";
            break;
          default:
            color = "#212A44";
            textColor = "#FFFFFF";
            break;
        }
        // Modal Header Color Change
        $('#preview-event-header').css('background-color', color);
        $('#preview-event-header *').css('color', textColor);

        $('.custom-popup-header').css('background-color', color);
        $('.custom-popup-header *').css('color', textColor);

        $('#preview-event-title').html(event.event._def.extendedProps.event_title);
        $('#preview-from-date').html(event.event._def.extendedProps.from);
        $('#preview-to-date').html(event.event._def.extendedProps.to);
        $('#preview-to-type').html(event.event._def.extendedProps.event_type);
        $('#preview-event-description').html(event.event._def.extendedProps.description);
        $('#preview-event-chassis_id').html(event.event._def.extendedProps.chassis_id);
        $('#preview-event-model_name').html(event.event._def.extendedProps.model_name);
        $('#preview-event-area_name').html(event.event._def.extendedProps.area_name);
        $('#preview-event-region_name').html(event.event._def.extendedProps.region_name);
        $('#preview-event-customer_name').html(event.event._def.extendedProps.customer_name);
        $('#preview-event-workshop_name').html(event.event._def.extendedProps.workshop_name);
        $('#preview-event-model_id').html(event.event._def.extendedProps.model_id);

        if (event.event._def.extendedProps.event_type != "Internal" && event.event._def.extendedProps.event_type != "Customer") {
          $('#preview-event-description-wrap').hide();
        } else {
          $('#preview-event-description-wrap').show();
        }

        var eventType = event.event._def.extendedProps.event_type;
        zohoEventId = event.event._def.extendedProps.zoho_response_id;
        modelId = event.event._def.extendedProps.model_id;
        customerId = event.event._def.extendedProps.customer_id;

        if (eventType !== "Internal" && eventType !== "Customer") {
          // Show the "Create Checklist" button
          $('#createChecklistBtn').show();
          $('#footer_createChecklistBtn').show();
          $('#markAsDoneBtn').hide();

        } else if (eventType == "Customer") {
          $('#createChecklistBtn').hide();
          $('#footer_createChecklistBtn').show();
          $('#markAsDoneBtn').show();
          $('#markAsDoneBtn').attr('data-eventid', zohoEventId);

        } else {
          // Hide the "Create Checklist" button
          $('#createChecklistBtn').hide();
          $('#footer_createChecklistBtn').hide();
          $('#markAsDoneBtn').hide();
        }

        var root_url = "<?php echo Request::root(); ?>";
        var url = root_url + '/event/check-update-request/' + event.event._def.extendedProps.zoho_response_id;
        $.ajax({
          type: "GET",
          url: url,
          processData: false,
          contentType: false,
          dataType: "json",
          success: function(response) {
            if (response.success) {
              if (response.is_exist) {
                $('#createChecklistBtn').hide();
                $('#reAssignBtn').hide();
                $('#reScheduleBtn').hide();
                $('#reAssignStatus').show();
                $("#footer_createChecklistBtn").hide();
                $("#requestType").html(response.data.type);
              } else {
                if (eventType !== "Internal" && eventType !== "Customer") {
                  $('#createChecklistBtn').show();
                  $('#reAssignBtn').show();
                  $('#reScheduleBtn').show();
                  $("#footer_createChecklistBtn").show();
                  fillEventDetails(event.event._def.extendedProps.zoho_response_id, event.event._def.extendedProps.from, event.event._def.extendedProps.to, event.event._def.extendedProps.workshop_id, event.event._def.extendedProps.workshop_name, event.event._def.extendedProps.area_id);
                }
                $('#reAssignStatus').hide();
              }
            } else {
              if (eventType !== "Internal" && eventType !== "Customer") {
                $('#createChecklistBtn').show();
                $('#reAssignBtn').show();
                $('#reScheduleBtn').show();
                $("#footer_createChecklistBtn").show();
                fillEventDetails(event.event._def.extendedProps.zoho_response_id, event.event._def.extendedProps.from, event.event._def.extendedProps.to, event.event._def.extendedProps.workshop_id, event.event._def.extendedProps.workshop_name, event.event._def.extendedProps.area_id);
              }
              $('#reAssignStatus').hide();
            }
          }
        })

        if (event.event._def.extendedProps.workshop_name == null) {
          $('.workshop_name').hide();
        } else {
          $('.workshop_name').show();
        }

        if (event.event._def.extendedProps.chassis_id == null) {
          $('.chassis_id').hide();
        } else {
          $('.chassis_id').show();
        }

        if (event.event._def.extendedProps.model_name == null || event.event._def.extendedProps.model_name == '') {
          $('.model_name').hide();
        } else {
          $('.model_name').show();
        }

        if (event.event._def.extendedProps.area_name == null || event.event._def.extendedProps.area_name == '') {
          $('.area_name').hide();
        } else {
          $('.area_name').show();
        }

        if (event.event._def.extendedProps.customer_name == null || event.event._def.extendedProps.customer_name == '') {
          $('.customer_name').hide();
        } else {
          $('.customer_name').show();
        }

        if (event.event._def.extendedProps.region_name == null || event.event._def.extendedProps.region_name == '') {
          $('.event-region_name').hide();

        } else {
          $('.event-region_name').show();
        }

        $('#previewEventPopup').modal();
      },
    });
    calendar.render();

    document.getElementById('createChecklistBtn').addEventListener('click', function() {

      var root_url = "<?php echo Request::root(); ?>";

      // Retrieve all the necessary data
      var chassisId = document.getElementById('preview-event-chassis_id').textContent;
      var modelName = document.getElementById('preview-event-model_name').textContent;
      var customerName = document.getElementById('preview-event-customer_name').textContent;
      var fromDate = document.getElementById('preview-from-date').textContent;
      var toDate = document.getElementById('preview-to-date').textContent;
      var eventType = document.getElementById('preview-to-type').textContent;
      var description = document.getElementById('preview-event-description').textContent;
      var workshopName = document.getElementById('preview-event-workshop_name').textContent;
      var areaName = document.getElementById('preview-event-area_name').textContent;
      var regionName = document.getElementById('preview-event-region_name').textContent;

      var checklistType = "";

      // console.log(eventType);

      if (eventType.includes("Weekly")) {
        checklistType = "weekly";
      } else if (eventType.includes("Oil")) {

        checklistType = "oil-change";
      } else {
        checklistType = "monthly";
      }

      var url = root_url + '/checklist/' + checklistType + '/create' +

        '?chassisId=' + encodeURIComponent(chassisId) +
        '&modelName=' + encodeURIComponent(modelName) +
        '&customerName=' + encodeURIComponent(customerName) +
        '&modelId=' + encodeURIComponent(modelId) +
        '&customerId=' + encodeURIComponent(customerId) +
        '&zohoEventId=' + encodeURIComponent(zohoEventId);
      window.location.href = url;

    });

  });
  $(document).ready(function() {
    $('#markAsDoneBtn').on('click', function() {
      var root_url = "<?php echo Request::root(); ?>";
      var eventId = $(this).attr('data-eventid');
      if (eventId != '') {
        $.ajax({
          type: "POST",
          url: root_url + '/api/v1/event/mark-as-done/' + eventId,
          processData: false,
          contentType: false,
          data: "",
          success: function(response) {
            if (response.success) {
              Swal.fire(
                'Good job!',
                'Event Updated Successfully',
                'success'
              )
              location.href = url = root_url + "/event/";
            }
          },
          error: function(textStatus, errorThrown) {
            if (errorThrown) {
              Swal.fire(
                'Oops!',
                'Something went wrong',
                'error'
              )
            }
          }
        });
      }
    });

    $('.eventUpdateBtn').on('click', function(e) {
      e.preventDefault();
      var type = $(this).attr('data-type');
      var form = $(this).attr('data-form');
      if (type == 'ReSchedule') {
        if ($("#requestFromDate").val() == "") {
          Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Please select start date',
          })
          return false;
        }
        if ($("#requestFromTime").val() == "") {
          Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Please select start time',
          })
          return false;
        }
        if ($("#requestToDate").val() == "") {
          Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Please select end date',
          })
          return false;
        }
        if ($("#requestToTime").val() == "") {
          Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Please select end time',
          })
          return false;
        }
      }
      Swal.fire({
        title: 'Are you sure you want to ' + type + '?',
        text: "",
        icon: 'info',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes',
        cancelButtonText: 'No'

      }).then((result) => {
        if (result.isConfirmed) {
          $('#' + form).submit();
        }
      })
    });

    // $('#reAssignPopup').modal();
    $('#reAssignBtn').on('click', function() {
      $('#previewEventPopup').modal('hide');
      $('#reSchedulePopup').modal('hide');
    });
    $('#reScheduleBtn').on('click', function() {
      $('#previewEventPopup').modal('hide');
      $('#reAssignPopup').modal('hide');
    });
    $('#reSchedulePopupClose').on('click', function() {
      $('#previewEventPopup').modal('show');
    });
    $('#reAssignPopupClose').on('click', function() {
      $('#previewEventPopup').modal('show');
    });
  });

  function fillEventDetails(eventId, fromDate, toDate, workshopId, workshopName, area) {
    $(".event_id").val(eventId);
    $("#current_workshop").val(workshopId);
    $("#current_workshop_name").val(workshopName);
    $(".currentFromDateTime").val(fromDate);
    $(".currentToDateTime").val(toDate);
    $("#area_id").val(area);
  }
</script>
@endpush
@section('content')
<div class="nk-block-head nk-block-head-sm">
  <div class="nk-block-between">
    <div class="nk-block-head-content">
      <h3 class="nk-block-title page-title">My Calendar</h3>
    </div><!-- .nk-block-head-content -->
  </div><!-- .nk-block-between -->
</div><!-- .nk-block-head -->
@if (session()->has('error') && session('error') != "")
<div class="alert alert-danger alert-icon alert-dismissible">
  <em class="icon ni ni-cross-circle"></em>
  {{ session('error') }}<button class="close" data-dismiss="alert"></button>
  @php
  \Session::put('error', '');
  @endphp
</div>
@endif
@if (session()->has('message') && session('message') != "")
<div class="alert alert-success alert-icon alert-dismissible">
  <em class="icon ni ni-check-circle"></em>
  {{ session('message') }}<button class="close" data-dismiss="alert"></button>
  @php
  \Session::put('message', '');
  @endphp
</div>
@endif
<div class="nk-block">
  <ul class="calendar-legends">
    <li><span class="dot oil-change-color"></span> Assigned Shifts</li>
    <li><span class="dot internal-event-color"></span> Unassigned Shifts</li>
  </ul>
  <div id="calendar" class="nk-calendar"></div>
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
                    @foreach($workshops as $wrkshp)
                    @if($wrkshp->workshop_id != $workshop[0]->ID)
                    <option value="{{$wrkshp->workshop_id}}">{{$wrkshp->workshop_name}}</option>
                    @endif
                    @endforeach
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



@endsection

@push('footerScripts')
<script src="{{ url('/js/mdtimepicker.js') }}"></script>
<script src="https://unpkg.com/dayjs@1.8.21/dayjs.min.js"></script>
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
@endpush