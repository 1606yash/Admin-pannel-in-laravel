@extends('layouts.app')
<meta name="csrf-token" content="{{ csrf_token() }}" />
@section('content')
<a class="btn" style="font-size: 18px;margin-bottom: 10px;" class="btn" href="javascript:history.back()"><em class='icon ni ni-arrow-left'></em>Back</a>
<div class="container">
    <div class="card mt-3 mb-5">
        <div class="card-header">
            <h5>Task Details</h5>
        </div>
        <div class="card-body" id="viewTaskInfo">
            <div class="form-row">
                <table class="table table-borderless">
                    @if(!empty($taskDetails))
                    <tr>
                        <td class="font-weight-bold" style="font-size: medium;">Task Title</td>
                        <td style="font-size: medium;">{{ $taskDetails->title ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold" style="font-size: medium;">Description</td>
                        <td style="font-size: medium;">{{ $taskDetails->description ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold" style="font-size: medium;">Priority</td>
                        <td style="font-size: medium;">{{ empty($taskDetails->priority) ? '-' : $taskDetails->priority }}</td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold" style="font-size: medium;">Assigned To</td>
                        <td style="font-size: medium;">
                            @if($taskDetails->task_assigned_to)
                            {{ $taskDetails->task_assigned_to }} ({{ $taskDetails->role_name }})
                            @else
                            -
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold" style="font-size: medium;">Creation Date</td>
                        <td style="font-size: medium;">{{ $taskDetails->task_created_date ?? '-'}}</td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold" style="font-size: medium;">Created By</td>
                        <td style="font-size: medium;">{{ $taskDetails->task_created_by ?? '-'}}</td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold" style="font-size: medium;">Status</td>
                        <td style="font-size: medium;">{{ $taskDetails->status ?? '-'}}</td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold" style="font-size: medium;">Last Updated Date</td>
                        <td style="font-size: medium;">{{ $taskDetails->updated_by ? $taskDetails->task_updated_date : '-' }}
                        </td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold" style="font-size: medium;">Remarks</td>
                        <td style="font-size: medium;">{{ $taskDetails->remark ?? '-'}}</td>
                    </tr>

                    <tr>
                        <td class="font-weight-bold" style="font-size: medium;">Attach File</td>
                        <td style="font-size: medium;">
                            @if(!empty($attachments))
                            @foreach($attachments as $attachment)
                            <a href="{{ $attachment->attachment_path }}" class="btn btn-link" target="_blank" style="text-decoration: none; cursor: pointer;">{{ $attachment->file_name }}</a><br>
                            @endforeach
                            @else
                            -
                            @endif
                        </td>
                    </tr>
                    @endif
                </table>
            </div>
            <div class="form-row">
            </div>
        </div>
    </div>
    <div class="mt-5">
        <div class="form-group text-center my-2">
            @if(($taskDetails->status == 'Created' || $taskDetails->status == 'Assigned' || $taskDetails->status == 'In Progress'))
            <button class="btn btn-primary getAssignTaskInfo" data-target='#assignTaskModal' data-toggle='modal'>@if(!empty($taskDetails->task_assigned_to))ReAssign @else Assign @endif</button>
            @if($taskDetails->status != 'Cancelled')
            <button class="btn btn-primary" data-target='#cancelTaskModal' data-toggle='modal'><span>Cancel Task</span></button>
            @endif
            @endif
        </div>
    </div>
</div>

<div class="modal fade zoom" tabindex="-1" id="cancelTaskModal">
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
                                <input type="hidden" name="task_id" value="{{ $taskDetails->id ?? ''}}">
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
                <h5 class="modal-title">@if(!empty($taskDetails->task_assigned_to))Re Assign @else Assign @endif Task</h5>
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
                                    <option value="" selected disabled>Select Users</option>
                                </select>
                                <input type="hidden" name="assign_task_id" value="{{ $taskDetails->id ?? '' }}">
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

<script>
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
                            $('#viewTaskInfo').load(window.location.href + ' #viewTaskInfo');
                            toastr.success(response.message);
                        } else {
                            toastr.error(response.message);
                        }
                    }
                });
            }
        });
    }



    $(document).ready(function() {
        $('#cancelTaskModal').on('hidden.bs.modal', function(e) {
            // Reset the values of input fields
            $('#reasonForCancellation').val('');
            $('#cancelTaskForm textarea').removeClass('error');
            $('#cancelTaskForm').validate().resetForm();
        });

        $('#assign_role_id').on('change', function() {

            var selectedRole = $('#assign_role_id').val();

            $.ajax({
                url: "{{url('/get-users')}}",
                type: 'GET',
                contentType: JSON,
                data: {
                    role_id: selectedRole,

                },
                success: function(response) {
                    $('#user_id').attr('disabled', false);
                    $('#user_id').empty();
                    $('#user_id').append('<option value="" selected disabled>Select Users</option>');

                    // Add new options
                    for (var i = 0; i < response.users.length; i++) {
                        $('#user_id').append('<option value="' + response.users[i].id + '">' + response.users[i].username + '</option>');
                    }
                },
            });
        });

        $('.getAssignTaskInfo').on('click', function() {
            var assignedTask = "{{$taskDetails->id ?? ''}}";

            $.ajax({
                url: "{{url('task/get-assigned-user')}}",
                type: 'GET',
                contentType: JSON,
                data: {
                    assign_task_id: assignedTask,
                },
                success: function(response) {
                    console.log(response);
                    $('#assign_role_id').val(response.roleId.role_id).trigger('change');
                    $('#viewTaskInfo').load(window.location.href + ' #viewTaskInfo');
                    setTimeout(function() {
                        $('#user_id').val(response.userId.assigned_to).trigger('change');
                    }, 1500);

                },

            });
        });
    });

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
                            $('#viewTaskInfo').load(window.location.href + ' #viewTaskInfo');
                            toastr.success(response.message);
                        } else {
                            toastr.error(response.message);
                        }
                    }
                });
            }
        });
    }
</script>
@endsection