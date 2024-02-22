@extends('layouts.app')
<meta name="csrf-token" content="{{ csrf_token() }}" />
@section('content')
@if($leaveData)
<a class="btn" style="font-size: 18px;margin-bottom: 10px;" class="btn" href="javascript:history.back()"><em class='icon ni ni-arrow-left'></em>Back</a>
<div class="container">
    <div class="card mt-3 mb-5">
        <div class="card-header">
            <h5>Leave Details</h5>
        </div>
        <div class="card-body" id="leaveRequestInfo">

            <div class="form-row">
                <table class="table table-borderless">
                    <tr>
                        <td class="font-weight-bold" style="font-size: medium;">Employee Name</td>
                        <td style="font-size: medium;">{{ ucwords($leaveData->user_name) ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold" style="font-size: medium;">Status</td>
                        <td style="font-size: medium;">{{ ucwords($leaveData->status ?? '-') }}</td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold" style="font-size: medium;">Leave Type</td>
                        <td style="font-size: medium;">{{ ucwords($leaveData->leave_name ?? '-') }}</td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold" style="font-size: medium;">From Date</td>
                        <td style="font-size: medium;">{{ $leaveData->from_date ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold" style="font-size: medium;">To Date</td>
                        <td style="font-size: medium;">{{ $leaveData->to_date ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold" style="font-size: medium;">Amount</td>
                        <td style="font-size: medium;">{{ $leaveData->amount ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold" style="font-size: medium;">Applied To</td>
                        <td style="font-size: medium;">
                            {{ $leaveData->applied_to_user_name ?? '-' }}
                            @if(!empty($leaveData->applied_to_user_name))
                            @if(!empty($leaveData->applied_to_role))
                            ({{ $leaveData->applied_to_role }})
                            @endif
                            @endif
                        </td>

                    </tr>
                    <tr>
                        <td class="font-weight-bold" style="font-size: medium;">Applied Date</td>
                        <td style="font-size: medium;">{{ $leaveData->leave_applied_date ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold" style="font-size: medium;">Approved On</td>
                        <td style="font-size: medium;">{{ $leaveData->leave_approved_date ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold" style="font-size: medium;">Reason</td>
                        <td style="font-size: medium;">{{ $leaveData->leave_reason ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold" style="font-size: medium;">Attach File</td>
                        <td style="font-size: medium;">
                            <a href="{{ $leaveData->attachment ?? '' }}" style="text-decoration: none; cursor: pointer;">@if($leaveData->attachment)View @else - @endif</a>
                        </td>
                    </tr>
                    @if($leaveData->status == 'rejected')
                    <tr>
                        <td class="font-weight-bold" style="font-size: medium;">Reject Reason</td>
                        <td style="font-size: medium;">{{ $leaveData->reject_reason ?? '-' }}</td>
                    </tr>
                    @endif
                </table>
            </div>
            <div class="form-row">
                <input type="hidden" name="user_id" id="user_id" value="{{$leaveData->user_id}}">
                <input type="hidden" name="leave_id" id="leave_id" value="{{$leaveData->id}}">
            </div>

        </div>
    </div>
    @endif
    @if($leaveData->status == 'pending')
    <div class="mt-5">
        <div class="form-group text-center my-2">
            <button class="btn btn-primary approveLeave" type="button">Approve</button>
            <button class="btn btn-danger rejectLeave" type="button"><span>Reject</span></button>
        </div>
    </div>
    @endif
</div>
@endsection
@push('footerScripts')
<script>
    var root_url = "<?php echo Request::root(); ?>";
    $('.approveLeave').on('click', function(e) {
        var leaveId = $('#leave_id').val();
        var userId = $('#user_id').val();

        Swal.fire({
            title: 'Confirmation',
            text: "Do you want to approve this Leave?",
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
                    //dataType: "html",
                    method: "GET",
                    cache: false,
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('', 'Leave Approved Successfully',
                                'success');
                            location.reload()
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: response.message,
                                //footer: '<a href>Why do I have this issue?</a>'
                            })
                        }
                    }
                });

            }
        });
        e.preventDefault();
    });

    $('.rejectLeave').on('click', function(e) {
        var leaveId = $('#leave_id').val();
        var userId = $('#user_id').val();

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
                cancelButtonText: 'Cancel',
                inputValidator: (value) => {
                    return !value && 'You need to enter a reason!';
                }
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
                                setTimeout(function() {
                                    location.reload();
                                }, 2000);

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
</script>
@endpush