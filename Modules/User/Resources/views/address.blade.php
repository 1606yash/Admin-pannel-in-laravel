@extends('layouts.app')
<style type="text/css">
	.profile-ud-list{max-width: 100% !important;}
</style>
@section('content')
<div class="nk-block-head nk-block-head-sm">
    <div class="nk-block-between g-3">
        <div class="nk-block-head-content">
            <h3 class="nk-block-title page-title"><a href="javascript:history.back()" class="pt-3"><em class="icon ni ni-chevron-left back-icon"></em> </a>  Users / <strong class="text-primary small">{{  $user->FullName }}</strong></h3>
            <div class="nk-block-des text-soft">
                {{-- <ul class="list-inline">
                    <li>User ID: <span class="text-base">UD003054</span></li>
                    <li>Last Login: <span class="text-base">15 Feb, 2019 01:02 PM</span></li>
                </ul> --}}
            </div>
        </div>
        <div class="nk-block-head-content">
            <a href="#" data-toggle="modal" data-target="#modalAddaddress" class="btn btn-primary d-none d-md-inline-flex"><em class="icon ni ni-plus"></em><span>Add Address</span></a>
        </div>
    </div>
</div><!-- .nk-block-head -->
<div class="nk-block">
    <div class="card">
        <div class="card-aside-wrap">
            <div class="card-content">
                <ul class="nav nav-tabs nav-tabs-mb-icon nav-tabs-card">
                    <li class="nav-item">
                        <a class="nav-link " href="{{ url('user/detail/'.$user_id) }}"><em class="icon ni ni-user-circle"></em><span>Personal</span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="#"><em class="icon ni ni-map-pin"></em><span>Address</span></a>
                    </li>
                </ul><!-- .nav-tabs -->
                <div class="card-inner">
                    <div class="nk-block">
                        <table class="table address-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Address</th>
                                    <th width="1%" nowrap="">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($addresses as $address)
                                <tr>
                                    <td>{{ $address->name }}</td>
                                    <td>{{ $address->address1.' '.$address->address2 }}, {{ $address->city }}, {{ $address->district }}, {{ $address->state }}, {{ $address->pincode }}</td>
                                    <td>
                                        <div class="dropdown">
                                            <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <ul class="link-list-opt no-bdr">
                                                    <li><a href="#" class="addressDetails" data-id={{ $address->id }}><em class="icon ni ni-edit"></em><span>Edit Address</span></a></li>
                                                    
                                                    <li><a href="{{ url('user/address/remove/'.$address->id) }}" onclick="return confirm('Are you sure, you want to delete it?')"><em class="icon ni ni-trash"></em><span>Delete Address</span></a></li>

                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" align="center">No data found</td>
                                    </tr>
                                @endforelse
                                
                            </tbody>
                        </table>
                    </div><!-- .nk-block -->                    
                </div><!-- .card-inner -->
            </div><!-- .card-content -->
            
        </div><!-- .card-aside-wrap -->
    </div><!-- .card -->
</div><!-- .nk-block -->
@php
$filterAction = array(
array(
'label' => 'Submit',
'type' => 'primary',
'click' => "$('#addressForm').submit()",
)
);
@endphp
<x-ui.modal modalId="modalAddaddress" title="Add Address" :footerActions="$filterAction">
    <form role="form" class="mb-0" method="post" action="#" id="addressForm">
        @csrf
        <div class="modal-body modal-body-lg">
            <div class="gy-3">

                <div class="row g-3 align-center">
                    <div class="col-lg-5">
                        <x-inputs.verticalFormLabel label="Type" for="type" suggestion="Select the type of address." required="true" />
                    </div>
                    <div class="col-lg-7">
                        <x-inputs.select  size="md" name="type" required="true" for="type" data-search="on">
                            <option value="1">Billing</option>
                            <option value="2">Shipping</option>
                        </x-inputs.select>
                    </div>
                </div>

                <div class="row g-3 align-center">
                    <div class="col-lg-5">
                        <x-inputs.verticalFormLabel label="Address Name" for="addressName" suggestion="Specify the address name.." required="true" />
                    </div>
                    <div class="col-lg-7">
                        <x-inputs.text value="" for="addressName" icon="user" required="true" placeholder="Address Name" name="addressName"/>
                    </div>
                </div>
                <div class="row g-3 align-center">
                    <div class="col-lg-5">
                        <x-inputs.verticalFormLabel label="Address 1" for="address1" suggestion="Specify the address." required="true" />
                    </div>
                    <div class="col-lg-7">
                        <x-inputs.text value="" for="address1" icon="map-pin" required="true" placeholder="Address 1" name="address1"/>
                    </div>
                </div>
                <div class="row g-3 align-center">
                    <div class="col-lg-5">
                        <x-inputs.verticalFormLabel label="Address 2" for="address2" suggestion="Specify the address." required="false" />
                    </div>
                    <div class="col-lg-7">
                        <x-inputs.text value="" for="address2" icon="map-pin" required="false" placeholder="Address 2" name="address2"/>
                    </div>
                </div>
                <div class="row g-3 align-center">
                    <div class="col-lg-5">
                        <x-inputs.verticalFormLabel label="Country" for="country" suggestion="Select the country for the address." required="true" />
                    </div>
                    <div class="col-lg-7">
                        <x-inputs.select  size="md" required="true" name="country" for="country" data-search="on">
                            <option selected value="103">India</option>
                        </x-inputs.select>
                    </div>
                </div>
                <div class="row g-3 align-center">
                    <div class="col-lg-5">
                        <x-inputs.verticalFormLabel label="State" for="state" suggestion="Select the state for the address." required="true" />
                    </div>
                    <div class="col-lg-7">
                        <x-inputs.select  size="md" name="state" required="true" for="state" data-search="on">
                            @foreach ($states as $key => $state)
                                <option value="{{ $state->id }}">{{ $state->name }}</option>
                            @endforeach
                        </x-inputs.select>
                    </div>
                </div>
                <div class="row g-3 align-center">
                    <div class="col-lg-5">
                        <x-inputs.verticalFormLabel label="District" for="district" suggestion="Select the district for the address." required="true" />
                    </div>
                    <div class="col-lg-7">
                        <x-inputs.select  size="md" name="district" required="true" for="district" data-search="on">
                        </x-inputs.select>
                    </div>
                </div>
                <div class="row g-3 align-center">
                    <div class="col-lg-5">
                        <x-inputs.verticalFormLabel label="City" for="city" suggestion="Select the city  for the address." required="true" />
                    </div>
                    <div class="col-lg-7">
                        <x-inputs.select  size="md" name="city" required="true" for="city" data-search="on">
                        </x-inputs.select>
                    </div>
                </div>
                <div class="row g-3 align-center">
                    <div class="col-lg-5">
                        <x-inputs.verticalFormLabel label="Pincode" for="pincode" suggestion="Specify the pincode for the address." required="true" />
                    </div>
                    <div class="col-lg-7">
                        <x-inputs.text value="" for="pincode" icon="map-pin" required="true" placeholder="Pincode" name="pincode" data-parsley-pattern="{{ \Config::get('constants.REGEX.VALIDATE_ZIP_CODE') }}"/>
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" id="address_id" name="address_id" value="0">
    </form>
</x-ui.modal>

<script type="text/javascript">
    $(document).ready(function(){
        $('.addressDetails').click(function(){

            var address_id = $(this).attr('data-id');

            var root_url = "<?php echo Request::root(); ?>";    
            $.ajax({
                url: root_url + '/user/address-details/'+address_id,
                data: {
                },
                //dataType: "html",
                method: "GET",
                cache: false,
                success: function (response) {
                    var address = response.address;
                    if(response.address){
                        $('#addressName').val(address.name);
                        $('#address_id').val(address.id);
                        $('#address1').val(address.address1);
                        $('#address2').val(address.address2);
                        $('#pincode').val(address.pincode);
                        $('#country').val(103).trigger('change');
                        $('#state').val(address.stateId).trigger('change');
                        $('#type').val(address.address_type).trigger('change');
                        changeDistrict(address.stateId,address.districtId);
                        changeCity(address.districtId,address.cityId);

                        $('.modal-title').text('Edit Address');
                        $('#modalAddaddress').modal('show');
                    }else{
                        Swal.fire('Address not found');
                    }
                }
            });    
        });

        $('#state').change(function(){
            changeDistrict($(this).val());
        });

        $('#district').change(function(){
            changeCity($(this).val());
        });

        function changeCity(district = 0,city = 0){
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
                            if(value.id == city) {
                                $("#city").append($('<option></option>').val(value.id).html(value.name).prop('selected', 'selected'));    
                            } else {
                                $("#city").append($('<option></option>').val(value.id).html(value.name));
                            }
                        }
                    });
                }
            });
        }

        function changeDistrict(stateId = 0,district = 0){
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
                            if(value.id == district) {
                                $("#district").append($('<option></option>').val(value.id).html(value.name).prop('selected', 'selected'));    
                            } else {
                                $("#district").append($('<option></option>').val(value.id).html(value.name));
                            }
                        }
                    });
                }
            });
        }

    });
</script>

@endsection