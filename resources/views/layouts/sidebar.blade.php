@php
$userRole = \Session::get('role');
$userPermission = \Session::get('userPermission');
$organization_type = \Session::get('organization_type');
$tallyIntegration = \Session::get('tallyIntegration');
$ecommerceDiscount = \Session::get('ecommerceDiscount');
$planApprovalRequired = \Session::get('planApprovalRequired');
@endphp
<div class="nk-sidebar nk-sidebar-fixed is-light " data-content="sidebarMenu">
    <div class="nk-sidebar-element nk-sidebar-head">
        <div class="nk-sidebar-brand">
            <a href="{{url('/dashboard')}}" class="logo-link nk-sidebar-logo">
                <img class="logo-light logo-img" src="{{url('images/parivaar.png')}}" srcset="{{url('images/parivaar.png')}}" alt="logo">
                <img class="logo-dark logo-img" src="{{url('images/parivaar.png')}}" srcset="{{url('images/parivaar.png')}}" alt="logo-dark">
                <!-- <img class="logo-small logo-img logo-img-small" src="{{url('images/logo-small.png')}}" srcset="{{url('images/logo-small2x.png 2x')}}" alt="logo-small"> -->
            </a>
        </div>
        {{-- <div class="nk-menu-trigger mr-n2">
            <a href="#" class="nk-nav-toggle nk-quick-nav-icon d-xl-none" data-target="sidebarMenu"><em class="icon ni ni-arrow-left"></em></a>
            <a href="#" class="nk-nav-compact nk-quick-nav-icon d-none d-xl-inline-flex" data-target="sidebarMenu"><em class="icon ni ni-menu"></em></a>
        </div> --}}
    </div><!-- .nk-sidebar-element -->
    <div class="nk-sidebar-element">
        <div class="nk-sidebar-content">
            <div class="nk-sidebar-menu" data-simplebar>
                <ul class="nk-menu">
                    <li class="nk-menu-item">
                        <a href="{{url('/dashboard')}}" class="nk-menu-link">
                            <span class="nk-menu-icon"><em class="icon ni ni-growth-fill"></em></span>
                            <span class="nk-menu-text">User Dashboard</span>
                        </a>
                    </li>

                    <li class="nk-menu-item">
                        <a href="{{url('/role')}}" class="nk-menu-link">
                            <span class="nk-menu-icon"><em class="icon ni ni-user"></em></span>
                            <span class="nk-menu-text">User Roles</span>
                        </a>
                    </li><!-- .nk-menu-item -->

                    <li class="nk-menu-item has-sub">
                        <a href="#" class="nk-menu-link nk-menu-toggle">
                            <span class="nk-menu-icon"><em class="icon ni ni-users"></em></span>
                            <span class="nk-menu-text">Human Resource</span>
                        </a>
                        <ul class="nk-menu-sub">
                            <li class="nk-menu-item has-sub">
                                <a href="#" class="nk-menu-link nk-menu-toggle">
                                    <span class="nk-menu-icon"><em class="icon ni ni-user-list"></em></span>
                                    <span class="nk-menu-text">Employees</span>
                                </a>
                                <ul class="nk-menu-sub">
                                    <li class="nk-menu-item">
                                        <a href="{{ url('/human-resource/employees/super-admin') }}" class="nk-menu-link">
                                            <span class="nk-menu-icon"><em class="icon ni ni-users"></em></span>
                                            <span class="nk-menu-text">Super Admin</span></a>
                                    </li>
                                    <li class="nk-menu-item">
                                        <a href="{{ url('/human-resource/employees/sub-admin') }}" class="nk-menu-link">
                                            <span class="nk-menu-icon"><em class="icon ni ni-users"></em></span>
                                            <span class="nk-menu-text">Sub Admin</span></a>
                                    </li>
                                    <li class="nk-menu-item">
                                        <a href="{{ url('/human-resource/employees/district-anchor') }}" class="nk-menu-link">
                                            <span class="nk-menu-icon"><em class="icon ni ni-users"></em></span>
                                            <span class="nk-menu-text">District Anchors</span>
                                        </a>
                                    </li>
                                    <li class="nk-menu-item">
                                        <a href="{{ url('/human-resource/employees/driver') }}" class="nk-menu-link">
                                            <span class="nk-menu-icon"><em class="icon ni ni-users"></em></span>
                                            <span class="nk-menu-text">Drivers</span>
                                        </a>
                                    </li>
                                    <li class="nk-menu-item">
                                        <a href="{{ url('/human-resource/employees/attendant') }}" class="nk-menu-link">
                                            <span class="nk-menu-icon"><em class="icon ni ni-users"></em></span>
                                            <span class="nk-menu-text">Attendants</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li class="nk-menu-item has-sub">
                                <a href="{{url('human-resource/attendance')}}" class="nk-menu-link">
                                    <span class="nk-menu-icon"><em class="icon ni ni-calender-date"></em></span>
                                    <span class="nk-menu-text">Attendance</span>
                                </a>
                            </li>
                            <li class="nk-menu-item has-sub">
                                <a href="{{url('human-resource/leaves')}}" class="nk-menu-link">
                                    <span class="nk-menu-icon"><em class="icon ni ni-bookmark"></em></span>
                                    <span class="nk-menu-text">Leaves</span>
                                </a>
                            </li>
                            <li class="nk-menu-item has-sub">
                                <a href="{{url('human-resource/resignations')}}" class="nk-menu-link">
                                    <span class="nk-menu-icon"><em class="icon ni ni-briefcase"></em></span>
                                    <span class="nk-menu-text">Resignations</span>
                                </a>
                            </li>
                            <li class="nk-menu-item has-sub">
                                <a href="{{url('human-resource/expenses')}}" class="nk-menu-link">
                                    <span class="nk-menu-icon"><em class="icon ni ni-money"></em></span>
                                    <span class="nk-menu-text">Expenses</span>
                                </a>
                            </li>
                        </ul>
                    </li><!-- .nk-menu-item -->

                    <li class="nk-menu-item has-sub">
                        <a href="#" class="nk-menu-link nk-menu-toggle">
                            <span class="nk-menu-icon"><em class="icon ni ni-globe"></em></span>
                            <span class="nk-menu-text">Masters</span>
                        </a>
                        <ul class="nk-menu-sub">
                            <li class="nk-menu-item">
                                <a href="{{url('master/state')}}" class="nk-menu-link">
                                    <span class="nk-menu-icon"><em class="icon ni ni-location"></em></span>
                                    <span class="nk-menu-text">States</span></a>
                            </li>
                            <li class="nk-menu-item">
                                <a href="{{url('/master/district')}}" class="nk-menu-link">
                                    <span class="nk-menu-icon"><em class="icon ni ni-map-pin"></em></span>
                                    <span class="nk-menu-text">Districts</span></a>
                            </li>
                            <li class="nk-menu-item">
                                <a href="{{url('master/holidays')}}" class="nk-menu-link">
                                    <span class="nk-menu-icon"><em class="icon ni ni-alarm"></em></span>
                                    <span class="nk-menu-text">Holidays</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nk-menu-item">
                        <a href="{{url('/task/')}}" class="nk-menu-link">
                            <span class="nk-menu-icon"><em class="icon ni ni-clipboad-check"></em></span>
                            <span class="nk-menu-text">Tasks</span>
                        </a>
                    </li><!-- .nk-menu-item -->

                    <!-- <li class="nk-menu-item">
                        <a href="{{url('/attendance')}}" class="nk-menu-link">
                            <span class="nk-menu-icon"><em class="icon ni ni-calendar"></em></span>
                            <span class="nk-menu-text">Attendance</span>
                        </a>
                    </li> -->
                    <!-- .nk-menu-item -->

                    <!-- <li class="nk-menu-item">
                        <a href="#" class="nk-menu-link">
                            <span class="nk-menu-icon"><em class="icon ni ni-call"></em></span>
                            <span class="nk-menu-text">Call Center</span>
                        </a>
                    </li>-->

                    <li class="nk-menu-item">
                        <a href="{{url('/ambulance/')}}" class="nk-menu-link">
                            <span class="nk-menu-icon"><em class="icon ni ni-truck"></em></span>
                            <span class="nk-menu-text">Ambulance</span>
                        </a>
                    </li>

                    <!-- <li class="nk-menu-item">
                        <a href="#" class="nk-menu-link">
                            <span class="nk-menu-icon"><em class="icon ni ni-ticket"></em></span>
                            <span class="nk-menu-text">Tickets</span>
                        </a>
                    </li>

                    <li class="nk-menu-item">
                        <a href="#" class="nk-menu-link">
                            <span class="nk-menu-icon"><em class="icon ni ni-setting"></em></span>
                            <span class="nk-menu-text">Settings</span>
                        </a>
                    </li> -->

                    <li class="nk-menu-item">
                        <a href="{{url('call-center/')}}" class="nk-menu-link">
                            <span class="nk-menu-icon"><em class="icon ni ni-call"></em></span>
                            <span class="nk-menu-text">Call Center</span>
                        </a>
                    </li>

                    <li class="nk-menu-item">
                        <a href="{{url('documents/')}}" class="nk-menu-link">
                            <span class="nk-menu-icon"><em class="icon ni ni-folder"></em></span>
                            <span class="nk-menu-text">Documents</span>
                        </a>
                    </li>

                    <!-- <li class="nk-menu-item">
                        <a href="{{url('administration/contactus')}}" class="nk-menu-link">
                            <span class="nk-menu-icon"><em class="icon ni ni-call"></em></span>
                            <span class="nk-menu-text">Contact Us</span>
                        </a>
                    </li> -->
                </ul>
            </div><!-- .nk-sidebar-menu -->
        </div><!-- .nk-sidebar-content -->
    </div><!-- .nk-sidebar-element -->
</div>