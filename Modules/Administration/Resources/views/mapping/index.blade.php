@extends('layouts.app')

@section('content')
	<div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">Buyer & SP Mapping</h3>
                {{-- <p>You have total 4 sales persons mapped.</p> --}}
            </div><!-- .nk-block-head-content -->
            <div class="nk-block-head-content">
                <div class="toggle-wrap nk-block-tools-toggle">
                    <a href="#" class="btn btn-icon btn-trigger toggle-expand mr-n1" data-target="more-options"><em class="icon ni ni-more-v"></em></a>
                    <div class="toggle-expand-content" data-content="more-options">
                        <ul class="nk-block-tools g-3">
                            <!-- <li>
                                <div class="dropdown">
                                    <a href="#" class="btn btn-trigger btn-icon dropdown-toggle" data-toggle="dropdown">
                                        <em class="icon ni ni-setting"></em>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-xs dropdown-menu-right">
                                        <ul class="link-check">
                                            <li><span>Actions</span></li>
                                            <li><a href="#"><em class="icon ni ni-download m-r10"></em> Export</a></li>
                                            <li><a href="#"><em class="icon ni ni-upload m-r10"></em> Import</a></li>
                                            </ul>
                                    </div>
                                </div>
                            </li> -->
                        </ul>
                    </div>
                </div>
            </div>
        </div><!-- .nk-block-between -->
    </div><!-- .nk-block-head -->
    <div class="nk-block table-compact">
        <div class="nk-tb-list is-separate mb-3">
            <table class="broadcast-init nowrap nk-tb-list is-separate" data-auto-responsive="false">
                <thead>
                    <tr class="nk-tb-item nk-tb-head">
                        <th class="nk-tb-col tb-col-mb"><span class="sub-text">Sales Person Name</span></th>
                        <th class="nk-tb-col tb-col-mb"><span class="sub-text">Email</span></th>
                        <th class="nk-tb-col tb-col-md"><span class="sub-text">Buyers Mapped</span></th>
                        <th class="nk-tb-col nk-tb-col-tools nk-tb-action-col text-right" nowrap="true">
                            <span class="sub-text">Action</span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>

        </div><!-- .nk-block -->
    </div><!-- .nk-block -->
@endsection
@push('footerScripts')
<script type="text/javascript">
    $(function() {
        NioApp.DataTable('.broadcast-init', {
            processing: true,
            serverSide: true,
            ajax: "{{ url('administration/mapping') }}",
            columns: [
                {
                    "class": "nk-tb-col tb-col-lg",
                    data: 'name',
                    name: 'name'
                },
                {
                    "class": "nk-tb-col tb-col-lg",
                    data: 'email',
                    name: 'email'
                },
                {
                    "class": "nk-tb-col tb-col-lg",
                    data: 'retailers',
                    name: 'retailers'
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