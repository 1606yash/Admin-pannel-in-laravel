@extends('layouts.app')
@section('content')
<div class="nk-block-head nk-block-head-sm">
    <div class="nk-block-between">
        <div class="nk-block-head-content">
            <h3 class="nk-block-title page-title">Organizations</h3>
            <div class="nk-block-des text-soft">
                @php 
                    if(count($organizations) > 1) 
                        $count = 'organizations';
                    else
                        $count = 'organization';                       
                @endphp
                <p>You have total {{ count($organizations) }} {{ $count }}.</p>
            </div>
        </div><!-- .nk-block-head-content -->
        <div class="nk-block-head-content">
            <div class="toggle-wrap nk-block-tools-toggle">
                <a href="#" class="btn btn-icon btn-trigger toggle-expand mr-n1" data-target="more-options"><em class="icon ni ni-more-v"></em></a>
                <div class="toggle-expand-content" data-content="more-options">
                    <ul class="nk-block-tools g-3">
                        <li class="nk-block-tools-opt">
                            <a href="#" class="btn btn-trigger btn-icon dropdown-toggle" data-toggle="modal" title="filter" data-target="#modalFilterOrganization">
                                <div class="dot dot-primary"></div>
                                <em class="icon ni ni-filter-alt"></em>
                            </a>
                        </li>
                        <li class="nk-block-tools-opt">
                            <a href="{{url('/saas/organization/add')}}" class="btn btn-primary d-none d-md-inline-flex"><em class="icon ni ni-plus"></em><span>Add Organization</span></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div><!-- .nk-block-between -->
</div><!-- .nk-block-head -->
<div class="nk-block table-compact">
    <div class="nk-tb-list is-separate mb-3">
         <table class="org-init nowrap nk-tb-list is-separate" data-auto-responsive="false">
            <thead>
                <tr class="nk-tb-item nk-tb-head">
                    <th class="nk-tb-col nk-tb-col-check">
                        <div class="custom-control custom-control-sm custom-checkbox notext">
                           #
                        </div>
                    </th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Organization Name</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Contact Person</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Contact Number</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Industry Type</span></th>
                    <th class="nk-tb-col tb-col-mb nk-tb-action-col text-center" nowrap="true"><span class="sub-text">Status</span></th>
                    <th class="nk-tb-col tb-col-mb nk-tb-action-col" nowrap="true"><span class="sub-text">Created at</span></th>
                    <th class="nk-tb-col nk-tb-col-tools text-right">
                        <span class="sub-text">Action</span>
                    </th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>       
    </div><!-- .nk-tb-list -->

</div><!-- .nk-block -->
@php
$filterAction = array(
array(
'label' => 'Clear Filter',
'type' => 'danger resetFilter',
'click' => '',
),
array(
'label' => 'Submit',
'type' => 'primary',
'click' => '',
)
);
@endphp
<x-ui.modal modalId="modalFilterOrganization" title="Filter" :footerActions="$filterAction">
    <form role="form" class="mb-0" method="get" action="#">
    @csrf
        <div class="modal-body modal-body-lg">
            <div class="gy-3">
                <div class="row align-top">
                    <div class="col-lg-5">
                        <x-inputs.verticalFormLabel label="Organization Name" for="organizationName" suggestion="Specify the organization name of the organization." required="false" />
                    </div>
                    <div class="col-lg-7">
                        <x-inputs.text value="" for="organizationName" icon="building" label="" required="false" placeholder="Organization Name" name="name"/>
                    </div>
                </div>
                <div class="row align-top">
                    <div class="col-lg-5">
                        <x-inputs.verticalFormLabel label="Contact Person" for="contactPerson" suggestion="Specify the contact person name of the organization." required="false" />
                    </div>
                    <div class="col-lg-7">
                        <x-inputs.text value="" for="contactPerson" icon="user" label="" required="false" placeholder="Contact Person" name="contact_person"/>
                    </div>
                </div>
                <div class="row  align-top">
                    <div class="col-lg-5">
                        <x-inputs.verticalFormLabel label="Industry Type" for="ratings" suggestion="Select the industry type of the organization." required="false" />
                    </div>
                    <div class="col-lg-7">
                        <div class="form-group">
                            <div class="form-control-wrap">
                                <select class="form-select form-control form-control-lg" data-placehoder="Select Industry Type" name="industry">
                                   <option></option>
                                    <option value="fmcg">FMCG</option>
                                    <option value="retailer">Retailer</option>
                                    <option value="automobile">Automobile</option>
                                    <option value="automotive">Automotive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row align-top">
                    <div class="col-lg-5">
                        <x-inputs.verticalFormLabel label="Created at" for="createdat" suggestion="Select the date range of the created organization." required="false" />
                    </div>
                    <div class="col-lg-7">
                        <div class="row">
                        	<div class="col-lg-6">
                        		<x-inputs.datePicker for="fromdate" size="sm" name="fromdate" placeholder="From Date" icon="calendar"/>
                        	</div>
                        	<div class="col-lg-6">
                        		<x-inputs.datePicker for="todate" size="sm" name="todate" placeholder="To Date" icon="calendar"/>
                        	</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</x-ui.modal>

<x-ui.modal modalId="modalOrganizationDetail" title="Organization Detail">
    <div class="modal-body modal-body-lg">
        <div class="gy-3">
            <div class="row">
            	<div class="col-lg-4">
            		<label class="form-label d-block">Industry Type</label>
            		<label id="industry"></label>
            	</div>
            	<div class="col-lg-4">
            		<label class="form-label d-block">Organization Name</label>
            		<label id="orgName"></label>
            	</div>
            	<div class="col-lg-4">
            		<label class="form-label d-block">Contact Person</label>
            		<label id="sellerName"></label>
            	</div>
            	<div class="col-lg-4">
            		<label class="form-label d-block">Contact Number</label>
            		<label>+91<span id="phonenumber"></span></label>
            	</div>
            	<div class="col-lg-4">
            		<label class="form-label d-block">Email</label>
            		<label id="email"></label>
            	</div>
            	<div class="col-lg-4">
            		<label class="form-label d-block">GST Number</label>
            		<label id="gst"></label>
            	</div>
            	<div class="col-lg-4">
            		<label class="form-label d-block">Street 1</label>
            		<label id="address1"></label>
            	</div>
            	<div class="col-lg-4">
            		<label class="form-label d-block">Country</label>
            		<label>India</label>
            	</div>
            	<div class="col-lg-4">
            		<label class="form-label d-block">State</label>
            		<label id="state"></label>
            	</div>
            	<div class="col-lg-4">
            		<label class="form-label d-block">City</label>
            		<label id="city"></label>
            	</div>
            	<div class="col-lg-4">
            		<label class="form-label d-block">Pincode</label>
            		<label id="pincode">452010</label>
            	</div>
            	<div class="col-lg-4">
            		<label class="form-label d-block">Status</label>
            		<label><span class="badge" id="org_status"></span></label>
            	</div>
            </div>
        </div>
    </div>
</x-ui.modal>

@endsection
@push('footerScripts')
<script type="text/javascript">
    function resetValues(){
        $('.submitBtn').text('Submit');
        $('.modalTitle').text('Add Brand');

        $('#itemId').val(0);
        $('#brName').val('');
        $('#brSlug').val('');
                
        $('body').removeClass('toggle-shown');
        $('#modal').removeClass('content-active');
        $('#itemId').val(0);
    }

    function updateMassItems() {
        var arr = [];
        $('input.cb-check:checkbox:checked').each(function () {
            arr.push($(this).val());
        });

        //
        var status = $('#mass-status').find(":selected").val();

        console.log(status);
        console.log(arr.length);

        if(arr.length==0){
            Swal.fire("Please select an organization first !");
        }else if(status == 0){
            Swal.fire("Please select bulk status !");
        }else{
            var root_url = "<?php echo Request::root(); ?>";
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: root_url + '/ecommerce/brands/mass-update',
                data: {'ids':arr,'status':status},
                //dataType: "html",
                method: "POST",
                cache: false,
                success: function (data) {
                    console.log(data);
                    
                    if(data.success){
                        
                        var table = $('.org-init').DataTable();
                        table.ajax.reload();

                        Swal.fire(
                          'Good job!',
                          'Item Updated Successfully.',
                          'success'
                        )

                    }else{

                        $('#check-all').prop('checked', false);
                        var table = $('.org-init').DataTable();
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
        
        return false;
    }
    var root_url = "<?php echo Request::root(); ?>";
    $(document).ready(function(){
        $( '.org-init' ).on( 'click', '.organizationDetails', function () {
            var org_id = $(this).attr('data-id');

            $.ajax({
                url: root_url + '/saas/organization/details/'+org_id,
                data: {},
                //dataType: "html",
                method: "GET",
                cache: false,
                success: function (data) {
                    if(data.success){
                        $('#industry').text(data.organization.industry);
                        $('#orgName').text(data.organization.organizationName);

                        var sellerName = data.organization.name+' '+data.organization.last_name;

                        $('#sellerName').text(sellerName);
                        $('#phonenumber').text(data.organization.phone_number);
                        $('#email').text(data.organization.email);
                        $('#gst').text(data.organization.gst);
                        $('#address1').text(data.organization.street_1);
                        $('#state').text(data.organization.state);
                        $('#city').text(data.organization.city);
                        $('#pincode').text(data.organization.pincode);

                        if(data.organization.status == 'active'){
                            $( "#org_status" ).removeClass( "badge-danger" );
                            $( "#org_status" ).addClass( "badge-success" );
                            $( "#org_status" ).text('Active');

                        }else{
                            $( "#org_status" ).removeClass( "badge-success" );
                            $( "#org_status" ).addClass( "badge-danger" );
                            $( "#org_status" ).text('Inactive');
                        }

                        $('#modalOrganizationDetail').modal('toggle');
                    }else{
                        Swal.fire('Details not found!')
                    }
                }
            });

            // $('#modalOrganizationDetail').modal('toggle');
        });
    });

     $(function() {
    
        $("#check-all").change(function() {
            // If checked
            if (this.checked)
                  $('.cb-check').each(function() {
                    this.checked = true;
                  });
            else
                $('.cb-check').each(function() {
                    this.checked = false;
                  });
        })

        
        $('.org-init').on('click', '.editItem', function() {
            var id = $(this).attr('data-id');
            console.log(id);
        
            $.ajax({
                url: root_url + '/ecommerce/brands/get-brand',
                data: {'id':id},
                //dataType: "html",
                method: "GET",
                cache: false,
                success: function (data) {
                    console.log(data);
                    if(data.success){
                        $('#itemId').val(id);
                        $('#brName').val(data.brand.name);
                        $('#brSlug').val(data.brand.slug);
                        $('.submitBtn').text('Update');
                        
                        //$('#brandForm').attr('action', root_url + '/product/update-brand');
                        
                        
                        $('.modalTitle').text('Edit Brand');

                        //$('#modal').modal('show');
                        //$('#modal').modal('toggle');
                        $('body').addClass('toggle-shown');
                        $('#modal').addClass('content-active');
                    }else{
                        Swal.fire('Details not found!')
                    }
                }
            });
        });

        $('.org-init').on('click','.eg-swal-av3', function (e) {
        var id = $(this).attr('data-id');
        Swal.fire({
          title: 'Are you sure?',
          text: "You won't be able to revert this!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Yes, delete it!'
        }).then(function (result) {
          if (result.value) {
            console.log('catId-' + id);
            $.ajax({
                url: root_url + '/ecommerce/brands/delete',
                data: {
                    'id': id
                },
                //dataType: "html",
                method: "GET",
                cache: false,
                success: function(data) {
                    console.log(data);
                    if (data.success) {
                        Swal.fire('Deleted!', 'Your item has been deleted.', 'success');
                        var table = $('.org-init').DataTable();
                        table.ajax.reload();
                    } else {
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
        });
        e.preventDefault();
      });


        var dt='';
        dt = NioApp.DataTable('.org-init', {
            processing: true,
            serverSide: true,
            // ajax: "{{ url('saas/organization') }}",
            ajax: {
                type:"GET",
                url: "{{ url('saas/organization') }}",
                data: function (d) {
                    d.name = $('#organizationName').val(),
                    d.contact_person = $('#contact_person').val(),
                    d.industry = $('#industry').val(),
                    d.dateFrom = $('#fromdate').val(),
                    d.dateTo = $('#todate').val()
                }
            },
            columns: [{
                    "class": "nk-tb-col tb-col-lg nk-tb-col-check",
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    "class": "nk-tb-col tb-col-lg",
                    data: 'name',
                    name: 'name'
                },
                {
                    "class": "nk-tb-col tb-col-lg",
                    data: 'contact_person',
                    name: 'contact_person'
                },
                {
                    "class": "nk-tb-col tb-col-lg",
                    data: 'mobile',
                    name: 'mobile'
                },
                {
                    "class": "nk-tb-col tb-col-lg",
                    data: 'industry',
                    name: 'industry'
                },
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
                    "class": "nk-tb-col tb-col-lg text-right nk-tb-col-tools",
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ],
            
        });

        $('.submitBtn').click(function(){
            dt['.org-init'].draw();
            $('#modalFilterOrganization').modal('toggle');
        });
        NioApp.resetModalForm('#modalFilterOrganization', dt['.org-init'], '.resetFilter');

    });
</script>
@endpush