@extends('layouts.app')

@section('content')
<div class="nk-block-head nk-block-head-sm">
    <div class="nk-block-between">
        <div class="nk-block-head-content">
            <h3 class="nk-block-title page-title"><a href="javascript:history.back()" class="pt-3"><em class="icon ni ni-chevron-left back-icon"></em> </a> Buyer Mapping with {{ ucfirst($dspDetails->FullName) }}</h3>
            <p>You have total {{ count($dspRetailers) }} mapped buyers & {{ $unmappedRetailersCount }} unmapped buyers.</p>
        </div><!-- .nk-block-head-content -->
        <div class="nk-block-head-content">
            <div class="toggle-wrap nk-block-tools-toggle">
                <a href="#" class="btn btn-icon btn-trigger toggle-expand mr-n1" data-target="more-options"><em class="icon ni ni-more-v"></em></a>
                <div class="toggle-expand-content" data-content="more-options">
                    <ul class="nk-block-tools g-3">
                        <li class="nk-block-tools-opt">
                            <a href="#" class="btn btn-trigger btn-icon dropdown-toggle" title="filter" id="showall" data-toggle="modal" data-target="#modalFilterMapping">
                                <div class="dot dot-primary"></div>
                                <em class="icon ni ni-filter-alt"></em>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div><!-- .nk-block-head-content -->
    </div><!-- .nk-block-between -->
</div><!-- .nk-block-head -->
<div class="card card-preview">
    <div class="card-inner pt-2 pl-2 pb-0">
        <ul class="nav nav-tabs mt-n3 bdr-btm-none">
            <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#tabItem2">Unmapped <span class="badge badge-info badge-pill">{{ $unmappedRetailersCount }}</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link " data-toggle="tab" href="#tabItem1">Mapped <span class="badge badge-info badge-pill">{{ count($dspRetailers) }}</span></a>
            </li>

        </ul>
    </div>
</div><!-- .card-preview -->
<div class="tab-content pt-28">
    <div class="tab-pane active" id="tabItem2">
        <form method="post">
            @csrf
            <!--  Filter Tag List -->
            <div id="filter_tag_list" class="filter-tag-list"></div>

            <div class="nk-block table-compact">
                <div class="nk-tb-list is-separate mb-3">
                    <table class="broadcast-init nowrap nk-tb-list is-separate" data-auto-responsive="false">
                        <thead>
                            <tr class="nk-tb-item nk-tb-head">
                                <th class="nk-tb-col nk-tb-col-check">
                                    <div class="custom-control custom-control-sm custom-checkbox notext">
                                        <input type="checkbox" class="custom-control-input" id="check-all" name="check_all"><label class="custom-control-label" for="check-all"></label>
                                    </div>
                                </th>
                                <th class="nk-tb-col tb-col-mb"><span class="sub-text">Buyer</span></th>
                                <th class="nk-tb-col tb-col-mb"><span class="sub-text">Mobile</span></th>
                                <th class="nk-tb-col tb-col-md"><span class="sub-text">District</span></th>
                                <th class="nk-tb-col tb-col-md"><span class="sub-text">City</span></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div><!-- .nk-tb-list -->
            </div><!-- .nk-block -->
            <div class="nk-block">
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-lg-7 text-right offset-lg-5">
                                <div class="form-group">
                                    <a href="javascript:history.back()" class="btn btn-outline-light">Cancel</a>
                                    <button type="submit" class="btn btn-primary">Map</button>
                                </div>
                            </div>
                        </div>
                    </div><!-- .col -->
                </div><!-- .row -->
            </div>
        </form>
    </div>
    <div class="tab-pane " id="tabItem1">
        <form method="post" action="{{ url('administration/mapping/unmap-buyers/'.$dspDetails->id) }}">
            @csrf

            <div class="nk-block table-compact">

                <div class="nk-tb-list is-separate mb-3">
                    <div class="nk-tb-item nk-tb-head">
                        <div class="nk-tb-col nk-tb-col-check">
                            <div class="custom-control custom-control-sm custom-checkbox notext">
                                <input type="checkbox" class="custom-control-input" id="check-all-mapped">
                                <label class="custom-control-label" for="check-all-mapped"></label>
                            </div>
                        </div>
                        <div class="nk-tb-col fw-bold"><span class="sub-text">Buyer</span></div>
                        <div class="nk-tb-col fw-bold tb-col-md"><span class="sub-text">Mobile</span></div>
                        <div class="nk-tb-col fw-bold tb-col-md"><span class="sub-text">District</span></div>
                        <div class="nk-tb-col fw-bold tb-col-md"><span class="sub-text">City</span></div>

                    </div><!-- .nk-tb-item -->
                    @forelse ($mappedRetailers as $mappedRetailer)
                    <div class="nk-tb-item">
                        <div class="nk-tb-col nk-tb-col-check">
                            <div class="custom-control custom-control-sm custom-checkbox notext">
                                <input type="checkbox" value="{{ $mappedRetailer->id }}"  class="custom-control-input mappedRetailer cb-check-mapped" id="uid{{ $mappedRetailer->id }}">
                                <label class="custom-control-label" for="uid{{ $mappedRetailer->id }}"></label>
                            </div>
                        </div>
                        <div class="nk-tb-col tb-col-mb">
                            @php
                            $username = $mappedRetailer->name . ' ' . $mappedRetailer->last_name;
                            if(!is_null($mappedRetailer->file)){
                                $file = public_path('uploads/users/') . $mappedRetailer->file;
                            }
                            @endphp
                            <a href="{{ url('user/detail/').'/'.$mappedRetailer->id }}">
                                <div class="user-card">
                                    <div class="user-avatar bg-primary">
                                        @if (!is_null($mappedRetailer->file) && file_exists($file))
                                            <img src="{{url('uploads/users/' . $mappedRetailer->file)}}">
                                        @else
                                            <span>{{\Helpers::getAcronym($username)}}</span>
                                        @endif
                                    </div>
                                    <div class="user-info">
                                        <span class="tb-lead">{{ $mappedRetailer->shop_name }} <span class="dot dot-success d-md-none ml-1"></span></span>
                                        <span>{{ $username }} </span>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="nk-tb-col tb-col-lg">
                            <span>{{ $mappedRetailer->phone_number }}</span>
                        </div>
                        <div class="nk-tb-col tb-col-lg">
                            <span>{{ $mappedRetailer->district }}</span>
                        </div>
                        <div class="nk-tb-col tb-col-lg">
                            <span>{{ $mappedRetailer->city }}</span>
                        </div>
                    </div><!-- .nk-tb-item -->
                    @empty
                    {{-- empty expr --}}
                    @endforelse
                </div><!-- .nk-tb-list -->
            </div><!-- .nk-block -->
            <div class="nk-block pt-0">
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-lg-7 text-right offset-lg-5">
                                <div class="form-group">
                                    <input type="hidden" id="unmapped" name="unmapped" value="">
                                    <a href="javascript:history.back()" class="btn btn-outline-light">Cancel</a>
                                    <button type="submit" class="btn btn-primary">Unmap</button>
                                </div>
                            </div>
                        </div>
                    </div><!-- .col -->
                </div><!-- .row -->
            </div>
        </form>
    </div>

</div>
<div class="modal fade zoom" tabindex="-1" id="modalFilterMapping">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Filter</h5>
                <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
            </div>
            <form role="form" class="mb-0" method="get" action="#">
                @csrf
                <div class="modal-body modal-body-lg">
                    <div class="gy-3">
                        
                        <div class="row g-3 align-center">
                                <div class="col-lg-5">
                                    <x-inputs.verticalFormLabel label="State" for="state" suggestion="Select the state of the user." />
                                </div>
                                <div class="col-lg-7">
                                    <x-inputs.select  size="md" name="state" for="state" data-search="on">
                                        <option value="">select</option>
                                        @foreach ($states as $key => $state)
                                            <option
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
                                <x-inputs.verticalFormLabel label="District" for="District" suggestion="Select the district of the buyer." />
                            </div>
                            <div class="col-lg-7">
                                <x-inputs.select size="sm" name="district" for="district">
                                    <option value="">select</option>
                                    {{-- @foreach ($districts as $key => $district)
                                    <option value="{{ $district->id }}">{{ $district->name }}</option>
                                    @endforeach --}}
                                </x-inputs.select>
                            </div>
                        </div>

                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <x-inputs.verticalFormLabel label="City" for="City" suggestion="Select the city of the buyer." />
                            </div>
                            <div class="col-lg-7">
                                <x-inputs.select size="sm" name="city" for="city">
                                    <option value="">select</option>
                                    {{-- @foreach ($cities as $key => $city)
                                    <option value="{{ $city->id }}">{{ $city->name }}</option>
                                    @endforeach --}}
                                </x-inputs.select>
                            </div>
                        </div>

                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <x-inputs.verticalFormLabel label="Buyer Category" for="category" suggestion="Select the buyer category of the buyer." />
                            </div>
                            <div class="col-lg-7">
                                <x-inputs.select size="sm" name="category" for="category">
                                    <option value="">select</option>
                                    @foreach ($retailerCategories as $key => $category)
                                    <option value="{{ $category->id }}">{{ $category->retailer_catagory }}</option>
                                    @endforeach
                                </x-inputs.select>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" id="userId" name="user_id" value="0">
                <div class="modal-footer bg-light">
                    <div class="row">
                        <div class="col-lg-12 p-0 text-right">
                            <button class="btn btn-outline-light" data-dismiss="modal" aria-label="Close">Cancel</button>
                            <button class="btn btn-danger resetFilter" data-dismiss="modal" aria-label="Close">Clear Filter</button>
                            <button class="btn btn-primary submitBtn" type="button">Submit</button>
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
    $(function() {

        $('.mappedRetailer').click(function() {
            var unmapped = [];
            $('.mappedRetailer:checked').each(function() {
                var value = (this.checked ? $(this).val() : "");
                alert(value);
                unmapped.push(value);
            });
            $('#unmapped').val(unmapped);
        });



        $('.broadcast-init').on("change", "#check-all", function() {
            if (this.checked)
                $('.cb-check').prop('checked', true);
            else
                $('.cb-check').prop('checked', false);
        })

        $('#check-all-mapped').on("change", function() {

            if (this.checked)
                $('.cb-check-mapped').prop('checked', true);
            else
                $('.cb-check-mapped').prop('checked', false);
        })

        var user_table = "";
        user_table = NioApp.DataTable('.broadcast-init', {
            processing: true,
            serverSide: true,
            // ajax: "{{ url('administration/mapping/map-buyers/'.$dspDetails->id) }}",
            ajax: {
                type: "GET",
                url: "{{ url('administration/mapping/map-buyers/'.$dspDetails->id) }}",
                data: function(d) {
                    
                    d.district = $('#district').val()
                    d.city = $('#city').val()
                    d.category = $('#category').val()
                    
                    var items = [
                        '#district',
                        '#city',
                        '#category'
                    ];
                    NioApp.filterTag(items, user_table['.broadcast-init'], '#filter_tag_list');
                }
            },
            columns: [{
                    "class": "nk-tb-col tb-col-lg nk-tb-col-check",
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row, meta) {
                        return '<td class="nk-tb-col nk-tb-col-check"><div class="custom-control custom-control-sm custom-checkbox notext"><input type="checkbox" class="custom-control-input cb-check" id="cb-' + row.id + '" value="' + row.id + '" name="buyers[]"><label class="custom-control-label" for="cb-' + row.id + '"></label></div></td>'
                    }
                },
                {
                    "class": "nk-tb-col tb-col-lg",
                    data: 'name',
                    name: 'name'
                },
                {
                    "class": "nk-tb-col tb-col-lg",
                    data: 'phone_number',
                    name: 'phone_number'
                },
                {
                    "class": "nk-tb-col tb-col-lg",
                    data: 'district',
                    name: 'district'
                },
                {
                    "class": "nk-tb-col tb-col-lg",
                    data: 'city',
                    name: 'city'
                },
            ]
        });

        $('.submitBtn').click(function() {
            user_table['.broadcast-init'].draw();
            $('#modalFilterMapping').modal('toggle');
        });

        NioApp.resetModalForm('#modalFilterMapping', user_table['.broadcast-init'], '.resetFilter');

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