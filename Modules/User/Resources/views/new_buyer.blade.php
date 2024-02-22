@extends('layouts.app')

@section('content')
@php
    $userPermission = \Session::get('userPermission');
@endphp
	<div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">New Registrations</h3>
                <p>You have total <span class="buyersCount">{{ $buyersCount }}</span> new registrations.</p>
            </div><!-- .nk-block-head-content -->
            <div class="nk-block-head-content">
                <div class="toggle-wrap nk-block-tools-toggle">
                    <a href="#" class="btn btn-icon btn-trigger toggle-expand mr-n1" data-target="more-options"><em class="icon ni ni-more-v"></em></a>
                    <div class="toggle-expand-content" data-content="more-options">
                        <ul class="nk-block-tools g-3">
                            <li>
                                <div class="form-inline flex-nowrap input-group gx-3">
                                    <select class="form-select form-select-sm" data-search="off" data-placeholder="Bulk Action" id="mass-status">
                                        <option value="" selected disabled>Bulk Action</option>
                                        <option value="1">Approve</option>
                                        <option value="0">Unapprove</option>
                                    </select>
                                    <div class="input-group-prepend">
                                        <span class="d-none d-md-block"><button class="btn btn-primary" name="submit_btn" id="mass-update" onclick="updateMassItems()">Apply</button></span>
                                        <span class="d-md-none"><button class="btn btn-dim btn-outline-light btn-icon disabled" onclick="updateMassItems()"><em class="icon ni ni-arrow-right"></em></button></span>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <a href="#" class="btn btn-trigger btn-icon dropdown-toggle" data-toggle="modal" title="filter" data-target="#modalFilterUser">
                                    <div class="dot dot-primary"></div>
                                    <em class="icon ni ni-filter-alt"></em>
                                </a>
                            </li>
                            <li>
                                <div class="dropdown">
                                    <a href="#" class="btn btn-trigger btn-icon dropdown-toggle" data-toggle="dropdown">
                                        <em class="icon ni ni-setting"></em>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-xs dropdown-menu-right">
                                        <ul class="link-check">
                                            <li><span>Actions</span></li>
                                            {{-- <li><a href="#"><em class="icon ni ni-download m-r10"></em> Export</a></li> --}}
                                            <li><a href="#"><em class="icon ni ni-upload m-r10"></em> Import</a></li>
                                            </ul>
                                    </div>
                                </div>
                            </li>

                            @if(isset($userPermission['buyer']) && ($userPermission['buyer']['edit_all'] || $userPermission['buyer']['edit_own']))
                            <li class="nk-block-tools-opt">
                               <!--  <a href="{{url('/user/create-staff')}}" class="btn btn-icon btn-primary d-md-none"><em class="icon ni ni-plus"></em></a> -->
                                <a href="{{url('/user/create')}}" class="btn btn-primary d-none d-md-inline-flex"><em class="icon ni ni-plus"></em><span>Add Buyer</span></a>
                            </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div><!-- .nk-block-between -->
    </div><!-- .nk-block-head -->
    <div class="card card-preview">
        <div class="card-inner pt-2 pl-2 pb-0">
            <ul class="nav nav-tabs mt-n3 bdr-btm-none">
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/user') }}">Buyers</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="{{ url('user/new') }}">New Registrations <span class="badge badge-info badge-pill buyersCount">{{ $buyersCount }}</span></a>
                </li>

            </ul>
        </div>
    </div><!-- .card-preview -->
    <div class="nk-block table-compact pt-28">
        <!--  Filter Tag List -->
        <div id="filter_tag_list" class="filter-tag-list"></div>
        <!-- -->
        <div class="nk-tb-list is-separate mb-3">
            <table class="broadcast-init nowrap nk-tb-list is-separate" data-auto-responsive="false">
                <thead>
                    <tr class="nk-tb-item nk-tb-head">
                        <th class="nk-tb-col nk-tb-col-check">
                            <div class="custom-control custom-control-sm custom-checkbox notext">
                                <input type="checkbox" class="custom-control-input" id="check-all" name="check_all"><label class="custom-control-label" for="check-all"></label>
                            </div>
                        </th>
                        <th class="nk-tb-col tb-col-mb"><span class="sub-text">User</span></th>
                        <th class="nk-tb-col tb-col-mb"><span class="sub-text">Created By</span></th>
                        <th class="nk-tb-col tb-col-md"><span class="sub-text">Approved</span></th>
                        <th class="nk-tb-col tb-col-md"><span class="sub-text">Contact Number</span></th>
                        <th class="nk-tb-col tb-col-md w-1" nowrap="true"><span class="sub-text">Created At</span></th>
                        <th class="nk-tb-col tb-col-md w-1" nowrap="true"><span class="sub-text">Updated At</span></th>
                        <th class="nk-tb-col nk-tb-col-tools w-1 text-right" nowrap="true">
                            <span class="sub-text">Action</span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>

        </div><!-- .nk-block -->
        <div class="modal fade zoom" tabindex="-1" id="modalFilterUser">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Filter</h5>
                        <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                            <em class="icon ni ni-cross"></em>
                        </a>
                    </div>
                    <form role="form" class="mb-0" method="get" action="#">
                    @csrf
                    <div class="modal-body modal-body-lg">
                        <div class="gy-3">
                            <div class="row g-3 align-center">
                                <div class="col-lg-5">
                                    <x-inputs.verticalFormLabel label="First Name" for="firstName" suggestion="Specify the first name of the user." />
                                </div>
                                <div class="col-lg-7">
                                    <x-inputs.text value="" for="firstName" icon="user" placeholder="First name" name="firstname"/>
                                </div>
                            </div>
                            <div class="row g-3 align-center">
                                <div class="col-lg-5">
                                    <x-inputs.verticalFormLabel label="Last Name" for="lastName" suggestion="Specify the last name of the user."  />
                                </div>
                                <div class="col-lg-7">
                                    <x-inputs.text value="" for="lastName" icon="user" placeholder="Last name" name="lastname"/>
                                </div>
                            </div>
                            <div class="row g-3 align-center">
                                <div class="col-lg-5">
                                    <x-inputs.verticalFormLabel label="Mobile Number" for="mobileNumber" suggestion="Specify the mobile number of the user."  />
                                </div>
                                <div class="col-lg-7">
                                    <x-inputs.text value="" for="mobileNumber" icon="call" placeholder="Mobile Number" name="mobileNumber"
                                    data-parsley-pattern="{{ \Config::get('constants.REGEX.VALIDATE_MOBILE_NUMBER_LENGTH') }}"
                                    />
                                </div>
                            </div>
                            <div class="row g-3 align-center">
                                <div class="col-lg-5">
                                    <x-inputs.verticalFormLabel label="Created at" for="createdAt" suggestion="Select the dates of created at." />
                                </div>
                                <div class="col-lg-7">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-control-wrap">
                                                <div class="form-icon form-icon-left">
                                                    <em class="icon ni ni-calendar"></em>
                                                </div>
                                                <input type="text" class="form-control date-picker" placeholder="Form Date" data-date-format="yyyy-mm-dd" id="fromDate">
                                            </div>
                                            <!-- <div class="form-note mt-0">Form Date</div> -->
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-control-wrap">
                                                <div class="form-icon form-icon-left">
                                                    <em class="icon ni ni-calendar"></em>
                                                </div>
                                                <input type="text" class="form-control date-picker" placeholder="To Date" data-date-format="yyyy-mm-dd"  id="toDate">
                                            </div>
                                            <!-- <div class="form-note mt-0">To Date</div> -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" id="userId" name="user_id" value="0">
                    <div class="modal-footer bg-light">
                        <div class="row">
                            <div class="col-lg-12 p-0 text-right">
                                <button class="btn btn-outline-light" data-dismiss="modal" aria-label="Close">Cancel</button>
                                <button class="btn btn-danger resetFilter" data-dismiss="modal" aria-label="Close">Clear Filter</button>
                                <button class="btn btn-primary submitBtn" type="button">Submit</button>
                            </div>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
<script type="text/javascript">
    $('.eg-swal-av3').on("click", function (e) {
        Swal.fire({
          title: 'Are you sure?',
          text: "You won't be able to revert this!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Yes, delete it!'
        }).then(function (result) {
          if (result.value) {
            Swal.fire('Deleted!', 'Your file has been deleted.', 'success');
          }
        });
        e.preventDefault();
      });
</script>
@endsection
@push('footerScripts')
<script type="text/javascript">

    function updateMassItems() {
        var arr = [];
        $('input.cb-check:checkbox:checked').each(function() {
            arr.push($(this).val());
        });

        var status = $('#mass-status').find(":selected").val();
        if (arr.length == 0) {
            Swal.fire("Please select a buyer first !");
        } else if (status == "") {
            Swal.fire("Please select bulk status !");
        } else {
            var root_url = "<?php echo Request::root(); ?>";
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: root_url + '/user/bulk-approve',
                data: {
                    'ids': arr,
                    'status': status
                },
                //dataType: "html",
                method: "POST",
                cache: false,
                success: function(data) {
                    console.log(data);
                    
                    if(data.success){

                        $('.buyersCount').text(data.newBuyers);

                        $('#check-all').prop('checked', false);
                        var table = $('.broadcast-init').DataTable();
                        table.ajax.reload();

                        Swal.fire(
                            'Good job!',
                            'Status updated successfully.',
                            'success'
                        )

                    } else {

                        $('#check-all').prop('checked', false);
                        var table = $('.broadcast-init').DataTable();
                        table.ajax.reload();
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: data.msg,
                            //footer: '<a href>Why do I have this issue?</a>'
                        })
                    }
                }
            });
        }
    }

    $(function() {
        var user_table = ""; 
        user_table = NioApp.DataTable('.broadcast-init', {
            processing: true,
            serverSide: true,
            ajax: {
                type:"GET",
                url: "{{ url('user/new') }}",
                data: function (d) {
                    d.firstname = $('#firstName').val()
                    d.lastname = $('#lastName').val()
                    d.phone_number = $('#mobileNumber').val()
                    d.fromDate = $('#fromDate').val()
                    d.toDate = $('#toDate').val()

                    var items = [
                        '#firstName',
                        '#lastName',
                        '#mobileNumber',
                        '#fromDate',
                        '#toDate'
                    ];
                    NioApp.filterTag(items, user_table['.broadcast-init'], '#filter_tag_list');
                }
            },
            "fnDrawCallback":function(){
                NioApp.BS.tooltip('[data-toggle="tooltip"]'); 
            },
            columns: [{
                    "class": "nk-tb-col tb-col-lg nk-tb-col-check",
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row, meta) {
                        return '<td class="nk-tb-col nk-tb-col-check"><div class="custom-control custom-control-sm custom-checkbox notext"><input type="checkbox" class="custom-control-input cb-check" id="cb-' + row.id + '" value="' + row.id + '" name="checked_items[]"><label class="custom-control-label" for="cb-' + row.id + '"></label></div></td>'
                    }
                },
                {
                    "class": "nk-tb-col tb-col-lg",
                    data: 'name',
                    name: 'name'
                },
                {
                    "class": "nk-tb-col tb-col-lg",
                    data: 'created_by',
                    name: 'created_by'
                },
                {
                    "class": "nk-tb-col tb-col-lg",
                    data: 'status',
                    name: 'status'
                },
                {
                    "class": "nk-tb-col tb-col-lg",
                    data: 'phone_number',
                    name: 'phone_number'
                },
                {
                    "class": "nk-tb-col tb-col-lg",
                    data: 'created_at',
                    name: 'created_at'
                },
                {
                    "class": "nk-tb-col tb-col-lg",
                    data: 'updated_at',
                    name: 'updated_at'
                },
                {
                    "class": "nk-tb-col tb-col-lg text-right",
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ]
        });

        console.log(user_table['.broadcast-init'],'table data');
        $('.submitBtn').click(function(){
            user_table['.broadcast-init'].draw();
            $('#modalFilterUser').modal('toggle');
        });

        $('.broadcast-init').on("change","#check-all",function() {
            // If checked
            if (this.checked)
                $('.cb-check').prop('checked', true);
            else
                $('.cb-check').prop('checked', false);
        });

        NioApp.resetModalForm('#modalFilterUser', user_table['.broadcast-init'], '.resetFilter');
    });
</script>
@endpush