@extends('layouts.app')

@section('content')
@php
    $buttonData = array(
        'label' => 'Link test',
    );
@endphp

<div class="nk-block-head nk-block-head-sm">
    <div class="nk-block-between">
        <div class="nk-block-head-content">
            <h3 class="nk-block-title page-title">Products</h3>
            @php 
                if( count($products) > 1) 
                    $count = 'products';
                else
                    $count = 'product';                       
            @endphp
            <p>You have total {{ count($products) }} {{ $count }}.</p>
        </div><!-- .nk-block-head-content -->
        <div class="nk-block-head-content">
            <div class="toggle-wrap nk-block-tools-toggle">
                <a href="#" class="btn btn-icon btn-trigger toggle-expand mr-n1" data-target="more-options"><em class="icon ni ni-more-v"></em></a>
                <div class="toggle-expand-content" data-content="more-options">
                    <ul class="nk-block-tools g-3">
                        <li>
                            <div class="form-wrap 50px mr-2">
                                <select class="form-select form-select-sm" data-search="off" data-placeholder="Bulk Action">
                                    <option value="" selected disabled>Bulk Action</option>
                                    <option value="edit">Edit</option>
                                    <option value="email">Remove</option>
                                </select>
                            </div>
                            <div class="btn-wrap">
                                <span class="d-none d-md-block"><button class="btn btn-primary" name="submit_btn" id="mass-update" onclick="updateMassItems()">Apply</button></span>
                                <span class="d-md-none"><button class="btn btn-dim btn-outline-light btn-icon"><em class="icon ni ni-arrow-right"></em></button></span>
                            </div>
                        </li>
                        <li>
                            <a href="#" class="btn btn-trigger btn-icon dropdown-toggle" data-toggle="modal" title="filter" data-target="#modalFilterProduct">
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
                            <a href="{{url('/saas/organization/ecommerce/products/add')}}" class="btn btn-primary d-none d-md-inline-flex"><em class="icon ni ni-plus"></em><span>Add Product</span></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div><!-- .nk-block-between -->
</div><!-- .nk-block-head -->

<div class="nk-block table-compact">
    
    <div class="nk-tb-list is-separate mb-3">
        <table class="products-init nowrap nk-tb-list is-separate" data-auto-responsive="false">
            <thead>
                <tr class="nk-tb-item nk-tb-head">
                    <th class="nk-tb-col nk-tb-col-check">
                        <div class="custom-control custom-control-sm custom-checkbox notext">
                            <input type="checkbox" class="custom-control-input" id="check-all" name="check_all"><label class="custom-control-label" for="check-all"></label>
                        </div>
                    </th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Name</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Price</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Brand</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Categories</span></th>
                    <th class="nk-tb-col tb-col-mb nk-tb-action-col text-center" nowrap="true"><span class="sub-text">Status</span></th>
                    <th class="nk-tb-col tb-col-mb nk-tb-action-col" nowrap="true"><span class="sub-text">Created at</span></th>
                    <th class="nk-tb-col nk-tb-col-tools text-right nk-tb-action-col" nowrap="true">
                        <span class="sub-text">Action</span>
                    </th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div><!-- .nk-tb-list -->
</div><!-- .nk-block -->
<!-- {{\Config::get('constants.ROLES.SELLER')}} -->
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

<x-ui.modal modalId="modalFilterProduct" title="Filter" :footerActions="$filterAction">
    <form role="form" class="mb-0" method="get" action="#">
        @csrf
        <div class="modal-body modal-body-lg">
            <div class="gy-3">
                <div class="row g-3 align-center">
                    <div class="col-lg-5">
                        <x-inputs.verticalFormLabel label="Product Name" for="productName" suggestion="Specify the product name of the product." required="false" />
                    </div>
                    <div class="col-lg-7">
                        <x-inputs.text value="" for="productName" icon="box" label="" required="false" placeholder="Product Name" name="productName"/>
                    </div>
                </div>
                <div class="row g-3 align-center">
                    <div class="col-lg-5">
                        <x-inputs.verticalFormLabel label="Brand" for="Brand" suggestion="Select the brand of the product." required="false" />
                    </div>
                    <div class="col-lg-7">
                        <div class="form-group">
                            <div class="form-control-wrap">
                                <select class="form-select form-control form-control-lg" name="brand_id" data-search="on" id="brand_id">
                                    <option></option>
                                            @forelse($brands as $key  => $brand)
                                            <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                            @empty
                                                <option></option>
                                            @endforelse
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row g-3 align-center">
                    <div class="col-lg-5">
                        <x-inputs.verticalFormLabel label="Categories" for="Categories" suggestion="Select the categories of the product." required="false" />
                    </div>
                    <div class="col-lg-7">
                        <div class="form-group">
                            <div class="form-control-wrap">
                                <select class="form-select form-control form-control-lg"   data-search="on" name="category_id" id="category_id">
                                    <option></option>
                                    @forelse($categories as $key  => $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @empty
                                        <option></option>
                                    @endforelse
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row g-3 align-center">
                    <div class="col-lg-5">
                        <x-inputs.verticalFormLabel label="Model" for="Model" suggestion="Select the model of the product." required="false" />
                    </div>
                    <div class="col-lg-7">
                        <div class="form-group">
                            <div class="form-control-wrap">
                                <select class="form-select form-control form-control-lg"  name="model" data-search="on" id="model">
                                    <option></option>
                                    @forelse($models as $key  => $mod)
                                    <option value="{{ $mod->id }}">{{ $mod->name }}</option>
                                    @empty
                                        <option></option>
                                    @endforelse
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row g-3 align-center">
                    <div class="col-lg-5">
                        <x-inputs.verticalFormLabel label="Active Products" for="ActiveProducts" suggestion="Select the active products." required="false" />
                    </div>
                    <div class="col-lg-7">
                        <x-inputs.checkbox for="number1" size="md" placeholder="" name="name" />
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
            Swal.fire("Please select a product first !");
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
                url: root_url + '/saas/organization/ecommerce/products/mass-update',
                data: {'ids':arr,'status':status},
                //dataType: "html",
                method: "POST",
                cache: false,
                success: function (data) {
                    console.log(data);
                    
                    if(data.success){
                        
                        var table = $('.products-init').DataTable();
                        table.ajax.reload();

                        Swal.fire(
                          'Good job!',
                          'Item Updated Successfully.',
                          'success'
                        )

                    }else{

                        $('#check-all').prop('checked', false);
                        var table = $('.products-init').DataTable();
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

        $('.products-init').on('click','.eg-swal-av3', function (e) {
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
                url: root_url + '/saas/organization/ecommerce/products/delete',
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
                        var table = $('.products-init').DataTable();
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
        dt = NioApp.DataTable('.products-init', {
            processing: true,
            serverSide: true,
            // ajax: "{{ url('saas/organization/ecommerce/products') }}",
            ajax: {
                type:"GET",
                url: "{{ url('saas/organization/ecommerce/products') }}",
                data: function (d) {
                    d.productName = $('#productName').val(),
                    d.brand_id = $('#brand_id').val(),
                    d.category_id = $('#category_id').val(),
                    d.model = $('#model').val()
                }
            },
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
                    data: 'price',
                    name: 'price'
                },
                {
                    "class": "nk-tb-col tb-col-lg",
                    data: 'brand',
                    name: 'brand'
                },
                {
                    "class": "nk-tb-col tb-col-lg",
                    data: 'categories',
                    name: 'categories'
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
            dt['.products-init'].draw();
            $('#modalFilterProduct').modal('toggle');
        });
        NioApp.resetModalForm('#modalFilterProduct', dt['.products-init'], '.resetFilter');
    });
</script>
@endpush