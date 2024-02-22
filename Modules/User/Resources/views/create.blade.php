@extends('layouts.app')
@php
$userPermission = \Session::get('userPermission');
$organization_type = \Session::get('organization_type');
@endphp
@section('content')
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title"><a href="javascript:history.back()" class="pt-3"><em class="icon ni ni-chevron-left back-icon"></em> </a> @if (isset($user)) Edit @else Add @endif Buyer</h3>
            </div><!-- .nk-block-head-content -->
        </div><!-- .nk-block-between -->
    </div><!-- .nk-block-head -->
    <form role="form" method="post" enctype="multipart/form-data"  >
        @csrf
        {{-- <div class="nk-block">
            <div class="card card-bordered sp-plan">
                <div class="row no-gutters">
                    <div class="col-md-3">
                        <div class="sp-plan-action card-inner">
                            <div class="icon">
                                <em class="icon ni ni-box fs-36px o-5"></em>
                                <h5 class="o-5">Role Information</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="sp-plan-info card-inner">
                            <div class="row g-3 align-center">
                                <div class="col-lg-5">
                                    <x-inputs.verticalFormLabel label="Role" for="role" suggestion="Select the role of the user." />
                                </div>
                                <div class="col-lg-7">
                                    <x-inputs.select  size="sm" required="true" name="role" for="role" data-search="on" class="roles">
                                        <option value="">Select Role</option>
                                        @foreach ($roles as $role)

                                        <option
                                        @if(isset($user) && $user->role == $role->name)
                                        selected
                                        @elseif(old('role') == $role->name) 
                                        selected
                                        @endif
                                        value="{{ $role->name }}">{{ $role->label }}</option>
                                        @endforeach
                                    </x-inputs.select>
                                    @if ($errors->has('role'))
                                        <span class="text-danger">{{ $errors->first('role') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- .nk-block --> --}}
        <input type="hidden" name="role" value="{{ $roles->id }}">
        <div class="nk-block">
            <div class="card card-bordered sp-plan">
                <div class="row no-gutters">
                    <div class="col-md-3">
                        <div class="sp-plan-action card-inner">
                            <div class="icon">
                                <em class="icon ni ni-box fs-36px o-5"></em>
                                <h5 class="o-5">Personal <br> Information</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="sp-plan-info card-inner">
                            <div class="row g-3 align-center">
                                <div class="col-lg-5">
                                    <x-inputs.verticalFormLabel label="First Name" for="firstName" suggestion="Specify the first name of the user." required="true" />
                                </div>
                                <div class="col-lg-7">
                                    <x-inputs.text  value="{{ isset($user) ? $user->name : old('firstname') }}" for="firstName" icon="user" placeholder="First name" name="firstname" required="true" />
                                    @if ($errors->has('firstname'))
                                        <span class="text-danger">{{ $errors->first('firstname') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="row g-3 align-center">
                                <div class="col-lg-5">
                                    <x-inputs.verticalFormLabel label="Last Name" for="lastName" suggestion="Specify the last name of the user." required="true" />
                                </div>
                                <div class="col-lg-7">
                                    <x-inputs.text value="{{ isset($user) ? $user->last_name : old('lastname') }}" for="lastName" icon="user" required="true" placeholder="Last name" name="lastname"/>
                                    @if ($errors->has('lastname'))
                                        <span class="text-danger">{{ $errors->first('lastname') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="row g-3 align-center">
                                <div class="col-lg-5">
                                    <x-inputs.verticalFormLabel label="Email" for="email" suggestion="Specify the email of the user." required="true" />
                                </div>
                                <div class="col-lg-7">
                                    <x-inputs.email value="{{ isset($user) ? $user->email : old('email') }}" for="email" icon="mail" required="true" class="" placeholder="Email" name="email" />
                                    @if ($errors->has('email'))
                                        <span class="text-danger custom-error-text">{{ $errors->first('email') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="row g-3 align-center">
                                <div class="col-lg-5">
                                    <x-inputs.verticalFormLabel label="Mobile Number" for="mobileNumber" suggestion="Specify the mobile number of the user." required="true" />
                                </div>
                                <div class="col-lg-7">
                                    <x-inputs.number value="{{ isset($user) ? $user->phone_number : old('mobileNumber') }}" for="mobileNumber" class="" icon="call" required="true" placeholder="Mobile Number" name="mobileNumber" 
                                    data-parsley-pattern="{{ \Config::get('constants.REGEX.VALIDATE_MOBILE_NUMBER_LENGTH') }}"
                                    />
                                    @if ($errors->has('mobileNumber'))
                                        <span class="text-danger">{{ $errors->first('mobileNumber') }}</span>
                                    @endif
                                </div>
                            </div>
                            <!-- <div class="row g-3 align-center">
                                <div class="col-lg-5">
                                    <x-inputs.verticalFormLabel label="Approved" for="approved" suggestion="Specify the approval of the user." required="true" />
                                </div>
                                <div class="col-lg-7">
                                    <x-inputs.switch for="approved" size="md" name="approved"/>
                                </div>
                            </div> -->
                            
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- .nk-block -->
        @if(isset($organization_type) && $organization_type == 'MULTIPLE')
        <div class="nk-block">
            <div class="card card-bordered sp-plan">
                <div class="row no-gutters">
                    <div class="col-md-3">
                        <div class="sp-plan-action card-inner">
                            <div class="icon">
                                <em class="icon ni ni-user fs-36px o-5"></em>
                                <h5 class="o-5">Organization</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="sp-plan-info card-inner">
                            <div class="row g-3 align-center">
                                <div class="col-lg-5">
                                    <x-inputs.verticalFormLabel label="Organization" for="organization" suggestion="Assign the organizations to the buyer" />
                                </div>
                                <div class="col-lg-7">
                                    <select class="form-select" multiple="" data-placeholder="Select Organization" data-parsley-errors-container=".proCatParsley" name="organization[]" required="">
                                        <option value=""> Select Organization</option>
                                        @foreach ($organizations as $key => $organization)
                                        <option 
                                        @if(isset($user) && in_array($organization->id,$buyerOrgs)) selected @endif 
                                        value="{{ $organization->id }}">{{ $organization->name }}</option>
                                        @endforeach
                                    </select>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- .nk-block -->
        @endif
        <div class="nk-block buyer-section">
            <div class="card card-bordered sp-plan">
                <div class="row no-gutters">
                    <div class="col-md-3">
                        <div class="sp-plan-action card-inner">
                            <div class="icon">
                                <em class="icon ni ni-box fs-36px o-5"></em>
                                <h5 class="o-5">Buyer <br> Information</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="sp-plan-info card-inner">
                            <div class="row g-3 align-center">
                                <div class="col-lg-5">
                                    <x-inputs.verticalFormLabel label="Shop Name" for="shopName" suggestion="Specify the shop name of the user." required="true" />
                                </div>
                                <div class="col-lg-7">
                                    <x-inputs.text value="{{ isset($user) ? $user->shop_name : old('shopname') }}" for="shopName" icon="building" required="true" placeholder="Shop Name" name="shopname" class="buyerFileds" />
                                    @if ($errors->has('shopname'))
                                        <span class="text-danger">{{ $errors->first('shopname') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="row g-3 align-center">
                                <div class="col-lg-5">
                                    <x-inputs.verticalFormLabel label="GST" for="gst" suggestion="Specify the GST number of the user." />
                                </div>
                                <div class="col-lg-7">
                                    <x-inputs.text value="{{ isset($user) ? $user->gst : old('gst') }}" for="gst" icon="notes-alt" placeholder="GST Number" name="gst" class="buyerFileds " data-attr="Blalala" data-parsley-pattern="{{ \Config::get('constants.REGEX.VALIDATE_GSTIN') }}"/>
                                    @if ($errors->has('gst'))
                                        <span class="text-danger">{{ $errors->first('gst') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="row g-3 align-center">
                                <div class="col-lg-5">
                                    <x-inputs.verticalFormLabel label="Credit Limit" for="creditLimit" suggestion="Specify the credit limit of the user." required="false" />
                                </div>
                                <div class="col-lg-7">
                                    <x-inputs.number value="{{ isset($user) ? ($user->credit_limit > 0) ? $user->credit_limit : 0 : old('creditLimit') }}" for="creditLimit" icon="sign-inr" placeholder="Credit Limit" name="creditLimit" class="buyerFileds" />
                                    @if ($errors->has('creditLimit'))
                                        <span class="text-danger custom-error-text">{{ $errors->first('creditLimit') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="row g-3 align-center">
                                <div class="col-lg-5">
                                    <x-inputs.verticalFormLabel label="Category" for="category" suggestion="Select the category of the user." required="true" />
                                </div>
                                <div class="col-lg-7">
                                    <x-inputs.select  size="md" name="category" for="category"  class="buyerFileds" >
                                        @foreach ($retailerCategories as $key => $category)
                                        <option 
                                        
                                        @if(isset($user) && $user->buyer_category == $category->id)
                                        selected
                                        @elseif(old('category') == $category->id) 
                                        selected
                                        @endif

                                        value="{{ $category->id }}">{{ $category->retailer_catagory }}</option>
                                    @endforeach
                                    </x-inputs.select>
                                    @if ($errors->has('category'))
                                        <span class="text-danger">{{ $errors->first('category') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- .nk-block -->
        <div class="nk-block">
            <div class="card card-bordered sp-plan">
                <div class="row no-gutters">
                    <div class="col-md-3">
                        <div class="sp-plan-action card-inner">
                            <div class="icon">
                                <em class="icon ni ni-map-pin fs-36px o-5"></em>
                                <h5 class="o-5">Address <br> Information</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="sp-plan-info card-inner">
                            <div class="row g-3 align-center">
                                <div class="col-lg-5">
                                    <x-inputs.verticalFormLabel label="Address 1" for="address1" suggestion="Specify the address of the user." required="true" />
                                </div>
                                <div class="col-lg-7">
                                    <x-inputs.text value="{{ isset($user) ? $user->address1 : old('address1') }}" for="address1" icon="map-pin" required="true" placeholder="Address 1" name="address1"/>
                                    @if ($errors->has('address1'))
                                        <span class="text-danger">{{ $errors->first('address1') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="row g-3 align-center">
                                <div class="col-lg-5">
                                    <x-inputs.verticalFormLabel label="Address 2" for="address2" suggestion="Specify the address of the user." required="false" />
                                </div>
                                <div class="col-lg-7">
                                    <x-inputs.text value="{{ isset($user) ? $user->address2 : old('address2') }}" for="address2" icon="map-pin" required="false" placeholder="Address 2" name="address2"/>
                                    @if ($errors->has('address2'))
                                        <span class="text-danger">{{ $errors->first('address2') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="row g-3 align-center">
                                <div class="col-lg-5">
                                    <x-inputs.verticalFormLabel label="Country" for="country" suggestion="Select the country of the user." required="true" />
                                </div>
                                <div class="col-lg-7">
                                    <x-inputs.select  size="md" required="true" name="country" for="country" data-search="on">
                                        <option value="103">India</option>
                                    </x-inputs.select>
                                    @if ($errors->has('country'))
                                        <span class="text-danger">{{ $errors->first('country') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="row g-3 align-center">
                                <div class="col-lg-5">
                                    <x-inputs.verticalFormLabel label="State" for="state" suggestion="Select the state of the user." required="true" />
                                </div>
                                <div class="col-lg-7">
                                    <x-inputs.select  size="md" name="state" required="true" for="state" data-search="on">
                                        <option value=""> Select State</option>
                                        @foreach ($states as $key => $state)
                                            <option 
                                            @if(isset($user) && $user->state == $state->id)
                                            selected
                                            @elseif(old('state') == $state->id) 
                                            selected
                                            @endif
                                            value="{{ $state->id }}">{{ $state->name }}</option>
                                        @endforeach
                                    </x-inputs.select>
                                    @if ($errors->has('state'))
                                        <span class="text-danger">{{ $errors->first('state') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="row g-3 align-center">
                                <div class="col-lg-5">
                                    <x-inputs.verticalFormLabel label="District" for="district" suggestion="Select the district of the user." required="true" />
                                </div>
                                <div class="col-lg-7">
                                    <x-inputs.select  size="md" name="district" required="true" for="district" data-search="on">
                                    @if(isset($user) && $user->district)
                                        @foreach ($districts as $key => $district)
                                            <option 
                                            @if(isset($user) && $user->district == $district->id)
                                            selected
                                            @elseif(old('district') == $district->id) 
                                            selected
                                            @endif
                                            value="{{ $district->id }}">{{ $district->name }}</option>
                                        @endforeach
                                    @endif
                                    </x-inputs.select>
                                    @if ($errors->has('district'))
                                        <span class="text-danger">{{ $errors->first('district') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="row g-3 align-center">
                                <div class="col-lg-5">
                                    <x-inputs.verticalFormLabel label="City" for="city" suggestion="Select the city of the user." required="true" />
                                </div>
                                <div class="col-lg-7">
                                    <x-inputs.select  size="md" name="city" required="true" for="city" data-search="on">
                                    @if(isset($user) && $user->city)
                                        @foreach ($cities as $key => $city)
                                            <option 
                                            @if(isset($user) && $user->city == $city->id)
                                            selected
                                            @elseif(old('city') == $city->id) 
                                            selected
                                            @endif
                                            value="{{ $city->id }}">{{ $city->name }}</option>
                                        @endforeach
                                    @endif
                                    </x-inputs.select>
                                    @if ($errors->has('city'))
                                        <span class="text-danger">{{ $errors->first('city') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="row g-3 align-center">
                                <div class="col-lg-5">
                                    <x-inputs.verticalFormLabel label="Pincode" for="pincode" suggestion="Specify the pincode of the user." required="true" />
                                </div>
                                <div class="col-lg-7">
                                    <x-inputs.text value="{{ isset($user) ? $user->pincode : old('pincode') }}" for="pincode" icon="map-pin" required="true" placeholder="Pincode" name="pincode" data-parsley-pattern="{{ \Config::get('constants.REGEX.VALIDATE_ZIP_CODE') }}"/>
                                    @if ($errors->has('pincode'))
                                        <span class="text-danger">{{ $errors->first('pincode') }}</span>
                                    @endif
                                </div>
                            </div>

                            @if(!isset($user))
                            <div class="userFoundBox">
                                <div class="row g-3 align-center">
                                    <div class="col-lg-5">
                                        <x-inputs.verticalFormLabel label="Copy Address" for="copy_address" suggestion="Copy same address as Billing and Shipping address."/>
                                    </div>
                                    <div class="col-lg-7">
                                        <x-inputs.checkbox value="1" for="billing" icon="map-pin" label="Billing" name="billing"/>

                                        <x-inputs.checkbox value="1" for="shipping" icon="map-pin" label="Shipping" name="shipping"/>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- .nk-block -->
        <!-- <div class="nk-block">
            <div class="card card-bordered sp-plan">
                <div class="row no-gutters">
                    <div class="col-md-3">
                        <div class="sp-plan-action card-inner">
                            <div class="icon">
                                <em class="icon ni ni-lock fs-36px o-5"></em>
                                <h5 class="o-5">Security</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="sp-plan-info card-inner">
                            <div class="row g-3 align-center">
                                <div class="col-lg-5">
                                    <x-inputs.verticalFormLabel label="Password" for="password" suggestion="Specify the password of the user." required="true" />
                                </div>
                                <div class="col-lg-7">
                                    <x-inputs.password value="" for="password" required="true" icon="lock" placeholder="Password" name="password"/>
                                </div>
                            </div>
                            <div class="row g-3 align-center">
                                <div class="col-lg-5">
                                    <x-inputs.verticalFormLabel label="Confirm Password" for="confirmPassword" suggestion="Specify the confirm password of the user." required="true" />
                                </div>
                                <div class="col-lg-7">
                                    <x-inputs.password value="" for="confirmPassword" icon="lock" placeholder="Confirm password" name="confirmPassword"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> --><!-- .nk-block -->
        <div class="nk-block userFoundBox">
            <div class="card card-bordered sp-plan">
                <div class="row no-gutters">
                    <div class="col-md-3">
                        <div class="sp-plan-action card-inner">
                            <div class="icon">
                                <em class="icon ni ni-user fs-36px o-5"></em>
                                <h5 class="o-5">Image</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="sp-plan-info card-inner">
                            <div class="row g-3 align-center">
                                <div class="col-lg-5">
                                    <x-inputs.verticalFormLabel label="Profile Image" for="Picture" suggestion="Upload the picture of the user." />
                                </div>
                                <div class="col-lg-7">
                                    <div class="form-group">
                                        <div class="form-control-wrap">
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input userFoundInput" name="file"  id="customFile">
                                                <label class="custom-file-label" for="customFile">Choose file</label>
                                                @if ($errors->has('file'))
                                                    <span class="text-danger">{{ $errors->first('file') }}</span>
                                                @endif
                                            </div>
                                            @if(isset($user) && !is_null($user->file))
                                            <div class="media_box">
                                                <img height="100" width="100" src="{{url('uploads/users/'.$user->file)}}">
                                                <a href="javascript:void(0);" data-id="{{ $user->id }}" class="removeMedia">
                                                    <i class="fa fa-trash"></i> Remove
                                                </a>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- .nk-block -->
        @if(!isset($user))
        <div class="nk-block userFoundBox">
            <div class="card card-bordered sp-plan">
                <div class="row no-gutters">
                    <div class="col-md-3">
                        <div class="sp-plan-action card-inner">
                            <div class="icon">
                                <em class="icon ni ni-lock fs-36px o-5"></em>
                                <h5 class="o-5">Security</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="sp-plan-info card-inner">
                            
                            <div class="row g-3 align-center">
                                <div class="col-lg-5">
                                    <x-inputs.verticalFormLabel label="Password" for="password" suggestion="Specify the password of the user." required="true" />
                                </div>
                                <div class="col-lg-7">
                                    <x-inputs.password value="" for="password" required="true" icon="lock" placeholder="Password" name="password" class="userFoundInput"/>
                                    @if ($errors->has('password'))
                                        <span class="text-danger custom-error-text">{{ $errors->first('password') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="row g-3 align-center">
                                <div class="col-lg-5">
                                    <x-inputs.verticalFormLabel label="Confirm Password" for="password_confirmation" suggestion="Specify the confirm password of the user." required="true" />
                                </div>
                                <div class="col-lg-7">
                                    <x-inputs.password value="" for="password_confirmation" icon="lock" placeholder="Confirm password" name="password_confirmation" class="userFoundInput"/>
                                    @if ($errors->has('password_confirmation'))
                                        <span class="text-danger custom-error-text">{{ $errors->first('password_confirmation') }}</span>
                                    @endif
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- .nk-block -->
        @endif
        <div class="nk-block">
            <div class="card card-bordered sp-plan">
                <div class="row no-gutters">
                    <div class="col-md-3">
                        <div class="sp-plan-action card-inner">
                            <div class="icon">
                                <em class="icon ni ni-thumbs-up fs-36px o-5"></em>
                                <h5 class="o-5">Approval</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="sp-plan-info card-inner">
                            <div class="row g-3 align-center">
                                <div class="col-lg-5">
                                    <x-inputs.verticalFormLabel label="Status" for="approved" suggestion="Specify the status of the user." />
                                </div>
                                <div class="col-lg-7">

                                    @php
                                        if(isset($user) && $user->status){
                                            $checked = 'checked';
                                        }else{
                                            $checked = '';
                                        }
                                    @endphp
                                    <x-inputs.switch for="approved" size="md" name="approved" checked={{$checked}}/>
                                    @if ($errors->has('approved'))
                                        <span class="text-danger">{{ $errors->first('approved') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- .nk-block -->        
        <div class="nk-block">
            @isset($user)
                <input type="hidden" name="userId" id="userId" value="{{ $user->id }}">
            @endisset
            <div class="row">
                <div class="col-md-12">
                    <div class="sp-plan-info pt-0 pb-0 card-inner">  
                            <div class="row">
                                <div class="col-lg-7 text-right offset-lg-5">
                                    <div class="form-group">
                                        <a href="javascript:history.back()" class="btn btn-outline-light">Cancel</a>
                                        <x-button type="submit" class="btn btn-primary">Submit</x-button>
                                    </div>
                                </div>
                            </div>
                    </div><!-- .sp-plan-info -->
                </div><!-- .col -->
            </div><!-- .row -->
        </div>
        <input type="hidden" name="userFound" id="userFound" value="0">
    </form>

    <input type="hidden" name="role_type" id="role_type" value="{{\Config::get('constants.ROLES.BUYER')}}">
    <input type="hidden" name="old_district" id="old_district" value="{{old('district')}}">
    <input type="hidden" name="old_city" id="old_city" value="{{old('city')}}">

    <script type="text/javascript">

        $(document).ready(function(){

            $('.checkUser').blur(function(){

                var userFound = $('#userFound').val();

                if(userFound == 0){
                    var root_url = "<?php echo Request::root(); ?>";
                    $.ajax({
                        url: root_url + '/user/check-user',
                        data: {
                            "_token": "{{ csrf_token() }}",
                            "searchKey": $(this).val(),
                            "role": $('#role').val()
                        },
                        //dataType: "html",
                        method: "POST",
                        cache: false,
                        success: function (response) {
                            if(response.user){
                                var user = response.user;
                                if(confirm("A user already exists with the same information. do you want to populate the same data ?")){
                                    $('#firstName').val(user.name).prop('readonly',true);
                                    $('#lastName').val(user.last_name).prop('readonly',true);
                                    $('#email').val(user.email).prop('readonly',true);
                                    $('#mobileNumber').val(user.phone_number).prop('readonly',true);
                                    $('#shopName').val(user.shop_name).prop('readonly',true);
                                    $('#gst').val(user.gst).prop('readonly',true);
                                    $('#address1').val(user.address1).prop('readonly',true);
                                    $('#address2').val(user.address2).prop('readonly',true);
                                    $('#pincode').val(user.pincode).prop('readonly',true);
                                    $('#state').val(user.stateId).trigger('change');
                                    $('#userFound').val(user.id);
                                    
                                    changeDistrict(user.districtId);
                                    changeCity(user.districtId,user.cityId);

                                    $('#district').prop('readonly',true);
                                    $('#city').prop('readonly',true);
                                    $('#state').prop('readonly',true);
                                    $('#country').prop('readonly',true);

                                    $('.userFoundBox').hide();
                                    $('.userFoundInput').prop( "disabled", true );
                                    $('.userFoundInput').prop( "required", false );
                                }
                            }else{
                                $('#firstName').prop('readonly',false);
                                $('#lastName').prop('readonly',false);
                                $('#email').prop('readonly',false);
                                $('#mobileNumber').prop('readonly',false);
                                $('#shopName').prop('readonly',false);
                                $('#gst').prop('readonly',false);
                                $('#address1').prop('readonly',false);
                                $('#address2').prop('readonly',false);
                                $('#pincode').prop('readonly',false);
                                $('#userFound').val(0)

                                $('.userFoundBox').show();
                                $('.userFoundInput').prop( "disabled", false );
                                $('.userFoundInput').prop( "required", true );
                            }
                        }
                    });

                }

            });

            $('.removeMedia').click(function(){
                if(confirm("Are you sure you want to delete this?")){
                    var id = $(this).attr('data-id');
                    var root_url = "<?php echo Request::root(); ?>";
                    $.ajax({
                        url: root_url + '/user/remove-image/'+id,
                        data: {
                        },
                        //dataType: "html",
                        method: "GET",
                        cache: false,
                        success: function (response) {
                            if(response.success){
                                $('.media_box').remove();
                            }
                        }
                    });
                }
                else{
                    return false;
                }
            });

            var distributorRole = "{{\Config::get('constants.ROLES.SELLER')}}";
            var retailerRole = "{{\Config::get('constants.ROLES.BUYER')}}";
            var dspRole = "{{\Config::get('constants.ROLES.SP')}}";

            var role_type = $('#role_type').val();

            if(role_type == "" || role_type != retailerRole){
                // $('.buyer-section').hide();
                $( ".buyerFileds" ).prop( "disabled", true );
                $( ".buyerFileds" ).prop( "required", false );
            }


            $("#role").on("change", function () {
                var role = $( "#role option:selected" ).text().toLowerCase();
                var role = role.split(' ').join('_');
                if(role != retailerRole){
                    $('.buyer-section').hide();
                    $( ".buyerFileds" ).prop( "disabled", true );
                    $( ".buyerFileds" ).prop( "required", false );
                }else{
                    $('.buyer-section').show();
                    $( ".buyerFileds" ).prop( "disabled", false );
                    $( ".buyerFileds" ).prop( "required", false );
                }

            });

            var old_district = $('#old_district').val();
            if(old_district != ""){
                changeDistrict();
            }

            var old_city = $('#old_city').val();
            if(old_city != ""){
                var old_district = $('#old_district').val();
                changeCity(old_district);
            }

            $("#state").on("change", function () {
                changeDistrict();
            });

            function changeDistrict(userDistrict = 0){
                var state = $('#state').val();
                var root_url = "<?php echo Request::root(); ?>";
                $.ajax({
                    url: root_url + '/user/districts/'+state,
                    data: {
                    },
                    //dataType: "html",
                    method: "GET",
                    cache: false,
                    success: function (response) {
                        $("#district").html('');
                        $("#district").append($('<option></option>').val('').html('Select district'));
                        $.each(response.districts, function (key, value) {
                            if(value.id != 0) {
                                if(value.id == old_district || value.id == userDistrict) {
                                    $("#district").append($('<option></option>').val(value.id).html(value.name).prop('selected', 'selected'));    
                                } else {
                                    $("#district").append($('<option></option>').val(value.id).html(value.name));
                                }
                            }
                        });
                    }
                });
            }

            $("#district").on("change", function () {
                var district = $('#district').val();
                changeCity(district);
            });

            function changeCity(district,userCity = 0){
                var root_url = "<?php echo Request::root(); ?>";
                
                $.ajax({
                    url: root_url + '/user/cities/'+district,
                    data: {
                    },
                    //dataType: "html",
                    method: "GET",
                    cache: false,
                    success: function (response) {
                        $("#city").html('');
                        $("#city").append($('<option></option>').val('').html('Select city'));
                        $.each(response.cities, function (key, value) {
                            if(value.id != 0) {
                                if(value.id == old_city || value.id == userCity) {
                                    $("#city").append($('<option></option>').val(value.id).html(value.name).prop('selected', 'selected'));    
                                } else {
                                    $("#city").append($('<option></option>').val(value.id).html(value.name));
                                }
                            }
                        });
                    }
                });
            }

        });
    </script>
@endsection
