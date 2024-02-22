@extends('layouts.app')
<meta name="csrf-token" content="{{ csrf_token() }}" />
@section('content')
<div class="container">
    <div class="toggle-bar">
        <ul class="nav nav-tabs justify-content-center">
            <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#ambulance" data-tab="all">
                    <h5 class="nk-block-title page-title">Ambulance</h5>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#other" data-tab="pending">
                    <h5 class="nk-block-title page-title">Other</h5>
                </a>
            </li>
        </ul>
    </div>
</div>
<a class="btn" style="font-size: 18px;margin-bottom: 10px;" class="btn" href="javascript:history.back()"><em class='icon ni ni-arrow-left'></em>Back</a>
<div class="tab-content">
    <div class="tab-pane fade show active mt-5" id="ambulance">
        <div class="card mt-3 mb-5">
            <div class="card-header">
                <h5>Ambulance Expense Details</h5>
            </div>
            <div class="card-body" id="ambulanceExpenseInfo">
                <div class="form-row">
                    <table class="table table-borderless">
                        @if(!empty($expenseData))
                        <tr>
                            <td class="font-weight-bold" style="font-size: medium;">Vehicle Number</td>
                            <td style="font-size: medium;">{{ $expenseData->ambulance_no ?? $expenseData->chassis_no ?? '-' }}
                            </td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold" style="font-size: medium;">Expense Date</td>
                            <td style="font-size: medium;">{{ $expenseData->expense_date ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold" style="font-size: medium;">Expense Type</td>
                            <td style="font-size: medium;">{{ $expenseData->expense_type_name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold" style="font-size: medium;">Fuel Type</td>
                            <td style="font-size: medium;">{{ $expenseData->fuel_type ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold" style="font-size: medium;">Quantity (L/KG)</td>
                            <td style="font-size: medium;">{{ $expenseData->quantity ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold" style="font-size: medium;">Entry Type</td>
                            <td style="font-size: medium;">{{ $expenseData->entry_type ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold" style="font-size: medium;">Vendor</td>
                            <td style="font-size: medium;">{{ $expenseData->vendor_name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold" style="font-size: medium;">Non Vendor</td>
                            <td style="font-size: medium;">{{ $expenseData->non_vendor ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold" style="font-size: medium;">Receipt</td>
                            <td style="font-size: medium;">
                                @if(!empty($expenseData->bill_photo_path))
                                <a href="{{ $expenseData->bill_photo_path }}" style="text-decoration: none; cursor: pointer;">View Receipt</a>
                                @else
                                -
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold" style="font-size: medium;">Amount</td>
                            <td style="font-size: medium;">{{ 'Rs '.$expenseData->amount ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold" style="font-size: medium;">Description</td>
                            <td style="font-size: medium;">{{ $expenseData->description ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold" style="font-size: medium;">KM Reading</td>
                            <td style="font-size: medium;">{{ $expenseData->km_reading ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold" style="font-size: medium;">Claim Date</td>
                            <td style="font-size: medium;">{{ $expenseData->claim_date ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold" style="font-size: medium;">Claim Status</td>
                            <td style="font-size: medium;">{{ $expenseData->claim_status ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold" style="font-size: medium;">Reimbursement</td>
                            <td style="font-size: medium;">{{ $expenseData->reimbursement_status ?? '-' }}
                                @if($expenseData->claim_status == 'Approved' && $expenseData->reimbursement_status == 'Completed')

                                @elseif($expenseData->claim_status == 'Rejected' || $expenseData->reimbursement_status == 'Rejected')

                                @else
                                <span class="ml-5">
                                    <a href="#" class="updateStatus" data-id="{{$expenseData->expense_id ?? ''}}" data-claim-status="{{$expenseData->claim_status ?? ''}}" style="text-decoration: none; cursor: pointer;" data-toggle="modal" @if($expenseData->claim_status == 'Pending' && $expenseData->reimbursement_status == 'Pending') data-target="#claimConfirmationModal" @else data-target="#reimbursementConfirmationModal" @endif>Update Status</a>
                                </span>
                                @endif
                                <input type="hidden" name="expense_id" id="expense_id" value="{{$expenseData->expense_id ?? ''}}">
                                <input type="hidden" name="claim_status" id="claim_status" value="{{$expenseData->claim_status ?? ''}}">
                            </td>
                        </tr>
                        @if($expenseData->reimbursement_status == 'Rejected')
                        <tr>
                            <td class="font-weight-bold" style="font-size: medium;">Rejection Reason</td>
                            <td style="font-size: medium;">{{ $expenseData->rejection_reason ?? '-' }}</td>
                        </tr>
                        @endif
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="tab-pane fade show active mt-5" id="other">

    </div>
</div>
<div class="modal fade zoom" tabindex="-1" id="claimConfirmationModal">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmation</h5>
                <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
            </div>
            <div class="modal-body">
                <div class="form-group text-center">
                    <div class="form-row justify-content-center mb-4">
                        <label class="form-label" style="font-size: 18px;">Select the Reimbursement status for this claim entry</label>
                    </div>
                    <div class="form-group d-flex justify-content-center">
                        <div class="form-row">
                            <a class="btn btn-primary ml-2" href="#" id="approve">Approve</a>
                            <a class="btn btn-danger ml-2" href="#" id="reject" data-toggle="modal" data-target="#rejectReimbursementModal">Reject</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade zoom" tabindex="-1" id="reimbursementConfirmationModal">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmation</h5>
                <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
            </div>
            <div class="modal-body">
                <div class="form-group text-center">
                    <div class="form-row justify-content-center mb-4">
                        <label class="form-label" style="font-size: 18px;">Do you also want to mark the reimbursement status as complete?</label>
                    </div>
                </div>
                <div class="form-group d-flex justify-content-center">
                    <div class="form-row">
                        <a class="btn btn-primary ml-2" href="#" id="approveReimbursement">Yes</a>
                        <a class="btn btn-danger ml-2" href="#" id="doItLater" data-dismiss="modal">Do it Later</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade zoom" tabindex="-1" id="rejectReimbursementModal">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmation</h5>
                <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
            </div>
            <div class="modal-body">
                <form action="#" role="form" class="mb-0" method="post" id="cancelReimbursementForm">
                    @csrf
                    <div class="form-group text-center">
                        <div class="form-row mb-4">
                            <label for="reason" class="form-label">Reason For Rejection <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="reason" name="reason" rows="4" placeholder="Enter Reason For Rejection Here ..." required></textarea>
                        </div>
                    </div>
                    <div class="form-group d-flex justify-content-center">
                        <div class="form-row">
                            <button class="btn btn-primary ml-2" href="#" id="rejectReimbursement" type="submit">Reject</button>
                            <button class="btn btn-danger ml-2" href="#" data-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
@push('footerScripts')
<script>
    var root_url = "<?php echo Request::root(); ?>";
    $(document).on('click', '#approve', function(e) {
        var expenseId = $('#expense_id').val();
        var claimStatus = $('#claim_status').val();
        $.ajax({
            url: root_url + '/human-resource/expenses/approve-reimbursement',
            data: {
                'expense_id': expenseId,
                'request_status': 'approved'
            },
            method: "GET",
            dataType: "json",
            cache: false,
            success: function(response) {
                if (response.status == 'success') {
                    $('#claimConfirmationModal').modal('hide');
                    $('#ambulanceExpenseInfo').load(window.location.href + ' #ambulanceExpenseInfo');
                    $('#reimbursementConfirmationModal').modal('show');
                }
            }
        });
    });

    if ($("#cancelReimbursementForm").length > 0) {
        $("#cancelReimbursementForm").validate({
            rules: {
                reason: {
                    required: true,
                },
            },
            messages: {
                reason: {
                    required: "Please enter reason for rejection.",
                },
            },
            submitHandler: function(form) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var expenseId = $('#expense_id').val();
                var formData = new FormData(form);

                // Append additional data to FormData object
                formData.append('expense_id', expenseId);

                $.ajax({
                    url: root_url + '/human-resource/expenses/reject-claim-entry',
                    data: formData,
                    type: "POST",
                    contentType: false,
                    processData: false, // Important for FormData
                    success: function(response) {
                        if (response.status == 'success') {
                            form.reset();
                            $('#rejectReimbursementModal').modal('toggle');
                            $('#claimConfirmationModal').modal('hide');
                            $('#ambulanceExpenseInfo').load(window.location.href + ' #ambulanceExpenseInfo');
                            toastr.success(response.message);
                        } else {
                            toastr.error(response.message);
                        }
                    },
                });
            }
        });
    }

    $(document).on('click', '#approveReimbursement', function(e) {
                var expenseId = $('#expense_id').val();
                var claimStatus = $('#claim_status').val();
                $.ajax({
                        url: root_url + '/human-resource/expenses/approve-reimbursement',
                        data: {
                            'expense_id': expenseId,
                            'request_status': 'complete',
                        },
                        method: "GET", // Change the method to POST
                        dataType: "json", // Expect JSON response
                        cache: false,
                        success: function(response) {
                            if (response.status == 'success') {
                                Swal.fire('', 'Reimbursement Status Updated', 'success');
                                setTimeout(function() {
                                        location.reload();
                                    }, 2000);
                                }
                                else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Oops...',
                                        text: response.message,
                                    });
                                }
                            },
                        });
                });

            $('#rejectReimbursementModal').on('hidden.bs.modal', function(e) {
                // Reset the values of input fields
                $('#reason').val('');
            });
</script>
@endpush