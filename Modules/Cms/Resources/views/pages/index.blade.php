@extends('layouts.app')
@section('content')
@php
    $userPermission = \Session::get('userPermission');
@endphp
	<div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">Page</h3>
                <p>You have total {{ $pagesCount }} pages.</p>
            </div><!-- .nk-block-head-content -->
            <div class="nk-block-head-content">
                <div class="toggle-wrap nk-block-tools-toggle">
                    <a href="#" class="btn btn-icon btn-trigger toggle-expand mr-n1" data-target="more-options"><em class="icon ni ni-more-v"></em></a>
                    <div class="toggle-expand-content" data-content="more-options">
                        <ul class="nk-block-tools g-3">
                            <li class="nk-block-tools-opt">
                                <a href="#" class="btn btn-trigger btn-icon dropdown-toggle" data-toggle="modal" title="filter" data-target="#modalFilterPages">
                                    <div class="dot dot-primary"></div>
                                    <em class="icon ni ni-filter-alt"></em>
                                </a>
                            </li>
                            @if(isset($userPermission['pages']) && ($userPermission['pages']['edit_all'] || $userPermission['pages']['edit_own']))
                            <li class="nk-block-tools-opt">
                                <a href="#" data-target="addRole" class="toggle btn btn-icon btn-primary d-md-none"><em class="icon ni ni-plus"></em></a>
                                <a href="{{url('cms/pages/create')}}" class=" btn btn-primary d-none d-md-inline-flex"><em class="icon ni ni-plus"></em><span>Add Page</span></a>
                            </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--  Filter Tag List -->
    <div id="filter_tag_list" class="filter-tag-list"></div>
    
    <div class="nk-block table-compact">
        <div class="nk-tb-list is-separate mb-3">

            <table class="broadcast-init nowrap nk-tb-list is-separate" data-auto-responsive="false">
                <thead>
                    <tr class="nk-tb-item nk-tb-head">
                        <th class="nk-tb-col tb-col-mb"><span class="sub-text">Title</span></th>
                        <th class="nk-tb-col tb-col-mb"><span class="sub-text">Slug</span></th>
                        <th class="nk-tb-col tb-col-mb"><span class="sub-text">Visiblity</span></th>
                        <th class="nk-tb-col tb-col-md nk-tb-action-col text-center" nowrap="true"><span class="sub-text">Status</span></th>
                        <th class="nk-tb-col tb-col-md nk-tb-action-col"><span class="sub-text">Updated At</span></th>
                        <th class="nk-tb-col nk-tb-col-tools nk-tb-action-col text-right" nowrap="true">
                            <span class="sub-text">Action</span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div><!-- .nk-tb-list -->
    </div><!-- .nk-block -->   
<div class="modal fade zoom" tabindex="-1" id="modalFilterPages">
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
                                <x-inputs.verticalFormLabel label="Title" for="title" required="false" suggestion="Specify the title of the page." />
                            </div>
                            <div class="col-lg-7">
                                <x-inputs.text value="" for="title" icon="list" required="false" placeholder="title" name="title"/>
                            </div>
                        </div>
                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <x-inputs.verticalFormLabel label="Slug" for="slug" required="false" suggestion="Specify the slug of the page." required="false" />
                            </div>
                            <div class="col-lg-7">
                                <x-inputs.text value="" for="slug" icon="label" required="false" placeholder="Slug" name="slug"/>
                            </div>
                        </div>
                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <x-inputs.verticalFormLabel label="Created At" for="created" suggestion="Select the date range of the pages." required="false" />
                            </div>
                            <div class="col-lg-7">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <x-inputs.datePicker for="number25" size="sm" placeholder="From Date" name="number25" icon="calendar"/>
                                    </div>
                                    <div class="col-lg-6">
                                        <x-inputs.datePicker for="number25" size="sm" placeholder="To Date" name="number25" icon="calendar"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <x-inputs.verticalFormLabel label="Published" for="created" suggestion="Select the published of the pages." required="false" />
                            </div>
                            <div class="col-lg-7">
                                <x-inputs.checkbox for="number1" size="md" placeholder="" name="name"/>
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
                            <button class="btn btn-primary submitBtn" type="submit">Submit</button>
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
        var root_url = "<?php echo Request::root(); ?>";
        var user_table = ""; 
        user_table = NioApp.DataTable('.broadcast-init', {
            processing: true,
            serverSide: true,
            ajax: {
                type:"GET",
                url: "{{ url('cms/pages') }}",
                data: function (d) {
                }
            },
            "fnDrawCallback":function(){
                NioApp.BS.tooltip('[data-toggle="tooltip"]'); 
            },
            columns: [
                {
                    "class": "nk-tb-col tb-col-lg",
                    data: 'title',
                    name: 'title'
                },
                {
                    "class": "nk-tb-col tb-col-lg",
                    data: 'slug',
                    name: 'slug'
                },
                {
                    "class": "nk-tb-col tb-col-lg",
                    data: 'visiblity',
                    name: 'visiblity'
                },
                {
                    "class": "nk-tb-col tb-col-lg text-center",
                    data: 'status',
                    name: 'status'
                },
                {
                    "class": "nk-tb-col tb-col-lg",
                    data: 'updated_at',
                    name: 'updated_at'
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
        $('.broadcast-init').on('click', '.eg-swal-av3', function(e) {
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
                        url: root_url + '/cms/pages/delete/'+id,
                        //dataType: "html",
                        method: "GET",
                        cache: false,
                        success: function(data) {
                            console.log(data);
                                Swal.fire('Deleted!', 'Your item has been deleted.', 'success');
                                user_table['.broadcast-init'].ajax.reload();
                        }
                    });

                }
            });
            e.preventDefault();
        });
        NioApp.resetModalForm('#modalFilterPages', user_table['.broadcast-init'], '.resetFilter');
    });
</script>
@endpush
