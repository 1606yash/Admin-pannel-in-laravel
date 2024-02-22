@extends('layouts.app')

@section('content')
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title"><a href="javascript:history.back()" class="pt-3"><em class="icon ni ni-chevron-left back-icon"></em> </a> @if (isset($organization)) Edit @else Create @endif Organization</h3>
            </div><!-- .nk-block-head-content -->
        </div><!-- .nk-block-between -->
    </div><!-- .nk-block-head -->
    <form role="form" method="post" enctype="multipart/form-data" >
        @csrf
        @isset($organization)
            <input type="hidden" name="organizationId" id="organizationId" value="{{ $organization->id }}">
        @endisset
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
                                    <x-inputs.verticalFormLabel label="Organization Name" for="organizationName" suggestion="Specify the organization name." required="true" />
                                </div>
                                <div class="col-lg-7">
                                    <x-inputs.text  value="{{ isset($organization) ? $organization->name : old('organizationName') }}" for="organizationName" icon="user" placeholder="Organization Name" name="organizationName" required="true" />
                                    @if ($errors->has('organizationName'))
                                        <span class="text-danger">{{ $errors->first('organizationName') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="row g-3 align-center">
                                <div class="col-lg-5">
                                    <x-inputs.verticalFormLabel label="Organization Tin" for="organizationTin" suggestion="Specify the organization tin." required="true" />
                                </div>
                                <div class="col-lg-7">
                                    <x-inputs.text value="{{ isset($organization) ? $organization->tin : old('organizationTin') }}" for="organizationTin" required="true" placeholder="Organization Tin" name="organizationTin"/>
                                    @if ($errors->has('organizationTin'))
                                        <span class="text-danger">{{ $errors->first('organizationTin') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="row g-3 align-center">
                                <div class="col-lg-5">
                                    <x-inputs.verticalFormLabel label="Owner Name" for="ownerName" suggestion="Specify the owner name." required="true" />
                                </div>
                                <div class="col-lg-7">
                                    <x-inputs.text value="{{ isset($organization) ? $organization->owner_name : old('ownerName') }}" for="ownerName" icon="user" required="true" placeholder="Owner Name" name="ownerName"/>
                                    @if ($errors->has('ownerName'))
                                        <span class="text-danger">{{ $errors->first('ownerName') }}</span>
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
                                    <x-inputs.text value="{{ isset($organization) ? $organization->street_1 : old('address1') }}" for="address1" icon="map-pin" required="true" placeholder="Address 1" name="address1"/>
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
                                    <x-inputs.text value="{{ isset($organization) ? $organization->street_2 : old('address2') }}" for="address2" icon="map-pin" required="false" placeholder="Address 2" name="address2"/>
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
                                            @if(isset($organization) && $organization->state == $state->id)
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
                                    @if(isset($organization) && $organization->district)
                                        @foreach ($districts as $key => $district)
                                            <option 
                                            @if(isset($organization) && $organization->district == $district->id)
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
                                    @if(isset($organization) && $organization->city)
                                        @foreach ($cities as $key => $city)
                                            <option 
                                            @if(isset($organization) && $organization->city == $city->id)
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
                                    <x-inputs.text value="{{ isset($organization) ? $organization->pincode : old('pincode') }}" for="pincode" icon="map-pin" required="true" placeholder="Pincode" name="pincode" data-parsley-pattern="{{ \Config::get('constants.REGEX.VALIDATE_ZIP_CODE') }}"/>
                                    @if ($errors->has('pincode'))
                                        <span class="text-danger">{{ $errors->first('pincode') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- .nk-block -->
  
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
    <input type="hidden" name="old_district" id="old_district" value="{{old('district')}}">
    <input type="hidden" name="old_city" id="old_city" value="{{old('city')}}">
@endsection
@push('footerScripts')
<script type="text/javascript">
    $(document).ready(function(){
        $("#state").on("change", function () {
            changeDistrict();
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
@endpush