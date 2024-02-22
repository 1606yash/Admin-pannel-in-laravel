@extends('layouts.app')
@section('content')
<div class="nk-block-head nk-block-head-sm">
    <div class="nk-block-between">
        <div class="nk-block-head-content">
            <h3 class="nk-block-title page-title">Broadcast</h3>
            <p>You have total 3 Broadcasts.</p>
        </div><!-- .nk-block-head-content -->
    </div><!-- .nk-block-between -->
</div><!-- .nk-block-head -->
<div class="nk-block table-compact">
    <div class="card mb-2">
        <div class="card-inner pt-2 pb-2 position-relative card-tools-toggle">
            <div class="card-title-group">
                <div class="card-tools"></div><!-- .card-tools -->
                <div class="card-tools mr-n1">
                    <ul class="btn-toolbar gx-1">
                        <li>
                            <a href="#" class="btn btn-icon search-toggle toggle-search" data-target="search"><em class="icon ni ni-search"></em></a>
                        </li><!-- li -->
                        <li class="btn-toolbar-sep"></li><!-- li -->
                        <li>
                            <div class="toggle-wrap">
                                <a href="#" class="btn btn-icon btn-trigger toggle" data-target="cardTools"><em class="icon ni ni-menu-right"></em></a>
                                <div class="toggle-content" data-content="cardTools">
                                    <ul class="btn-toolbar gx-1">
                                        <li class="toggle-close">
                                            <a href="#" class="btn btn-icon btn-trigger toggle" data-target="cardTools"><em class="icon ni ni-arrow-left"></em></a>
                                        </li><!-- li -->
                                        <li>
                                            <a href="#" class="btn btn-trigger btn-icon dropdown-toggle" title="Export">
                                                <em class="icon ni ni-download"></em>
                                            </a>
                                        </li>
                                        <li>
                                            <div class="dropdown">
                                                <a href="#" class="btn btn-trigger btn-icon dropdown-toggle" data-toggle="dropdown">
                                                    <em class="icon ni ni-setting"></em>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-xs dropdown-menu-right">
                                                    <ul class="link-check">
                                                        <li><span>Show</span></li>
                                                        <li class="active"><a href="#">10</a></li>
                                                        <li><a href="#">20</a></li>
                                                        <li><a href="#">50</a></li>
                                                    </ul>
                                                </div>
                                            </div><!-- .dropdown -->
                                        </li><!-- li -->
                                        <li class="btn-toolbar-sep mr-2"></li>
                                        <li>
                                            <a href="#" data-target="addRole" class="toggle btn btn-primary d-none d-md-inline-flex"><em class="icon ni ni-plus"></em><span>Add Broadcast</span></a>
                                        </li>
                                    </ul><!-- .btn-toolbar -->
                                </div><!-- .toggle-content -->
                            </div><!-- .toggle-wrap -->
                        </li><!-- li -->
                    </ul><!-- .btn-toolbar -->
                </div><!-- .card-tools -->
            </div><!-- .card-title-group -->
            <div class="card-search search-wrap" data-search="search">
                <div class="card-body">
                    <div class="search-content">
                        <a href="#" class="search-back btn btn-icon toggle-search" data-target="search"><em class="icon ni ni-arrow-left"></em></a>
                        <input type="text" class="form-control border-transparent form-focus-none" placeholder="Search">
                        <button class="search-submit btn btn-icon"><em class="icon ni ni-search"></em></button>
                    </div>
                </div>
            </div><!-- .card-search -->
        </div><!-- .card-inner -->
    </div>
    <div class="nk-tb-list is-separate mb-3">
        <table class="broadcast-init nowrap nk-tb-list is-separate" data-auto-responsive="false">
            <thead>
                <tr class="nk-tb-item nk-tb-head">
                    <th class="nk-tb-col nk-tb-col-check">
                        <div class="custom-control custom-control-sm custom-checkbox notext">
                            <input type="checkbox" class="custom-control-input" id="uid">
                            <label class="custom-control-label" for="uid"></label>
                        </div>
                    </th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Name</span></th>
                    <th class="nk-tb-col tb-col-md"><span class="sub-text">Slug</span></th>
                    <th class="nk-tb-col nk-tb-col-tools text-right">
                        <span class="sub-text">Action</span>
                    </th>
                </tr>
            </thead>

            <tbody>
               
            </tbody>
        </table>
    </div>
</div><!-- .nk-block -->

@endsection
@push('footerScripts')
<script type="text/javascript">
    $(function() {
        NioApp.DataTable('.broadcast-init', {
            processing: true,
            serverSide: true,
            ajax: "{{ url('user/broadcast/table') }}",
            columns: [{
                    "class": "nk-tb-col tb-col-lg nk-tb-col-check",
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    render: function(data, type, row, meta) {
                        return '<td class="nk-tb-col nk-tb-col-check"><div class="custom-control custom-control-sm custom-checkbox notext"><input type="checkbox" class="custom-control-input" id="puid1"><label class="custom-control-label" for="puid1"></label></div></td>'
                    }
                },
                {
                    "class": "nk-tb-col tb-col-lg",
                    data: 'name',
                    name: 'name'
                },
                {
                    "class": "nk-tb-col tb-col-lg",
                    data: 'slug',
                    name: 'slug'
                },
                {
                    "class": "nk-tb-col tb-col-lg text-right",
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ]
        });
    });
</script>
@endpush