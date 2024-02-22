@extends('layouts.app')
<meta name="csrf-token" content="{{ csrf_token() }}" />
@section('content')
<div class="nk-block-head nk-block-head-sm">
    <div class="nk-block-between">
        <div class="nk-block-head-content">
            <h5 class="nk-block-title page-title">Documents </h5>
        </div>
        <div class="nk-block-head-content">
            <div class="toggle-wrap nk-block-tools-toggle">
                <a href="#" class="btn btn-icon btn-trigger toggle-expand mr-n1" data-target="more-options"><em class="icon ni ni-more-v"></em></a>
                <div class="toggle-expand-content" data-content="more-options">
                    <ul class="nk-block-tools g-3">
                        <li class="nav-item">
                            <a class="btn btn-primary nav-link mr-2" href="#" data-target='#addFolderModal' data-toggle='modal'><em class="icon ni ni-plus" style="color: #fff;"></em><span>Add Folder</span></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="tab-content">
    <div class="tab-pane fade show active mt-5" id="all-folders">
        <div class="row">
            @if(!empty($folders))
            @foreach($folders as $folder)
            <div class="col-md-2 mb-4">
                <div class="d-flex flex-column">
                    <div class="align-items-center">
                        <!-- Folder Icon -->
                        <span class="nk-menu-icon">
                            <em class="icon ni ni-folder-fill" style="font-size: 160px;"></em><br>
                        </span>

                        <div class="d-flex ml-2 align-items-center">
                            <!-- Folder Name -->
                            <span class="nk-menu-text">
                                <a href="{{ url('documents/folder/' . $folder->id) }}" style="text-decoration: none; cursor: pointer;" title="{{ $folder->name ?? ''}}">
                                    {{ $folder->name ?? ''}}
                                </a>
                            </span>

                            <!-- More Options Dropdown -->
                            <div class='drodown ml-2'>
                                <a href='#' class='dropdown-toggle btn btn-icon btn-trigger' data-toggle='dropdown'>
                                    <em class='icon ni ni-more-v'></em>
                                </a>
                                <div class='dropdown-menu dropdown-menu-right'>
                                    <ul class='link-list-opt no-bdr'>
                                        <li>
                                            <a href="#" class="toggle" onclick="getFolderName('{{ $folder->name }}', '{{ $folder->id }}');" title="rename folder">

                                                <em class='icon ni ni-edit'></em> <span>Rename</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#" class='toggle' onclick="deleteFolder('{{ $folder->id }}');" title="delete folder">
                                                <em class='icon ni ni-trash'></em> <span>Delete</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#" class='toggle' onclick="getUploadFolder('{{ $folder->name }}', '{{ $folder->id }}');" title="upload document">
                                                <em class='icon ni ni-upload-cloud'></em> <span>Upload</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ url('documents/view-folder-permissions/'. $folder->id)}}" title="manage permissions">
                                                <em class='icon ni ni-note-add'></em> <span>Manage Permissions</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @endforeach
            @endif
        </div>
    </div>
</div>

<div class="modal fade zoom" tabindex="-1" id="addFolderModal" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Folder</h5>
                <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
            </div>
            <form action="#" role="form" class="mb-0" method="post" id="addFolderForm">
                @csrf
                <div class="modal-body modal-body-lg">
                    <div class="gy-3">
                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <label for="folder_name" class="form-label mr-2">Folder Name<span class="text-danger">*</span></label>
                            </div>
                            <div class="col-lg-7">
                                <input type="text" name="folder_name" id="folder_name" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <div class="row">
                        <div class="col-lg-12 p-0 text-right">
                            <button class="btn btn-outline-light" data-dismiss="modal" aria-label="Close">Cancel</button>
                            <button class="btn btn-primary submitBtnAddFolder" type="submit">Submit</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade zoom" tabindex="-1" id="renameFolderModal" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Rename Folder</h5>
                <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
            </div>
            <form action="#" role="form" class="mb-0" method="post" id="renameFolderForm">
                @csrf
                <div class="modal-body modal-body-lg">
                    <div class="gy-3">
                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <label for="rename_folder" class="form-label mr-2">Folder Name<span class="text-danger">*</span></label>
                            </div>
                            <div class="col-lg-7">
                                <input type="text" name="rename_folder" id="rename_folder" class="form-control">
                                <input type="hidden" name="rename_folder_id" id="rename_folder_id" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <div class="row">
                        <div class="col-lg-12 p-0 text-right">
                            <button class="btn btn-outline-light" data-dismiss="modal" aria-label="Close">Cancel</button>
                            <button class="btn btn-primary submitBtnRenameFolder" type="submit">Submit</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade zoom" tabindex="-1" id="uploadDocumentModal" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Documents</h5>
                <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
            </div>
            <form action="#" role="form" class="mb-0" method="post" id="uploadDocumentForm">
                @csrf
                <div class="modal-body modal-body-lg">
                    <div class="gy-3">
                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <label for="folder" class="form-label mr-2">Folder</label>
                            </div>
                            <div class="col-lg-7">
                                <span id="folderName"></span>
                            </div>
                        </div>
                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <label for="rename_folder" class="form-label mr-2">Documents<span class="text-danger">*</span></label>
                            </div>
                            <div class="col-lg-7">
                                <div class="form-control-wrap">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="attach_file" name="attach_file[]" multiple onchange="updateFileName(this)" data-parsley-excluded="true" accept=".doc, .docx, .pdf, .txt, .xls, .xlsx, .jpg, .jpeg, .png"><label class="custom-file-label" for="attach_file[]">Choose file</label>
                                        <span id="filesSelected"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="folderId" id="folderId">
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <div class="row">
                        <div class="col-lg-12 p-0 text-right">
                            <button class="btn btn-outline-light" data-dismiss="modal" aria-label="Close">Cancel</button>
                            <button class="btn btn-primary submitBtnUploadDocument" type="submit">Submit</button>
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
    var root_url = "<?php echo Request::root(); ?>";
    if ($("#addFolderForm").length > 0) {
        $("#addFolderForm").validate({
            rules: {
                folder_name: {
                    required: true,
                }
            },
            messages: {
                folder_name: {
                    required: "Please enter folder name.",
                }
            },
            submitHandler: function(form) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                // disabled submit button during process running status
                $(".submitBtnAddFolder").attr("disabled", true);

                $.ajax({
                    type: "POST",
                    url: "{{ url('documents/add-folder') }}",
                    data: $('#addFolderForm').serialize(),
                    success: function(response) {
                        $(".submitBtnAddFolder").attr("disabled", false);
                        if (response.status == 'success') {
                            form.reset();
                            $('#addFolderModal').modal('toggle');
                            // refresh datatable without page reload 
                            $('#all-folders').load(window.location.href + ' #all-folders');
                            toastr.success(response.message);
                        } else {
                            toastr.error(response.message);
                        }
                    }
                });
                $(".submitBtnAddFolder").attr("disabled", false);
            }
        });
    }
    $('#addFolderModal').on('hidden.bs.modal', function(e) {
        // Reset form fields 
        $('#addFolderForm')[0].reset();
        $('#folder_name').val('').trigger('change');
        $('#addFolderForm input').removeClass('error');
        // Reset validate.js validations 
        if ($('#addFolderForm').validate && typeof $('#addFolderForm').validate === 'function') {
            $('#addFolderForm').validate().resetForm();
        }
    });

    $('#renameFolderModal').on('hidden.bs.modal', function(e) {
        // Reset form fields 
        $('#renameFolderForm')[0].reset();
        $('#renameFolderForm input').removeClass('error');
        // Reset validate.js validations 
        if ($('#renameFolderForm').validate && typeof $('#renameFolderForm').validate === 'function') {
            $('#renameFolderForm').validate().resetForm();
        }
    });

    function getFolderName(folder_name, folder_id) {
        $('#rename_folder').val(folder_name);
        $('#rename_folder_id').val(folder_id);
        $('#renameFolderModal').modal('show');
    }
    if ($("#renameFolderForm").length > 0) {
        $("#renameFolderForm").validate({
            rules: {
                rename_folder: {
                    required: true,
                }
            },
            messages: {
                rename_folder: {
                    required: "Please enter folder name.",
                }
            },
            submitHandler: function(form) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                // disabled submit button during process running status
                $(".submitBtnRenameFolder").attr("disabled", true);

                $.ajax({
                    type: "POST",
                    url: "{{ url('documents/rename-folder') }}",
                    data: $('#renameFolderForm').serialize(),
                    success: function(response) {
                        $(".submitBtnRenameFolder").attr("disabled", false);
                        if (response.status == 'success') {
                            $('#renameFolderModal').modal('toggle');
                            // refresh list without page reload 
                            $('#all-folders').load(window.location.href + ' #all-folders');
                            toastr.success(response.message);
                        } else {
                            toastr.error(response.message);
                        }
                    }
                });
                $(".submitBtnRenameFolder").attr("disabled", false);
            }
        });
    }

    function deleteFolder(folderId) {
        Swal.fire({
            title: 'Are you sure you want to delete this folder?',
            text: "Deleting the folder will also result in the removal of the documents contained in it. You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Delete it!'
        }).then(function(result) {
            if (result.value) {
                $.ajax({
                    url: root_url + '/documents/delete-folder',
                    data: {
                        'id': folderId
                    },
                    method: "GET",
                    success: function(response) {
                        // console.log(data);
                        if (response.status == 'success') {
                            Swal.fire('Deleted!', response.message,
                                'success');
                            $('#all-folders').load(window.location.href + ' #all-folders');
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: response.message,
                            })
                        }
                    }
                });
            }
        });
    }

    function updateFileName(input) {
        var files = input.files;
        var label = $('#filesSelected');

        if (files.length > 1) {
            label.text(files.length + ' files selected');
        } else if (files.length === 1) {
            label.text(files[0].name);
        } else {
            label.text('Choose file');
        }
    }

    function getUploadFolder(folder_name, folder_id) {
        $('#folderName').text(folder_name);
        $('#folderId').val(folder_id);
        $('#uploadDocumentModal').modal('show');
    }

    if ($("#uploadDocumentForm").length > 0) {
        $("#uploadDocumentForm").validate({
            rules: {
                'attach_file[]': {
                    required: true,
                }
            },
            messages: {
                'attach_file[]': {
                    required: "Please select at least one document.",
                }
            },
            submitHandler: function(form) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                // disable submit button during the process
                $(".submitBtnUploadDocument").attr("disabled", true);

                $.ajax({
                    type: "POST",
                    url: "{{ url('documents/uploads-documents') }}",
                    data: new FormData($('#uploadDocumentForm')[0]),
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $(".submitBtnUploadDocument").attr("disabled", false);
                        if (response.status == 'success') {
                            $('#uploadDocumentModal').modal('toggle');
                            // refresh list without page reload 
                            $('#all-folders').load(window.location.href + ' #all-folders');
                            toastr.success(response.message);
                        } else {
                            toastr.error(response.message);
                        }
                    }
                });

                // re-enable submit button after the process
                $(".submitBtnUploadDocument").attr("disabled", false);
            }
        });
    }

    $('#uploadDocumentModal').on('hidden.bs.modal', function(e) {
        // Reset form fields 
        $('#uploadDocumentForm')[0].reset();
        $('#uploadDocumentForm input').removeClass('error');
        $('#filesSelected').text('');
        $('#attach_file').val('');
        $('.custom-file-label').text('Choose file');
        // Reset validate.js validations 
        if ($('#uploadDocumentForm').validate && typeof $('#uploadDocumentForm').validate === 'function') {
            $('#uploadDocumentForm').validate().resetForm();
        }
    });
</script>
@endpush