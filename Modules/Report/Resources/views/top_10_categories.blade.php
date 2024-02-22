@extends('layouts.app')
@section('content')
<div class="nk-block-head nk-block-head-sm">
    <div class="nk-block-between">
        <div class="nk-block-head-content">
            <h3 class="nk-block-title page-title">Top 10 Categories</h3>
        </div><!-- .nk-block-head-content -->
        <div class="nk-block-head-content">
            <div class="toggle-wrap nk-block-tools-toggle">
                <a href="#" class="btn btn-icon btn-trigger toggle-expand mr-n1" data-target="more-options"><em class="icon ni ni-more-v"></em></a>
                <div class="toggle-expand-content" data-content="more-options">
                    <ul class="nk-block-tools g-3">
                        <li>
                            <div class="dropdown">
                                <a href="#" class="btn btn-trigger btn-icon dropdown-toggle" data-toggle="dropdown">
                                    <em class="icon ni ni-setting"></em>
                                </a>
                                <div class="dropdown-menu dropdown-menu-xs dropdown-menu-right">
                                    <ul class="link-check">
                                        <li><span>Actions</span></li>
                                        <li><a href="{{ url('report/export-top-10-categories') }}"><em class="icon ni ni-download m-r10"></em> Export</a></li>
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
<div class="nk-block table-compact">
    <div class="nk-tb-list is-separate is-medium mb-3">
        <table id="top_categories_init" class="products-init nowrap nk-tb-list is-separate" data-auto-responsive="false">
            <thead>
                <tr class="nk-tb-item nk-tb-head">
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Name</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Parent</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Products</span></th>
                    <th class="nk-tb-col tb-col-mb nk-tb-action-col text-center" nowrap="true"><span class="sub-text">Status</span></th>
                    <th class="nk-tb-col tb-col-mb nk-tb-action-col" nowrap="true"><span class="sub-text">Created at</span></th>
                    <th class="nk-tb-col tb-col-mb nk-tb-action-col" nowrap="true"><span class="sub-text">Updated at</span></th>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
        </table>
    </div><!-- .nk-tb-list -->
</div><!-- .nk-block -->

@endsection

@push('footerScripts')
<script src="{{url('js/APIDataTable.js')}}"></script>
<script src="{{url('js/dayjs.min.js?t='.time())}}"></script>
<script type="text/javascript">
    
    var token = '{{ \Session::get('token') }}';//get logged in user session.
    var organizationId = "{{ \Session::get('currentOrganization') }}";

    var dataTable = new APIDataTable({
        tableElem: '#top_categories_init',
        api: "{{ url('api/v1/report/top-categories') }}/"+organizationId,
        authToken: 'Bearer '+token,
        columns: [{
                data: "name",
            },
            {
                data: "parent",
            },
            {
                data: "totalProducts",
            },
            {
                "class": "nk-tb-col tb-col-mb text-center",
                data: "status",
                render: function(row) {
                    var activeClass =
                        row.status == "active" ?
                        "badge-success" :
                        "badge-danger";
                    return (
                        '<span class="badge ' +
                        activeClass +
                        '">' +
                        row.status +
                        "</span>"
                    );
                },
            },
            {
                data: "created_at",
                render: function(row) {
                    return dayjs(row.created_at).format(
                        "DD MMM YYYY"
                    );
                },
            },
            {
                data: "updated_at",
                render: function(row) {
                    return dayjs(row.updated_at).format(
                        "DD MMM YYYY"
                    );
                },
            }
        ]
    });
    
</script>
@endpush