@extends('layouts.app')
@section('content')
<div class="nk-block-head nk-block-head-sm">
    <div class="nk-block-between">
        <div class="nk-block-head-content">
            <h3 class="nk-block-title page-title">Organization</h3>
            <p>You have total {{ $count }} organization.</p>
        </div><!-- .nk-block-head-content -->
        <div class="nk-block-head-content">
            <div class="toggle-wrap nk-block-tools-toggle">
                <a href="#" class="btn btn-icon btn-trigger toggle-expand mr-n1" data-target="more-options"><em class="icon ni ni-more-v"></em></a>
                <div class="toggle-expand-content" data-content="more-options">
                    <ul class="nk-block-tools g-3">
                        <li>
                            <div class="form-inline flex-nowrap input-group gx-3">
                                <select class="form-select" data-search="off" data-placeholder="Bulk Action" id="mass-status">
                                    <option value="" selected disabled>Bulk Action</option>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                                <div class="input-group-prepend">
                                    <span class="d-none d-md-block"><button class="btn btn-primary" name="submit_btn" id="mass-update" onclick="updateMassItems()">Apply</button></span>
                                    <span class="d-md-none"><button class="btn btn-dim btn-outline-light btn-icon disabled"><em class="icon ni ni-arrow-right"></em></button></span>
                                </div>
                            </div>
                        </li>
                        <li>
                            <a href="#" class="btn btn-trigger btn-icon dropdown-toggle" data-toggle="modal" title="filter" data-target="#modalFilterOrganization">
                                <div class="dot dot-primary"></div>
                                <em class="icon ni ni-filter-alt"></em>
                            </a>
                        </li>
                        <li class="nk-block-tools-opt">
                            <a href="{{url('/administration/organization-create')}}" class="btn btn-icon btn-primary d-md-none"><em class="icon ni ni-plus"></em></a>
                            <a href="{{url('/administration/organization-create')}}" class="btn btn-primary d-none d-md-inline-flex"><em class="icon ni ni-plus"></em><span>Create Organization</span></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div><!-- .nk-block-between -->
</div><!-- .nk-block-head -->
<div class="nk-block table-compact">
    <div class="nk-tb-list is-separate is-medium mb-3">
        <table class="organization-init nowrap nk-tb-list is-separate" data-auto-responsive="false">
            <thead>
                <tr class="nk-tb-item nk-tb-head">
                    <th class="nk-tb-col nk-tb-col-check">
                        <div class="custom-control custom-control-sm custom-checkbox notext">
                            <input type="checkbox" class="custom-control-input" id="check-all" name="check_all"><label class="custom-control-label" for="check-all"></label>
                        </div>
                    </th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Organization</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Organization Tin</span></th>
                    {{-- <th class="nk-tb-col tb-col-mb"><span class="sub-text">Owner Name</span></th> --}}
                    <th class="nk-tb-col tb-col-mb w-1 text-center" nowrap="true"><span class="sub-text">Status</span></th>
                    <th class="nk-tb-col tb-col-md w-1" nowrap="true"><span class="sub-text">Created At</span></th>
                    <th class="nk-tb-col nk-tb-col-tools text-right w-1" nowrap="true">
                        <span class="sub-text">Action</span>
                    </th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div><!-- .nk-tb-list -->
</div><!-- .nk-block -->
<div class="modal fade zoom" tabindex="-1" id="modalFilterOrganization">
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
                                <x-inputs.verticalFormLabel label="Organization Name" for="organizationName" suggestion="Specify the organization name." />
                            </div>
                            <div class="col-lg-7">
                                <x-inputs.text value="" for="organizationName" icon="user" placeholder="Organization Name" name="organizationName" />
                            </div>
                        </div>
                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <x-inputs.verticalFormLabel label="Owner Name" for="ownerName" suggestion="Specify the owner name." />
                            </div>
                            <div class="col-lg-7">
                                <x-inputs.text value="" for="ownerName" icon="user" placeholder="Owner Name" name="ownerName" />
                            </div>
                        </div>
                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <x-inputs.verticalFormLabel label="Organization Tin" for="organizationTin" suggestion="Specify the organization tin." />
                            </div>
                            <div class="col-lg-7">
                                <x-inputs.text value="" for="organizationTin" placeholder="Organization Tin" name="organizationTin" />
                            </div>
                        </div>
                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <x-inputs.verticalFormLabel label="Status" for="Status" suggestion="Select the status of the organization." />
                            </div>
                            <div class="col-lg-7">
                                <x-inputs.select size="sm" name="status" for="status">
                                    <option value="">Select</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </x-inputs.select>
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
<div class="modal fade zoom" tabindex="-1" id="modalLogs">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Audit Logs</h5>
                <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
            </div>
            <div class="modal-body modal-body-lg">
                <div class="timeline">                                  
                    <ul id="orderLogs" class="timeline-list">
                    </ul>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <div class="row">
                    <div class="col-lg-12 p-0 text-right">
                        <button class="btn btn-outline-light" data-dismiss="modal" aria-label="Close">Close</button>
                    </div>
                </div>
            </div>            
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
            Swal.fire("Please select a organization first !");
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
                url: root_url + '/administration/organization/bulk-update',
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
                        $('#check-all').prop('checked', false);
                        var table = $('.organization-init').DataTable();
                        table.ajax.reload();

                        Swal.fire(
                            'Good job!',
                            'Status updated successfully.',
                            'success'
                        )

                    } else {

                        $('#check-all').prop('checked', false);
                        var table = $('.organization-init').DataTable();
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

        var root_url = "<?php echo Request::root(); ?>";
        var logUrl = root_url + '/administration/organization/logs';
        NioApp.getAuditLogs('.organization-init','.audit_logs','resourceId',logUrl,'#modalLogs');

        var organization_table = ""; 
        organization_table = NioApp.DataTable('.organization-init', {
            processing: true,
            serverSide: true,
            ajax: {
                type:"GET",
                url: "{{ url('administration/organization') }}",
                data: function (d) {
                    d.name = $('#organizationName').val()
                    d.owner_name = $('#ownerName').val()
                    d.tin = $('#organizationTin').val()
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
                    data: 'tin',
                    name: 'tin'
                },
                /*{
                    "class": "nk-tb-col tb-col-lg",
                    data: 'owner_name',
                    name: 'owner_name'
                },*/
                {
                    "class": "nk-tb-col tb-col-lg text-center",
                    data: 'status',
                    name: 'status'
                },
                {
                    "class": "nk-tb-col tb-col-lg",
                    data: 'created_at',
                    name: 'created_at'
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

        console.log(organization_table['.organization-init'],'table data');
        $('.submitBtn').click(function(){
            organization_table['.organization-init'].draw();
            $('#modalFilterOrganization').modal('toggle');
        });

        $('.organization-init').on("change","#check-all",function() {
            // If checked
            if (this.checked)
                $('.cb-check').prop('checked', true);
            else
                $('.cb-check').prop('checked', false);
        });

        NioApp.resetModalForm('#modalFilterOrganization', organization_table['.organization-init'], '.resetFilter');
    });
</script>
@endpush