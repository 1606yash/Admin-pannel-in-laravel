@extends('layouts.app')
@section('content')
<div class="nk-block-head nk-block-head-sm">
    <div class="nk-block-between">
        <div class="nk-block-head-content">
            <h3 class="nk-block-title page-title"><a href="javascript:history.back()" class="pt-3"><em class="icon ni ni-chevron-left back-icon"></em> </a> Add Organization</h3>
        </div><!-- .nk-block-head-content -->
    </div><!-- .nk-block-between -->
</div><!-- .nk-block-head -->

<form role="form" class="mb-0" method="post" action="{{ url('saas/organization/store') }}" enctype="multipart/form-data" oninput='cn_password.setCustomValidity(cn_password.value != password.value ? "Passwords does not match." : "")'>

    @csrf
    <div class="nk-block">
    	<div class="card card-bordered sp-plan">
            <div class="row no-gutters">
                <div class="col-md-3">
                    <div class="sp-plan-action card-inner">
                        <div class="icon">
                            <em class="icon ni ni-box fs-36px o-5"></em>
                            <h5 class="o-5">Type</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="sp-plan-info card-inner">
                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <x-inputs.verticalFormLabel label="Industry Type" for="industryType" suggestion="Select the industry type of the organization." required="true" />
                            </div>
                            <div class="col-lg-7">
                                <x-inputs.select   size="sm" required="true" name="industry" for="industryType" >
                                    <option></option>
                                    <option value="fmcg">FMCG</option>
                                    <option value="retailer">Retailer</option>
                                    <option value="automobile">Automobile</option>
                                    <option value="automotive">Automotive</option>
                                </x-inputs.select>
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
                            <em class="icon ni ni-building fs-36px o-5"></em>
                            <h5 class="o-5">Detail</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="sp-plan-info card-inner">
                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <x-inputs.verticalFormLabel label="Organization Name" for="organizationName" suggestion="Specify the organization name of the organization." required="true" minlength="3" maxlength="200"/>
                            </div>
                            <div class="col-lg-7">
                                <x-inputs.text value="" for="organizationName" icon="building" placeholder="Organization Name"  name="organizationName" required="true" minlength="3" maxlength="200"/>
                            </div>
                        </div>
                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <x-inputs.verticalFormLabel label="Contact Person" for="contactPerson" suggestion="Specify the contact person name of the organization." required="true" minlength="3" maxlength="200"/>
                            </div>
                            <div class="col-lg-7">
                                <x-inputs.text value="" for="contactPerson" icon="user" label="" required="true" placeholder="Contact Person" name="contactPerson" minlength="3" maxlength="200"/>
                            </div>
                        </div>
                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <x-inputs.verticalFormLabel label="Contact Number" for="contactNumber" suggestion="Specify the contact number of the organization." required="true" minlength="3" maxlength="200"/>
                            </div>
                            <div class="col-lg-7">
                                <input type="number" class="form-control" minlength="10" maxlength="10" name="mobile" data-parsley-errors-container=".mobileParsley" data-parsley-error-message="This value should be 10 digit long." required value="">
                                <div class="mobileParsley"></div>
                                @if ($errors->has('mobile'))
                                    <span class="text-danger">{{ $errors->first('mobile') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <x-inputs.verticalFormLabel label="Email" for="email" suggestion="Specify the email address of the organization." required="true" minlength="3" maxlength="200"/>
                            </div>
                            <div class="col-lg-7">
                                <x-inputs.email value="" for="email" icon="mail" label="" required="true" placeholder="Email Address" name="email" />
                            </div>
                        </div>
                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <x-inputs.verticalFormLabel label="GST Number" for="gstNumber" suggestion="Specify the gst number of the organization." required="true" minlength="3" maxlength="200"/>
                            </div>
                            <div class="col-lg-7">
                                <x-inputs.text value="" for="gstNumber" icon="file-text" label="" required="true" placeholder="GST Number" name="gstNumber"  minlength="3" maxlength="200"/>
                                @if ($errors->has('gstNumber'))
                                    <span class="text-danger">{{ $errors->first('gstNumber') }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <x-inputs.verticalFormLabel label="Currency" for="currency" suggestion="Select the currency of the organization." required="true" />
                            </div>
                            <div class="col-lg-7">
                                <div class="form-control-wrap select-error-order">
                                    <select class="form-select form-control form-control-lg " data-search="on" name="currency"  id="currency" required="true">
                                        <option value="">Select Currency</option>
                                        @foreach ($currencies as $key => $currency)
                                            <option 
                                            @if(old('currency') == $currency->id) 
                                            selected
                                            @endif
                                            value={{ $currency->id }}>{{ $currency->code }} ({{$currency->symbol }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="parsley-container-currency"></div>
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
                            <h5 class="o-5">Address</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="sp-plan-info card-inner">
                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <x-inputs.verticalFormLabel label="Street 1" for="Street1" suggestion="Specify the address of your organization." required="true" />
                            </div>
                            <div class="col-lg-7">
                                <x-inputs.text value="" for="Street1" icon="map-pin" label="" placeholder="Street 1" name="Street1" required="true"/>
                            </div>
                        </div>
                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <x-inputs.verticalFormLabel label="Street 2" for="Street2" suggestion="Specify the address of your organization." />
                            </div>
                            <div class="col-lg-7">
                                <x-inputs.text value="" for="Street2" icon="map-pin" label="" placeholder="Street 1" name="Street2"/>
                            </div>
                        </div>
                        <!-- <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <x-inputs.verticalFormLabel label="Country" for="country" suggestion="Select the country of the organization." required="true" />
                            </div>
                            <div class="col-lg-7">
                                <div class="form-control-wrap">
                                    <select class="form-select form-control form-control-lg" data-placeholder="Select Country" data-search="on" required for="country"  data-parsley-errors-container=".parsley-container-country">
                                        <option></option>
                                        <option value="option_select_name">India</option>
                                        <option value="option_select_name">USA</option>
                                    </select>
                                </div>
                                <div class="parsley-container-country"></div>
                            </div>
                        </div> -->
                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <x-inputs.verticalFormLabel label="State" for="state" suggestion="Select the state of the organization." required="true" />
                            </div>
                            <div class="col-lg-7">
                                <div class="form-control-wrap">
                                    <select class="form-select form-control form-control-lg " data-search="on" name="state"  id="state" required>
                                        <option value="">Select State</option>
                                        @foreach ($states as $key => $state)
                                            <option 
                                            @if(!empty($users) && $users[0]['state'] == $state->id)
                                            selected
                                            @elseif(old('state') == $state->id) 
                                            selected
                                            @endif
                                            value={{ $state->id }}>{{ ucfirst($state->name) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="parsley-container-state"></div>
                            </div>
                        </div>

                            <div class="row g-3 align-center">
                                <div class="col-lg-5">
                                    <x-inputs.verticalFormLabel label="District" for="district" suggestion="Select the district of the user." required="true" />
                                </div>
                                <div class="col-lg-7">
                                    <select class="form-select form-control form-control-lg " data-search="on" name="district"  id="district" required>
                                    <option></option>
                                    </select>
                                    @if ($errors->has('district'))
                                        <span class="text-danger">{{ $errors->first('district') }}</span>
                                    @endif
                                </div>
                            </div>

                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <x-inputs.verticalFormLabel label="City" for="city" suggestion="Select the city of the organization." required="true" />
                            </div>
                            <div class="col-lg-7">
                                <div class="form-control-wrap">
                                    <select class="form-select form-control form-control-lg" data-search="on" name="city" id="city" required>
                                        <option value="">Select City</option>
                                    </select>
                                </div>
                                <div class="parsley-container-city"></div>
                            </div>
                        </div>
                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <x-inputs.verticalFormLabel label="pincode" for="pincode" suggestion="Specify the pincode of your organization." required="true" />
                            </div>
                            <div class="col-lg-7">
                                <x-inputs.text value="" for="pincode" icon="map-pin" label="" placeholder="Pincode" name="pincode" required="true" autocomplete="off" data-parsley-pattern="{{ \Config::get('constants.REGEX.VALIDATE_ZIP_CODE') }}"/>
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
                            <em class="icon ni ni-account-setting-alt fs-36px o-5"></em>                                        
                            <h5 class="o-5">Security</h5>
                        </div>
                    </div>
                </div><!-- .col -->
                <div class="col-md-9">
                    <div class="sp-plan-info card-inner">
                        
                            <div class="row g-3 align-center">
                                <div class="col-lg-5">
                                    <div class="form-group">
                                        <label class="form-label">Password<span class="text-danger">*</span></label>
                                        <span class="form-note">Specify the password of organization.</span>
                                    </div>
                                </div>
                                <div class="col-lg-7">
                                    <div class="form-control-wrap">
                                        <x-inputs.password value="" for="password" required="true" icon="lock" placeholder="Password" name="password"/>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-3 align-center">
                                <div class="col-lg-5">
                                    <div class="form-group">
                                        <label class="form-label">Confirm Password<span class="text-danger">*</span></label>
                                        <span class="form-note">Specify the confirm password of organization.</span>
                                    </div>
                                </div>
                                <div class="col-lg-7">
                                    <div class="form-control-wrap">
                                            <div class="form-icon form-icon-left">
                                                <em class="icon ni ni-lock"></em>
                                            </div>
                                            <x-inputs.password value="" for="cn_password" icon="lock" placeholder="Confirm password" name="cn_password" required="true" />
                                        </div>
                                </div>
                            </div>
                    </div><!-- .sp-plan-info -->
                    
                </div><!-- .col -->
                
            </div><!-- .row -->
        </div>      
    </div><!-- .nk-block -->
    <div class="nk-block">
        <div class="card card-bordered sp-plan">
            <div class="row no-gutters">
                <div class="col-md-3">
                    <div class="sp-plan-action card-inner">
                        <div class="icon">
                            <em class="icon ni ni-box fs-36px o-5"></em>
                            <h5 class="o-5">Settings</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="sp-plan-info card-inner">

                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <x-inputs.verticalFormLabel label="Is Active" for="isActive" suggestion="Select the status of the product." />
                            </div>
                            <div class="col-lg-7">
                                <x-inputs.switch for="isActive" size="md" label="" value="active" name="status" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- .nk-block -->
    <div class="nk-block">
        <div class="row">
            <div class="col-md-12">
                <div class="sp-plan-info pt-0 pb-0 card-inner">  
                        <div class="row">
                            <div class="col-lg-7 text-right offset-lg-5">
                                <div class="form-group">
                                    <a href="javascript:history.back()" class="btn btn-outline-light">Cancel</a>
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </div>
                        </div>
                </div><!-- .sp-plan-info -->
            </div><!-- .col -->
        </div><!-- .row -->
    </div>
</form>
@endsection
@push('footerScripts')
<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script> -->
<script type="text/javascript">      
$(document).ready(function () {   

$("#gstNumber").keyup(function () {    
    console.log($(this).val());
        var inputvalues = $(this).val();    
        var gstinformat = new RegExp('^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$');    
        if (gstinformat.test(inputvalues)) {    
            $('.parsley-container-gstNumber').html('');
            return true;    
        } else {    
            $("#gstNumber").focus();
            $('.parsley-container-gstNumber').html('Please Enter Valid GST Number');
             
        }    
    });   

$("#pincode").keyup(function () {    
    console.log($(this).val());
        var inputvalues = $(this).val();    
        var gstinformat = new RegExp('^[1-9][0-9]{5}$'); 

        if (gstinformat.test(inputvalues)) {    
            $('.parsley-container-pincode').html('');
            console.log('p-true');
            return true;    
        } else {    
            console.log('p-false');
            $("#pincode").focus();
            $('.parsley-container-pincode').css('color','red');
            $('.parsley-container-pincode').html('Please Enter Valid Pincode');
             
        }    
    });

 });          


    $(document).ready(function(){

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
                    $("#city").html('');
                    $("#district").append($('<option></option>').val('').html('Select district'));
                    $.each(response.districts, function (key, value) {
                        if(value.id != 0) {
                            $("#district").append($('<option></option>').val(value.id).html(value.name));
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
                            $("#city").append($('<option></option>').val(value.id).html(value.name));
                        }
                    });
                }
            });
        }
    });
    
</script>
@endpush