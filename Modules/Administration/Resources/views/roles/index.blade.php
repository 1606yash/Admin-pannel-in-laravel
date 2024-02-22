@extends('layouts.app')

@section('content')
@php
    $userPermission = \Session::get('userPermission');
@endphp
	<div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title"></h3>
                @php 
                    if($roles->total() > 1) 
                        $count = 'roles';
                    else
                        $count = 'role';                       
                @endphp
                {{-- <p>You have total {{ $roles->total() }} {{ $count }}.</p> --}}
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
                            @if(isset($userPermission['roles']) && ($userPermission['roles']['edit_all'] || $userPermission['roles']['edit_own']))
                            <li class="nk-block-tools-opt">
                               <!--  <a href="{{url('/administration/create-staff')}}" class="btn btn-icon btn-primary d-md-none"><em class="icon ni ni-plus"></em></a> -->
                                <a href="#" data-target="addRole" class="toggle btn btn-primary d-none d-md-inline-flex addRole"><em class="icon ni ni-plus"></em><span>Add Role</span></a>
                            </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div><!-- .nk-block-between -->
    </div><!-- .nk-block-head -->
    <div class="nk-block table-compact">
        
        <div class="nk-block-head-content">
            <h3 class="nk-block-title page-title">Default Roles</h3>
            @php 
                if(count($defaultRoles) > 1) 
                    $count = 'default roles';
                else
                    $count = 'default role';                       
            @endphp
            <p>You have total {{ count($defaultRoles) }} {{ $count }}.</p>
        </div><!-- .nk-block-head-content -->
        <div class="nk-tb-list is-separate mb-3">
            @if($defaultRoles->count() > 0)
            <div class="nk-tb-item nk-tb-head">
                <div class="nk-tb-col fw-bold"><span class="sub-text">Role Name</span></div>
                <div class="nk-tb-col fw-bold"><span class="sub-text">Label</span></div>
                <div class="nk-tb-col fw-bold tb-col-md"><span class="sub-text">User Count</span></div>
            </div><!-- .nk-tb-item -->
            @endif
            @forelse($defaultRoles as $key => $drole)
            <div class="nk-tb-item">
                
                <div class="nk-tb-col tb-col-mb">
                    <span>{{ $drole->name }}</span>
                </div>
                <div class="nk-tb-col tb-col-mb">
                    <span>{{ $drole->label }}</span>
                </div>
                <div class="nk-tb-col tb-col-lg">
                    <span>{{ $drole->count }}</span>
                </div>
            </div><!-- .nk-tb-item -->
            @empty
            <div class="nk-content ">
                <div class="nk-block nk-block-middle wide-md mx-auto">
                    <div class="nk-block-content nk-error-ld text-center">
                        <img class="nk-error-gfx pb-4" src="{{url('images/slides/nodata.png')}}" alt="">
                        <div class="wide-xs mx-auto">
                            <h6 class="nk-error-title">No Records Found</h6>
                            
                        </div>
                    </div>
                </div><!-- .nk-block -->
            </div>
            @endforelse
        </div><!-- .nk-tb-list -->

        <hr>

        <div class="nk-tb-list is-separate mb-3">

            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">User Roles</h3>
                @php 
                    if($roles->total() > 1) 
                        $count = 'roles';
                    else
                        $count = 'role';                       
                @endphp
                <p>You have total {{ $roles->total() }} {{ $count }}.</p>
            </div><!-- .nk-block-head-content -->

            @if($roles->count() > 0)
            <div class="nk-tb-item nk-tb-head">
                <div class="nk-tb-col fw-bold"><span class="sub-text">Role Name</span></div>
                <div class="nk-tb-col fw-bold"><span class="sub-text">Label</span></div>
                <div class="nk-tb-col fw-bold tb-col-md"><span class="sub-text">User Count</span></div>
                <div class="nk-tb-col fw-bold tb-col-md w-1 nowrap"><span class="sub-text">Created At</span></div>
                <div class="nk-tb-col fw-bold tb-col-md w-1 nowrap"><span class="sub-text">Updated At</span></div>
                <div class="nk-tb-col nk-tb-col-tools nk-tb-action-col w-1 nowrap">
                    <span class="sub-text">Action</span>
                </div>
            </div><!-- .nk-tb-item -->
            @endif
            @forelse($roles as $key => $role)
            <div class="nk-tb-item">
                
                <div class="nk-tb-col tb-col-mb">
                    <span>{{ $role->name }}</span>
                </div>
                <div class="nk-tb-col tb-col-mb">
                    <span>{{ $role->label }}</span>
                </div>
                <div class="nk-tb-col tb-col-lg">
                    <span>{{ $role->count }}</span>
                </div>
                <div class="nk-tb-col tb-col-lg w-1 nowrap">
                    <span>{{  date(\Config::get('constants.DATE.DATE_FORMAT') , strtotime($role->created_at)) }}</span>
                </div>
                <div class="nk-tb-col tb-col-lg w-1 nowrap">
                    <span>{{  date(\Config::get('constants.DATE.DATE_FORMAT') , strtotime($role->updated_at)) }}</span>
                </div>
                <div class="nk-tb-col nk-tb-col-tools w-1 nowrap">
                    <ul class='nk-tb-actions gx-1'><li><div class='drodown mr-n1'><a href='#' class='dropdown-toggle btn btn-icon btn-trigger' data-toggle='dropdown'><em class='icon ni ni-more-h'></em></a><div class='dropdown-menu dropdown-menu-right'><ul class='link-list-opt no-bdr'>
                        @if(isset($userPermission['roles']) && ($userPermission['roles']['edit_all'] || $userPermission['roles']['edit_own']))
                            @if($role->is_default != 1)
                            <li>
                                <a href="javascript:void(0);" data-target="addRole" class="toggle editRole" data-id="{{ $role->id }}" >
                                    <em class="icon ni ni-edit-alt"></em> <span>Edit</span>
                                </a>
                            </li>
                            @endif
                        @endif
                        @if(isset($userPermission['roles']) && ($userPermission['roles']['delete_all'] || $userPermission['roles']['delete_own']))
                            @if($role->is_default != 1)
                            <li>
                                <a href="{{ url('administration/roles/delete-role/'.$role->id) }}" onclick="return confirm('Are you sure, you want to delete it?')">
                                    <em class="icon ni ni-trash"></em> <span>Delete</span>
                                </a>
                            </li>
                            @endif
                        @endif
                    </ul></div></div></li></ul>
                </div>
            </div><!-- .nk-tb-item -->
            @empty
            <div class="nk-content ">
                <div class="nk-block nk-block-middle wide-md mx-auto">
                    <div class="nk-block-content nk-error-ld text-center">
                        <img class="nk-error-gfx pb-4" src="{{url('images/slides/nodata.png')}}" alt="">
                        <div class="wide-xs mx-auto">
                            <h6 class="nk-error-title">No Records Found</h6>
                            
                        </div>
                    </div>
                </div><!-- .nk-block -->
            </div>
            @endforelse
        </div><!-- .nk-tb-list -->
        @if ($roles->lastPage() > 1)
            <div class="card">
                <div class="card-inner pt-2 pb-2">
                    <div class="nk-block-between-md g-3">
                        <div class="g">
                            <ul class="pagination justify-content-center justify-content-md-start">
                                <li class="page-item {{ ($roles->currentPage() == 1) ? ' disabled' : '' }}">
                                    <a class="page-link" href="{{ $roles->url($roles->currentPage()-1) }}">Previous</a>
                                </li>
                                {{-- @for ($i = 1; $i <= $roles->lastPage(); $i++)
                                    <li class="page-item {{ ($roles->currentPage() == $i) ? ' active' : '' }}">
                                        <a class="page-link" href="{{ $roles->url($i) }}">{{ $i }}</a>
                                    </li>
                                @endfor --}}
                                <li class="page-item {{ ($roles->currentPage() == $roles->lastPage()) ? ' disabled' : '' }}">
                                    <a class="page-link" href="{{ $roles->url($roles->currentPage()+1) }}" >Next</a>
                                </li>
                            </ul>
                        </div>
                        <div class="g">
                            <div class="pagination-goto d-flex justify-content-center justify-content-md-start gx-3">
                                <div>Page</div>
                                <div>
                                    <select class="form-select form-select-sm paginationGoto" data-search="on" data-dropdown="xs center" onchange="changePage(this.options[this.selectedIndex].value)">
                                        @for ($i = 1; $i <= $roles->lastPage() ; $i++)
                                            <option @if($roles->currentPage() == $i) selected @endif value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div>OF {{ $roles->lastPage() }}</div>
                            </div>
                        </div><!-- .pagination-goto -->
                    </div><!-- .nk-block-between -->
                </div><!-- .card-inner -->
            </div><!-- .card -->
        @endif
        <div id="modal" class="nk-add-product toggle-slide toggle-slide-right" data-content="addRole" data-toggle-screen="any" data-toggle-overlay="true" data-toggle-body="true" data-simplebar>
            <div class="nk-block-head">
                <div class="nk-block-head-content">
                    <h5 class="nk-block-title modalTitle">Add Role</h5>
                    <div class="nk-block-des">
                        <p>Add role for the user.</p>
                    </div>
                </div>
            </div><!-- .nk-block-head -->
            <div class="nk-block">
                <form role="form" method="post" id="addRoleForm" action="{{ url('administration/roles/add') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12">
                            <x-inputs.text  for="role_name" label="Role Name" required="true" icon="sign-dash" placeholder="Role Name" name="name" formNote="All lowercase, no-space, only '_' allowed  " minlength="3" maxlength="20" autocomplete="off"/>
                            
                        </div>
                        <div class="col-mb-12">
                            <x-inputs.text  for="Label" label="Label" icon="tag" required="true" placeholder="Label" name="label"  minlength="3" maxlength="20" autocomplete="off"/>
                        </div>
                        <!-- <div class="col-12">
                             <x-inputs.textarea for="number25" size="sm" label="Description" name="number25"/>
                        </div> -->
                        <div class="col-12 text-right">
                            <a href="javascript:resetValues();" class="btn btn-outline-light">Cancel</a>
                            <button class="btn btn-primary submitBtn" name="submit"><span>Submit</span></button>
                        </div>

                        <input type="hidden" id="itemId" name="role_id" value="0">
                    </div>
                </form>
            </div><!-- .nk-block -->
        </div>
    </div><!-- .nk-block -->
<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script> -->
<script type="text/javascript">
function resetValues(){
        $('.submitBtn').text('Submit');
        //$('.modalTitle').text('Add Role');

        $('#role_name').val('');
        $('#Label').val('');
        NioApp.Toggle.collapseDrawer('addRole');
        // $('body').removeClass('toggle-shown');
        // $('#modal').removeClass('content-active');
        $('#itemId').val(0);
        $('#addRoleForm')[0].reset();
        $('#addRoleForm').parsley().reset();
}
$('.addRole').click(function(){
    $('.modalTitle').text('Add Role');
});

 var root_url = "<?php echo Request::root(); ?>";
    $('.editRole').click(function(){
        
        var id = $(this).attr('data-id');
        console.log(id);
        $('.modalTitle').text('Edit Role');
        
        $.ajax({
            url: root_url + '/administration/roles/get-role',
            data: {'id':id},
            //dataType: "html",
            method: "GET",
            cache: false,
            success: function (data) {
                console.log(data);
                //return false;
                if(data.success){
                    $('#itemId').val(id);
                    $('#role_name').val(data.role.name);
                    $('#Label').val(data.role.label);
                    $('.submitBtn').text('Update');
                    //$('.modalTitle').text('Edit Role');
                    // NioApp.Toggle.collapseDrawer('addRole');
                }else{
                    Swal.fire('Details not found!')
                }
            }
        });
    });
</script>
@endsection
