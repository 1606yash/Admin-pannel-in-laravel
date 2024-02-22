@extends('layouts.app')

@section('content')
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">Master Category</h3>
                @php
            if(count($categories) > 1)
            $count = 'categories';
            else
            $count = 'category';
            @endphp
            <p>You have total {{ count($categories) }} {{ $count }}.</p>
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
                                    <option value="active" >Active</option>
                                    <option value="inactive" >Inactive</option>
                                </select>
                            </div>
                            <div class="btn-wrap">
                                <span class="d-none d-md-block"><button class="btn btn-primary" name="submit_btn" id="mass-update" onclick="updateMassItems()">Apply</button></span>
                                <span class="d-md-none"><button class="btn btn-dim btn-outline-light btn-icon disabled"><em class="icon ni ni-arrow-right"></em></button></span>
                            </div>
                        </li>
                        <li>
                            <a href="#" class="btn btn-trigger btn-icon dropdown-toggle" data-toggle="modal" title="filter" data-target="#modalFilterCategory">
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
                        <li class="nk-block-tools-opt">
                            <a href="#" data-target="addCategoryMaster" data-target="addCategoryMaster" class="toggle btn btn-primary d-none d-md-inline-flex"><em class="icon ni ni-plus"></em><span>Add Category</span></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        </div><!-- .nk-block-between -->
    </div><!-- .nk-block-head -->
    <div class="nk-block table-compact">
        <div class="nk-tb-list is-separate mb-3">
            <table class="category-init nowrap nk-tb-list is-separate" data-auto-responsive="false">
            <thead>
                <tr class="nk-tb-item nk-tb-head">
                    <th class="nk-tb-col nk-tb-col-check">
                        <div class="custom-control custom-control-sm custom-checkbox notext">
                            <input type="checkbox" class="custom-control-input" id="check-all" name="check_all"><label class="custom-control-label" for="check-all"></label>
                        </div>
                    </th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Name</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Parent</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Products</span></th>
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

        <div class="nk-add-product toggle-slide toggle-slide-right" data-content="addCategoryMaster" data-toggle-screen="any" data-toggle-overlay="true" data-toggle-body="true" data-simplebar id="modal">
        <div class="nk-block-head">
            <div class="nk-block-head-content">
                <h5 class="nk-block-title">Add Category</h5>
                <div class="nk-block-des">
                    <p>Add information and add new category.</p>
                </div>
            </div>
        </div><!-- .nk-block-head -->
        <div class="nk-block">
            <form role="form" method="post" action="{{ url('saas/organization/ecommerce/categories/add') }}" enctype="multipart/form-data">
                <input type="hidden" value="0" name="id" id="itemId">
                <div class="row g-3">
                    @csrf
                    <div class="col-12">
                        <label class="form-label" for="site-name">Category Name<span class="text-danger">*</span></label>
                        <input class="form-control" type="text" for="category_name" label="Category Name" required="true" icon="network" placeholder="Category Name" name="name" minlength="3" maxlength="255" data-parsley-errors-container=".catName" autocomplete="off" id="catName" />
                        <div class="catName"></div>
                    </div>

                    <div class="col-mb-12">
                        <label class="form-label" for="site-name">Slug</label>
                        <input class="form-control" type="text" for="slug" label="Slug" icon="ticket" placeholder="Slug" name="slug" minlength="3" data-parsley-errors-container=".catSlug" autocomplete="off" id="catSlug" />
                        <div class="catSlug"></div>
                    </div>

                    <div class="col-mb-12">
                        <label class="form-label" for="site-name">Parent Category</span></label>
                        <select size="sm" class="form-select form-control form-control-lg" data-placeholder="Parent Category" data-parsley-errors-container=".categoryParsley" name="parent_id" id="parentCat">
                            <option></option>
                            @forelse($parentCtegories as $key => $parent)
                            <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                            @empty
                            <option></option>
                            @endforelse
                        </select>

                    </div>
                    <div class="col-6">
                        <x-inputs.switch size="md" for="catStatus" label="Status" name="status"/>
                    </div>
                    <div class="col-6">
                        <x-inputs.switch size="md" for="catIs_featured" label="Featured" name="is_featured" value="on"/>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label class="form-label" for="tags">Thumbnail</label>
                            <div class="form-control-wrap">
                                <div class="custom-file">
                                    <input type="file" multiple class="custom-file-input" id="customFile">
                                    <label class="custom-file-label" for="customFile">Choose file</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <x-inputs.textarea for="description" label="Description" size="sm" name="description" required="" id="description"  maxlength="500"/>
                        <!-- <div class="form-group">
                            <label class="form-label" for="tags">Description</label>
                            <div class="form-control-wrap">
                                <textarea class="form-control"></textarea>
                            </div>
                        </div> -->
                    </div>
                    <div class="col-12 text-right">
                        <a href="#" class="btn btn-outline-light cancel">Cancel</a>
                        <button class="btn btn-primary submitBtn" name="submit" type="submit"><span>Submit</span></button>
                    </div>
                </div>
            </form>
        </div><!-- .nk-block -->
    </div>
    </div><!-- .nk-block -->

<div class="modal fade zoom" tabindex="-1" id="modalFilterCategory">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Filter</h5>
                <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
            </div>
            <form action="{{ url('saas/organization/ecommerce/categories') }}" role="form" class="mb-0" method="get">
            @csrf
            <div class="modal-body modal-body-lg">
                <div class="gy-3">
                    <div class="row g-3 align-center">
                        <div class="col-lg-5">
                            <div class="form-group">
                                <label class="form-label" for="site-name">Category Name</label>
                                <span class="form-note">Specify the category name of the category.</span>
                            </div>
                        </div>
                        <div class="col-lg-7">
                            <div class="form-group">
                                <div class="form-control-wrap">
                                    <div class="form-icon form-icon-left">
                                        <em class="icon ni ni-network"></em>
                                    </div>
                                    <input type="text" class="form-control" placeholder="Category Name" name="name" value="" autocomplete="off">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row g-3 align-center">
                        <div class="col-lg-5">
                            <div class="form-group">
                                <label class="form-label" for="site-name">Parent Category</label>
                                <span class="form-note">Select the parent category of the category.</span>
                            </div>
                        </div>
                        <div class="col-lg-7">
                            <div class="form-group">
                                <div class="form-control-wrap">
                                    <select class="form-select form-control form-control-lg" data-placeholder="Select Category" name="parent_id">
                                        <option></option>
                                    </select>

                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="row g-3 align-center">
                        <div class="col-lg-5">
                            <div class="form-group">
                                <label class="form-label" for="site-name">Created At</label>
                                <span class="form-note">Select the date of the created at.</span>
                            </div>
                        </div>
                        <div class="col-lg-7">
                            <div class="form-group">
                                <div class="form-control-wrap">
                                    <div class="form-icon form-icon-left "><em class="icon ni ni-calendar"></em></div>
                                    <input type="text" class="form-control date-picker">
                                </div>
                            </div>
                        </div>
                    </div> -->
                </div>
            </div>
            
            <div class="modal-footer bg-light">
                <div class="row">
                    <div class="col-lg-12 p-0 text-right">
                        <button class="btn btn-outline-light" data-dismiss="modal" aria-label="Close">Cancel</button>
                        <button class="btn btn-danger resetFilter" data-dismiss="modal" aria-label="Close">Clear Filter</button>
                        <button class="btn btn-primary submitBtn" type="submit">Submit</button>
                    </div>
                </div>
            </div>
            </form>
        </div>
    </div>
</div>
@endsection
@push('footerScripts')

<script type="text/javascript">
function resetValues() {
    $('.submitBtn').text('Submit');
    $('.modalTitle').text('Add Category');
    $('.modalTitle').text('Add New');

    $('#itemId').val(0);
    $('#catName').val('');
    $('#catSlug').val('');

    $('#itemId').val(0);
}
$('.cancel').on('click', function() {
        resetValues();
        NioApp.Toggle.collapseDrawer('addCategoryMaster')
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
            Swal.fire("Please select a category first !");
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
                url: root_url + '/saas/organization/ecommerce/categories/mass-update',
                data: {'ids':arr,'status':status},
                //dataType: "html",
                method: "POST",
                cache: false,
                success: function (data) {
                    console.log(data);
                    
                    if(data.success){
                        
                        var table = $('.category-init').DataTable();
                        table.ajax.reload();

                        Swal.fire(
                          'Good job!',
                          'Item Updated Successfully.',
                          'success'
                        )

                    }else{

                        $('#check-all').prop('checked', false);
                        var table = $('.category-init').DataTable();
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
        $('.category-init').on('click', '.editItem', function() {
            var id = $(this).attr('data-id');
            console.log('id',id);

            $.ajax({
                url: root_url + '/saas/organization/ecommerce/categories/get',
                data: {
                    'id': id
                },
                //dataType: "html",
                method: "GET",
                cache: false,
                success: function(data) {
                    console.log(data);
                    if (data.success) {
                        $('#itemId').val(id); 
                        $('#catName').val(data.category.name);
                        $('#catSlug').val(data.category.slug);
                        $('#parentCat').val(data.category.parent_id).trigger('change');
                        $('#description').val(data.category.description);
                        $('.submitBtn').text('Update');

                        $('#catStatus').attr('checked', data.category.status == "active"? true : false);
                        $('#catIs_featured').attr('checked', data.category.is_featured == "1"? true : false);
                        //$('#brandForm').attr('action', root_url + '/product/update-brand');


                        $('.modalTitle').text('Edit Category');
                    } else {
                        Swal.fire('Details not found!')
                    }
                }
            });
        });

        $('.category-init').on('click','.eg-swal-av3', function (e) {
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
                url: root_url + '/saas/organization/ecommerce/categories/delete',
                data: {
                    'id': id
                },
                //dataType: "html",
                method: "GET",
                cache: false,
                success: function(data) {
                    console.log(data);
                    if (data.success) {
                        Swal.fire('Deleted!', 'Your file has been deleted.', 'success');
                        var table = $('.category-init').DataTable();
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


        var dt = NioApp.DataTable('.category-init', {
            processing: true,
            serverSide: true,
            ajax: "{{ url('saas/organization/ecommerce/categories') }}",
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
                    data: 'name',
                    name: 'name'
                },
                {
                    "class": "nk-tb-col tb-col-lg",
                    data: 'parent',
                    name: 'parent'
                },
                {
                    "class": "nk-tb-col tb-col-lg",
                    data: 'products',
                    name: 'products'
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

        $('.submitBtn').click(function() {
            dt['.category-init'].draw();
            $('#modalFilterCategory').modal('toggle');
        });
        NioApp.resetModalForm('#modalFilterCategory', dt['.category-init'], '.resetFilter');
    });



</script>
@endpush