@extends('layouts.app')
<meta name="csrf-token" content="{{ csrf_token() }}" />
@section('content')
<div class="nk-block-head nk-block-head-sm">
    <div class="nk-block-between">
        <div class="nk-block-head-content">
            <h3 class="nk-block-title page-title">Tasks</h3>
        </div><!-- .nk-block-head-content -->
        <div class="nk-block-head-content">
            <div class="toggle-wrap nk-block-tools-toggle">
                <a href="#" class="btn btn-icon btn-trigger toggle-expand mr-n1" data-target="more-options"><em class="icon ni ni-more-v"></em></a>
                <div class="toggle-expand-content" data-content="more-options">
                    <ul class="nk-block-tools g-3">
                        <li class="nav-item">
                            <a href="#" class="btn btn-trigger btn-icon toggle" data-target="filterTask">
                                <div class="dot dot-primary"></div>
                                <em class="icon ni ni-filter-alt"></em>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link btn btn-primary mr-2" href="{{ url('/task/create-task') }}"><em class="icon ni ni-plus" style="color: #fff;"></em><span>Add Task</span></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div><!-- .nk-block-between -->
    <div class="container justify-content-center mx-auto" style="margin: 10px;padding:15px;">
        <div class="card-deck">
            <div class="card" style="margin: 0 10px 20px 0;box-sizing: border-box;float: left;width: calc(100% / 3 - 20px); ">
                <div class="card-body" style="text-align: center;">
                    <div class="card-title">Total Task</div>
                    <div style="font-weight: bold; text-align: center;">{{$count['totalTask'] ?? ''}}</div>
                </div>
            </div>
            <div class="card" style="margin: 0 10px 20px 0;box-sizing: border-box; float: left;width: calc(100% / 3 - 20px);">
                <div class="card-body" style="text-align: center;">
                    <div class="card-title">Unassigned Task</div>
                    <div style="font-weight: bold; text-align: center;">{{$count['unassignedTask'] ?? ''}}</div>
                </div>
            </div>
            <div class="card" style="margin: 0 10px 20px 0; box-sizing: border-box;float: left;width: calc(100% / 3 - 20px);">
                <div class="card-body" style="text-align: center;">
                    <div class="card-title">Assigned Task</div>
                    <div style="font-weight: bold; text-align: center;">{{$count['assignedTask'] ?? ''}}</div>
                </div>
            </div>
            <div class="card" style="margin: 0 10px 20px 0;box-sizing: border-box;float: left;width: calc(100% / 3 - 20px); ">
                <div class="card-body" style="text-align: center;">
                    <div class="card-title">In Progress Task</div>
                    <div style="font-weight: bold; text-align: center;">{{$count['inProgressTask'] ?? ''}}</div>
                </div>
            </div>
            <div class="card" style="margin: 0 10px 20px 0;box-sizing: border-box;float: left;width: calc(100% / 3 - 20px); ">
                <div class="card-body" style="text-align: center;">
                    <div class="card-title">Completed Task</div>
                    <div style="font-weight: bold; text-align: center;">{{$count['completedTask'] ?? ''}}</div>
                </div>
            </div>
            <div class="card" style="margin: 0 10px 20px 0; box-sizing: border-box;float: left;width: calc(100% / 3 - 20px);">
                <div class="card-body" style="text-align: center;">
                    <div class="card-title">Cancelled Task</div>
                    <div style="font-weight: bold; text-align: center;">{{$count['cancelledTask'] ?? ''}}</div>
                </div>
            </div>
        </div>
    </div>
</div><!-- .nk-block-head -->
<!--  Filter Tag List -->
<div id="filter_tag_list" class="filter-tag-list"></div>
<div class="nk-block table-compact">
    <div class="nk-tb-list is-separate mb-3">
        <table id="brand_init" class="brand-init nowrap nk-tb-list is-separate" data-auto-responsive="false">
            <thead>
                <tr class="nk-tb-item nk-tb-head">
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Task Title</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Created By</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Role</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">District</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Date</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Assigned To</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Priority</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Status</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Action</span></th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div><!-- .nk-tb-list -->
</div><!-- .nk-block -->
<div class="nk-add-product toggle-slide toggle-slide-right" tabindex="-1" data-content="filterTask" data-toggle-screen="any" data-toggle-overlay="true" data-toggle-body="true" data-simplebar id="filterTask">
    <div class="nk-block-head">
        <div class="nk-block-head-content">
            <h5 class="nk-block-title modalTitle">Task Filter</h5>
        </div>
    </div>
    <div class="nk-block">
        <form action="#" role="form" class="mb-0" method="get" id="taskFilterForm">
            @csrf
            <div class="row g-3">
                <div class="col-mb-12">
                    <input type="text" class="form-control" id="taskTitle" name="taskTitle" placeholder="Enter Task Title" onkeypress="return isCharSpace(event)">
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
                    <x-inputs.select size="md" name="priority" for="priority" data-search="on" placeholder='Select Priority'>
                        <option value="" selected disabled>Select Priority</option>
                        <option value="high">High</option>
                        <option value="medium">Medium</option>
                        <option value="low">Low</option>
                    </x-inputs.select>
                </div>
                <div class="col-mb-12">
                    <x-inputs.select size="md" name="status" for="status" data-search="on" placeholder='Select Status'>
                        <option value="" selected disabled>Select Status</option>
                        <option value="Created">Created</option>
                        <option value="Assigned">Assigned</option>
                        <option value="In Progress">In Progress</option>
                        <option value="Completed">Completed</option>
                        <option value="Cancelled">Cancelled</option>
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
                <div class="col-mb-12">
                    <x-inputs.select size="md" name="created_byuser_id" for="created_byuser_id" data-search="on" placeholder='Select User'>
                        <option value="" selected disabled>Select User</option>
                        @php
                        $users = Helpers::getAllUsers();
                        @endphp
                        @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->first_name.' '.$user->last_name }}
                        </option>
                        @endforeach
                    </x-inputs.select>
                </div>
            </div>
            <div class="col-12 text-center mt-5">
                <a class="btn btn-outline-light cancel" data-target='filterTask'>Cancel</a>
                <a class="btn btn-danger resetFilter" data-target='filterTask' style="color:#fff">Clear
                    Filter</a>
                <a class="btn btn-primary submitBtnFilter" style="color:#fff">Submit</a>
            </div>
        </form>
    </div>
</div>
@endsection
@push('footerScripts')
<script src="{{ url('js/tableFlow.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function() {

        $(document).on("click", function(event) {
            var overlay = $("#filterTask");
            if (!overlay.is(event.target) && overlay.has(event.target).length === 0) {
                //resetForm();
            }
        });

        function resetForm() {
            // Assuming the form has the id "myForm"
            $('#taskFilterForm')[0].reset();
        }

        $('#fromDate').on('change', function() {
            // Get the selected value of fromDate
            var fromDateValue = $(this).val();

            $('#toDate').val(fromDateValue);

        });

        var items = [
            '#taskTitle', '#role_id', '#priority', '#status', '#created_byuser_id', '#fromDate', '#toDate',
        ];
        var dt = '';

        dt = new CustomDataTable({
            tableElem: '.brand-init',
            option: {
                processing: true,
                serverSide: true,
                ajax: {
                    type: "GET",
                    url: "{{ url('task') }}",
                },
                columns: [{
                        "class": "nk-tb-col tb-col-lg",
                        data: null,
                        render: function(data, type, row) {
                            var titleValue = row['title'];

                            return titleValue || 'NA';
                        },
                        name: 'title',
                        orderable: false
                    },
                    {
                        "class": "nk-tb-col tb-col-lg",
                        data: null,
                        render: function(data, type, row) {
                            var taskCreatedByValue = row['task_created_by'];

                            return taskCreatedByValue || 'NA';
                        },
                        name: 'task_created_by',
                        orderable: false
                    },
                    {
                        "class": "nk-tb-col tb-col-lg",
                        data: null,
                        render: function(data, type, row) {
                            var taskCreatedUserRoleValue = row['task_created_user_role'];

                            return taskCreatedUserRoleValue || 'NA';
                        },
                        name: 'task_created_user_role',
                        orderable: false
                    },
                    {
                        "class": "nk-tb-col tb-col-lg",
                        data: null,
                        render: function(data, type, row) {
                            var taskCreatedUserDistrictValue = row['task_created_user_district'];

                            return taskCreatedUserDistrictValue || 'NA';
                        },
                        name: 'task_created_user_district',
                        orderable: false
                    },
                    {
                        "class": "nk-tb-col tb-col-lg",
                        data: null,
                        render: function(data, type, row) {
                            var taskCreatedDateValue = row['task_created_date'];

                            return taskCreatedDateValue || 'NA';
                        },
                        name: 'task_created_date',
                        orderable: true
                    },
                    {
                        "class": "nk-tb-col tb-col-lg",
                        data: null,
                        render: function(data, type, row) {
                            var assignedTo = row['task_assigned_to'];
                            var assignedRole = row['task_assigned_user_role'];

                            if (assignedTo || assignedRole) {
                                return assignedTo + ' (' + (assignedRole || 'NA') + ')';
                            } else {
                                return 'NA';
                            }
                        },
                        name: 'task_assigned_to',
                        orderable: false
                    },

                    {
                        "class": "nk-tb-col tb-col-lg",
                        data: null,
                        render: function(data, type, row) {
                            var priorityValue = row['priority'];

                            return priorityValue || 'NA';
                        },
                        name: 'priority',
                        orderable: false
                    },
                    {
                        "class": "nk-tb-col tb-col-lg",
                        data: null,
                        render: function(data, type, row) {
                            var statusValue = row['status'];

                            return statusValue || 'NA';
                        },
                        name: 'status',
                        orderable: false
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
                NioApp.Toggle.collapseDrawer('filterTask')
            },
            filterClearSubmit: '.resetFilter',
            filterModalId: '#filterTask',
            filterItems: items,
            tagId: '#filter_tag_list',
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

        $('.cancel').on('click', function() {
            NioApp.Toggle.collapseDrawer('filterTask')
        })
    });
</script>
@endpush