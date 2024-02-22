@extends('layouts.app')
<meta name="csrf-token" content="{{ csrf_token() }}" />
@section('content')
<div class="nk-block-head nk-block-head-sm">
    <div class="nk-block-between">
        <div class="nk-block-head-content">
            <h3 class="nk-block-title page-title">Districts</h3>
        </div><!-- .nk-block-head-content -->
        <div class="nk-block-head-content">
            <div class="toggle-wrap nk-block-tools-toggle">
                <a href="#" class="btn btn-icon btn-trigger toggle-expand mr-n1" data-target="more-options"><em class="icon ni ni-more-v"></em></a>
                <div class="toggle-expand-content" data-content="more-options">
                    <ul class="nk-block-tools g-3">
                        <li class="nk-block-tools-opt">
                            <a href="#" class="btn btn-trigger btn-icon toggle" data-target="filterDistrict">
                                <div class="dot dot-primary"></div>
                                <em class="icon ni ni-filter-alt"></em>
                            </a>
                        </li>
                        <li class="nk-block-tools-opt">
                            <a href="#" data-target="addDistrict" class="toggle btn btn-primary d-none d-md-inline-flex" id="add"><em class="icon ni ni-plus"></em><span>Add District</span></a>
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
        <table id="brand_init" class="brand-init nowrap nk-tb-list is-separate" data-auto-responsive="false">
            <thead>
                <tr class="nk-tb-item nk-tb-head">
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">District</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">State Name</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Created By</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Updated By</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Action</span></th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div><!-- .nk-tb-list -->
</div><!-- .nk-block -->
<div class="nk-add-product toggle-slide toggle-slide-right" data-content="addDistrict" data-toggle-screen="any" data-toggle-overlay="true" data-toggle-body="true" data-simplebar id="modal">
    <form role="form" method="post" id="addDistrictForm" enctype="multipart/form-data">
        @csrf
        <div class="nk-block-head">
            <div class="nk-block-head-content">
                <h5 class="nk-block-title modalTitle">Add District</h5>
                <div class="nk-block-des">
                    <p>Add information and add new district.</p>
                </div>
            </div>
        </div><!-- .nk-block-head -->
        <input type="hidden" value="0" name="id" id="itemId">
        <div class="nk-block">
            <div class="row g-3">
                <div class="col-12">
                    <div class="form-control-required-wrap">
                        <label class="form-label" for="state_id">State<span class="text-danger">*</span></label>
                        <select name="state_id" id="state_id" class="form-control form-select" placeholder='Select State' data-placeholder="Select State" data-parsley-excluded="true">
                            <option selected disabled>Select State</option>
                            @foreach ($states as $key => $state)
                            <option value="{{ $state->id }}">{{ $state->state_name }}</option>
                            @endforeach
                            <!-- Add your options here -->
                        </select>
                    </div>
                </div>
                <div class="col-12">
                    <label class="form-label" for="state_id">District Name<span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="district_name" name="district_name" value="" placeholder="Enter District Name" onkeypress="return isCharSpace(event)" data-parsley-excluded="true">
                </div>

                <div class="col-12 text-right">
                    <a data-target='addDistrict' class="btn btn-outline-light cancel">Cancel</a>
                    <button class="btn btn-primary submitBtn" name="submit"><span>Submit</span></button>
                </div>
            </div>
        </div><!-- .nk-block -->
    </form>
</div>

<div class="nk-add-product toggle-slide toggle-slide-right" tabindex="-1" data-content="filterDistrict" data-toggle-screen="any" data-toggle-overlay="true" data-toggle-body="true" data-simplebar id="filterDistrict">
    <form action="#" role="form" class="mb-0" method="get">
        @csrf
        <div class="nk-block-head">
            <div class="nk-block-head-content">
                <h5 class="nk-block-title">District Filter</h5>
            </div>
        </div>
        <div class="nk-block">
            <div class="row g-3">
                <div class="col-mb-12">
                    <x-inputs.select size="md" name="filter_state_id" for="filter_state_id" id="filter_state_id" data-search="on" placeholder='Select State'>
                        @if(!empty($states))
                        <option value="" selected disabled>Select State</option>
                        @foreach($states as $key => $state)
                        <option value="{{$state->id}}">{{$state->state_name ?? '' }}</option>
                        @endforeach
                        @endif
                    </x-inputs.select>
                </div>

                <div class="col-mb-12">
                    <x-inputs.select size="md" name="creation_date" for="creation_date" id="creation_date" data-search="on" placeholder='Select Creation Date'>
                        <option value="" selected disabled>Select Creation Date</option>
                        <option value="LastThreeMonth">Last Three Months</option>
                        <option value="LastSixMonth">Last Six Months</option>
                        <option value="CurrentYear">Current Year</option>
                        <option value="LastYear">Last Year</option>
                        <option value="LastThreeYear">Last Three Year</option>
                    </x-inputs.select>
                    </select>
                </div>
            </div>
            <div class="col-12 text-center mt-5">
                <a data-target='filterDistrict' class="btn btn-outline-light cancelFilter">Cancel</a>
                <a class="btn btn-danger resetFilter" data-target='filterDistrict' style="color:#fff">Clear Filter</a>
                <a class="btn btn-primary submitBtnFilter" style="color:#fff">Submit</a>
            </div>
        </div>
    </form>
</div>
@endsection
@push('footerScripts')
<script src="{{ url('js/tableFlow.js') }}"></script>
<script type="text/javascript">
    var validator = null;

    function resetValues() {
        $('.submitBtn').text('Submit');
        $('.modalTitle').text('Add District');
        $('#itemId').val(0);
        $('#district_name').val('');
        $('#state_id').val(0).trigger("change");
        $('#addDistrictForm')[0].reset();
        $('#addDistrictForm').parsley().reset();
        validator.resetForm();
    }

    $('.cancel').on('click', function() {
        resetValues();
        NioApp.Toggle.collapseDrawer('addDistrict')
    })

    $('.cancelFilter').on('click', function() {
        NioApp.Toggle.collapseDrawer('filterDistrict')
    });


    $(function() {
        loadAllDistrictsDataTable();
        var root_url = "<?php echo Request::root(); ?>";
        $('.brand-init').on('click', '.editItem', function() {
            var id = $(this).attr('data-id');
            $.ajax({
                url: root_url + '/master/district/get-district',
                data: {
                    'id': id,
                },
                // dataType: "html",
                method: "GET",
                // cache: false,
                success: function(data) {
                    console.log(data);
                    if (data.success) {
                        $('#itemId').val(id);
                        $('#district_name').val(data.district.district_name);
                        $('#state_id').val(data.district.state_id).trigger('change');
                        $('.submitBtn').text('Update');
                        $('.modalTitle').text('Edit District');
                    } else {
                        Swal.fire('Details not found!')
                    }
                }
            });
        });

        $('.brand-init').on('click', '.eg-swal-av3', function(e) {
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
                        url: root_url + '/master/district/delete',
                        data: {
                            'id': id
                        },
                        //dataType: "html",
                        method: "GET",
                        // cache: false,
                        success: function(data) {
                            console.log(data);
                            if (data.success) {
                                Swal.fire('Deleted!', 'District has been deleted.',
                                    'success');
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
    });


    // add/edit district form with ajax 
    if ($("#addDistrictForm").length > 0) {
        validator = $("#addDistrictForm").validate({
            rules: {
                state_id: {
                    required: true,
                },
                district_name: {
                    required: true,
                    maxlength: 50
                },
            },
            messages: {
                state_id: {
                    required: "Please select state.",
                },
                district_name: {
                    required: "Please enter district name.",
                    maxlength: "Your district name maxlength should be 50 characters long."
                },
            },
            submitHandler: function(form) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                // disabled submit button during process running status
                $(".submitBtn").attr("disabled", true);

                $.ajax({
                    type: "POST",
                    url: "{{ url('master/district/add') }}",
                    data: $('#addDistrictForm').serialize(),
                    success: function(response) {
                        $(".submitBtn").attr("disabled", false);
                        if (response.status == 'success') {
                            // after success response add/edit  hide sidebar form
                            if ($('body').hasClass('toggle-shown')) {
                                //Close the sidebar
                                $('body').removeClass('toggle-shown');
                            }
                            $('#modal').removeClass('content-active');
                            $('#add').removeClass('active');
                            $('.toggle-overlay[data-target="addDistrict"]').remove();

                            // refresh datatable without page reload
                            var table = $('.brand-init').DataTable();
                            table.ajax.reload();
                            toastr.success(response.message);
                        } else {
                            toastr.error(response.message);
                        }
                    }
                });
            }
        });
    }

    $("#state_id").change(function() {
        $("#addDistrictForm").validate().element("#state_id");
    });

    $(document).on("click", function(event) {
        var overlay = $("#filterDistrict");
        if (!overlay.is(event.target) && overlay.has(event.target).length === 0) {
            //resetForm();
        }
    });

    $(document).on("click", function(event) {
        var overlay = $("#modal");
        if (!overlay.is(event.target) && overlay.has(event.target).length === 0) {
            resetValues();
        }
    });

    function loadAllDistrictsDataTable() {
        $('.brand-init').DataTable().destroy();
        var items = [
            '#filter_state_id', '#creation_date'
        ];
        var dt = '';
        dt = new CustomDataTable({
            tableElem: '.brand-init',
            option: {
                processing: true,
                serverSide: true,
                ajax: {
                    type: "GET",
                    url: "{{ url('master/district')}}",
                },
                columns: [{
                        "class": "nk-tb-col tb-col-lg",
                        data: 'district_name',
                        name: 'district_name',
                        orderable: true
                    },
                    {
                        "class": "nk-tb-col tb-col-lg",
                        data: 'state_name',
                        name: 'state_name',
                        orderable: true
                    },
                    {
                        "class": "nk-tb-col tb-col-lg",
                        data: 'created_by',
                        name: 'created_by',
                        orderable: true
                    },
                    {
                        "class": "nk-tb-col tb-col-lg",
                        data: 'updated_by',
                        name: 'updated_by',
                        orderable: true
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
                NioApp.Toggle.collapseDrawer('filterDistrict')
            },
            filterClearSubmit: '.resetFilter',
            filterModalId: '#filterDistrict',
            filterItems: items,
            tagId: '#filter_tag_list',
        });
    }
</script>
@endpush