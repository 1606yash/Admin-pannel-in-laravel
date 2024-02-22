@extends('layouts.app')
@section('content')
<div class="nk-block-head nk-block-head-sm">
    <div class="nk-block-between">
        <div class="nk-block-head-content">
            <h3 class="nk-block-title page-title">Settings</h3>
        </div><!-- .nk-block-head-content -->
        <div class="nk-block-head-content">
            <div class="toggle-wrap nk-block-tools-toggle">
                <a href="#" class="btn btn-icon btn-trigger toggle-expand mr-n1" data-target="more-options"><em class="icon ni ni-more-v"></em></a>
                <div class="toggle-expand-content" data-content="more-options">
                    <ul class="nk-block-tools g-3">
                        <li>
                            <div class="drodown">
                                <a href="#" class="dropdown-toggle dropdown-indicator btn btn-primary" data-toggle="dropdown">Create</a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <ul class="link-list-opt no-bdr">
                                        <li><a href="#" data-id='1' class="toggle" onClick="addSetting(this)"><span>Text</span></a></li>
                                        <li><a href="#" data-id='2' class="toggle" onClick="addSetting(this)"><span>Text Area</span></a></li>
                                        <li><a href="#" data-id='3' class="toggle" onClick="addSetting(this)"><span>Boolean</span></a></li>
                                        <li><a href="#" data-id='4' class="toggle" onClick="addSetting(this)"><span>Number</span></a></li>
                                        <li><a href="#" data-id='5' class="toggle" onClick="addSetting(this)"><span>Date</span></a></li>
                                        <li><a href="#" data-id='6' class="toggle" onClick="addSetting(this)"><span>Select Options</span></a></li>

                                    </ul>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div><!-- .nk-block-head-content -->
    </div><!-- .nk-block-between -->
</div><!-- .nk-block-head -->
<div class="card">
    <div class="card-inner pt-2 pl-2 pb-0">
        <ul class="nav nav-tabs mt-n3 bdr-btm-none">
            <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#tabGeneral"><em class="icon ni ni-setting"></em> <span>General</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#tabUser"><em class="icon ni ni-user"></em><span>User</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#tabTheme"><em class="icon ni ni-text-rich"></em><span>Theme</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#tabEcommerce"><em class="icon ni ni-cart"></em><span>Ecommerce</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#tabNotification"><em class="icon ni ni-bell"></em><span>Notification</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#tabSystem"><em class="icon ni ni-opt-dot-alt"></em><span>System</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#tabHomepage"><em class="icon ni ni-home"></em><span>Home Page</span></a>
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
                        <table class="general-init nowrap nk-tb-list is-separate" data-auto-responsive="false">
                            <thead>
                                <tr class="nk-tb-item nk-tb-head">
                                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">SN</span></th>
                                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Label</span></th>
                                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Code</span></th>
                                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Type</span></th>
                                    <th class="nk-tb-col tb-col-mb nk-tb-action-col" nowrap="true"><span class="sub-text">Updated at</span></th>
                                    <th class="nk-tb-col nk-tb-col-tools text-right nk-tb-action-col" nowrap="true">
                                        <span class="sub-text">Action</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div><!-- .nk-tb-list -->
                </div>
                <div class="tab-pane table-compact" id="tabUser">
                    <div class="nk-tb-list is-separate mb-3">
                        <table class="user-init nowrap nk-tb-list is-separate" data-auto-responsive="false">
                            <thead>
                                <tr class="nk-tb-item nk-tb-head">
                                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">SN</span></th>
                                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Label</span></th>
                                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Code</span></th>
                                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Type</span></th>
                                    <th class="nk-tb-col tb-col-mb nk-tb-action-col" nowrap="true"><span class="sub-text">Updated at</span></th>
                                    <th class="nk-tb-col nk-tb-col-tools text-right nk-tb-action-col" nowrap="true">
                                        <span class="sub-text">Action</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div><!-- .nk-tb-list -->
                </div>
                <div class="tab-pane table-compact" id="tabTheme">
                    <div class="nk-tb-list is-separate mb-3">
                        <table class="theme-init nowrap nk-tb-list is-separate" data-auto-responsive="false">
                            <thead>
                                <tr class="nk-tb-item nk-tb-head">
                                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">SN</span></th>
                                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Label</span></th>
                                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Code</span></th>
                                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Type</span></th>
                                    <th class="nk-tb-col tb-col-mb nk-tb-action-col" nowrap="true"><span class="sub-text">Updated at</span></th>
                                    <th class="nk-tb-col nk-tb-col-tools text-right nk-tb-action-col" nowrap="true">
                                        <span class="sub-text">Action</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div><!-- .nk-tb-list -->
                </div>
                <div class="tab-pane table-compact" id="tabEcommerce">
                    <div class="nk-tb-list is-separate mb-3">
                        <table class="ecom-init nowrap nk-tb-list is-separate" data-auto-responsive="false">
                            <thead>
                                <tr class="nk-tb-item nk-tb-head">
                                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">SN</span></th>
                                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Label</span></th>
                                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Code</span></th>
                                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Type</span></th>
                                    <th class="nk-tb-col tb-col-mb nk-tb-action-col" nowrap="true"><span class="sub-text">Updated at</span></th>
                                    <th class="nk-tb-col nk-tb-col-tools text-right nk-tb-action-col" nowrap="true">
                                        <span class="sub-text">Action</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div><!-- .nk-tb-list -->
                </div>
                <div class="tab-pane table-compact" id="tabNotification">
                    <div class="nk-tb-list is-separate mb-3">
                        <table class="notification-init nowrap nk-tb-list is-separate" data-auto-responsive="false">
                            <thead>
                                <tr class="nk-tb-item nk-tb-head">
                                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">SN</span></th>
                                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Label</span></th>
                                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Code</span></th>
                                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Type</span></th>
                                    <th class="nk-tb-col tb-col-mb nk-tb-action-col" nowrap="true"><span class="sub-text">Updated at</span></th>
                                    <th class="nk-tb-col nk-tb-col-tools text-right nk-tb-action-col" nowrap="true">
                                        <span class="sub-text">Action</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div><!-- .nk-tb-list -->
                </div>
                <div class="tab-pane table-compact" id="tabSystem">
                    <div class="nk-tb-list is-separate mb-3">
                        <table class="system-init nowrap nk-tb-list is-separate" data-auto-responsive="false">
                            <thead>
                                <tr class="nk-tb-item nk-tb-head">
                                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">SN</span></th>
                                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Label</span></th>
                                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Code</span></th>
                                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Type</span></th>
                                    <th class="nk-tb-col tb-col-mb nk-tb-action-col" nowrap="true"><span class="sub-text">Updated at</span></th>
                                    <th class="nk-tb-col nk-tb-col-tools text-right nk-tb-action-col" nowrap="true">
                                        <span class="sub-text">Action</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div><!-- .nk-tb-list -->
                </div>
                <div class="tab-pane table-compact" id="tabHomepage">
                    <div class="nk-tb-list is-separate mb-3">
                        <table class="home-init nowrap nk-tb-list is-separate" data-auto-responsive="false">
                            <thead>
                                <tr class="nk-tb-item nk-tb-head">
                                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">SN</span></th>
                                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Label</span></th>
                                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Code</span></th>
                                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Type</span></th>
                                    <th class="nk-tb-col tb-col-mb nk-tb-action-col" nowrap="true"><span class="sub-text">Updated at</span></th>
                                    <th class="nk-tb-col nk-tb-col-tools text-right nk-tb-action-col" nowrap="true">
                                        <span class="sub-text">Action</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div><!-- .nk-tb-list -->
                </div>
            </div>
        </div>
    </div><!-- .card-preview -->



</div><!-- .nk-block -->
<div id="modal" class="nk-add-product toggle-slide toggle-slide-right" data-content="addSettingsMaster" data-toggle-screen="any" data-toggle-overlay="true" data-toggle-body="true" data-simplebar>
    <form role="form" method="post" action="{{ url('saas/organization/settings/add') }}" enctype="multipart/form-data">
        @csrf
        <div class="nk-block-head">
            <div class="nk-block-head-content">
                <h5 class="nk-block-title modalTitle">Add Setting</h5>
                <div class="nk-block-des">
                    <!-- 
                        <p>Add text for the setting.</p> -->
                </div>
            </div>
        </div><!-- .nk-block-head -->
        <div class="nk-block">
            <form action="#">
                <div class="row g-3">
                    <div class="col-12">
                        <div class="form-group">
                            <label class="form-label">Code<span class="text-danger">*</span></label>
                            <div class="form-control-wrap">
                                <div class="form-icon form-icon-left "><em class="icon ni ni-list"></em></div>
                                <input type="text" class="form-control" required="true" data-parsley-errors-container=".parsley-container-text-code" id="code" placeholder="Code" name="code" minlength="3" maxlength="200" autocomplete="off">

                            </div>
                            <span class="form-note mt-0">code will be used for getting the setting Code.</span>
                            <div class="parsley-container-text-code"></div>
                        </div>
                    </div>
                    <div class="col-12">
                        <x-inputs.select label="Category" size="sm" required="true" for="textCategory" name="category">
                            <option></option>
                            <option value="General">General</option>
                            <option value="User">User</option>
                            <option value="Theme">Theme</option>
                            <option value="Ecommerce">Ecommerce</option>
                            <option value="Notification">Notification</option>
                            <option value="System">System</option>
                            <option value="Home">Home</option>
                        </x-inputs.select>
                    </div>
                    <div class="col-12">
                        <x-inputs.text Code="" for="textLabel" icon="label" required="true" label="Label" placeholder="Label" name="label" autocomplete="off" />
                    </div>

                    <div class="col-12" id="selectOptionsBox" style="display: none;">
                        <x-inputs.textarea for="selectOptions" size="sm" required="false" label="Select Option" minlength="3" maxlength="500" id="selectOptions" name="selectOptions" formNote='A json is required here like : {"home":"Home","office":"Office","shipping":"Shipping","billing":"Billing"}' />
                    </div>

                    <div class="col-12">
                        <x-inputs.textarea for="number25" size="sm" required="true" label="Description" minlength="3" maxlength="500" id="description" name="description" />
                    </div>
                    <input type="hidden" value="0" name="type" id="type">
                    <input type="hidden" value="0" name="id" id="itemId">
                    <div class="col-12 text-right">
                        <a href="javascript:resetValues();" class="btn btn-outline-light">Cancel</a>
                        <button class="btn btn-primary submitBtn" type="Submit"><span>Submit</span></button>
                    </div>
                </div>
        </div><!-- .nk-block -->
    </form>
</div>
@endsection
@push('footerScripts')
<script type="text/javascript">
    function addSetting(elem) {
        var dataId = $(elem).data("id");
        $('#type').val(dataId);
        if (dataId == 6) {
            $('#selectOptionsBox').show();
            $('#selectOptions').attr('required', true);
        } else {
            $('#selectOptionsBox').hide();
            $('#selectOptions').attr('required', false);
        }
        NioApp.Toggle.expendDrawer('addSettingsMaster')
    }

    function resetValues() {
        $('.submitBtn').text('Submit');
        $('.modalTitle').text('Add Setting');

        $('#code').val('');
        $('#textCategory,#textLabel,#desctription,#selectOptions').val('');
        NioApp.Toggle.collapseDrawer('addSettingsMaster')
    }


    function renderTable(dataType, elem) {
        NioApp.DataTable(elem, {
            processing: true,
            serverSide: true,
            ajax: "{{ url('saas/organization/settings?category={dataType}') }}".replace("{dataType}", dataType),
            columns: [{
                    "class": "nk-tb-col tb-col-lg nk-tb-col-check",
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                },
                {
                    "class": "nk-tb-col tb-col-lg",
                    data: 'label',
                    name: 'label'
                },
                {
                    "class": "nk-tb-col tb-col-lg",
                    data: 'code',
                    name: 'code'
                },
                {
                    "class": "nk-tb-col tb-col-lg",
                    data: 'type',
                    name: 'type'
                },
                {
                    "class": "nk-tb-col tb-col-lg",
                    data: 'updated_at',
                    name: 'updated_at'
                },
                {
                    "class": "nk-tb-col tb-col-lg text-right nk-tb-col-tools",
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ],
            "fnDrawCallback": function() {
                NioApp.TGL.content('.editItem',{
                    onCloseCallback: resetValues
                });
                NioApp.BS.tooltip('[data-toggle="tooltip"]');
            }
        });
    }

    $(function() {

        renderTable('General', '.general-init');
        renderTable('User', '.user-init');
        renderTable('Theme', '.theme-init');
        renderTable('Ecommerce', '.ecom-init');
        renderTable('Notification', '.notification-init');
        renderTable('System', '.system-init');
        renderTable('Homepage', '.home-init');



        var root_url = "<?php echo Request::root(); ?>";
        $('.general-init,.user-init,.theme-init,.ecom-init,.notification-init,.system-init,.home-init').on('click', '.eg-swal-av3', function(e) {
            var id = $(this).attr('data-id');
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!'
            }).then(function(result) {
                if (result.value) {
                    console.log('catId-' + id);
                    $.ajax({
                        url: root_url + '/saas/organization/settings/delete',
                        data: {
                            'id': id
                        },
                        //dataType: "html",
                        method: "GET",
                        cache: false,
                        success: function(data) {
                            console.log(data);
                            if (data.success) {
                                Swal.fire('Deleted!', 'Your item has been deleted.', 'success');
                                var table = $('.general-init').DataTable();
                                table.ajax.reload();
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: data.msg,
                                    //footer: '<a href>Why do I have this issue?</a>'
                                })
                            }
                        }
                    });

                }
            });
            e.preventDefault();
        });
        $('.general-init,.user-init,.theme-init,.ecom-init,.notification-init,.system-init,.home-init').on('click', '.editItem', function() {
            var id = $(this).attr('data-id');
            console.log(id);


            $.ajax({
                url: root_url + '/saas/organization/get-setting',
                data: {
                    'id': id
                },
                //dataType: "html",
                method: "GET",
                cache: false,
                success: function(data) {
                    console.log(data);

                    if (data.success) {
                        if (data.setting.type == 'SELECT') {
                            $('#selectOptionsBox').show();
                            $('#selectOptions').attr('required', true);
                            $('#selectOptions').val(data.setting.value);
                        }
                        $('#itemId').val(id);
                        $('#code').val(data.setting.code);
                        $('#textLabel').val(data.setting.label);
                        $('#desctription').val(data.setting.desctription);
                        $('.submitBtn').text('Update');
                        $('#textCategory').val(data.setting.category).trigger('change');
                        $('.modalTitle').text('Edit Setting (' + data.setting.type + ')');



                    } else {
                        Swal.fire('Details not found!')
                    }
                }
            });
        });
    });
</script>
@endpush