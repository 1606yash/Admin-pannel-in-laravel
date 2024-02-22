@extends('layouts.app')

@section('content')
	<div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">Segment Master</h3>
                 @php 
                    if(count($segments) > 1) 
                        $count = 'segment masters';
                    else
                        $count = 'segment master';                       
                @endphp
                <p>You have total <span id="listCount">{{ count($segments) }}</span> {{ $count }}.</p>
            </div><!-- .nk-block-head-content -->
            <div class="nk-block-head-content">
                <div class="toggle-wrap nk-block-tools-toggle">
                    <a href="#" class="btn btn-icon btn-trigger toggle-expand mr-n1" data-target="more-options"><em class="icon ni ni-more-v"></em></a>
                    <div class="toggle-expand-content" data-content="more-options">
                        <ul class="nk-block-tools g-3">
                            <li  class="nk-block-tools-opt">
                                <div class="form-wrap 50px mr-2">
                                    <select class="form-select form-select-sm" data-search="off" data-placeholder="Bulk Action" id="mass-status">
                                    <option value="" selected disabled>Bulk Action</option>
                                    <option value="active" >Active</option>
                                    <option value="inactive" >Inactive</option>
                                    
                                    </select>
                                </div>
                                <div class="btn-wrap">
                                    <span class="d-none d-md-block"><button class="btn btn-primary" name="submit_btn" id="mass-update" onclick="updateMassItems()">Apply</button></span>
                                    <span class="d-md-none"><button class="btn btn-dim btn-outline-light btn-icon disabled"><em class="icon ni ni-arrow-right"></em></button></span>
                                </div>
                            </li>
                            <li class="nk-block-tools-opt">
                                <a href="#" data-target="addSegmentMaster" class="toggle btn btn-primary d-none d-md-inline-flex"><em class="icon ni ni-plus"></em><span>Add Segment Master</span></a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div><!-- .nk-block-between -->
    </div><!-- .nk-block-head -->
    <div class="nk-block table-compact buyer">
        
        <div class="nk-tb-list is-separate mb-3">
            <table class="segment-init nowrap nk-tb-list is-separate" data-auto-responsive="false">
                <thead>
                    <tr class="nk-tb-item nk-tb-head">
                        <th class="nk-tb-col nk-tb-col-check">
                            <div class="custom-control custom-control-sm custom-checkbox notext">
                                <input type="checkbox" class="custom-control-input" id="check-all" name="check_all"><label class="custom-control-label" for="check-all"></label>
                            </div>
                        </th>
                        <th class="nk-tb-col tb-col-mb"><span class="sub-text">Type</span></th>
                        <th class="nk-tb-col tb-col-mb"><span class="sub-text">Status</span></th>
                        <th class="nk-tb-col nk-tb-col-tools text-right">
                            <span class="sub-text">Action</span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div><!-- .nk-tb-list -->


        <div class="nk-add-product toggle-slide toggle-slide-right" data-content="addSegmentMaster" data-toggle-screen="any" data-toggle-overlay="true" data-toggle-body="true" data-simplebar id="modal">
       <form role="form" id="addSegment" method="post" action="{{ url('saas/organization/segment/add') }}" enctype="multipart/form-data">
            @csrf 
            <div class="nk-block-head">
                <div class="nk-block-head-content">
                    <h5 class="nk-block-title modalTitle">Add Segment Master</h5>
                    <div class="nk-block-des">
                    </div>
                </div>
            </div><!-- .nk-block-head -->
            <div class="nk-block">
                <form action="#">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label">Segment Type<span class="text-danger">*</span></label>
                                <div class="form-control-wrap">
                                    <x-inputs.text value="" for="industryType" icon="building" placeholder="Segment Type" name="segmentType" required="true" minlength="3" maxlength="255"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label">Slug</label>
                                <div class="form-control-wrap">
                                    <x-inputs.text value="" for="slug" icon="label" placeholder="Slug" name="slug" required="" minlength="3" maxlength="255"/>
                                </div>
                            </div>
                        </div>


                        <div class="col-12">
                            <x-inputs.select  size="sm" name="industry" label="Industry" for="industry" required="true">
                                <option></option>
                                @forelse ($industries as $industry)
                                <option value="{{ $industry->id }}">{{ $industry->type }}</option>
                                @empty
                                    {{-- empty expr --}}
                                @endforelse
                            </x-inputs.select>
                        </div>


                        
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label">Status</label>
                                <div class="form-control-wrap">
                                    <x-inputs.switch for="Status" size="md" name="status" value="1"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label">Description</label>
                                <div class="form-control-wrap">
                                    <x-inputs.textarea for="description" size="sm" required="" name="description" maxlength="500" id="description"/>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" id="itemId" name="id" value="0">
                        <div class="col-12 text-right">
                           <a href="#" class="btn btn-outline-light cancel">Cancel</a>
                        <button class="btn btn-primary submitBtn" name="submit"><span>Submit</span></button>
                        </div>
                    </div>
                </form>
            </div><!-- .nk-block -->
        </div>
    </div><!-- .nk-block -->   
@endsection
@push('footerScripts')
<script type="text/javascript">
    function resetValues(){
        $('.submitBtn').text('Submit');
        $('.modalTitle').text('Add Segment');
        $('#itemId').val(0);
        $('#industryType').val('');
        $('#industry').val('').trigger('change');
        $('#slug').val('');
        $('#Status').prop('checked', true); 
        $('#description').val('');
        $('#addSegment')[0].reset();
        $('#addSegment').parsley().reset();
    }

    $('.cancel').on('click', function() {
        resetValues();
        NioApp.Toggle.collapseDrawer('addSegmentMaster')
    })

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
            Swal.fire("Please select a segment first !");
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
                url: root_url + '/saas/organization/segment/mass-update',
                data: {'ids':arr,'status':status},
                //dataType: "html",
                method: "POST",
                cache: false,
                success: function (data) {
                    console.log(data);
                    
                    if(data.success){
                        
                        var table = $('.segment-init').DataTable();
                        table.ajax.reload();

                        Swal.fire(
                          'Good job!',
                          'Item Updated Successfully.',
                          'success'
                        )

                    }else{

                        $('#check-all').prop('checked', false);
                        var table = $('.segment-init').DataTable();
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
    
        $('.segment-init').on("change","#check-all",function() {
            if (this.checked)
                    $('.cb-check').prop('checked', true);
            else
                    $('.cb-check').prop('checked', false);
        })

                var root_url = "<?php echo Request::root(); ?>";
                $('.segment-init').on('click', '.editItem', function() {
                    var id = $(this).attr('data-id');
                    console.log(id);
            
                $.ajax({
                    url: root_url + '/saas/organization/segment/get',
                    data: {'id':id},
                    //dataType: "html",
                    method: "GET",
                    cache: false,
                    success: function (data) {
                        console.log(data);
                        if(data.success){
                            $('#itemId').val(id);
                            $('#industryType').val(data.segment.type);
                            $('#industry').val(data.segment.industry).trigger('change');
                            $('#slug').val(data.segment.slug);

                            if(data.segment.status=='active'){
                                $('#Status').prop('checked', true); 
                            }
                            
                            $('#description').val(data.segment.description);
                            $('.submitBtn').text('Update');
                            
                            $('.modalTitle').text('Edit Segment Type');

                            
                        }else{
                            Swal.fire('Details not found!')
                        }
                    }
                });
            });

            $('.segment-init').on('click','.eg-swal-av3', function (e) {
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
                    url: root_url + '/saas/organization/segment/delete',
                    data: {
                        'id': id
                    },
                    //dataType: "html",
                    method: "GET",
                    cache: false,
                    success: function(data) {
                        console.log(data);
                        if (data.success) {
                            Swal.fire('Deleted!', 'Segment has been deleted.', 'success');
                            var table = $('.segment-init').DataTable();
                            table.ajax.reload();
                            var listCount = $('#listCount').text();
                            listCount = listCount-1;
                            $('#listCount').text(listCount);
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


        NioApp.DataTable('.segment-init', {
            processing: true,
            serverSide: true,
            ajax: "{{ url('saas/organization/segments') }}",
            columns: [{
                    "class": "nk-tb-col tb-col-lg nk-tb-col-check",
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row, meta) {
                        return '<td class="nk-tb-col nk-tb-col-check"><div class="custom-control custom-control-sm custom-checkbox notext"><input type="checkbox" class="custom-control-input cb-check" id="cb-'+ row.id +'" value="'+ row.id +'" name="checked_items[]"><label class="custom-control-label" for="cb-'+ row.id +'"></label></div></td>'
                    }
                },
                {
                    "class": "nk-tb-col tb-col-lg",
                    data: 'type',
                    name: 'type'
                },
                {
                    "class": "nk-tb-col tb-col-lg",
                    data: 'status',
                    name: 'status'
                },
                {
                    "class": "nk-tb-col tb-col-lg text-right nk-tb-col-tools",
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ],
            "fnDrawCallback":function(){
            NioApp.TGL.content('.editItem',{
                    onCloseCallback: resetValues
                });
            NioApp.BS.tooltip('[data-toggle="tooltip"]');   
            }
            
        });
    });
</script>
@endpush