@extends('layouts.app')
<meta name="csrf-token" content="{{ csrf_token() }}" />
@section('content')
<div class="nk-block-between">
    <div class="nk-block-head-content">
        <h5 class="nk-block-title page-title">Expenses </h5>
    </div>
</div>
<div class="toggle-bar">
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link active" data-toggle="tab" href="#ambulance" data-tab="all">Ambulance</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#other" data-tab="pending">Other</a>
        </li>
    </ul>
</div>
<div class="tab-content">
    <div class="tab-pane fade show active mt-5" id="ambulance">
        <div class="nk-block table-compact">
            <div class="toggle-wrap nk-block-tools-toggle" style="float: right;">
                <div class="toggle-expand-content" data-content="more-options">
                    <ul class="nk-block-tools ">
                        <li class="nk-block-tools-opt">
                            <a href="#" data-target="filterExpense" class="toggle btn btn-trigger btn-icon">
                                <div class="dot dot-primary"></div>
                                <em class="icon ni ni-filter-alt"></em>
                            </a>
                        </li>
                        <li>
                            <a class="nav-link btn btn-primary d-none d-md-inline-flex ml-2 export" href="#" style="position: relative;letter-spacing: 0.02em;display: inline-flex;align-items: center;color: #fff; float: right; background-color: #1849ba;border-color: #1849ba;"><em class="icon ni ni-file-download" style="color: #fff;"></em><span>Export</span></a>
                        </li>

                    </ul>
                </div>
            </div>
            <div id="filter_tag_list" class="filter-tag-list"></div>

            <div class="nk-tb-list is-separate mt-3">
                <table id="brand_init" class="brand-init nowrap nk-tb-list is-separate" data-auto-responsive="false">
                    <thead>
                        <tr class="nk-tb-item nk-tb-head">
                            <th class="nk-tb-col tb-col-mb"><span class="sub-text">Vehicle Number</span></th>
                            <th class="nk-tb-col tb-col-mb"><span class="sub-text">Entry Type</span></th>
                            <th class="nk-tb-col tb-col-mb"><span class="sub-text">Expense Type</span></th>
                            <th class="nk-tb-col tb-col-mb"><span class="sub-text">Amount</span></th>
                            <th class="nk-tb-col tb-col-mb"><span class="sub-text">Expense Date</span></th>
                            <th class="nk-tb-col tb-col-mb"><span class="sub-text">Claim Status</span></th>
                            <th class="nk-tb-col tb-col-mb"><span class="sub-text">Reimbursment Status</span></th>
                            <th class="nk-tb-col tb-col-mb"><span class="sub-text">Action</span></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="tab-pane fade" id="other">
        <div class="nk-block table-compact">
            <div class="nk-tb-list is-separate mt-3">
                <table id="pending_requestsId" class="pending_requestsId nowrap nk-tb-list is-separate" data-auto-responsive="false">
                    <thead>
                        <tr class="nk-tb-item nk-tb-head">
                            <th class="nk-tb-col tb-col-mb"><span class="sub-text">Vehicle Number</span></th>
                            <th class="nk-tb-col tb-col-mb"><span class="sub-text">Entry Type</span></th>
                            <th class="nk-tb-col tb-col-mb"><span class="sub-text">Expense Type</span></th>
                            <th class="nk-tb-col tb-col-mb"><span class="sub-text">Amount</span></th>
                            <th class="nk-tb-col tb-col-mb"><span class="sub-text">Date</span></th>
                            <th class="nk-tb-col tb-col-mb"><span class="sub-text">Status</span></th>
                            <th class="nk-tb-col tb-col-mb"><span class="sub-text">Reimbursment</span></th>
                            <th class="nk-tb-col tb-col-mb"><span class="sub-text">Action</span></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="nk-add-product toggle-slide toggle-slide-right" tabindex="-1" data-content="filterExpense" data-toggle-screen="any" data-toggle-overlay="true" data-toggle-body="true" data-simplebar id="filterExpense">
    <form action="#" role="form" class="mb-0" method="get" id="expenseFilterForm">
        @csrf
        <div class="nk-block-head">
            <div class="nk-block-head-content">
                <h5 class="nk-block-title modalTitle">Expense Filter</h5>
            </div>
        </div>
        <div class="nk-block">
            <div class="row g-3">
                <div class="col-mb-12">
                    <x-inputs.select size="md" name="vehicle_id" for="vehicle_id" id="vehicle_id" data-search="on" placeholder='Select Vehicle Number'>
                        <option value="" selected disabled>Select Vehicle Number</option>
                        @if(!empty($ambulances))
                        @foreach($ambulances as $key => $ambulance)
                        <option value="{{$ambulance->id}}">{{$ambulance->ambulance_no ?? $ambulance->chassis_no }}</option>
                        @endforeach
                        @endif
                    </x-inputs.select>
                </div>

                <!-- Dropdown for Entry Type Range -->
                <div class="col-mb-12">
                    <x-inputs.select size="md" name="entry_type" for="entry_type" id="entry_type" data-search="on" placeholder='Select Entry Type'>
                        <option value="" selected disabled>Select Entry Type</option>
                        <option value="Claim">Claim</option>
                        <option value="Record">Record</option>
                    </x-inputs.select>
                </div>

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
                <!-- Dropdown for Status -->
                <div class=" col-mb-12">
                    <x-inputs.select size="md" name="status" for="status" id="status" data-search="on" placeholder='Select Status'>
                        <option value="" selected disabled>Select Status</option>
                        <option value="Pending">Pending</option>
                        <option value="Approved">Approved</option>
                        <option value="Rejected">Rejected</option>
                    </x-inputs.select>
                </div>

                <div class="col-mb-12">
                    <x-inputs.select size="md" name="reimbursment_status" for="reimbursment_status" id="reimbursment_status" data-search="on" placeholder='Select Reimbursment Status'>
                        <option value="" selected disabled>Select Reimbursment Status</option>
                        <option value="Pending">Pending</option>
                        <option value="Completed">Completed</option>
                        <option value="Approved">Approved</option>
                        <option value="Rejected">Rejected</option>
                    </x-inputs.select>
                </div>

                <div class="row g-3">
                    <div class="col-mb-6">
                        <input type="text" placeholder="Select From date" class="form-control" name="fromDate" id="fromDate" readonly />
                    </div>
                    <div class="col-mb-6">
                        <input type="text" placeholder="Select To date" name="toDate" class="form-control" id="toDate" readonly />
                    </div>
                </div>

            </div>
            <div class="col-12 text-center mt-5">
                <a data-target='filterExpense' class="btn btn-outline-light cancel">Cancel</a>
                <a class="btn btn-danger resetFilter" data-target='filterExpense' style="color:#fff">Clear
                    Filter</a>
                <a class="btn btn-primary submitBtnFilter" style="color:#fff">Submit</a>
            </div>
        </div>

    </form>
</div>
@endsection
@push('footerScripts')
<script src="{{url('js/tableFlow.js')}}"></script>
<script>
    function resetForm() {
        $('#expenseFilterForm')[0].reset();
        $('#vehicle_id').val(0).trigger("change");
        $('#entry_type').val(0).trigger("change");
        $('#expense_type_id').val(0).trigger("change");
        $('#status').val(0).trigger("change");
        $('#reimbursment_status').val(0).trigger("change");
        $('#fromDate').val('').trigger("change");
        $('#toDate').val('').trigger("change");
    }

    $(document).on("click", function(event) {
        var overlay = $("#filterExpense");
        if (!overlay.is(event.target) && overlay.has(event.target).length === 0) {
            //resetForm();
        }
    });

    $(document).ready(function() {
        $(".resetFilter").click();
        var items = [
            '#vehicle_id', '#entry_type', '#status', '#expense_type_id', '#reimbursment_status', '#fromDate', '#toDate'
        ];
        var dt = '';

        dt = new CustomDataTable({
            tableElem: '.brand-init',
            option: {
                processing: true,
                serverSide: true,
                ajax: {
                    type: "GET",
                    url: "{{ url('human-resource/expenses/get-expense-list') }}",
                },
                columns: [{
                        "class": "nk-tb-col tb-col-lg",
                        data: null,
                        render: function(data, type, row) {
                            var vechicle_no = row['vechicle_no'];

                            return vechicle_no || 'NA';
                        },
                        name: 'vechicle_no',
                        orderable: true
                    },
                    {
                        "class": "nk-tb-col tb-col-lg",
                        data: null,
                        render: function(data, type, row) {
                            var entry_type = row['entry_type'];

                            return entry_type || 'NA';
                        },
                        name: 'entry_type',
                        orderable: true
                    },
                    {
                        "class": "nk-tb-col tb-col-lg",
                        data: null,
                        render: function(data, type, row) {
                            var expense_type_name = row['expense_type_name'];

                            return expense_type_name || 'NA';
                        },
                        name: 'expense_type_name',
                        orderable: true
                    },
                    {
                        "class": "nk-tb-col tb-col-lg",
                        data: null,
                        render: function(data, type, row) {
                            var amount = row['amount'];

                            return amount || 'NA';
                        },
                        name: 'amount',
                        orderable: true
                    },
                    {
                        "class": "nk-tb-col tb-col-lg",
                        data: null,
                        render: function(data, type, row) {
                            var expense_date = row['expense_date'];

                            return expense_date || 'NA';
                        },
                        name: 'expense_date',
                        orderable: true
                    },
                    {
                        "class": "nk-tb-col tb-col-lg",
                        data: null,
                        render: function(data, type, row) {
                            var claim_status = row['claim_status'];

                            return claim_status || 'NA';
                        },
                        name: 'claim_status',
                        orderable: true
                    },
                    {
                        "class": "nk-tb-col tb-col-lg",
                        data: null,
                        render: function(data, type, row) {
                            var reimbursement_status = row['reimbursement_status'];

                            return reimbursement_status || 'NA';
                        },
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
                NioApp.Toggle.collapseDrawer('filterExpense')
            },
            filterClearSubmit: '.resetFilter',
            filterModalId: '#filterExpense',
            filterItems: items,
            tagId: '#filter_tag_list',
        });
        $('.cancel').on('click', function() {
           // resetForm();
            NioApp.Toggle.collapseDrawer('filterExpense')
        })

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
            format: "mm/dd/yyyy",
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

        $('.export').on('click', function(e) {
            var fromDate = $('#fromDate').val();
            var toDate = $('#toDate').val();
            var vehicle_id = $('#vehicle_id').val();
            var entry_type = $('#entry_type').val();
            var status = $('#status').val();
            var expense_type_id = $('#expense_type_id').val();
            var reimbursment_status = $('#reimbursment_status').val();

            $.ajax({
                url: "{{url('/human-resource/expenses/export-expense-grid')}}",
                type: 'GET',
                data: {
                    fromDate: fromDate,
                    toDate: toDate,
                    vehicle_id: vehicle_id,
                    entry_type: entry_type,
                    status: status,
                    expense_type_id: expense_type_id,
                    reimbursment_status: reimbursment_status

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
                    link.download = "expense_report.xlsx";

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
    });
</script>
@endpush