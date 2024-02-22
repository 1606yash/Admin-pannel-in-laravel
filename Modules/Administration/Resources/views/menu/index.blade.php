@php
	$organization_type = \Session::get('organization_type');
@endphp
@extends('layouts.app')

@section('content')
	<div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">Menu</h3>
            </div><!-- .nk-block-head-content -->
            <div class="nk-block-head-content">
                <div class="toggle-wrap nk-block-tools-toggle">
                    <a href="#" class="btn btn-icon btn-trigger toggle-expand mr-n1" data-target="more-options"><em class="icon ni ni-more-v"></em></a>
                    <div class="toggle-expand-content" data-content="more-options">
                        <ul class="nk-block-tools g-3">
                        	<li class="nk-block-tools-opt w-200">
                                <select size="sm" class="form-select form-control form-control-lg" data-placeholder="Select Type" name="filter_menu_type" id="filter_menu_type">
		                            <option @if($menu_type == 'Side-Menu')selected @endif value="Side-Menu">Side-Menu</option>
		                            <option @if($menu_type == 'Desktop')selected @endif value="Desktop">Desktop</option>
									<option @if($menu_type == 'Mobile-Footer')selected @endif value="Mobile-Footer">Mobile-Footer</option>
		                        </select>
                            </li>
                            <li class="nk-block-tools-opt">
                                <a href="#" data-target="addMenu" class="toggle btn btn-primary d-none d-md-inline-flex addMenu"><em class="icon ni ni-plus"></em><span>Add Menu</span></a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div><!-- .nk-block-between -->
    </div><!-- .nk-block-head -->

	<div class="card">
		<div class="card-inner pt-2 pl-2 pb-0">
			<ul class="nav nav-tabs mt-n3 bdr-btm-none">
				<li class="nav-item">
					<a class="nav-link {{ ((isset($_GET['role']) && $_GET['role'] == 'buyer') || !isset($_GET['role'])) ? 'active' : '' }}" href="?role=buyer"><em class="icon ni ni-user-list-fill"></em> <span>Buyer</span></a>
				</li>
				<li class="nav-item">
					<a class="nav-link {{ (isset($_GET['role']) && $_GET['role'] == 'sales_person') ? 'active' : '' }}" href="?role=sales_person"><em class="icon ni ni-user-list-fill"></em><span>Sales Person</span></a>
				</li>
				<li class="nav-item">
					<a class="nav-link {{ (isset($_GET['role']) && $_GET['role'] == 'seller') ? 'active' : '' }}" href="?role=seller"><em class="icon ni ni-user-list-fill"></em><span>Seller</span></a>
				</li>
				@if($organization_type == 'MULTIPLE')
				<li class="nav-item">
					<a class="nav-link {{ (isset($_GET['role']) && $_GET['role'] == 'owner') ? 'active' : '' }}" href="?role=owner"><em class="icon ni ni-user-list-fill"></em><span>Owner</span></a>
				</li>
				@endif
			</ul>
		</div>
	</div>
	<div class="nk-block table-compact nk-block-lg pt-28">
		<div class="tab-content">
			<div class="tab-pane active" id="tabBuyer">
				<div class="cf nestable-lists">
					<div class="dd" id="nestable">
						<ol class="dd-list">
							@forelse ($allMenus as $menus)
								<li class="dd-item" data-type="{{ $menus[0]['menu_type'] }}" data-id="{{ $menus[0]['id'] }}">
									<div class="dd-handle dd-handle-move-icon"><em class="icon ni ni-move"></em></div>
									<div class="dd-content">
										<div class="icon-box">
											@php
												if(!is_null($menus[0]['icon'])){
					                                $file = public_path('uploads/menu_icons/') . $menus[0]['icon'];
					                            }

					                            if(!is_null($menus[0]['icon']) && file_exists($file))
					                                $icon = "<img class='mr-2' height='20' width='20' src=".url('uploads/menu_icons/'.$menus[0]['icon']).">";
					                            else
					                                $icon = '<em class="icon ni ni-home-fill"></em>';
											@endphp	
											{!! $icon !!}
										</div>
										{{ $menus[0]['name'] }}
										<div class="ml-auto d-flex align-center">
											{{-- <x-inputs.switch for="menuStatus" size="md" label="" name="menuStatus" value='1' checked /> --}}
											@php
	                                        $menuData = json_encode($menus[0]);
	                                        @endphp
											<span class="mr-2 badge badge-{{ ($menus[0]['status'] == 'active' ? 'success' : 'danger') }}">{{ $menus[0]['status'] }}</span>
											<a href="#" data-target="addMenu"  onclick="editSettings({{ $menuData }});" data-id='{{ $menus[0]['id'] }}' class="toggle btn btn-trigger btn-icon" data-toggle="tooltip" data-placement="top" title="Edit">
												<em class="icon ni ni-edit"></em>
											</a>

											@php
												$selected_role = (isset($_GET['role'])) ? $_GET['role'] : 'buyer';
											@endphp

											<a onclick='return confirm("Are you sure, you want to delete it?")' href="{{ url('administration/menu/delete-menu/'.$menus[0]['id'].'?role='.$selected_role) }}" class="btn btn-icon" data-toggle="tooltip" data-placement="top" title="Delete">
												<em class="icon ni ni-trash-fill"></em>
											</a>
										</div>
									</div>
									@if(isset($menus['children']))
										<ol class="dd-list">
										@forelse ($menus['children'] as $child)
										<li class="dd-item" data-type="{{ $child['menu_type'] }}" data-id="{{ $child['id'] }}">
											<div class="dd-handle dd-handle-move-icon"><em class="icon ni ni-move"></em></div>
											<div class="dd-content">
												<div class="icon-box">
													@php
														if(!is_null($child['icon'])){
							                                $file = public_path('uploads/menu_icons/') . $child['icon'];
							                            }

							                            if(!is_null($child['icon']) && file_exists($file))
							                                $icon = "<img class='mr-2' height='20' width='20' src=".url('uploads/menu_icons/'.$child['icon']).">";
							                            else
							                                $icon = '<em class="icon ni ni-home-fill"></em>';
													@endphp	
													{!! $icon !!} 
												</div>
												{{ $child['name'] }}
												<div class="ml-auto d-flex align-center">
													{{-- <x-inputs.switch for="menuStatus" size="md" label="" name="menuStatus" value='1' checked /> --}}
													@php
			                                        $menuData = json_encode($child);
			                                        @endphp
													<span class="mr-2 badge badge-{{ ($child['status'] == 'active' ? 'success' : 'danger') }}">{{ $child['status'] }}</span>
													<a href="#" data-target="addMenu" onclick="editSettings({{ $menuData }});" data-id='{{ $child['id'] }}' class="toggle btn btn-trigger btn-icon" data-toggle="tooltip" data-placement="top" title="Edit">
														<em class="icon ni ni-edit"></em>
													</a>
													@php
														$selected_role = (isset($_GET['role'])) ? $_GET['role'] : 'buyer';
													@endphp
													<a onclick='return confirm("Are you sure, you want to delete it?")' href="{{ url('administration/menu/delete-menu/'.$child['id'].'?role='.$selected_role) }}" class="btn btn-icon" data-toggle="tooltip" data-placement="top" title="Delete">
														<em class="icon ni ni-trash-fill"></em>
													</a>
												</div>
											</div>
										</li>
										@empty
											{{-- empty expr --}}
										@endforelse
										</ol>
									@endif
								</li>
							@empty
								{{-- empty expr --}}
							@endforelse
						</ol>
					</div>
				</div>
				<div class="row">					
					<div class="col-12 pt-4 text-right">
						<a data-target='addManuf' class="btn btn-outline-light cancel">Cancel</a>
						<button class="btn btn-primary buyerSubmit" name="submit"><span>Save</span></button>
					</div>
				</div>
			</div>
		</div>
	</div>


	<div id="modal" class="nk-add-product toggle-slide toggle-slide-right" data-content="addMenu" data-toggle-screen="any" data-toggle-overlay="true" data-toggle-body="true" data-simplebar>
		<div class="nk-block-head">
			<div class="nk-block-head-content">
				<h5 class="nk-block-title modalTitle drawerTitle">Add Menu</h5>
				<div class="nk-block-des">
					<p>Add menu for the user.</p>
				</div>
			</div>
		</div><!-- .nk-block-head -->
		<div class="nk-block">
			<form role="form" method="post" id="addMenuForm" action="{{ url('administration/menu/add') }}" enctype="multipart/form-data">
				@csrf
				<input type="hidden" value="{{ (isset($_GET['role'])) ? $_GET['role'] : 'buyer' }}" name="selected_role">
				<div class="row g-3">
					<div class="col-12">
                        <label class="form-label" for="menu_type">Menu Type</span></label>
                        <select size="sm" class="form-select form-control form-control-lg" data-placeholder="Select Type" name="menu_type" id="menu_type">
                            <option value="Side-Menu">Side-Menu</option>
                            <option value="Desktop">Desktop</option>
							<option value="Mobile-Footer">Mobile-Footer</option>
                        </select>
                    </div>
					<div class="col-12">
						<x-inputs.text  for="menu_name" label="Name" required="true" icon="sign-dash" placeholder="Name" name="name" minlength="3" autocomplete="off"/>
					</div>
					<div class="col-12">
                        <div class="form-group">
                            <label class="form-label" for="menuStatus">Status</label>
                            <div class="form-control-wrap">
                                <x-inputs.switch for="menuStatus" size="md" label="" name="status" value='1' checked />
                            </div>
                        </div>
                    </div>
					<div class="col-12">
						<x-inputs.text  for="menu_url" label="URL" required="true" icon="link" placeholder="URL" name="menu_url" minlength="3" autocomplete="off"/>
					</div>
					{{-- <div class="col-12">
						<x-inputs.text  for="menu_active_url" label="Active URL pattern" required="true" placeholder="Active URL pattern" name="menu_active_url" formNote="e.g. users* (then this menu item will be active in users/create url)" minlength="3" autocomplete="off"/>
					</div> --}}
                    {{-- <div class="col-12">
                        <div class="form-group">
                            <label class="form-label" for="tags">Icon</label>
                            <div class="form-control-wrap">
                                <div class="custom-file">
                                    <input name="file" type="file" class="custom-file-input" id="customFile">
                                    <label class="custom-file-label" for="customFile">Choose file</label>
                                </div>
                            </div>
                            <div id="image_file">
                                
                            </div>
                        </div>
                    </div> --}}

                    <div class="col-12">
                        <label class="form-label" for="menu_icon">Icon</span></label>
                        <select size="sm" class="form-select form-control form-control-lg" data-placeholder="Select Icon" name="menu_icon" id="menu_icon">
                            @forelse ($allicons as $icon)
                            	
		                            {{-- @php
									if(!is_null($icon)){
		                                $file = public_path('uploads/menu_icons/') . $icon;
		                            }
		                            if(!is_null($icon) && file_exists($file)){
		                                $iconImg = "<img class='mr-2' height='20' width='20' src=".url('uploads/menu_icons/'.$icon).">";
		                                $path = url('uploads/menu_icons/'.$icon);
		                            }
		                            	
		                            @endphp
		                            @if(!is_null($icon) && file_exists($file))
		                            @endif --}}
                            		<option value="{{ $icon }}">{{ $icon }}</option>
                            @empty
                            	{{-- empty expr --}}
                            @endforelse
                        </select>
                    </div>

					<div class="col-12">
                        <label class="form-label" for="menu_target">Target</span></label>
                        <select size="sm" class="form-select form-control form-control-lg" data-placeholder="Select Target" name="menu_target" id="menu_target">
                            <option value="_self">Self</option>
							<option value="_blank">New Window</option>
                        </select>
                    </div>
					<div class="col-12">
                        <label class="form-label" for="menu_roles">Roles<span class="text-danger">*</span></span></label>
						<x-inputs.select size="sm" name="menu_roles[]" id="menu_roles" for="menu_roles" data-placeholder="Select Roles" multiple="true" required='true'>
                            
							@foreach($roles as $key  => $role)
                                <option value="{{ $role->id }}">{{ $role->label }}</option>
                            @endforeach
							</x-inputs.select>
                    </div>
					{{-- <div class="col-12">
						<x-inputs.textarea for="Description" size="sm" label="Description" name="description"/>
					</div> --}}
					<div class="col-12 text-right">
						<a href="javascript:resetValues();" class="btn btn-outline-light">Cancel</a>
						<button class="btn btn-primary submitBtn" name="submit"><span>Submit</span></button>
					</div>
					
					<input type="hidden" id="menu_id" name="menu_id" value="0">
				</div>
			</form>
		</div><!-- .nk-block -->
	</div>	
@endsection

@push('footerScripts')
<script src="{{url('js/jquery.nestable.js')}}"></script>
<script type="text/javascript">

function editSettings(rowData) {
    
    $('.drawerTitle').text('Edit Menu');
    $('#menu_id').val(rowData.id);
    $('#menu_type').val(rowData.menu_type).trigger('change');
    $('#menu_name').val(rowData.name);
    $('#menuStatus').attr('checked', rowData.status == "active" ? true : false);

    if(rowData.icon){
    	var root_url = "<?php echo Request::root(); ?>";
    	var icon = root_url + '/uploads/menu_icons/'+rowData.icon;
    	$('#image_file').html('<img height="50" width="50" src="'+icon+'">');
    	$('#image_file').show();
    }

    $('#menu_url').val(rowData.url);
    $('#menu_target').val(rowData.target).trigger('change');   	
    $('#menu_roles').val(rowData.role_id).trigger('change');
    if(rowData.is_default){
    	$('#menu_url').attr('readonly',true);
    	$('#menu_type option:not(:selected)').attr('disabled', true);
    	$('#menu_roles option:not(:selected)').attr('disabled', true);
    	$('#menu_target option:not(:selected)').attr('disabled', true);
    }else{
    	$('#menu_url').attr('readonly',false);
    	$('#menu_type option:not(:selected)').attr('disabled', false);
    	$('#menu_roles option:not(:selected)').attr('disabled', false);
    	$('#menu_target option:not(:selected)').attr('disabled', false);
    }
}

$(document).ready(function()
{

	function formatState (icons) {
	  if (!icons.id) {
	    return icons.text;
	  }

	  var baseUrl = "<?php public_path('uploads/menu_icons/') ?>";

	  var root_url = "<?php echo Request::root(); ?>";
      var iconPath = root_url + '/uploads/menu_icons/'+icons.element.value;
	  console.log(iconPath);

	  var $icons = $(
	    '<span class="select2-selection__rendered"><img height="20" width="20" src="' +iconPath+'" class="img-flag" /> ' + icons.element.value + '</span>'
	  );
	  return $icons;
	};

	$("#menu_icon").select2({
	  templateResult: formatState
	});

	$('#filter_menu_type').change(function(){
        var menu_type = $(this).val();
        var root_url = "<?php echo Request::root(); ?>";
        var goto = root_url + '/administration/menu/'+menu_type;
        window.location = goto;
        return;
    });

	$('.buyerSubmit').on('click', function() {
        var nest = $('#nestable').nestable('serialize');
        console.log(nest);
        var root_url = "<?php echo Request::root(); ?>";
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: root_url + '/administration/menu/update-order',
            data: {'menus':nest},
            //dataType: "html",
            method: "POST",
            cache: false,
            success: function (data) {
                Swal.fire(
                          'Good job!',
                          data.msg,
                          'success'
                        )
                setTimeout(function(){
                    location.reload();
                }, 3000);
            }
        });
	});

    var updateOutput = function(e)
    {
        var list   = e.length ? e : $(e.target),
            output = list.data('output');
        if (window.JSON) {
            var menu = output.val(window.JSON.stringify(list.nestable('serialize')));//, null, 2));
        } else {
            output.val('JSON browser support required for this demo.');
        }

        console.log(output,'output');
        console.log(menu,'menu');

    };

    // activate Nestable for list 1
    $('#nestable').nestable({
        group: 1
    })
    .on('change', updateOutput);

    // activate Nestable for list 2
    $('#nestable2').nestable({
        group: 1
    })
    .on('change', updateOutput);

    // output initial serialised data
    updateOutput($('#nestable').data('output', $('#nestable-output')));
    updateOutput($('#nestable2').data('output', $('#nestable-output')));
	updateOutput($('#nestable3').data('output', $('#nestable-output')));



    $('#nestable-menu').on('click', function(e)
    {
        var target = $(e.target),
            action = target.data('action');
        if (action === 'expand-all') {
            $('.dd').nestable('expandAll');
        }
        if (action === 'collapse-all') {
            $('.dd').nestable('collapseAll');
        }
    });

    $('#nestable3').nestable();

});
</script>
<script type="text/javascript">
function resetValues(){
        $('.drawerTitle').text('Add Menu');
	    $('#menu_id').val(0);
	    $('#menu_type').val('').trigger('change');
	    $('#menu_name').val('');
	    $('#menu_url').val('');
	    $('#menu_url').attr('readonly',false);
    	$('#menu_type option:not(:selected)').attr('disabled', false);
    	$('#menu_roles option:not(:selected)').attr('disabled', false);
    	$('#menu_target option:not(:selected)').attr('disabled', false);
	    $('#image_file').hide();
	    $('#menu_target').val('_self').trigger('change');   	
	    $('#menu_type').val('Side-Menu').trigger('change');   	
	    $('#menu_roles').val('').trigger('change');
        NioApp.Toggle.collapseDrawer('addMenu');
        $('#addMenuForm')[0].reset();
        $('#addMenuForm').parsley().reset();
}
$('.addMenu').click(function(){
    $('.modalTitle').text('Add Menu');
});

 var root_url = "<?php echo Request::root(); ?>";
    $('.editRole').click(function(){
        
        var id = $(this).attr('data-id');
        console.log(id);
        $('.modalTitle').text('Edit Menu');
        
        $.ajax({
            url: root_url + '/administration/menus/get-role',
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
                    // NioApp.Toggle.collapseDrawer('addMenu');
                }else{
                    Swal.fire('Details not found!')
                }
            }
        });
    });
</script>
@endpush