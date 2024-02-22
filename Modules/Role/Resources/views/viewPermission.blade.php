@extends('layouts.app')
<meta name="csrf-token" content="{{ csrf_token() }}" />
<style type="text/css">
    .w-200 .select2-container {
        width: 200px !important;
    }
</style>
@section('content')
<!-- <a class="btn" style="font-size: 18px;margin-bottom: 10px;" class="btn" href="{{url('/role')}}"><em class='icon ni ni-arrow-left'></em>Back</a> -->
<div class="nk-block-head nk-block-head-sm">
    <div class="nk-block-between">
        <div class="nk-block-head-content">
            <h3 class="nk-block-title page-title">Permissions</h3>
        </div><!-- .nk-block-head-content -->
        <div class="nk-block-head-content">
            <div class="toggle-wrap nk-block-tools-toggle">
                <a href="#" class="btn btn-icon btn-trigger toggle-expand mr-n1" data-target="more-options"><em class="icon ni ni-more-v"></em></a>
                <div class="toggle-expand-content" data-content="more-options">
                    <ul class="nk-block-tools g-3">


                    </ul>
                </div>
            </div>
        </div><!-- .nk-block-head-content -->
    </div><!-- .nk-block-between -->
</div><!-- .nk-block-head -->

<div class="nk-block-head-content" style="display: flex; align-items: center;">
    <div class="row ml-1" style="flex-grow: 1; display: flex; align-items: baseline;">
        <h6 style="margin-right: 10px;">Role:</h6>
        <h6 style="color: black; font-weight: bold;">{{ $roleName ?? ''}}</h6>
    </div>

    <div class="custom-control custom-switch mb-2">
        <input type="checkbox" class="custom-control-input master-toggle-checkbox" name="masterToggle" id="masterToggle">
        <label class="custom-control-label" for="masterToggle">Select All Permission</label>
    </div>
</div>



<form role="form" class="mb-0" method="post" id="updatePermissionForm">
    @csrf
    <input type="hidden" name="role_id" class="role_id" value="{{$roleId}}">
    <div class="nk-block">
        <div class="card">
            <div class="card-inner">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Module</th>
                            <th class="text-center">Permission</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(!empty($permissionCategories))
                        @foreach($permissionCategories as $permissionCategory)
                        <tr>
                            <td>{{ ucwords($permissionCategory->category_name) }}</td>
                            <td class="text-center">
                                <div class="custom-control custom-switch">
                                    <input type="hidden" name="{{ strtolower(str_replace(' ', '_',$permissionCategory->category_name)) }}_id" value="{{ $permissionCategory->id }}">
                                    <input type="checkbox" class="custom-control-input toggle-checkbox" name="{{ strtolower(str_replace(' ', '_',$permissionCategory->category_name)) }}" id="{{ $permissionCategory->category_name }}" value="full-access" @if($permissions->contains('category_name', $permissionCategory->category_name)) checked @endif>
                                    <label class="custom-control-label" for="{{ $permissionCategory->category_name }}"></label>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="nk-block">
        <div class="">
            <div class="row">
                <div class="col-md-12">
                    <div class="sp-plan-info pt-0 pb-0 card-inner">
                        @csrf
                        <div class="row">
                            <div class="col-lg-7 text-right offset-lg-5">
                                <div class="form-group">
                                    <a href="{{url('/role')}}" class="btn btn-outline-light">Cancel</a>
                                    <a class="btn btn-danger cancel" href="" id="resetPermission">Reset</a>
                                    <input type="submit" class="btn btn-primary" value="Save" id="updatePermission">
                                </div>
                            </div>
                        </div>
                    </div><!-- .sp-plan-info -->
                </div><!-- .col -->
            </div><!-- .row -->
        </div>
        <!--  -->
    </div>
</form>

@endsection
@push('footerScripts')
<script type="text/javascript">
    $(document).ready(function() {
        $('#updatePermission').on('click', function(e) {
            $("#updatePermission").prop("disabled", true);

            // Initialize an empty array to store the selected categories
            var dataArray = [];

            // Get all checkboxes
            var checkboxes = document.querySelectorAll('.custom-control-input');

            // Loop through checkboxes to find the selected ones
            checkboxes.forEach(function(checkbox) {
                if (checkbox.checked) {
                    // If checkbox is checked, add the corresponding data to the array
                    var previousSibling = checkbox.previousElementSibling;
                    var categoryId = previousSibling && previousSibling.tagName === 'INPUT' ? previousSibling.value : null;
                    var categoryName = checkbox.name;
                    var categoryPermission = checkbox.value;

                    dataArray.push({
                        categoryId: categoryId,
                        categoryName: categoryName,
                        categoryPermission: categoryPermission
                    });
                }
            });

            // Create FormData and add the array as a parameter
            var form = $('#updatePermissionForm')[0];
            var data = new FormData(form);
            data.append('categoryData', JSON.stringify(dataArray));

            e.preventDefault();
            $.ajax({
                type: "POST",
                enctype: 'multipart/form-data',
                url: "{{ url('role/update-permission') }}",
                data: data,
                processData: false,
                contentType: false,
                cache: false,
                timeout: 800000, // in milliseconds
                success: function(response) {
                    if (response.status == 'success') {
                        toastr.success(response.message);
                        $("#updatePermission").prop("disabled", false);
                        //window.location.href = "{{ url('dashboard') }}";
                    } else {
                        toastr.error(response.message);
                        $("#updatePermission").prop("disabled", false);
                    }
                    $('#updatePermission').removeClass('active');
                }
            });
        });

        function updateMasterToggle() {
            var allChecked = true;
            $('.toggle-checkbox').each(function() {
                if (!$(this).prop('checked')) {
                    allChecked = false;
                    return false; // Exit loop if any category checkbox is unchecked
                }
            });
            $('#masterToggle').prop('checked', allChecked);
        }

        // Toggle all checkboxes when the master toggle is clicked
        $('#masterToggle').change(function() {
            var isChecked = $(this).prop('checked');
            $('.toggle-checkbox').prop('checked', isChecked);
        });
        $('.toggle-checkbox').change(function() {
            updateMasterToggle();
        });
        // $('#resetPermission').on('click', function(e) {
        //     $('#updatePermissionForm').load(window.location.href + ' #updatePermissionForm');
        // });
        updateMasterToggle();
    });
</script>
@endpush