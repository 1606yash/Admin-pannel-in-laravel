@extends('layouts.app')
<style type="text/css">
	.timeline-date{width: 200px !important;}
</style>
@section('content')
@php
    $userPermission = \Session::get('userPermission');
@endphp
<div class="nk-content-inner">
	<div class="nk-content-body">
        <h4 style="text-align: center;">Language file not found.</h4>
	</div>
</div>
<!-- Mopdal Small -->
@endsection