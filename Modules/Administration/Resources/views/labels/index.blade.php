@extends('layouts.app')
@section('content')
<div class="nk-block-head nk-block-head-sm">
    <div class="nk-block-between">
        <div class="nk-block-head-content">
            <h3 class="nk-block-title page-title">Labels</h3>
        </div><!-- .nk-block-head-content -->

    </div><!-- .nk-block-between -->
</div><!-- .nk-block-head -->

<div class="card">
    <div class="card-inner pt-2 pl-2 pb-0">
        <ul class="nav nav-tabs mt-n3 bdr-btm-none">
            <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#tabLabels"><em class="icon ni ni-setting"></em> <span>Labels</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#tabMessages"><em class="icon ni ni-setting"></em> <span>Messages</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#tabNotifications"><em class="icon ni ni-setting"></em> <span>Notifications</span></a>
            </li>
        </ul>
    </div>
</div>

<br><br>

<form method="post" action="">
    @csrf
	<div class="row mb-3">
		<div class="col-md-4">
			<input class="form-control" type="text" id="search" placeholder="Search..">
		</div>
		<div class="col-md-8 text-right">
			<button type="submit" class="btn btn-primary">Update</button>
		</div>
	</div>
	<div class="nk-block">
		<div class="tab-content">
			<div class="tab-pane active" id="tabLabels">
				<div class="card1">
					<div class="card-inner1 p-0">
						<table class="nowrap-th nk-tb-list is-separate dataTable no-footer" id="labelTable">
							<thead>
								<tr class="nk-tb-item nk-tb-head">
									<th class="nk-tb-col tb-col-mb word-wrap tb-col-lg"><span class="sub-text">Label</span></th>
									<th class="nk-tb-col tb-col-mb word-wrap tb-col-lg"><span class="sub-text">English</span></th>
									<th class="nk-tb-col tb-col-mb word-wrap tb-col-lg"><span class="sub-text">Hindi</span></th>
									<th class="nk-tb-col tb-col-mb word-wrap tb-col-lg"><span class="sub-text">Kannada</span></th>
									<th class="nk-tb-col tb-col-mb word-wrap tb-col-lg"><span class="sub-text">Tamil</span></th>
									<th class="nk-tb-col tb-col-mb word-wrap tb-col-lg"><span class="sub-text">Telugu</span></th>
									<th class="nk-tb-col tb-col-mb word-wrap tb-col-lg text-center" width="1%" nowrap="true"><span class="sub-text">Action</span></th>
								</tr>
							</thead>
							<tbody>
								@forelse ($langData['labels'] as $key => $lang)
									<tr class="nk-tb-item odd">
										<td class=" nk-tb-col tb-col-lg word-wrap">{{ ucfirst($key) }}</td>
										<td class=" nk-tb-col tb-col-lg word-wrap" data-langen="{{ $key }}">
											<input type="text" class="form-control lang-input input-{{$key}}" name="labels[{{ $key }}][en]"  value="{{ $lang['en'] }}">
											<span class="lang-text text-{{$key}}">{{ $lang['en'] }}</span>
										</td>
										<td class=" nk-tb-col tb-col-lg word-wrap" data-langhi="{{ $key }}">
											<input type="text" class="form-control lang-input input-{{$key}}" name="labels[{{ $key }}][hi]" value="{{ $lang['hi'] }}">
											<span class="lang-text text-{{$key}}">{{ $lang['hi'] }}</span>
										</td>
										<td class=" nk-tb-col tb-col-lg word-wrap" data-langhi="{{ $key }}">
											<input type="text" class="form-control lang-input input-{{$key}}" name="labels[{{ $key }}][kannada]" value="{{ $lang['kannada'] }}">
											<span class="lang-text text-{{$key}}">{{ $lang['kannada'] }}</span>
										</td>

										<td class=" nk-tb-col tb-col-lg word-wrap" data-langhi="{{ $key }}">
											<input type="text" class="form-control lang-input input-{{$key}}" name="labels[{{ $key }}][tamil]" value="{{ $lang['tamil'] }}">
											<span class="lang-text text-{{$key}}">{{ $lang['tamil'] }}</span>
										</td>


										<td class=" nk-tb-col tb-col-lg word-wrap" data-langhi="{{ $key }}">
											<input type="text" class="form-control lang-input input-{{$key}}" name="labels[{{ $key }}][telugu]" value="{{ $lang['telugu'] }}">
											<span class="lang-text text-{{$key}}">{{ $lang['telugu'] }}</span>
										</td>

										<td class=" nk-tb-col tb-col-lg word-wrap text-center">
											<a href="javascript:void(0)" data-target-div="input-{{$key}}" data-target-text="text-{{$key}}" class="editLang" data-lang="{{ $key }}">	<em class='icon ni ni-edit text-{{$key}}'></em> <em class='icon ni ni-cross input-{{$key}}'></em></a>
										</td>
									</tr>
								@empty
									{{-- empty expr --}}
								@endforelse
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="tab-pane" id="tabMessages">
				<div class="card1">
					<div class="card-inner1 p-0">
						<table class="nowrap-th nk-tb-list is-separate dataTable no-footer" id="labelTable">
							<thead>
								<tr class="nk-tb-item nk-tb-head">
									<th class="nk-tb-col tb-col-mb word-wrap tb-col-lg"><span class="sub-text">Label</span></th>
									<th class="nk-tb-col tb-col-mb word-wrap tb-col-lg"><span class="sub-text">English</span></th>
									<th class="nk-tb-col tb-col-mb word-wrap tb-col-lg"><span class="sub-text">Hindi</span></th>
									<th class="nk-tb-col tb-col-mb word-wrap tb-col-lg"><span class="sub-text">Kannada</span></th>
									<th class="nk-tb-col tb-col-mb word-wrap tb-col-lg"><span class="sub-text">Tamil</span></th>
									<th class="nk-tb-col tb-col-mb word-wrap tb-col-lg"><span class="sub-text">Telugu</span></th>
									<th class="nk-tb-col tb-col-mb word-wrap tb-col-lg text-center" width="1%" nowrap="true"><span class="sub-text">Action</span></th>
								</tr>
							</thead>
							<tbody>
								@forelse ($langData['validationMessages'] as $key => $lang)
									<tr class="nk-tb-item odd">
										<td class=" nk-tb-col tb-col-lg word-wrap">{{ ucfirst($key) }}</td>
										<td class=" nk-tb-col tb-col-lg word-wrap" data-langen="{{ $key }}">
											<input type="text" class="form-control lang-input input-{{$key}}" name="validationMessages[{{ $key }}][en]"  value="{{ $lang['en'] }}">
											<span class="lang-text text-{{$key}}">{{ $lang['en'] }}</span>
										</td>
										<td class=" nk-tb-col tb-col-lg word-wrap" data-langhi="{{ $key }}">
											<input type="text" class="form-control lang-input input-{{$key}}" name="validationMessages[{{ $key }}][hi]" value="{{ $lang['hi'] }}">
											<span class="lang-text text-{{$key}}">{{ $lang['hi'] }}</span>
										</td>

										<td class=" nk-tb-col tb-col-lg word-wrap" data-langhi="{{ $key }}">
											<input type="text" class="form-control lang-input input-{{$key}}" name="validationMessages[{{ $key }}][kannada]" value="{{ $lang['kannada'] }}">
											<span class="lang-text text-{{$key}}">{{ $lang['kannada'] }}</span>
										</td>

										<td class=" nk-tb-col tb-col-lg word-wrap" data-langhi="{{ $key }}">
											<input type="text" class="form-control lang-input input-{{$key}}" name="validationMessages[{{ $key }}][tamil]" value="{{ $lang['tamil'] }}">
											<span class="lang-text text-{{$key}}">{{ $lang['tamil'] }}</span>
										</td>

										<td class=" nk-tb-col tb-col-lg word-wrap" data-langhi="{{ $key }}">
											<input type="text" class="form-control lang-input input-{{$key}}" name="validationMessages[{{ $key }}][telugu]" value="{{ $lang['telugu'] }}">
											<span class="lang-text text-{{$key}}">{{ $lang['telugu'] }}</span>
										</td>



										<td class=" nk-tb-col tb-col-lg word-wrap text-center">
											<a href="javascript:void(0)" data-target-div="input-{{$key}}" data-target-text="text-{{$key}}" class="editLang" data-lang="{{ $key }}">	<em class='icon ni ni-edit text-{{$key}}'></em> <em class='icon ni ni-cross input-{{$key}}'></em></a>
										</td>
									</tr>
								@empty
									{{-- empty expr --}}
								@endforelse
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="tab-pane" id="tabNotifications">
				<div class="card1">
					<div class="card-inner1 p-0">
						<table class="nowrap-th nk-tb-list is-separate dataTable no-footer" id="labelTable">
							<thead>
								<tr class="nk-tb-item nk-tb-head">
									<th class="nk-tb-col tb-col-mb word-wrap tb-col-lg"><span class="sub-text">Label</span></th>
									<th class="nk-tb-col tb-col-mb word-wrap tb-col-lg"><span class="sub-text">English</span></th>
									<th class="nk-tb-col tb-col-mb word-wrap tb-col-lg"><span class="sub-text">Hindi</span></th>
									<th class="nk-tb-col tb-col-mb word-wrap tb-col-lg"><span class="sub-text">Kannada</span></th>
									<th class="nk-tb-col tb-col-mb word-wrap tb-col-lg"><span class="sub-text">Tamil</span></th>
									<th class="nk-tb-col tb-col-mb word-wrap tb-col-lg"><span class="sub-text">Telugu</span></th>
									<th class="nk-tb-col tb-col-mb word-wrap tb-col-lg text-center" width="1%" nowrap="true"><span class="sub-text">Action</span></th>
								</tr>
							</thead>
							<tbody>
								@forelse ($langData['notificationMessages'] as $key => $lang)
									<tr class="nk-tb-item odd">
										<td class=" nk-tb-col tb-col-lg word-wrap">{{ ucfirst($key) }}</td>
										<td class=" nk-tb-col tb-col-lg word-wrap" data-langen="{{ $key }}">
											<input type="text" class="form-control lang-input input-{{$key}}" name="notificationMessages[{{ $key }}][en]"  value="{{ $lang['en'] }}">
											<span class="lang-text text-{{$key}}">{{ $lang['en'] }}</span>
										</td>
										<td class=" nk-tb-col tb-col-lg word-wrap" data-langhi="{{ $key }}">
											<input type="text" class="form-control lang-input input-{{$key}}" name="notificationMessages[{{ $key }}][hi]" value="{{ $lang['hi'] }}">
											<span class="lang-text text-{{$key}}">{{ $lang['hi'] }}</span>
										</td>

										<td class=" nk-tb-col tb-col-lg word-wrap" data-langhi="{{ $key }}">
											<input type="text" class="form-control lang-input input-{{$key}}" name="notificationMessages[{{ $key }}][kannada]" value="{{ $lang['kannada'] }}">
											<span class="lang-text text-{{$key}}">{{ $lang['kannada'] }}</span>
										</td>

										<td class=" nk-tb-col tb-col-lg word-wrap" data-langhi="{{ $key }}">
											<input type="text" class="form-control lang-input input-{{$key}}" name="notificationMessages[{{ $key }}][tamil]" value="{{ $lang['tamil'] }}">
											<span class="lang-text text-{{$key}}">{{ $lang['tamil'] }}</span>
										</td>

										<td class=" nk-tb-col tb-col-lg word-wrap" data-langhi="{{ $key }}">
											<input type="text" class="form-control lang-input input-{{$key}}" name="notificationMessages[{{ $key }}][telugu]" value="{{ $lang['telugu'] }}">
											<span class="lang-text text-{{$key}}">{{ $lang['telugu'] }}</span>
										</td>


										<td class=" nk-tb-col tb-col-lg word-wrap text-center">
											<a href="javascript:void(0)" data-target-div="input-{{$key}}" data-target-text="text-{{$key}}" class="editLang" data-lang="{{ $key }}">	<em class='icon ni ni-edit text-{{$key}}'></em> <em class='icon ni ni-cross input-{{$key}}'></em></a>
										</td>
									</tr>
								@empty
									{{-- empty expr --}}
								@endforelse
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="nk-block">
		<div class="row">
			<div class="col-md-12 text-right">
				<button type="submit" class="btn btn-primary">Update</button>
			</div>
		</div>
	</div>
</form>
@endsection
@push('footerScripts')
<script>
	var $rows = $('#labelTable tbody tr');
    $('#search').keyup(function() {
	    var val = $.trim($(this).val()).replace(/ +/g, ' ').toLowerCase();
	    
	    $rows.show().filter(function() {
	        var text = $(this).text().replace(/\s+/g, ' ').toLowerCase();
	        return !~text.indexOf(val);
	    }).hide();
	});
	$('.lang-input, .ni-cross').hide();
	$('.editLang').click(function(){
		$('.lang-input, .ni-cross').hide(200);
		$('.lang-text, .ni-edit').show(200);
		$('.'+$(this).data('target-div')).show();
		$('.'+$(this).data('target-text')).hide();
	})
</script>
@endpush
