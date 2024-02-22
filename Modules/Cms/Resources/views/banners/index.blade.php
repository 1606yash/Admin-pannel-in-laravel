@extends('layouts.app')
@section('content')
@php
    $userPermission = \Session::get('userPermission');
@endphp
	<div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">Bannner</h3>
                <p>You have total {{ $bannersCount }} banners.</p>
            </div><!-- .nk-block-head-content -->
            <div class="nk-block-head-content">
                <div class="toggle-wrap nk-block-tools-toggle">
                    <a href="#" class="btn btn-icon btn-trigger toggle-expand mr-n1" data-target="more-options"><em class="icon ni ni-more-v"></em></a>
                    <div class="toggle-expand-content" data-content="more-options">
                        <ul class="nk-block-tools g-3">
                            @if(isset($userPermission['banners']) && ($userPermission['banners']['edit_all'] || $userPermission['banners']['edit_own']))
                            <li class="nk-block-tools-opt">
                                <a href="#" data-target="addRole" class="toggle btn btn-icon btn-primary d-md-none"><em class="icon ni ni-plus"></em></a>
                                <a href="{{url('cms/banners/create')}}" class="btn btn-primary d-none d-md-inline-flex"><em class="icon ni ni-plus"></em><span>Add Banner</span></a>
                            </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="nk-block table-compact">
        <div class="nk-tb-list is-separate mb-3">
            <table class="broadcast-init nowrap nk-tb-list is-separate" data-auto-responsive="false">
                <thead>
                    <tr class="nk-tb-item nk-tb-head">
                        <th class="nk-tb-col tb-col-mb"><span class="sub-text">Image / Title</span></th>
                        <th class="nk-tb-col tb-col-md nk-tb-action-col text-center"><span class="sub-text">Status</span></th>
                        <th class="nk-tb-col nk-tb-col-tools nk-tb-action-col text-right" nowrap="true">
                            <span class="sub-text">Action</span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div><!-- .nk-block -->   
<script type="text/javascript">
    $('.eg-swal-av3').on("click", function (e) {
        Swal.fire({
          title: 'Are you sure?',
          text: "You won't be able to revert this!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Yes, delete it!'
        }).then(function (result) {
          if (result.value) {
            Swal.fire('Deleted!', 'Your file has been deleted.', 'success');
          }
        });
        e.preventDefault();
      });
</script>    
@endsection
@push('footerScripts')
<script type="text/javascript">

    $(function() {
        var user_table = ""; 
        user_table = NioApp.DataTable('.broadcast-init', {
            processing: true,
            serverSide: true,
            ajax: {
                type:"GET",
                url: "{{ url('cms/banners') }}",
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
                    "class": "nk-tb-col tb-col-lg text-center",
                    data: 'status',
                    name: 'status'
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
