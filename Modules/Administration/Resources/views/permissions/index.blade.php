@extends('layouts.app')
<style type="text/css">
    .w-200 .select2-container{width: 200px !important;}
</style>
@section('content')
@php
    $userPermission = \Session::get('userPermission');
@endphp
	<div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">Permissions</h3>
            </div><!-- .nk-block-head-content -->
            <div class="nk-block-head-content">
                <div class="toggle-wrap nk-block-tools-toggle">
                    <a href="#" class="btn btn-icon btn-trigger toggle-expand mr-n1" data-target="more-options"><em class="icon ni ni-more-v"></em></a>
                    <div class="toggle-expand-content" data-content="more-options">
                        <ul class="nk-block-tools g-3">
                            <!-- <li class="nk-block-tools-opt">
                                <a href="#" id="showall" data-toggle="modal" data-target="#modalFilterUser" class="btn btn-outline-light d-none d-md-inline-flex"><em class="icon ni ni-filter-alt"></em><span>Filter</span></a>
                            </li> -->
                            @if(isset($userPermission['roles']) && ($userPermission['roles']['read_all'] || $userPermission['roles']['read_own']))
                            <li class="nk-block-tools-opt w-200">
                                <x-inputs.select  size="sm" name="role" for="role" placeholder="Select Role">
                                    <option value="" selected disabled>Select Role</option>
                                    @foreach($roles as $key  => $role)
                                        <option @if($role_id == $role->id)selected @endif value="{{ $role->id }}">{{ $role->label }}</option>
                                    @endforeach
                                </x-inputs.select>
                            </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div><!-- .nk-block-head-content -->
        </div><!-- .nk-block-between -->
    </div><!-- .nk-block-head -->

    @if($role_id == 0)
    <div class="alert alert-info alert-icon">
        <em class="icon ni ni-alert-circle"></em> Please select role to see the permissions.
    </div>
    @else
    
    <form role="form" class="mb-0" method="post" action="{{url('administration/permissions/add')}}">
        @csrf
        <input type="hidden" name="role_id" class="role_id" value="{{ $role_id }}">
        <div class="nk-block">
            <div class="card">
                <div class="card-inner">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Module</th>
                                <th>Feature</th>
                                <th class="text-center">Read Own</th>
                                <th class="text-center">Read All</th>
                                <th class="text-center">Edit Own</th>
                                <th class="text-center">Edit All</th>
                                <th class="text-center">Delete Own</th>
                                <th class="text-center">Delete All</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($features as $moduleName => $moduleFeature)
                            <tr>
                                <td colspan="8">{{ $moduleName }}</td>
                            </tr>
                                @forelse($moduleFeature as $key =>$feature)
                                @php
                                    $featureName = strtolower(str_replace(' ','_', $feature));
                                    $featureName = $featureName.'__'.$key;

                                    $read_own = $read_all = $edit_own = $edit_all = $delete_own = $delete_all = "";

                                    if(!empty($permissions)){
                                        $permissionKey = array_search($key, array_column($permissions, 'feature_id'));
                                            if($permissionKey !== false){
                                                $read_own = ($permissions[$permissionKey]['read_own'] == 1) ? "checked" : "";
                                                $read_all = ($permissions[$permissionKey]['read_all'] == 1) ? "checked" : "";
                                                $edit_own = ($permissions[$permissionKey]['edit_own'] == 1) ? "checked" : "";
                                                $edit_all = ($permissions[$permissionKey]['edit_all'] == 1) ? "checked" : "";
                                                $delete_own = ($permissions[$permissionKey]['delete_own'] == 1) ? "checked" : "";
                                                $delete_all = ($permissions[$permissionKey]['delete_all'] == 1) ? "checked" : "";
                                            }
                                        
                                    }

                                @endphp
                                <tr>
                                    <td>&nbsp;</td>
                                    <td>{{ $feature }}</td>
                                    <td class="text-center">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" name="{{ $moduleName }}[{{ $featureName }}][]" id="customSwitch_{{ $key }}" value="read_own" {{ $read_own }}>
                                            <label class="custom-control-label" for="customSwitch_{{ $key }}"></label>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" name="{{ $moduleName }}[{{ $featureName }}][]" id="customSwitch2_{{ $key }}" value="read_all" {{ $read_all }}>
                                            <label class="custom-control-label" for="customSwitch2_{{ $key }}"></label>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" name="{{ $moduleName }}[{{ $featureName }}][]" id="customSwitch3_{{ $key }}" value="edit_own" {{ $edit_own }}>
                                            <label class="custom-control-label" for="customSwitch3_{{ $key }}"></label>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" name="{{ $moduleName }}[{{ $featureName }}][]" id="customSwitch4_{{ $key }}" value="edit_all" {{ $edit_all }}>
                                            <label class="custom-control-label" for="customSwitch4_{{ $key }}"></label>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" name="{{ $moduleName }}[{{ $featureName }}][]" id="customSwitch5_{{ $key }}" value="delete_own" {{ $delete_own }}>
                                            <label class="custom-control-label" for="customSwitch5_{{ $key }}"></label>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" name="{{ $moduleName }}[{{ $featureName }}][]" id="customSwitch6_{{ $key }}" value="delete_all" {{ $delete_all }}>
                                            <label class="custom-control-label" for="customSwitch6_{{ $key }}"></label>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                @endforelse
                            @empty
                            @endforelse

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="nk-block">
            <div class="=">
                <div class="row">
                    
                    <div class="col-md-12">
                        <div class="sp-plan-info pt-0 pb-0 card-inner">
                            
                                @csrf    
                                <div class="row">
                                    <div class="col-lg-7 text-right offset-lg-5">
                                        <div class="form-group">
                                            <a href="{{ url('administration/permissions') }}" class="btn btn-outline-light">Cancel</a>
                                            @if(isset($userPermission['permissions']) && ($userPermission['permissions']['edit_all'] || $userPermission['permissions']['edit_own']))
                                            <button type="submit" class="btn btn-primary">Save</button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            
                        </div><!-- .sp-plan-info -->
                        
                    </div><!-- .col -->
                    
                </div><!-- .row -->
            </div>
            <!--  -->
        </div>
    </form>
    @endif
@endsection
@push('footerScripts')
<script type="text/javascript">
    $('#role').change(function(){
        var role_id = $(this).val();
        var root_url = "<?php echo Request::root(); ?>";
        var goto = root_url + '/administration/permissions/'+role_id;
        window.location = goto;
        return;
    });

</script>
@endpush