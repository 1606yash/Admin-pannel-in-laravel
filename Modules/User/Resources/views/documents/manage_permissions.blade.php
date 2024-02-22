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
            <h3 class="nk-block-title page-title">Folder Permissions</h3>
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
<form role="form" class="mb-0" method="post" id="updatePermissionForm">
    @csrf
    <div class="nk-block-head-content" style="display: flex; align-items: center;">
        <div class="row ml-1" style="flex-grow: 1; display: flex; align-items: baseline;">
            <h6 style="margin-right: 10px;">Folder:</h6>
            <h6 style="color: black; font-weight: bold;">{{ $folder->name ?? ''}}</h6>
        </div>

        <div class="form-group" style="padding-right: 15px; width: 150px;">
            @php
            $viewFolderPermission = $folderPermissionCategories->firstWhere('category_name', 'View Folder');
            @endphp
            <div class="custom-control custom-switch mb-2">
                <input type="hidden" name="view_folder_id" value="{{ $viewFolderPermission->id }}">
                <input type="checkbox" class="custom-control-input access-toggle-checkbox" name="view_folder" id="view_folder" value="full-access">
                <label class="custom-control-label" for="view_folder">Access</label>
            </div>
        </div>

        <div class="form-group" style="padding-right: 15px; width: 200px;">
            <select name="role_id" id="role_id" class="form-control form-select" data-search="on" placeholder="Select Role" onchange="getFolderPermission();">
                @if(!empty($roles))
                @foreach($roles as $role)
                <option value="{{$role->id}}">{{$role->role_name}}</option>
                @endforeach
                @endif
            </select>
        </div>

        <div class="form-group" style="padding-right: 15px; width: 150px;">
            <div class="custom-control custom-switch mb-2">
                <input type="checkbox" class="custom-control-input master-toggle-checkbox" name="masterToggle" id="masterToggle">
                <label class="custom-control-label" for="masterToggle">Select All Permission</label>
            </div>
        </div>
    </div>


    <input type="hidden" name="folder_id" class="folder_id" value="{{ $folder->id ?? '' }}">
    <div class="nk-block">
        <div class="card">
            <div class="card-inner">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Folder Permission</th>
                            <th class="text-center">Permission</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(!empty($folderPermissionCategories))
                        @foreach($folderPermissionCategories as $folderPermissionCategory)
                        @if($folderPermissionCategory->category_name != 'View Folder')
                        <tr class="category-row">
                            <td>{{ ucwords($folderPermissionCategory->category_name) }}</td>
                            <td class="text-center">
                                <div class="custom-control custom-switch">
                                    <input type="hidden" name="{{ strtolower(str_replace(' ', '_',$folderPermissionCategory->category_name)) }}_id" value="{{ $folderPermissionCategory->id }}">
                                    <input type="checkbox" class="custom-control-input toggle-checkbox" name="{{ strtolower(str_replace(' ', '_',$folderPermissionCategory->category_name)) }}" id="{{ $folderPermissionCategory->category_name }}" value="full-access">
                                    <label class="custom-control-label" for="{{ $folderPermissionCategory->category_name }}"></label>
                                </div>
                            </td>
                        </tr>
                        @endif
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
                                    <a href="{{url('/documents')}}" class="btn btn-outline-light">Cancel</a>
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
    // Check master toggle checkbox initially when the page loads
    updateMasterToggle();
    getFolderPermission();
    $(document).ready(function() {

        // Check master toggle checkbox when all category checkboxes are checked
        $('.toggle-checkbox').change(function() {
            var anyChecked = false;
            $('.toggle-checkbox').each(function() {
                if ($(this).is(':checked')) {
                    anyChecked = true;
                    return false; // exit the loop early if any checkbox is checked
                }
            });

            // Update the state of access-toggle-checkbox
            $('.access-toggle-checkbox').prop('checked', anyChecked);
            updateMasterToggle();
        });

        // Toggle all checkboxes when the master toggle is clicked
        $('#masterToggle').change(function() {
            var isChecked = $(this).prop('checked');
            $('.toggle-checkbox').prop('checked', isChecked);
            if (isChecked === true) {
                $('.access-toggle-checkbox').prop('checked', isChecked);
            }

        });

        $('#updatePermission').on('click', function(e) {
            $("#updatePermission").prop("disabled", true);

            // Initialize an empty array to store the selected categories
            var dataArray = [];

            // Get all checkboxes
            var checkboxes = document.querySelectorAll('.custom-control-input');
            var roleId = $('#role_id').val();
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
            data.append('role_id', roleId);

            e.preventDefault();
            $.ajax({
                type: "POST",
                enctype: 'multipart/form-data',
                url: "{{ url('documents/update-folder-permissions') }}",
                data: data,
                processData: false,
                contentType: false,
                cache: false,
                timeout: 800000, // in milliseconds
                success: function(response) {
                    if (response.status == 'success') {
                        toastr.success(response.message);
                        getFolderPermission();
                    } else {
                        toastr.error(response.message);
                    }
                    $('#updatePermission').removeClass('active');
                }
            });
            $("#updatePermission").prop("disabled", false);
        });
    });

    function updateMasterToggle() {
        var viewFolderChecked = $('input[name="view_folder"]').prop('checked');
        var allOtherChecked = true;

        $('.toggle-checkbox').each(function() {
            if (!$(this).prop('checked')) {
                allOtherChecked = false;
                return false; // exit each loop early
            }
        });
        $('#masterToggle').prop('checked', viewFolderChecked && allOtherChecked);
    }

    function getFolderPermission() {
        $('.overlay').show();
        var folderId = "{{ $folder->id ?? '' }}";
        var roleId = $('#role_id').val();
        var allCategoriesChecked = true; // Flag to track all categories checked
        $.ajax({
            url: "{{url('/documents/get-folder-permissions-by-role')}}",
            type: 'GET',
            dataType: 'json',
            data: {
                folder_id: folderId,
                role_id: roleId,
            },
            success: function(response) {

                // Uncheck all checkboxes
                $('.toggle-checkbox').prop('checked', false);
                $('.master-toggle-checkbox').prop('checked', false);

                response.folderPermissions.forEach(function(permission) {
                    var categoryName = permission.category_name;
                    if (categoryName) {
                        var dynamicCategoryId = categoryName.toLowerCase().replace(/\s+/g, '_');
                        $('input[name="' + dynamicCategoryId + '"]').prop('checked', true);
                    }
                });
                // Check if all categories are checked
                $('.toggle-checkbox').each(function() {
                    if (!$(this).prop('checked')) {
                        allCategoriesChecked = false;
                        return false; // Exit loop early if any category is unchecked
                    }
                });

                if (response.folderPermissions.length == 0) {
                    $('input[name="view_folder"]').prop('checked', false);
                    allCategoriesChecked = false;
                }

                // Update master toggle checkbox based on allCategoriesChecked flag
                $('.master-toggle-checkbox').prop('checked', allCategoriesChecked);
            },
            complete: function() {
                $('.overlay').hide();
            }
        });
    }

    $(document).ready(function() {
        // Function to handle view folder checkbox change event
        $('input[name="view_folder"]').change(function() {
            if ($(this).prop('checked')) {
                //$('.toggle-checkbox').prop('checked', true);
                //$('.category-row').removeClass('d-none');

            } else {
                $('.toggle-checkbox').prop('checked', false);
                //$('.category-row').addClass('d-none');
            }
            updateMasterToggle();
        });
    });
</script>
@endpush