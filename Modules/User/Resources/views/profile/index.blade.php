@extends('layouts.app')
@section('content')
@php
$profileDetails = Helpers::getProfileDetailsOnDashboard();
@endphp
<style>
    .data-item .data-label {
        width: 30%
    }

    .data-item .data-value {
        width: 100%
    }

    .data-item .data-col-end {
        width: 50px
    }

    .data-item .data-value .select2,
    .data-item .data-value .select2 span {
        display: grid;
    }
</style>
<div class="nk-block">
    <div class="card">
        <div class="card-aside-wrap">
            <div class="card-inner card-inner-lg">
                <div class="nk-block-head nk-block-head-lg">
                    <div class="nk-block-between">
                        <div class="nk-block-head-content">
                            <h4 class="nk-block-title">Personal Information</h4>
                            <div class="nk-block-des">
                                <p>Basic info, like your name and address.</p>
                            </div>
                        </div>
                        <div class="nk-block-head-content align-self-start d-lg-none">
                            <a href="#" class="toggle btn btn-icon btn-trigger mt-n1" data-target="userAside"><em class="icon ni ni-menu-alt-r"></em></a>
                        </div>
                    </div>
                </div><!-- .nk-block-head -->
                <div class="nk-block">
                    <div class="nk-data data-list">
                        <form role="form" method="post" enctype="multipart/form-data" id="updateProfileForm" novalidate>
                            @csrf
                            <div class="data-item inputs_user_details">
                                <div class="data-col">
                                    <span class="data-label form-label">Full Name<span class="text-danger">*</span></span>
                                    <span class="data-value data-input">
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col">
                                                    <input type="text" class="form-control" value="{{ $user->first_name }}" for="firstName" name="first_name" label="First Name" placeholder="Enter First Name" onkeypress="return isCharSpace(event)" maxlength="50" />
                                                </div>
                                                <div class="col">
                                                    <input type="text" class="form-control" value="{{ $user->middle_name }}" for="middleName" name="middle_name" label="Middle Name" placeholder="Enter Middle Name" onkeypress="return isCharSpace(event)" maxlength="50" />
                                                </div>
                                                <div class="col">
                                                    <input type="text" class="form-control sm-2" value="{{ $user->last_name }}" for="lastName" name="last_name" label="Last Name" placeholder="Enter Last Name" onkeypress="return isCharSpace(event)" maxlength="50" />
                                                </div>
                                            </div>
                                        </div>
                                    </span>
                                </div>
                            </div><!-- data-item -->

                            <div class="data-item">
                                <div class="data-col">
                                    <span class="data-label form-label">{{trans('user::labels.designation')}}</span>
                                    <span class="data-value ml-2">{{ $profileDetails->role_name }}</span>
                                </div>
                                <div class="data-col data-col-end">
                                    <span class="data-more disable"><em class="icon ni ni-lock-alt"></em></span>
                                </div>
                            </div>
                            <div class="data-item">
                                <div class="data-col">
                                    <span class="data-label form-label">{{trans('user::labels.email')}}</span>
                                    <span class="data-value ml-2">{{ $user->email }}</span>
                                </div>
                                <div class="data-col data-col-end">
                                    <span class="data-more disable"><em class="icon ni ni-lock-alt"></em></span>
                                </div>
                            </div><!-- data-item -->

                            <div class="data-item">
                                <div class="data-col">
                                    <span class="data-label form-label">{{trans('user::labels.phoneno')}}<span class="text-danger">*</span></span>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col">
                                                <input type="text" class="form-control" value="{{ $user->phone_no }}" placeholder="Enter Mobile Number" name="phone_no" for="phoneNo" maxlength="10" onkeypress="return isNumber(event)" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- data-item -->

                            <div class="data-item">
                                <div class="data-col">
                                    <span class="data-label form-label">{{trans('user::labels.dob')}}</span>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col">
                                                <input type="text" placeholder="Select Date Of Birth" class="form-control" name="dob" id="dob" readonly />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <!-- data-item -->

                            <div class="data-item inputs_user_details">
                                <div class="data-col">
                                    <span class="data-label form-label">Profile Image</span>

                                    @if(isset($user) && !is_null($user->profile_path))
                                    <span class="data-value data-value-text ml-2">
                                        <img src="{{ $profileDetails->profile_path }}" width="60" class="rounded-circle z-depth-2" />
                                    </span>
                                    @else
                                    <span class="data-value data-value-text ml-2">
                                        <img src="https://cdn4.iconfinder.com/data/icons/small-n-flat/24/user-alt-512.png" width="60" class="rounded-circle z-depth-2" />
                                    </span>
                                    @endif

                                    <span class="data-value data-input">
                                        <div class="form-control-wrap">
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="profile_path" name="profile_path" accept="image/*"><label class="custom-file-label" for="profile_path">Choose file</label>
                                            </div>
                                        </div>
                                    </span>
                                </div>
                            </div>
                            <div class="data-item">
                                <div class="data-col">
                                    <span class="data-label form-label">{{trans('user::labels.account_created_date')}}</span>
                                    <span class="data-value ml-2">{{ $user->created_at ? $user->created_at->format('d-m-Y') : 'N/A'  }}</span>
                                </div>
                                <div class="data-col data-col-end">
                                    <span class="data-more disable"><em class="icon ni ni-lock-alt"></em></span>
                                </div>
                            </div><!-- data-item -->

                            <div class="form-group text-right my-2" id="action_user_details">
                                <a class="btn btn- btn-outline-light" href="javascript:history.back()">Cancel</a>
                                <x-button buttonType="primary" type="submit" id="updateProfile">
                                    Update Profile
                                </x-button>
                                <!-- <input type="submit" buttonType="primary" value="Update Profile" id="updateProfile"> -->
                            </div>
                        </form>
                    </div><!-- data-list -->
                </div><!-- .nk-block -->
            </div>
            <div class="card-aside card-aside-left user-aside toggle-slide toggle-slide-left toggle-break-lg" data-content="userAside" data-toggle-screen="lg" data-toggle-overlay="true">
                <div class="card-inner-group" data-simplebar>
                    <div class="card-inner">
                        <div class="user-card">
                            <div class="user-avatar bg-primary">
                                <img src="{{ $profileDetails->profile_path }}" alt="Your Profile Image">
                            </div>
                            <div class="user-info">
                                <span class="lead-text">{{ ucfirst($user->first_name) }}</span>
                                <span class="sub-text">{{ $user->email }}</span>
                            </div>
                            {{-- <div class="user-action">
                                <div class="dropdown">
                                    <a class="btn btn-icon btn-trigger mr-n2" data-toggle="dropdown" href="#"><em class="icon ni ni-more-v"></em></a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <ul class="link-list-opt no-bdr">
                                            <li><a href="#"><em class="icon ni ni-camera-fill"></em><span>Change Photo</span></a></li>
                                            <li><a href="#"><em class="icon ni ni-edit-fill"></em><span>Update Profile</span></a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div> --}}
                        </div><!-- .user-card -->
                    </div><!-- .card-inner -->

                    <div class="card-inner p-0">
                        <ul class="link-list-menu">
                            <li><a class="active" href="{{url('/profile')}}"><em class="icon ni ni-user-fill-c"></em><span>Profile</span></a></li>
                            <li><a href="{{url('/profile/address')}}"><em class="icon ni ni-info-fill"></em><span>Address</span></a></li>
                            <!-- <li><a href="{{url('/user/profile/notification')}}"><em class="icon ni ni-bell-fill"></em><span>Notifications</span></a></li> -->
                            <li><a href="{{url('/profile/setting')}}"><em class="icon ni ni-lock-alt-fill"></em><span>Security Settings</span></a></li>
                        </ul>
                    </div><!-- .card-inner -->
                </div><!-- .card-inner-group -->
            </div><!-- card-aside -->
        </div><!-- .card-aside-wrap -->
    </div><!-- .card -->
</div><!-- .nk-block -->
@endsection
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- Include jQuery -->


<script>
    $(document).ready(function() {
        // Function to limit the character input
        function limitCharacters(input, maxLength) {
            if (input.value.length > maxLength) {
                input.value = input.value.slice(0, maxLength);
            }
        }

        // Add event listeners to the input fields
        $('#firstName, #middleName, #lastName').on('input', function() {
            limitCharacters(this, 25); // Limit input to 25 characters
        });

        // validate update profile form using ajax validatiton
        if ($("#updateProfileForm").length > 0) {
            $("#updateProfileForm").validate({
                rules: {
                    first_name: {
                        required: true,
                        maxlength: 50
                    },
                    last_name: {
                        required: true,
                        maxlength: 50,
                    },
                    phone_no: {
                        required: true,
                        maxlength: 10,
                        minlength: 10,
                        digits: true,
                    }
                },
                messages: {
                    first_name: {
                        required: "Please enter first name",
                        maxlength: "Your first name maxlength should be 50 characters long."
                    },
                    last_name: {
                        required: "Please enter last name",
                        maxlength: "Your last name maxlength should be 50 characters long."
                    },
                    phone_no: {
                        required: "Please enter mobile number",
                        minlength: "Minimum 10 digits required"
                    }
                },
                submitHandler: function(form) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    // disabled submit button during process running status
                    $("#updateProfile").attr("disabled", true);

                    $.ajax({
                        type: "POST",
                        //enctype: 'multipart/form-data',
                        data: new FormData($('#updateProfileForm')[0]),
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            if (response.status == 'success') {
                                $('#updateProfileForm').load(window.location.href + ' #updateProfileForm');
                                toastr.success(response.message);
                                //window.location.reload();
                            } else {
                                toastr.error(response.message);
                            }
                        }
                    });
                    $("#updateProfile").attr("disabled", false);
                }
            })
        }

        $("#dob").datepicker({
            todayBtn: true,
            orientation: "auto",
            todayHighlight: true,
            autoclose: true,
            format: "dd/mm/yyyy",
            startView: "days",
            endDate: "-18Y"
        });
        $('#dob').each(function() {
            $(this).on('changeDate', function(e) {
                dob = $(this).datepicker('getDate');
                $('#dob').datepicker('update', dob);
            });
        });
    });
</script>