@extends('layouts.app')

@section('content')
	<div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">Modules</h3>
                @php 
                    if(count($modules) > 1) 
                        $count = 'modules';
                    else
                        $count = 'module';                       
                @endphp
                <p>You have total {{ count($modules) }} {{ $count }}.</p>
            </div><!-- .nk-block-head-content -->
        </div><!-- .nk-block-between -->
    </div><!-- .nk-block-head -->
    <div class="nk-block table-compact buyer">
        
        <div class="nk-tb-list is-separate mb-3">
            <table class="module-init nowrap nk-tb-list is-separate" data-auto-responsive="false">
                <thead>
                    <tr class="nk-tb-item nk-tb-head">
                        <th class="nk-tb-col nk-tb-col-check">
                            <div class="custom-control custom-control-sm custom-checkbox notext">
                               
                            </div>
                        </th>
                        <th class="nk-tb-col tb-col-mb"><span class="sub-text">Module Name</span></th>
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
        
        <div class="nk-add-product toggle-slide toggle-slide-right" data-content="addModule" data-toggle-screen="any" data-toggle-overlay="true" data-toggle-body="true" data-simplebar id="modal">
        <form role="form" method="post" action="{{ url('saas/organization/update-module') }}" enctype="multipart/form-data">
            @csrf
            <div class="nk-block-head">
                <div class="nk-block-head-content">
                    <h5 class="nk-block-title modalTitle">Edit Module</h5>
                    <div class="nk-block-des">
                        
                    </div>
                </div>
            </div><!-- .nk-block-head -->
            <div class="nk-block">
                <div class="row g-3">
                    <div class="col-12">
                        <div class="form-group">
                            <label class="form-label" for="product-title">Module Name</label>
                            <div class="form-control-wrap">
                                <input type="text" class="form-control" id="name" required="true" name="name" minlength="3" maxlength="255" data-parsley-errors-container=".name" autocomplete="off" value="" readonly="" />
                                <div class="name"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="product-title">Description<span class="text-danger">*</span></label>
                        <textarea class="form-control" for="description" label="Description" size="sm" name="description" required="required" id="description" data-parsley-errors-container=".description" minlength="3" maxlength="500"></textarea>
                        <span class="form-note mt-0">Note: Maximum character limit is 500 for description.</span>
                        <div class="description"></div>
                    </div>
                    <input type="hidden" name="id" id="itemId" value="0">
                    <div class="col-12 text-right">
                        <a href="javascript:resetValues();" data-target="addModule" class="btn btn-outline-light editItem">Cancel</a>
                        <button class="btn btn-primary submitBtn" name="submit"><span>Submit</span></button>
                    </div>
                </div>
            </div><!-- .nk-block -->
        </form>
        </div>
    </div><!-- .nk-block -->   
@endsection
@push('footerScripts')
<script type="text/javascript">
    function resetValues(){
        $('.submitBtn').text('Submit');

        $('#itemId').val(0);
        $('#name').val('');
        $('#description').val('');
                
        $('#itemId').val(0);
    }

 

     $(function() {

                var root_url = "<?php echo Request::root(); ?>";
                $('.module-init').on('click', '.editItem', function() {
                    var id = $(this).attr('data-id');
                    console.log(id);
            
                $.ajax({
                    url: root_url + '/saas/organization/get-module',
                    data: {'id':id},
                    //dataType: "html",
                    method: "GET",
                    cache: false,
                    success: function (data) {
                        console.log(data);
                        if(data.success){
                            $('#itemId').val(id);
                            $('#name').val(data.module.name);
                            $('#description').val(data.module.description);
                            $('.submitBtn').text('Update');
                            
                            
                        }else{
                            Swal.fire('Details not found!')
                        }
                    }
                });
            });


        NioApp.DataTable('.module-init', {
            processing: true,
            serverSide: true,
            ajax: "{{ url('/saas/organization/modules') }}",
            columns: [{
                    "class": "nk-tb-col tb-col-lg nk-tb-col-check",
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                },
                {
                    "class": "nk-tb-col tb-col-lg",
                    data: 'name',
                    name: 'name'
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
