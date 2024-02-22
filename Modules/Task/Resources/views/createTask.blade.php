@extends('layouts.app')
<meta name="csrf-token" content="{{ csrf_token() }}" />
@section('content')
<a class="btn" style="font-size: 18px; margin-bottom: 10px;" href="javascript:history.back()"><em class='icon ni ni-arrow-left'></em> Back</a>

<div class="container">
    <form role="form" method="post" id="addTaskForm" enctype="multipart/form-data">
        @csrf

        <div class="card">
            <div class="card-header">
                <h5>Create Task</h5>
            </div>
            <div class="card-body">
                <div class="form-row">
                    <div class="form-group col-md-1">
                    </div>
                    <div class="form-group col-md-2">
                        <label for="title">Task Title<span class="text-danger">*</span></label>
                    </div>
                    <div class="form-group col-md-2">
                        <input type="text" class="form-control" id="title" name="title" value="" placeholder="Enter Task Title" onkeypress="return isCharSpace(event)" data-parsley-excluded="true">
                    </div>
                    <div class="form-group col-md-2">
                    </div>
                    <div class="form-group col-md-2">
                        <label for="priority">Priority<span class="text-danger">*</span></label>
                    </div>

                    <div class="form-group col-md-2 form-control-required-wrap">
                        <select name="priority" id="priority" class="form-control form-select" data-search="on" data-parsley-excluded="true">
                            <option selected disabled>Select Priority</option>
                            <option value="high">High</option>
                            <option value="medium">Medium</option>
                            <option value="low">Low</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-1">
                    </div>
                    <div class="form-group col-md-2">
                        <label for="attach_file">Attach File</label>
                    </div>
                    <div class="form-group col-md-2">
                        <!-- <div class="custom-file mb-3">
                            <input type="file" id="attach_file" name="attach_file" style="display: none;">
                            <label for="attach_file">
                                <span class="icon ni ni-link"></span> Tap to attach a file
                            </label>
                        </div> -->
                        <div class="form-control-wrap">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="attach_file" name="attach_file[]" multiple onchange="updateFileName(this)" data-parsley-excluded="true"><label class="custom-file-label" for="attach_file">Choose file</label>
                                <span id="filesSelected"></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-md-2">
                    </div>
                    <div class="form-group col-md-2">
                        <label for="user_id" class="mr-2">Assign To<span class="text-danger">*</span></label>
                    </div>

                    <div class="form-group col-md-2 form-control-required-wrap">
                        <select name="assigned_to" id="assigned_to" class="form-control form-select" data-search="on" data-parsley-excluded="true">
                            <option selected disabled>Select User</option>
                            @if(!empty($users))
                            @foreach($users as $user)
                            <option value="{{ $user->id }}">
                                {{ $user->user_name }} ({{ $user->role_name }})
                            </option>
                            @endforeach
                            @endif
                            <!-- Add your options here -->
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-1">
                    </div>
                    <div class="form-group col-md-10">
                        <label for="description">Task Description<span class="text-danger">*</span></label>
                        <textarea class="form-control" id="description" name="description" rows="4" placeholder="Enter Task Description Here ..." data-parsley-excluded="true"></textarea>
                    </div>

                </div>
            </div>
        </div>

        <div class="mt-3">
            <div class="form-group text-right my-2">
                <a class="btn btn-outline-light cancel" href="javascript:history.back()">Cancel</a>
                <a class="btn btn-danger resetFilter cancel" href="">Reset</a>
                <button class="btn btn-primary submitBtn" name="submit"><span>Submit</span></button>
            </div>
        </div>
    </form>
</div>
@endsection
@push('footerScripts')
<script src="{{url('js/additional-methods.js')}}"></script>
<script>
    $(document).ready(function() {
        if ($("#addTaskForm").length > 0) {
            $("#addTaskForm").validate({
                rules: {
                    title: {
                        required: true,
                        maxlength: 50
                    },
                    description: {
                        required: true,
                        maxlength: 150,
                    },
                    priority: {
                        required: true,
                    },
                    assigned_to: {
                        required: true,
                    },
                },
                messages: {
                    title: {
                        required: "Please enter task title",
                        maxlength: "Your role name maxlength should not exceed more than 50 characters long."
                    },
                    description: {
                        required: "Please enter task description",
                        maxlength: "Your task description maxlength should not exceed more than 150 characters long."
                    },
                    priority: {
                        required: "Please select task priority ",
                    },
                    assigned_to: {
                        required: "Please select assigned user",
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
                        url: "{{ url('task/store-task') }}",
                        data: new FormData($('#addTaskForm')[0]),
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            $(".submitBtn").attr("disabled", false);
                            if (response.status == 'success') {
                                form.reset();
                                window.location.href = "{{ url('task') }}";
                                toastr.success(response.message);
                            } else {
                                toastr.error(response.message);
                            }
                        }
                    });
                    $(".submitBtn").attr("disabled", false);
                }
            });
        }
        $("#priority").change(function() {
            $("#addTaskForm").validate().element("#priority");
        });
        $("#assigned_to").change(function() {
            $("#addTaskForm").validate().element("#assigned_to");
        });
    });

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
</script>
@endpush