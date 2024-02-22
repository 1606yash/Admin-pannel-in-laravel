@extends('layouts.app')

@section('content')
<div class="nk-block-head nk-block-head-sm">
    <div class="nk-block-between">
        <div class="nk-block-head-content">
            <h3 class="nk-block-title page-title">Manufacturer</h3>
            @php
            if(count($manufacturers) > 1)
            $count = 'manufacturers';
            else
            $count = 'manufacturer';
            @endphp
            <p>You have total {{ count($manufacturers) }} {{ $count }}.</p>
        </div><!-- .nk-block-head-content -->
        <div class="nk-block-head-content">
            <div class="toggle-wrap nk-block-tools-toggle">
                <a href="#" class="btn btn-icon btn-trigger toggle-expand mr-n1" data-target="more-options"><em class="icon ni ni-more-v"></em></a>
                <div class="toggle-expand-content" data-content="more-options">
                    <ul class="nk-block-tools g-3">
                        <li>
                            <div class="form-wrap 50px mr-2">
                                <select class="form-select form-select-sm" data-search="off" data-placeholder="Bulk Action" id="mass-status">
                                    <option value="" selected disabled>Bulk Action</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                            <div class="btn-wrap">
                                <span class="d-none d-md-block"><button class="btn btn-primary" name="submit_btn" id="mass-update" onclick="updateMassItems()">Apply</button></span>
                                <span class="d-md-none"><button class="btn btn-dim btn-outline-light btn-icon disabled"><em class="icon ni ni-arrow-right"></em></button></span>
                            </div>
                        </li>
                        <li class="nk-block-tools-opt">
                            <a href="#" data-target="addManufacturer" class="toggle btn btn-primary d-none d-md-inline-flex"><em class="icon ni ni-plus"></em><span>Add Manufacturer </span></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div><!-- .nk-block-between -->
</div><!-- .nk-block-head -->
<div class="nk-block table-compact">
    <div class="nk-tb-list is-separate mb-3">
        <table class="manu-init nowrap nk-tb-list is-separate" data-auto-responsive="false">
            <thead>
                <tr class="nk-tb-item nk-tb-head">
                    <th class="nk-tb-col nk-tb-col-check">
                        <div class="custom-control custom-control-sm custom-checkbox notext">
                            <input type="checkbox" class="custom-control-input" id="check-all" name="check_all"><label class="custom-control-label" for="check-all"></label>
                        </div>
                    </th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Manufacturer Name</span></th>
                    <th class="nk-tb-col tb-col-mb nk-tb-action-col text-center" nowrap="true"><span class="sub-text">Status</span></th>
                    <th class="nk-tb-col tb-col-mb nk-tb-action-col" nowrap="true"><span class="sub-text">Created at</span></th>
                    <th class="nk-tb-col tb-col-mb nk-tb-action-col" nowrap="true"><span class="sub-text">Updated at</span></th>
                    <th class="nk-tb-col nk-tb-col-tools text-right nk-tb-action-col" nowrap="true">
                        <span class="sub-text">Action</span>
                    </th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div><!-- .nk-tb-list -->
    <div class="nk-add-product toggle-slide toggle-slide-right" data-content="addManufacturer" data-toggle-screen="any" data-toggle-overlay="true" data-toggle-body="true" data-simplebar id="modal">
        <form role="form" method="post" action="{{ url('saas/organization/ecommerce/manufacturer/add') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" value="0" name="id" id="itemId">
            <div class="nk-block-head">
                <div class="nk-block-head-content">
                    <h5 class="nk-block-title modalTitle">Add Manufacturer</h5>
                    <div class="nk-block-des modalDescription">
                        <p>Add information and add new manufacturer.</p>
                    </div>
                </div>
            </div><!-- .nk-block-head -->
            <div class="nk-block">
                <form action="#">
                    <div class="row g-3">
                        <div class="col-12">
                            <x-inputs.text for="name" label="Manufacturer Name" required="true" icon="network" placeholder="Manufacturer Name" name="name" minlength="3" maxlength="200" autocomplete="off" />
                        </div>
                        <div class="col-12">
                            <x-inputs.text for="slug" label="Slug" required="" icon="label" placeholder="Slug" name="slug" minlength="3" autocomplete="off" />
                        </div>
                        <div class="col-12">

                            <x-inputs.switch for="manStatus" label="Status" size="md" name="status" value='1' />

                        </div>
                        <div class="col-12 text-right">
                            <a href="#" class="btn btn-outline-light cancel">Cancel</a>
                            <button class="btn btn-primary submitBtn" name="submit"><span>Submit</span></button>
                        </div>
                    </div>
                </div>
                </form>
            </div><!-- .nk-block -->
    </div>
</div><!-- .nk-block -->
@endsection
@push('footerScripts')
<script type="text/javascript">
    function resetValues() {
        $('.submitBtn').text('Submit');
        $('.modalTitle').text('Add Manufacturer');
        $('.modalDescription').show();

        $('#itemId').val(0);
        $('#name').val('');
        $('#slug').val('');
                
        $('body').removeClass('toggle-shown');
        $('#modal').removeClass('content-active');
        $('#itemId').val(0);
    }

    $('.cancel').on('click', function() {
        resetValues();
        NioApp.Toggle.collapseDrawer('addManufacturer')
    })

    function updateMassItems() {
        var arr = [];
        $('input.cb-check:checkbox:checked').each(function() {
            arr.push($(this).val());
        });

        //
        var status = $('#mass-status').find(":selected").val();

        console.log(status);
        console.log(arr.length);

        if (arr.length == 0) {
            Swal.fire("Please select a manufacturer first !");
        } else if (status == 0) {
            Swal.fire("Please select bulk status !");
        } else {
            var root_url = "<?php echo Request::root(); ?>";
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: root_url + '/saas/organization/ecommerce/manufacturer/mass-update',
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
                        
                        var table = $('.manu-init').DataTable();
                        table.ajax.reload();

                        Swal.fire(
                            'Good job!',
                            'Manufacturer status updated Successfully.',
                            'success'
                        )

                    } else {

                        $('#check-all').prop('checked', false);
                        var table = $('.manu-init').DataTable();
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
        
        $('.manu-init').on('click', '.editItem', function() {
            var id = $(this).attr('data-id');
            console.log(id);

            $.ajax({
                url: root_url + '/saas/organization/ecommerce/manufacturer/get',
                data: {
                    'id': id
                },
                //dataType: "html",
                method: "GET",
                cache: false,
                success: function(data) {
                    console.log(data);

                    if (data.success) {
                        $('.modalTitle').text('Edit Manufacturer');
                        $('.modalDescription').hide();
                        $('#itemId').val(id);
                        $('#name').val(data.manufacturer.name);
                        $('#slug').val(data.manufacturer.slug);
                        $('.submitBtn').text('Update');
                        
                        $('#manStatus').attr('checked', data.manufacturer.status == "active"? true : false);
                        //$('#brandForm').attr('action', root_url + '/product/update-brand');


                    } else {
                        Swal.fire('Details not found!')
                    }
                }
            });
        });

        $('.manu-init').on('click', '.eg-swal-av3', function(e) {
            var id = $(this).attr('data-id');
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!'
            }).then(function(result) {
                if (result.value) {
                    console.log('catId-' + id);
                    $.ajax({
                        url: root_url + '/saas/organization/ecommerce/manufacturer/delete',
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
                                var table = $('.manu-init').DataTable();
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


        NioApp.DataTable('.manu-init', {
            processing: true,
            serverSide: true,
            ajax: "{{ url('saas/organization/ecommerce/manufacturer') }}",
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
            "fnDrawCallback": function() {
                NioApp.TGL.content('.editItem',{
                    onCloseCallback: resetValues
                });
                NioApp.BS.tooltip('[data-toggle="tooltip"]');
            }
        });
    });
</script>
@endpush
