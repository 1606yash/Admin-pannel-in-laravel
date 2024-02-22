@extends('layouts.app')
<style type="text/css">
    .profile-ud-list {
        max-width: 100% !important;
    }

    .profile-ud-item {
        width: 33.33% !important;
        padding: 0 3.25rem;
    }
</style>
@section('content')
<div class="nk-block-head nk-block-head-sm">
    <div class="nk-block-between g-3">
        <div class="nk-block-head-content">
            <h3 class="nk-block-title page-title"><a href="javascript:history.back()" class="pt-3"><em class="icon ni ni-chevron-left back-icon"></em> </a> Staff / <strong class="text-primary small">{{ $user->FullName }}</strong></h3>
            <div class="nk-block-des text-soft">
                {{-- <ul class="list-inline">
                    <li>User ID: <span class="text-base">UD003054</span></li>
                    <li>Last Login: <span class="text-base">15 Feb, 2019 01:02 PM</span></li>
                </ul> --}}
            </div>
        </div>

    </div>
</div><!-- .nk-block-head -->
<div class="nk-block">
    <div class="card">
        <div class="card-aside-wrap">
            <div class="card-content">
                <ul class="nav nav-tabs nav-tabs-mb-icon nav-tabs-card">
                    <li class="nav-item">
                        <a class="nav-link active" href="#"><em class="icon ni ni-user-circle"></em><span>Personal</span></a>
                    </li>
                    {{-- <li class="nav-item">
                        <a class="nav-link" href="#"><em class="icon ni ni-file-text"></em><span>Documents</span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><em class="icon ni ni-bell"></em><span>Notifications</span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><em class="icon ni ni-activity"></em><span>Activities</span></a>
                    </li>
                    <li class="nav-item nav-item-trigger d-xxl-none">
                        <a href="#" class="toggle btn btn-icon btn-trigger" data-target="userAside"><em class="icon ni ni-user-list-fill"></em></a>
                    </li> --}}
                </ul><!-- .nav-tabs -->
                <div class="card-inner">
                    <div class="nk-block">
                        <div class="nk-block-head">
                            <div class="user-info-avtar">
                                <div class="user-avatar lg bg-primary">
                                    @php
                                    if(!is_null($user->file)){
                                    $file = public_path('uploads/users/') . $user->file;
                                    }
                                    @endphp

                                    @if(!is_null($user->file) && file_exists($file))
                                    <img src="{{ url('uploads/users/'.$user->file) }}">
                                    @else
                                    <span>{{ \Helpers::getAcronym($user->FullName) }}</span>
                                    @endif
                                </div>
                                <div>
                                    @if($user->status)
                                    <h5 class="title">Personal Information <span class="badge badge-success">Approved</span></h5>
                                    @else
                                    <h5 class="title">Personal Information <span class="badge badge-danger">Not Approved</span></h5>
                                    @endif
                                    <p>Basic info, like your name and address, that you use on Profitley.</p>
                                </div>
                            </div>
                        </div><!-- .nk-block-head -->

                        <div class="nk-block">
                            <div class="nk-data data-list">
                                <div class="data-head">
                                    <h6 class="overline-title">Basics</h6>
                                </div>
                                <div class="data-item" data-toggle="modal" data-target="#profile-edit">
                                    <div class="data-col">
                                        <span class="data-label">Full Name</span>
                                        <span class="data-value">{{ $user->FullName }}</span>
                                    </div>
                                </div><!-- data-item -->
                                <div class="data-item" data-toggle="modal" data-target="#profile-edit">
                                    <div class="data-col">
                                        <span class="data-label">Mobile Number</span>
                                        <span class="data-value">{{ $user->phone_number }}</span>
                                    </div>
                                </div><!-- data-item -->
                                <div class="data-item">
                                    <div class="data-col">
                                        <span class="data-label">Email Address</span>
                                        <span class="data-value">{{ $user->email }}</span>
                                    </div>
                                </div><!-- data-item -->
                                <div class="data-item" data-toggle="modal" data-target="#profile-edit">
                                    <div class="data-col">
                                        <span class="data-label">Role</span>
                                        <span class="data-value text-soft">{{ ucfirst($user->roleName) }}</span>
                                    </div>
                                </div><!-- data-item -->
                                <div class="data-item" data-toggle="modal" data-target="#profile-edit">
                                    <div class="data-col">
                                        <span class="data-label">Address</span>
                                        <span class="data-value">{{ $user->address1 }} {{ $user->address2 }}, {{ $user->city }}, {{ $user->district }}, {{ $user->state }}, India {{ $user->pincode }}</span>
                                    </div>
                                </div><!-- data-item -->
                            </div><!-- data-list -->
                        </div>
                    </div><!-- .profile-ud-list -->
                </div><!-- .nk-block -->
            </div><!-- .card-inner -->
        </div><!-- .card-content -->
    </div><!-- .card-aside-wrap -->
</div><!-- .card -->
</div><!-- .nk-block -->
@endsection