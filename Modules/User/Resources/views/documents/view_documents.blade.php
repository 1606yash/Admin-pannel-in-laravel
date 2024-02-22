@extends('layouts.app')
<meta name="csrf-token" content="{{ csrf_token() }}" />
@section('content')

<div class="nk-block-head nk-block-head-sm">
    <div class="nk-block-between">
        <div class="nk-block-head-content">
            <a class="justify-content-right" style="color:inherit;font-size: 18px;margin-bottom: 10px;letter-spacing: 0.02em;position:relative;" class="btn" href="javascript:history.back()"><em class='icon ni ni-arrow-left'></em>Back</a>
        </div>
        <div class="nk-block-head-content">
            <div class="toggle-wrap nk-block-tools-toggle">
                <a href="#" class="btn btn-icon btn-trigger toggle-expand mr-n1" data-target="more-options"><em class="icon ni ni-more-v"></em></a>
                <div class="toggle-expand-content" data-content="more-options">
                    <ul class="nk-block-tools g-3">
                        <li class="nav-item">
                            <a class="btn btn-primary nav-link mr-2" href="#" data-target='#uploadDocumentModal' data-toggle='modal'><em class="icon ni ni-upload-cloud" style="color: #fff;"></em><span>Upload Document</span></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="nk-block-head-content mt-5">
        <h5 class="nk-block-title page-title">
            <em class="icon ni ni-folder-fill" style="font-size: 24px; margin-right: 8px;"></em>
            {{ $folderName ?? ''}}
        </h5>
    </div>

</div>
<div class="tab-content">
    <div class="tab-pane fade show active mt-2" id="all-documents">
        <div class="row">
            @if(!empty($getDocuments))
            @foreach($getDocuments as $document)
            <div class="col-md-2 mb-4">
                <div class="d-flex flex-column">
                    <div class="align-items-center">
                        <span class="nk-menu-icon pl-4">
                            <em class="icon ni ni-file-text-fill" style="font-size: 100px;"></em><br>
                        </span>

                        <div class="d-flex ml-2 align-items-center">
                            <span class="nk-menu-text pl-4">
                                <a href="{{ $document->path ?? ''}}" style="text-decoration: none; text-align:center; cursor: pointer;" target="_blank" title="{{ $document->name ?? ''}}">{{ $document->name ?? ''}}
                                </a>
                            </span>

                            <div class='drodown ml-2'>
                                <a href='#' class='dropdown-toggle btn btn-icon btn-trigger' data-toggle='dropdown'>
                                    <em class='icon ni ni-more-v'></em>
                                </a>
                                <div class='dropdown-menu dropdown-menu-right'>
                                    <ul class='link-list-opt no-bdr'>
                                        <li>
                                            <a href="#" class='toggle' onclick="deleteDocument('{{ $document->id }}');">
                                                <em class='icon ni ni-trash'></em> <span>Delete</span>
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
                                <span id="folderName">{{ $folderName ?? ''}}</span>
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
                        <input type="hidden" name="folderId" id="folderId" value="{{ $folderId ?? ''}}">
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
                            $('#all-documents').load(window.location.href + ' #all-documents');
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

    function deleteDocument(documentId) {
        Swal.fire({
            title: 'Are you sure you want to delete this document?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Delete it!'
        }).then(function(result) {
            if (result.value) {
                $.ajax({
                    url: root_url + '/documents/delete-document',
                    data: {
                        'id': documentId
                    },
                    method: "GET",
                    success: function(response) {
                        // console.log(data);
                        if (response.status == 'success') {
                            Swal.fire('Deleted!', response.message,
                                'success');
                            $('#all-documents').load(window.location.href + ' #all-documents');
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
</script>
@endpush