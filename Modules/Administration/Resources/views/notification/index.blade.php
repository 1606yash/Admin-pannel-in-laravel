@extends('layouts.app')

@section('content')
	<div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">Notifications Templates</h3>
                @php 
                    if(count($notiTemplates) > 1) 
                        $count = 'Notifications Templates';
                    else
                        $count = 'Notifications Template';                       
                @endphp
                <p>You have total {{ count($notiTemplates) }} {{ $count }}.</p>
            </div><!-- .nk-block-head-content -->
            <div class="nk-block-head-content">
                <div class="toggle-wrap nk-block-tools-toggle">
                    <a href="#" class="btn btn-icon btn-trigger toggle-expand mr-n1" data-target="more-options"><em class="icon ni ni-more-v"></em></a>
                    <div class="toggle-expand-content" data-content="more-options">
                        <ul class="nk-block-tools g-3">
                            <li>
                                <a href="#" class="btn btn-trigger btn-icon dropdown-toggle" data-toggle="modal" title="filter" data-target="#modalFilterNotification">
                                    <div class="dot dot-primary"></div>
                                    <em class="icon ni ni-filter-alt"></em>
                                </a>
                            </li>                        
                        </ul>
                    </div>
                </div>
            </div>
        </div><!-- .nk-block-between -->
    </div><!-- .nk-block-head -->

    <!--  Filter Tag List -->
    <div id="filter_tag_list" class="filter-tag-list"></div>
    
    <div class="nk-block table-compact">
        <div class="nk-tb-list is-separate mb-3">
            <table class="brand-init nowrap nk-tb-list is-separate" data-auto-responsive="false">
                <thead>
                    <tr class="nk-tb-item nk-tb-head">
                        <th class="nk-tb-col nk-tb-col-check">
                            <span class="sub-text">SN</span>
                        </th>
                        <th class="nk-tb-col tb-col-mb"><span class="sub-text">Name</span></th>
                        <th class="nk-tb-col tb-col-mb"><span class="sub-text">Code</span></th>
                        <th class="nk-tb-col tb-col-mb nk-tb-action-col" nowrap="true"><span class="sub-text">Created at</span></th>
                        <th class="nk-tb-col tb-col-mb nk-tb-action-col" nowrap="true"><span class="sub-text">Updated at</span></th>
                        <th class="nk-tb-col nk-tb-col-tools nk-tb-action-col text-right" nowrap="true">
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
'click' => 'Swal.fire("Submit")',
)
);
@endphp

<x-ui.modal modalId="modalFilterNotification" title="Filter" :footerActions="$filterAction">
    <form role="form" class="mb-0" method="get" action="#">
        @csrf
        <div class="modal-body modal-body-lg">
            <div class="gy-3">
                <div class="row g-3 align-center">
                    <div class="col-lg-5">
                        <x-inputs.verticalFormLabel label="Notification Name" for="NotificationName" suggestion="Specify the name of the notification." required="false" />
                    </div>
                    <div class="col-lg-7">
                        <x-inputs.text value="" for="NotificationName" icon="bell" label="" required="false" placeholder="Notification Name" name="NotificationName"/>
                    </div>
                </div>
                
            </div>
        </div>
        <input type="hidden" id="userId" name="user_id" value="0">
    </form>
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
            Swal.fire("Please select an notification first !");
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
                        
                        var table = $('.brand-init').DataTable();
                        table.ajax.reload();

                        Swal.fire(
                          'Good job!',
                          'Item Updated Successfully.',
                          'success'
                        )

                    }else{

                        $('#check-all').prop('checked', false);
                        var table = $('.brand-init').DataTable();
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

                var root_url = "<?php echo Request::root(); ?>";
                $('.brand-init').on('click', '.editItem', function() {
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

            $('.brand-init').on('click','.eg-swal-av3', function (e) {
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
                            var table = $('.brand-init').DataTable();
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
        dt = NioApp.DataTable('.brand-init', {
            processing: true,
            serverSide: true,
            ajax: "{{ url('administration/notification/templates') }}",
            columns: [{
                    "class": "nk-tb-col tb-col-lg nk-tb-col-check",
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                },
                {
                    "class": "nk-tb-col tb-col-lg",
                    data: 'friendly_name',
                    name: 'friendly_name'
                },
                {
                    "class": "nk-tb-col tb-col-lg",
                    data: 'name',
                    name: 'name'
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
                    "class": "nk-tb-col tb-col-lg text-right nk-tb-col-tools",
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ],
            
        });

        NioApp.resetModalForm('#modalFilterNotification', dt['.brand-init'], '.resetFilter', true);

    });
</script>
@endpush