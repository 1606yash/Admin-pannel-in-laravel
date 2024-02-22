@extends('layouts.app')
@section('content')

<div class="toggle-bar">
    <ul class="nav nav-tabs nk-block-tools">
        <li class="nav-item">
            <a class="nav-link " href="{{ url('/human-resource/district-anchor') }}">District Anchor</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ url('/human-resource/cluster-coordinator') }}">Cluster Coordinator</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ url('/human-resource/teacher') }}">Teacher</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ url('/human-resource/driver') }}">Driver</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ url('/human-resource/attendant') }}">Attendant</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ url('/human-resource/add-user') }}"
                style="position: relative;letter-spacing: 0.02em;display: inline-flex;align-items: center;color: #fff;height: 0px;
                width: 100px;background-color: #1849ba;border-color: #1849ba;"
                class="btn btn-primary d-none d-md-inline-flex "><em class="icon ni ni-plus" style="color: #fff;"></em><span>Add User</span></a>
        </li>
    </ul>
</div>
    <h1>Teacher</h1>

@endsection