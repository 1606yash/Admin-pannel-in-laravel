@extends('layouts.app')
@section('content')
<div class="nk-block-head nk-block-head-sm">
    <div class="nk-block-between">
        <div class="nk-block-head-content">
            <h3 class="nk-block-title page-title">Target / Achievement For Sales Person</h3>
            <p id="note">{{ $note }}</p>
        </div><!-- .nk-block-head-content -->
        <div class="nk-block-head-content">
            <div class="toggle-wrap nk-block-tools-toggle">
                <a href="#" class="btn btn-icon btn-trigger toggle-expand mr-n1" data-target="more-options"><em class="icon ni ni-more-v"></em></a>
                <div class="toggle-expand-content" data-content="more-options">
                    <ul class="nk-block-tools g-3">
                        <li>
                            <a href="#" class="btn btn-trigger btn-icon dropdown-toggle" data-toggle="modal" title="filter" data-target="#modalFilterorder">
                                <div class="dot dot-primary"></div>
                                <em class="icon ni ni-filter-alt"></em>
                            </a>
                        </li>
                        <li>
                            <div class="dropdown">
                                <a href="#" class="btn btn-trigger btn-icon dropdown-toggle" data-toggle="dropdown">
                                    <em class="icon ni ni-setting"></em>
                                </a>
                                <div class="dropdown-menu dropdown-menu-xs dropdown-menu-right">
                                    <ul class="link-check">
                                        <li><span>Actions</span></li>
                                        <li><a href="#"><em class="icon ni ni-download m-r10"></em> Export</a></li>
                                        {{-- <li><a href="#"><em class="icon ni ni-upload m-r10"></em> Import</a></li> --}}
                                        </ul>
                                </div>
                            </div>
                        </li>
                        
                    </ul>
                </div>
            </div>
        </div>
    </div><!-- .nk-block-between -->
</div><!-- .nk-block-head -->
<!--  Filter Tag List -->
<div id="filter_tag_list" class="filter-tag-list"></div>
<div class="nk-block table-compact">
    <div class="nk-tb-list is-separate is-medium mb-3">
        <table id="SP_target" class="products-init nowrap nk-tb-list is-separate table-head-bottom" data-auto-responsive="false">
            <thead>
                <tr class="nk-tb-item nk-tb-head">
                    <th class="nk-tb-col tb-col-mb" rowspan="2"><span class="sub-text">Sales Person</span></th>
                    <th class="nk-tb-col tb-col-mb" rowspan="2"><span class="sub-text">Period</span></th>
                    <th class="nk-tb-col tb-col-mb text-center" colspan="2"><span class="sub-text">Total Sales</span></th>
                    <th class="nk-tb-col tb-col-mb text-center" colspan="2"><span class="sub-text">Total New Order</span></th>
                    <th class="nk-tb-col tb-col-mb text-center" colspan="2"><span class="sub-text">Total New Line Items</span></th>
                    <th class="nk-tb-col tb-col-mb" rowspan="2"><span class="sub-text">State</span></th>
                    <th class="nk-tb-col tb-col-mb" rowspan="2"><span class="sub-text">District</span></th>
                    <th class="nk-tb-col tb-col-mb" rowspan="2"><span class="sub-text">City</span></th>
                </tr>
                <tr class="nk-tb-item nk-tb-head">
                    <th class="nk-tb-col tb-col-mb text-center"><span class="sub-text">Target</span></th>
                    <th class="nk-tb-col tb-col-mb text-center"><span class="sub-text">Achievement</span></th>
                    <th class="nk-tb-col tb-col-mb text-center"><span class="sub-text">Target</span></th>
                    <th class="nk-tb-col tb-col-mb text-center"><span class="sub-text">Achievement</span></th>
                    <th class="nk-tb-col tb-col-mb text-center"><span class="sub-text">Target</span></th>
                    <th class="nk-tb-col tb-col-mb text-center"><span class="sub-text">Achievement</span></th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div><!-- .nk-tb-list -->
</div><!-- .nk-block -->

<div id="table_pagination"></div>


<div class="modal fade zoom" tabindex="-1" id="modalFilterorder">
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
                                <x-inputs.verticalFormLabel label="Month" for="month" suggestion="Select the month." />
                            </div>
                            <div class="col-lg-7">
                                <x-inputs.select  size="sm" name="month" for="month" placeholder="Month">
                                    <option value="">Select</option>
                                    @for ($i = 1; $i <= 12 ; $i++)
                                        <option value="{{ $i }}">{{ date('F',strtotime('2020-'.$i.'-01')) }}</option>
                                    @endfor
                                </x-inputs.select>
                            </div>
                        </div>
                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <x-inputs.verticalFormLabel label="Year" for="year" suggestion="Select the year." />
                            </div>
                            <div class="col-lg-7">
                                <x-inputs.select  size="sm" name="year" for="year" placeholder="Year">
                                    <option value="">Select</option>
                                    @for ($i = 2020; $i <= date('Y') ; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </x-inputs.select>
                            </div>
                        </div>
                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <x-inputs.verticalFormLabel label="Sales Person" for="salesPerson" suggestion="Select the sales person." />
                            </div>
                            <div class="col-lg-7">
                                <x-inputs.select  size="sm" name="salesPerson" for="salesPerson" placeholder="Sales Person">
                                    <option value="">Select</option>
                                    @foreach ($targets as $key => $target)
                                        <option
                                        value="{{ $target->user_id }}">{{ $target->username }}</option>
                                    @endforeach
                                    
                                </x-inputs.select>
                            </div>
                        </div>
                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <x-inputs.verticalFormLabel label="State" for="state" suggestion="Specify the state." />
                            </div>
                            <div class="col-lg-7">
                                <x-inputs.select  size="md" name="state" for="state" data-search="on">
                                    <option value="">select</option>
                                    @foreach ($states as $key => $state)
                                        <option
                                        value="{{ $state->id }}">{{ $state->name }}</option>
                                    @endforeach
                                </x-inputs.select>
                            </div>
                        </div>
                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <x-inputs.verticalFormLabel label="District" for="district" suggestion="Specify the district." />
                            </div>
                            <div class="col-lg-7">
                                <x-inputs.select size="sm" name="district" for="district">
                                    <option value="">select</option>
                                </x-inputs.select>
                            </div>
                        </div>
                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <x-inputs.verticalFormLabel label="City" for="city" suggestion="Specify the city." />
                            </div>
                            <div class="col-lg-7">
                                <x-inputs.select size="sm" name="city" for="city">
                                    <option value="">select</option>
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
<script src="{{url('js/APIDataTable.js')}}"></script>
<script src="{{url('js/dayjs.min.js?t='.time())}}"></script>
<script type="text/javascript">
    
    var token = '{{ \Session::get('token') }}';//get logged in user session.
    var organizationId = "{{ \Session::get('currentOrganization') }}";

    var dataTable = new APIDataTable({
        tableElem: '#SP_target',
        pageinationElem: '#table_pagination',
        api: "{{ url('api/v1/report/target-achievement-sales-person') }}/"+organizationId,
        authToken: 'Bearer '+token,
        filterIds: [
            '#month',
            '#year',
            '#salesPerson',
            '#state',
            '#district',
            '#city'
        ],
        
        filterSubmit: '.submitBtn',
        filterSubmitCallback: function(){
            $('#modalFilterorder').modal('toggle');
        },
        filterClearSubmit: '.resetFilter',
        filterModalId: '#modalFilterorder',
        tagId: '#filter_tag_list',
        columns: [{
                data: "username"
            },
            {
                data: "period"
            },
            {
                data: "total_sales",
                render:function(row){
                    return NioApp.formatToCurrency(row.total_sales);
                }
            },
            {
                data: "achivedSales",
                render:function(row){
                    return NioApp.formatToCurrency(row.achivedSales);
                }
            },
            {
                data: "total_orders"
            },
            {
                data: "achivedOrders"
            },
            {
                data: "total_line_items"
            },
            {
                data: "achivedItems"
            },
            {
                data: "state"
            },
            {
                data: "district"
            },
            {
                data: "city"
            }
        ]
    });
    // $('.submitBtn').click(function(){
    //     var month = $('#month').val();
    //         var monthText = $( "#month option:selected" ).text();
    //         var year = $('#year').val();

    //         if(year != "" && month == ""){
    //             alert('Please select month');
    //             return;
    //         }

    //         if(year == "" && month != ""){
    //             alert('Please select year');
    //             return;
    //         }

    //         var note = "Showing results for "+monthText+'-'+year;
    //         $('#note').text(note);


    //     var d = {}
    //     d.month = $('#month').val()
    //     d.year = $('#year').val()
    //     d.salesPerson = $('#salesPerson').val()
    //     d.state = $('#state').val()
    //     d.district = $('#district').val()
    //     d.city = $('#city').val()
    //     dataTable.filter(d)
    //     $('#modalFilterorder').modal('toggle');

    //     var items = [
    //         '#month',
    //         '#year',
    //         '#salesPerson',
    //         '#state',
    //         '#district',
    //         '#city'
    //     ];
    //     NioApp.filterTag(items, dataTable['#SP_target'], '#filter_tag_list');
    // });
    // $('.resetFilter').click(function(){
    //     var currentMonth = "{{ date('F') }}";
    //         var currentYear = "{{ date('Y') }}";

    //         var note = "Showing results for "+currentMonth+'-'+currentYear;
    //         $('#note').text(note);            
    //     var d = {}
    //     dataTable.filter(d)
    //     $('#modalFilterorder').modal('toggle');
    // });
 

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