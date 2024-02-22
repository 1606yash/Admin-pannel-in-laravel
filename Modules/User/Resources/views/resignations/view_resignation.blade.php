@extends('layouts.app')

@section('content')
<a class="btn" style="font-size: 18px;margin-bottom: 10px;" class="btn" href="javascript:history.back()"><em class='icon ni ni-arrow-left'></em>Back</a>
<div class="container">
    @if(!empty($resignationData))
    <div class="card mt-3 mb-5">
        <div class="card-header">
            <h5>Resignation Details</h5>
        </div>
        <div class="card-body" id="resignationRequestInfo">

            <div class="form-row">
                <table class="table table-borderless">
                    <tr>
                        <td class="font-weight-bold" style="font-size: medium;">Employee Name</td>
                        <td style="font-size: medium;">{{ ucwords($resignationData->user_name) ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold" style="font-size: medium;">Role</td>
                        <td style="font-size: medium;">{{ ucwords($resignationData->role_name) ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold" style="font-size: medium;">District</td>
                        <td style="font-size: medium;">{{ ucwords($resignationData->district_name) ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold" style="font-size: medium;">Resignation Submission Date</td>
                        <td style="font-size: medium;">{{ $resignationData->resignation_date ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold" style="font-size: medium;">Last Working Date</td>
                        <td style="font-size: medium;">{{ $resignationData->last_working_day ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold" style="font-size: medium;">Notice Period</td>
                        <td style="font-size: medium;">{{ $resignationData->notice_period.' days' ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold" style="font-size: medium;">Reason</td>
                        <td style="font-size: medium;">{{ ucfirst($resignationData->reason) ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold" style="font-size: medium;">Applied To</td>
                        <td style="font-size: medium;">
                            {{ ucwords($resignationData->applied_to_user_name) ?? '-' }}
                            @if(!empty($resignationData->applied_to_user_name))
                            @if(!empty($resignationData->applied_to_role))
                            ({{ $resignationData->applied_to_role }})
                            @endif
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold" style="font-size: medium;">Remarks</td>
                        <td style="font-size: medium;">{{ ucfirst($resignationData->remark) ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold" style="font-size: medium;">Status</td>
                        <td style="font-size: medium;">{{ ucwords($resignationData->status) ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold" style="font-size: medium;">Attachment</td>
                        <td style="font-size: medium;">
                            <a href="{{ $resignationData->attachment ?? '' }}" style="text-decoration: none; cursor: pointer;">@if($resignationData->attachment)View @else - @endif</a>
                        </td>
                    </tr>
                    @if($resignationData->status == 'rejected')
                    <tr>
                        <td class="font-weight-bold" style="font-size: medium;">Reject Reason</td>
                        <td style="font-size: medium;">{{ $resignationData->rejection_reason ?? '-' }}</td>
                    </tr>
                    @endif
                </table>
            </div>
            <div class="form-row">
                <input type="hidden" name="user_id" id="user_id" value="{{$resignationData->user_id}}">
                <input type="hidden" name="resignation_id" id="resignation_id" value="{{$resignationData->resignation_id}}">
            </div>

        </div>
    </div>
    @if($resignationData->status == 'pending')
    <div class="mt-5">
        <div class="form-group text-center my-2">
            <button class="btn btn-primary acceptResignation" type="button">Accept</button>
            <button class="btn btn-danger rejectResignation" type="button"><span>Reject</span></button>
        </div>
    </div>
    @endif
    @endif
</div>
<script>
    var root_url = "<?php echo Request::root(); ?>";
    $('.rejectResignation').on('click', function(e) {
        var resignationId = $('#resignation_id').val();
        var userId = $('#user_id').val();

        Swal.fire({
            title: 'Confirmation',
            text: "Do you want to Reject this Resignation Request?",
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'No'
        }).then(function(result) {
            Swal.fire({
                title: 'Please give reason for rejection',
                input: 'text',
                inputPlaceholder: 'Enter your reason here',
                showCancelButton: true,
                confirmButtonText: 'Finish',
                cancelButtonText: 'Cancel',
                inputValidator: (value) => {
                    return !value && 'You need to enter a reason!';
                }
            }).then(function(result) {
                if (result.value) {
                    var reason = result.value;
                    $.ajax({
                        url: root_url + '/human-resource/resignations/reject-resignation',
                        data: {
                            'resignation_id': resignationId,
                            'user_id': userId,
                            'reason': reason
                        },
                        method: "GET",
                        cache: false,
                        success: function(response) {
                            console.log(response);
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Resignation Rejected',
                                })
                                location.reload();
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
            })

        });
        e.preventDefault();
    });

    $('.acceptResignation').on('click', function(e) {
        var resignationId = $('#resignation_id').val();
        var userId = $('#user_id').val();

        Swal.fire({
            title: 'Confirmation',
            text: "Do you want to Accept this Resignation Request?",
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'No'
        }).then(function(result) {
            Swal.fire({
                title: 'Please select last working day',
                html: '<input type="date" id="last_working_date" class="swal2-input" placeholder="Please select date">',
                showCancelButton: true,
                confirmButtonText: 'Finish',
                cancelButtonText: 'Cancel'
            }).then(function(result) {
                if (result.value) {
                    var last_working_date = $('#last_working_date').val();
                    $.ajax({
                        url: root_url + '/human-resource/resignations/accept-resignation',
                        data: {
                            'resignation_id': resignationId,
                            'user_id': userId,
                            'last_working_date': last_working_date
                        },
                        method: "GET",
                        cache: false,
                        success: function(response) {
                            console.log(response);
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Resignation Approved',
                                })
                                setTimeout(function() {
                                    location.reload();
                                }, 2000);
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
            })

        });
        e.preventDefault();
    });
</script>
@endsection