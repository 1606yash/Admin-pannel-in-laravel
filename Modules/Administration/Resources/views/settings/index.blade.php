@extends('layouts.app')
@section('content')
@php
$userPermission = \Session::get('userPermission');
@endphp
<div class="nk-block-head nk-block-head-sm">
    <div class="nk-block-between">
        <div class="nk-block-head-content">
            <h3 class="nk-block-title page-title">Settings</h3>
        </div><!-- .nk-block-head-content -->

    </div><!-- .nk-block-between -->
</div><!-- .nk-block-head -->
<div class="card">
    <div class="card-inner pt-2 pl-2 pb-0">
        <ul class="nav nav-tabs mt-n3 bdr-btm-none">
            <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#tabGeneral"><em class="icon ni ni-setting"></em> <span>General</span></a>
            </li>
           {{--  <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#tabUser"><em class="icon ni ni-user"></em><span>User</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#tabTheme"><em class="icon ni ni-text-rich"></em><span>Theme</span></a>
            </li> --}}
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#tabEcommerce"><em class="icon ni ni-cart"></em><span>Ecommerce</span></a>
            </li>
            {{-- <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#tabNotification"><em class="icon ni ni-bell"></em><span>Notification</span></a>
            </li> --}}
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#tabHomeSettings"><em class="icon ni ni-setting"></em><span>Home Settings</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#tabIntegration"><em class="icon ni ni-setting"></em><span>Integration</span></a>
            </li>
        </ul>
    </div>
</div>
<div class="nk-block table-compact nk-block-lg pt-28">
    <div class="">
        <div class="">

            <div class="tab-content">
                <div class="tab-pane active" id="tabGeneral">

                    <div class="nk-tb-list is-separate mb-3">
                        <div class="nk-tb-item nk-tb-head">
                            <div class="nk-tb-col fw-bold"><span class="sub-text">Label</span></div>
                            <div class="nk-tb-col fw-bold tb-col-mb"><span class="sub-text">Value</span></div>
                            <div class="nk-tb-col fw-bold tb-col-md nk-tb-action-col nowrap"><span class="sub-text">Updated At</span></div>
                            <div class="nk-tb-col nk-tb-col-tools nk-tb-action-col text-right nowrap">
                                <span class="sub-text">Action</span>
                            </div>
                        </div><!-- .nk-tb-item -->

                        @isset ($settingData['General'])
                        @foreach ($settingData['General'] as $key => $setting)
                        <div class="nk-tb-item">
                            <div class="nk-tb-col">
                                <a href="#">
                                    <div class="user-card">
                                        <div class="user-info">
                                            <span>{{ $setting['label'] }}</span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="nk-tb-col tb-col-md" style="">
                                <span>{{ $setting['value'] }}</span>
                            </div>
                            <div class="nk-tb-col tb-col-lg nowrap">
                                <span>{{ date(\Config::get('constants.DATE.DATE_FORMAT'),strtotime($setting['updated_at'])) }}</span>
                            </div>
                            <div class="nk-tb-col nk-tb-col-tools">
                                @if($setting['editable'])
                                    <ul class="nk-tb-actions gx-1"><li><div class="drodown mr-n1"><a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-toggle="dropdown"><em class="icon ni ni-more-h"></em></a><div class="dropdown-menu dropdown-menu-right"><ul class="link-list-opt no-bdr">
                                        @if(isset($userPermission['settings']) && ($userPermission['settings']['edit_all'] || $userPermission['settings']['edit_own']))
                                        <li>
                                            @php
                                            $rowData = json_encode($setting);
                                            @endphp
                                            <a href="#" class="toggle" data-target="editSetting" onclick="editSettings({{ $rowData }});">
                                                <em class="icon ni ni-edit-alt"></em> <span>Edit</span>
                                            </a>
                                            @endif
                                        </li>
                                    </ul></div></div></li></ul>
                                @endif
                            </div>
                        </div><!-- .nk-tb-item -->
                        @endforeach
                        @endisset
                    </div><!-- .nk-tb-list -->
                </div>
                <div class="tab-pane table-compact" id="tabUser">

                    <div class="nk-tb-list is-separate mb-3">
                        <div class="nk-tb-item nk-tb-head">
                            <div class="nk-tb-col fw-bold"><span class="sub-text">Label</span></div>
                            <div class="nk-tb-col fw-bold tb-col-mb"><span class="sub-text">Value</span></div>
                            <div class="nk-tb-col fw-bold tb-col-md nk-tb-action-col nowrap"><span class="sub-text">Updated At</span></div>
                            <div class="nk-tb-col nk-tb-col-tools nk-tb-action-col text-right nowrap">
                                <span class="sub-text">Action</span>
                            </div>
                        </div><!-- .nk-tb-item -->
                        @isset ($settingData['User'])
                        @foreach ($settingData['User'] as $key => $setting)
                        <div class="nk-tb-item">
                            <div class="nk-tb-col">
                                <a href="#">
                                    <div class="user-card">
                                        <div class="user-info">
                                            <span>{{ $setting['label'] }}</span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="nk-tb-col tb-col-md">
                                <span>{{ $setting['value'] }}</span>
                            </div>
                            <div class="nk-tb-col tb-col-lg nowrap">
                                <span>{{ date(\Config::get('constants.DATE.DATE_FORMAT'),strtotime($setting['updated_at'])) }}</span>
                            </div>
                            <div class="nk-tb-col nk-tb-col-tools">
                                @if($setting['editable'])
                                <ul class="nk-tb-actions gx-1"><li><div class="drodown mr-n1"><a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-toggle="dropdown"><em class="icon ni ni-more-h"></em></a><div class="dropdown-menu dropdown-menu-right"><ul class="link-list-opt no-bdr">
                                    <li>
                                        @php
                                        $rowData = json_encode($setting);
                                        @endphp

                                        <a href="#" class="toggle" data-target="editSetting" onclick="editSettings({{ $rowData }});" data-toggle="tooltip" data-placement="top" title="Edit">
                                            <em class="icon ni ni-edit-alt"></em> <span>Edit</span>
                                        </a>
                                    </li>
                                </ul></div></div></li></ul>
                                @endif
                            </div>
                        </div><!-- .nk-tb-item -->
                        @endforeach
                        @endisset
                    </div><!-- .nk-tb-list -->
                </div>
                <div class="tab-pane table-compact" id="tabTheme">

                    <div class="nk-tb-list is-separate mb-3">
                        <div class="nk-tb-item nk-tb-head">
                            <div class="nk-tb-col fw-bold"><span class="sub-text">Label</span></div>
                            <div class="nk-tb-col fw-bold tb-col-mb"><span class="sub-text">Value</span></div>
                            <div class="nk-tb-col fw-bold tb-col-md nk-tb-action-col nowrap"><span class="sub-text">Updated At</span></div>
                            <div class="nk-tb-col nk-tb-col-tools nk-tb-action-col text-right nowrap">
                                <span class="sub-text">Action</span>
                            </div>
                        </div><!-- .nk-tb-item -->
                        @isset ($settingData['Theme'])
                        @foreach ($settingData['Theme'] as $key => $setting)
                        <div class="nk-tb-item">
                            <div class="nk-tb-col">
                                <a href="#">
                                    <div class="user-card">
                                        <div class="user-info">
                                            <span>{{ $setting['label'] }}</span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="nk-tb-col tb-col-md">
                                <span>{{ $setting['value'] }}</span>
                            </div>
                            <div class="nk-tb-col tb-col-lg nowrap">
                                <span>{{ date(\Config::get('constants.DATE.DATE_FORMAT'),strtotime($setting['updated_at'])) }}</span>
                            </div>
                            <div class="nk-tb-col nk-tb-col-tools">
                                @if($setting['editable'])
                                <ul class="nk-tb-actions gx-1"><li><div class="drodown mr-n1"><a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-toggle="dropdown"><em class="icon ni ni-more-h"></em></a><div class="dropdown-menu dropdown-menu-right"><ul class="link-list-opt no-bdr">
                                    <li>
                                        @php
                                        $rowData = json_encode($setting);
                                        @endphp

                                        <a href="#" class="toggle" data-target="editSetting" onclick="editSettings({{ $rowData }});">
                                            <em class="icon ni ni-edit-alt"></em> <span>Edit</span>
                                        </a>
                                    </li>
                                </ul></div></div></li></ul>
                                @endif
                            </div>
                        </div><!-- .nk-tb-item -->
                        @endforeach
                        @endisset
                    </div><!-- .nk-tb-list -->
                </div>
                <div class="tab-pane table-compact" id="tabEcommerce">

                    <div class="nk-tb-list is-separate mb-3">
                        <div class="nk-tb-item nk-tb-head">
                            <div class="nk-tb-col fw-bold"><span class="sub-text">Label</span></div>
                            <div class="nk-tb-col fw-bold tb-col-mb"><span class="sub-text">Value</span></div>
                            <div class="nk-tb-col fw-bold tb-col-md nk-tb-action-col nowrap"><span class="sub-text">Updated At</span></div>
                            <div class="nk-tb-col nk-tb-col-tools nk-tb-action-col text-right nowrap">
                                <span class="sub-text">Action</span>
                            </div>
                        </div><!-- .nk-tb-item -->
                        @isset ($settingData['Ecommerce'])
                        @foreach ($settingData['Ecommerce'] as $key => $setting)
                        <div class="nk-tb-item">
                            <div class="nk-tb-col">
                                <a href="#">
                                    <div class="user-card">
                                        <div class="user-info">
                                            <span>{{ $setting['label'] }}</span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="nk-tb-col tb-col-md">
                                <span>{{ $setting['value'] }}</span>
                            </div>
                            <div class="nk-tb-col tb-col-lg nowrap">
                                <span>{{ date(\Config::get('constants.DATE.DATE_FORMAT'),strtotime($setting['updated_at'])) }}</span>
                            </div>
                            <div class="nk-tb-col nk-tb-col-tools">
                                @if($setting['editable'])
                                <ul class="nk-tb-actions gx-1"><li><div class="drodown mr-n1"><a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-toggle="dropdown"><em class="icon ni ni-more-h"></em></a><div class="dropdown-menu dropdown-menu-right"><ul class="link-list-opt no-bdr">
                                    <li>
                                        @php
                                        $rowData = json_encode($setting);
                                        @endphp

                                        <a href="#" class="toggle" data-target="editSetting" onclick="editSettings({{ $rowData }});">
                                            <em class="icon ni ni-edit-alt"></em> <span>Edit</span>
                                        </a>
                                    </li>
                                </ul></div></div></li></ul>
                                @endif
                            </div>
                        </div><!-- .nk-tb-item -->
                        @endforeach
                        @endisset
                    </div><!-- .nk-tb-list -->
                </div>
                <div class="tab-pane table-compact" id="tabNotification">

                    <div class="nk-tb-list is-separate mb-3">
                        <div class="nk-tb-item nk-tb-head">
                            <div class="nk-tb-col fw-bold"><span class="sub-text">Label</span></div>
                            <div class="nk-tb-col fw-bold tb-col-mb"><span class="sub-text">Value</span></div>
                            <div class="nk-tb-col fw-bold tb-col-md nk-tb-action-col nowrap"><span class="sub-text">Updated At</span></div>
                            <div class="nk-tb-col nk-tb-col-tools nk-tb-action-col text-right nowrap">
                                <span class="sub-text">Action</span>
                            </div>
                        </div><!-- .nk-tb-item -->
                        @isset ($settingData['Notification'])
                        @foreach ($settingData['Notification'] as $key => $setting)
                        <div class="nk-tb-item">
                            <div class="nk-tb-col">
                                <a href="#">
                                    <div class="user-card">
                                        <div class="user-info">
                                            <span>{{ $setting['label'] }}</span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="nk-tb-col tb-col-md">
                                <span>{{ $setting['value'] }}</span>
                            </div>
                            <div class="nk-tb-col tb-col-lg nowrap">
                                <span>{{ date(\Config::get('constants.DATE.DATE_FORMAT'),strtotime($setting['updated_at'])) }}</span>
                            </div>
                            <div class="nk-tb-col nk-tb-col-tools">
                                @if($setting['editable'])
                                <ul class="nk-tb-actions gx-1"><li><div class="drodown mr-n1"><a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-toggle="dropdown"><em class="icon ni ni-more-h"></em></a><div class="dropdown-menu dropdown-menu-right"><ul class="link-list-opt no-bdr">
                                    <li>
                                        @php
                                        $rowData = json_encode($setting);
                                        @endphp

                                        <a href="#" class="toggle" data-target="editSetting" onclick="editSettings({{ $rowData }});">
                                            <em class="icon ni ni-edit-alt"></em> <span>Edit</span>
                                        </a>
                                    </li>
                                </ul></div></div></li></ul>
                                @endif
                            </div>
                        </div><!-- .nk-tb-item -->
                        @endforeach
                        @endisset
                    </div><!-- .nk-tb-list -->
                </div>
                <div class="tab-pane table-compact" id="tabHomeSettings">
                    <form method="post" action="{{ url('administration/settings/home-settings/') }}">
                        @csrf
                        <div class="card">
                            <div class="card-header border-bottom">Toggle below sections on homepage</div>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">
                                    <div class="row">
                                        <div class="col-sm-5">
                                            <x-inputs.verticalFormLabel label="Categories" for="categories" suggestion="Select the product categories." />
                                        </div>
                                        <div class="col-sm-7 text-right">
                                            <div class="form-group">
                                                <div class="form-control-wrap">

                                                    @php
                                                        if(isset($homeSettings) && !empty($homeSettings->categories)){
                                                            $homeCategories = explode(',', $homeSettings->categories);
                                                        }else{
                                                            $homeCategories = array();
                                                        }
                                                    @endphp

                                                    <select class="form-select" multiple="" data-placeholder="Select Categories" data-parsley-errors-container=".proCatParsley" name="categories[]">
                                                        <option value="" disabled>Select Categories</option>
                                                        @forelse($categories as $key => $cat)
                                                        <option 
                                                        @if(in_array($cat->id, $homeCategories))
                                                        selected 
                                                        @endif
                                                        value="{{ $cat->id }}">{{ $cat->name }}
                                                        </option>
                                                        @empty
                                                        <option></option>
                                                        @endforelse
                                                    </select>
                                                </div>
                                                <div class="proCatParsley"></div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item">
                                    <div class="row">
                                        <div class="col-sm-5">
                                            <x-inputs.verticalFormLabel label="Best Seller" for="best_seller" />
                                        </div>
                                        <div class="col-sm-7 text-right">

                                            @php
                                            if(isset($homeSettings) && $homeSettings->best_seller){
                                            $checked = 'true';
                                            }else{
                                            $checked = '';
                                            }
                                            @endphp
                                            <x-inputs.switch for="best_seller" size="md" name="best_seller" checked={{$checked}} />
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item">
                                    <div class="row">
                                        <div class="col-sm-5">
                                            <x-inputs.verticalFormLabel label="Featured Products" for="featured_products" />
                                        </div>
                                        <div class="col-sm-7 text-right">

                                            @php
                                            if(isset($homeSettings) && $homeSettings->featured_product){
                                                $checked = 'true';
                                            }else{
                                                $checked = '';
                                            }
                                            @endphp
                                            <x-inputs.switch for="featured_products" size="md" name="featured_products" checked={{$checked}} />
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item">
                                    <div class="row">
                                        <div class="col-sm-5">
                                            <x-inputs.verticalFormLabel label="Brands" for="brands" />
                                        </div>
                                        <div class="col-sm-7 text-right">

                                            @php
                                            if(isset($homeSettings) && $homeSettings->brands){
                                            $checked = 'true';
                                            }else{
                                            $checked = '';
                                            }
                                            @endphp
                                            <x-inputs.switch for="brands" size="md" name="brands" checked={{$checked}} />
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item" style="display:none;">
                                    <div class="row">
                                        <div class="col-sm-5">
                                            <x-inputs.verticalFormLabel label="Models" for="models" />
                                        </div>
                                        <div class="col-sm-7 text-right">

                                            @php
                                            if(isset($homeSettings) && $homeSettings->models){
                                            $checked = 'true';
                                            }else{
                                            $checked = '';
                                            }
                                            @endphp
                                            <x-inputs.switch for="models" size="md" name="models" checked={{$checked}} />
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item">
                                    <div class="row">
                                        <div class="col-sm-5">
                                            <x-inputs.verticalFormLabel label="Recommended Products" for="recommended_products" />
                                        </div>
                                        <div class="col-sm-7 text-right">

                                            @php
                                            if(isset($homeSettings) && $homeSettings->recommended_products){
                                            $checked = 'true';
                                            }else{
                                            $checked = '';
                                            }
                                            @endphp
                                            <x-inputs.switch for="recommended_products" size="md" name="recommended_products" checked={{$checked}} />
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item">
                                    <div class="row">
                                        <div class="col-sm-5">
                                            <x-inputs.verticalFormLabel label="New Arrivals" for="new_arrivals" />
                                        </div>
                                        <div class="col-sm-7 text-right">

                                            @php
                                            if(isset($homeSettings) && $homeSettings->new_arrivals){
                                            $checked = 'true';
                                            }else{
                                            $checked = '';
                                            }
                                            @endphp
                                            <x-inputs.switch for="new_arrivals" size="md" name="new_arrivals" checked={{$checked}} />
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item">
                                    <div class="row">
                                        <div class="col-sm-5">
                                            <x-inputs.verticalFormLabel label="Inventory" for="inventory" />
                                        </div>
                                        <div class="col-sm-7 text-right">

                                            @php
                                            if(isset($homeSettings) && $homeSettings->inventory){
                                            $checked = 'true';
                                            }else{
                                            $checked = '';
                                            }
                                            @endphp
                                            <x-inputs.switch for="inventory" size="md" name="inventory" checked={{$checked}} />
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item">
                                    <div class="row">
                                        <div class="col-sm-5">
                                            <x-inputs.verticalFormLabel label="Segments" for="segments" />
                                        </div>
                                        <div class="col-sm-7 text-right">

                                            @php
                                            if(isset($homeSettings) && $homeSettings->segments){
                                            $checked = 'true';
                                            }else{
                                            $checked = '';
                                            }
                                            @endphp
                                            <x-inputs.switch for="segments" size="md" name="segments" checked={{$checked}} />
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div><!-- No Header -->
                        <br />
                        <br />
                        <div class="text-right">
                        <button type="submit" name="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>

                <div class="tab-pane table-compact" id="tabIntegration">
                    <div class="nk-tb-list is-separate mb-3">
                        <div class="nk-tb-item nk-tb-head">
                            <div class="nk-tb-col fw-bold"><span class="sub-text">Label</span></div>
                            <div class="nk-tb-col fw-bold tb-col-mb"><span class="sub-text">Value</span></div>
                            <div class="nk-tb-col fw-bold tb-col-md nk-tb-action-col nowrap"><span class="sub-text">Updated At</span></div>
                            <div class="nk-tb-col nk-tb-col-tools nk-tb-action-col text-right nowrap">
                                <span class="sub-text">Action</span>
                            </div>
                        </div><!-- .nk-tb-item -->
                        @isset ($settingData['Integration'])
                        @foreach ($settingData['Integration'] as $key => $setting)
                        <div class="nk-tb-item">
                            <div class="nk-tb-col">
                                <a href="#">
                                    <div class="user-card">
                                        <div class="user-info">
                                            <span>{{ $setting['label'] }}</span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="nk-tb-col tb-col-md">
                                <span>{{ $setting['value'] }}</span>
                            </div>
                            <div class="nk-tb-col tb-col-lg nowrap">
                                <span>{{ date(\Config::get('constants.DATE.DATE_FORMAT'),strtotime($setting['updated_at'])) }}</span>
                            </div>
                            <div class="nk-tb-col nk-tb-col-tools">
                                @if($setting['editable'])
                                <ul class="nk-tb-actions gx-1"><li><div class="drodown mr-n1"><a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-toggle="dropdown"><em class="icon ni ni-more-h"></em></a><div class="dropdown-menu dropdown-menu-right"><ul class="link-list-opt no-bdr">
                                    <li>
                                        @php
                                        $rowData = json_encode($setting);
                                        @endphp

                                        <a href="#" class="toggle" data-target="editSetting" onclick="editSettings({{ $rowData }});">
                                            <em class="icon ni ni-edit-alt"></em> <span>Edit</span>
                                        </a>
                                    </li>
                                </ul></div></div></li></ul>
                                @endif
                            </div>
                        </div><!-- .nk-tb-item -->
                        @endforeach
                        @endisset
                    </div><!-- .nk-tb-list -->

                </div>

            </div>
        </div>
    </div><!-- .card-preview -->
    <div class="nk-add-product toggle-slide toggle-slide-right" data-content="editSetting" data-toggle-screen="any" data-toggle-overlay="true" data-toggle-body="true" data-simplebar>
        <div class="nk-block-head">
            <div class="nk-block-head-content">
                <h5 class="nk-block-title">Edit <span id="settingLable"></span></h5>
                <div class="nk-block-des">
                    {{-- <p>Edit text for the setting.</p> --}}
                </div>
            </div>
        </div><!-- .nk-block-head -->
        <div class="nk-block">
            <form action="#" role="form" class="mb-0" method="post" enctype="multipart/form-data">
                @csrf
                <div class="row g-3">
                    <div class="col-12">
                        <div id="inputBox">
                            <x-inputs.text for="settingValue" size="sm" required="true" label="Desctription" name="value" />
                        </div>
                        <input type="hidden" value="" name="id" id="settingId">
                    </div>
                    <div class="col-12 text-right">
                        <a data-target='editSetting' class="btn btn-outline-light cancel">Cancel</a>
                        <button class="btn btn-primary" type="Submit"><span>Submit</span></button>
                    </div>
                </div>

            </form>
        </div><!-- .nk-block -->
    </div>
</div><!-- .nk-block -->
@endsection
@push('footerScripts')
<script src="{{url('js/jquery-ui.js?t='.time())}}"></script>
<script type="text/javascript">
    $
    $('.cancel').on('click', function() {
        $('#settingValue').val('');
        $('#settingId').val('');
        NioApp.Toggle.collapseDrawer('editSetting')
    })

    function editSettings(rowData) {
        var input = 'text';

        switch (rowData.type) {
            case 'TEXT':
                input = '<input type="text" class="form-control" required value="' + rowData.value + '" name="value">';
                break;
            case 'NUMBER':
                input = '<input type="number" class="form-control" required value="' + rowData.value + '" name="value">';
                break;
            case 'SELECT':
                var select = '<select  class="form-control" required  name="value">';

                console.log(rowData.default_options);

                $.each(JSON.parse(rowData.default_options), function(key, value) {

                    if (key == rowData.value) {
                        var selected = 'selected';
                    } else {
                        var selected = '';
                    }

                    select += '<option ' + selected + ' value="' + key + '">' + value + '</option>';
                });
                select += "</select>";
                input = select;
                break;
            case 'TEXTAREA':
                var textarea = '<textarea name="value" class="form-control" required>' + rowData.value + '</textarea>';
                input = textarea;
                break;
            case 'FILE':
                var fileData = '<div><img class="rounded-circle z-depth-2" src="{{ url('uploads/settings ') }}/' + rowData.value + '" width="200"><input type="file" class="form-control" required name="value"></div>';
                input = fileData;
                break;
            case 'BOOLEAN':

                if (rowData.value == 'true') {
                    var checked = 'checked';
                } else {
                    var checked = '';
                }

                var boolean = '<div class="form-control-wrap">\
                                    <div class="custom-control custom-switch">\
                                        <input name="value" type="checkbox" class="custom-control-input" id="value" ' + checked + '>\
                                        <label class="custom-control-label" for="value">&nbsp;</label>\
                                    </div>\
                                </div>';

                input = boolean;
                break;
            default:
                input = 'editSetting';
        }

        $('#inputBox').html('');
        $('#inputBox').html(input);

        $('#settingId').val(rowData.id);
        $('#settingLable').text(rowData.label);
    }
    // sortable / draggable 
    $(function(){
        $(".sortable-menu").sortable({
            placeholder: "ui-state-highlight"
        });
        $(".sortable-menu").disableSelection();
    });
</script>
@endpush