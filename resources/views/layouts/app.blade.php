<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token For security -->
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>Parivaar</title>
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{url('css/dashlite.css?t='.time())}}">
    <link id="skin-default" rel="stylesheet" href="{{url('css/theme.css?t='.time())}}">
    <link href="{{url('css/toastr.min.css')}}" rel="stylesheet" type="text/css" />
    @php
    $loggedInUser = \Auth::user();
    $userRole = \Session::get('role');
    $userPermission = \Session::get('userPermission');
    $organization_type = \Session::get('organization_type');
    $currentOrganization = \Session::get('currentOrganization');
    $currentOrganizationName = \Session::get('currentOrganizationName');
    $profileDetails = Helpers::getProfileDetailsOnDashboard();
    $notificationCount = Helpers::getNotificationCount();
    $notificationList = Helpers::getNoticationList();
    @endphp
    <script type="text/javascript">
        var APP_BASE_URL = "{{ url('/') }}";
        var LOGIN_USER_ID = "{{ $loggedInUser->id }}";
    </script>
    <!-- <script src="{{url('js/jquery-3.5.1.min.js?t='.time())}}"></script> -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://code.jquery.com/jquery-migrate-3.3.1.min.js" integrity="sha256-APllMc0V4lf/Rb5Cz4idWUCYlBDG3b0EcN1Ushd3hpE=" crossorigin="anonymous"></script>
    <script src="{{url('js/firebase-app.js?t='.time()) }}"></script>
    <script src="{{url('js/firebase-auth.js?t='.time()) }}"></script>
    <script src="{{url('js/firebase-firestore.js?t='.time()) }}"></script>
    <script src="{{url('js/firebase-database.js?t='.time()) }}"></script>
    <script src="{{url('js/print.min.js?t='.time())}}"></script>
    <script src="{{url('js/toastr.min.js')}}"></script>
    <script src="{{url('js/jquery.validate.js')}}"></script>
    <script src="{{url('js/additional-methods.js')}}"></script>

    @stack('headerScripts')
</head>

<body class="nk-body bg-lighter npc-default has-sidebar ui-bordered">
    <div class="overlay"></div>
    <style type="text/css">
        body.loading {
            overflow: hidden;
        }

        /* Make spinner image visible when body element has the loading class */
        body.loading .overlay {
            display: block;
        }


        .overlay {
            display: none;
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: 1051;
            background: rgba(255, 255, 255, 0.8) url("{{ asset('images/loader.gif') }}") center no-repeat;
        }

        .circle {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background-color: red;
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 10px;
            margin-top: 2px;
        }

        .fc-col-header {
            width: 1300px;
        }

        .fc-view-harness {
            height: 1000px;
        }

        .fc-daygrid-body {
            width: 1300px;
        }

        .fc-scrollgrid-sync-table {
            width: 1300px;
            height: 935px;
        }
    </style>
    <div class="nk-app-root">
        <!-- main @s -->
        <div class="nk-main ">
            <!-- sidebar @s -->
            @include('layouts.sidebar')
            <!-- sidebar @e -->
            <!-- wrap @s -->
            <div class="nk-wrap ">
                <!-- main header @s -->
                <div class="nk-header nk-header-fixed is-light">
                    <div class="container-fluid">
                        <div class="nk-header-wrap">
                            <div class="nk-menu-trigger d-xl-none ml-n1">
                                <a href="#" class="nk-nav-toggle nk-quick-nav-icon" data-target="sidebarMenu"><em class="icon ni ni-menu"></em></a>
                            </div>
                            <div class="nk-header-brand d-xl-none">
                                <a href="{{url('/dashboard')}}" class="logo-link">
                                    <img class="logo-light logo-img" src="{{url('images/softude.png')}}" srcset="{{url('images/softude.png')}}" alt="logo">
                                    <img class="logo-dark logo-img" src="{{url('images/softude.png')}}" srcset="{{url('images/softude.png')}}" alt="logo-dark">
                                </a>
                            </div><!-- .nk-header-brand -->
                            <!-- <div class="nk-header-search ml-3 ml-xl-0">
                                <em class="icon ni ni-search"></em>
                                <input type="text" class="form-control border-transparent form-focus-none" placeholder="Search anything">
                            </div> -->

                            <!-- .nk-header-news -->
                            <div class="nk-header-tools">
                                <ul class="nk-quick-nav">

                                    <li class="dropdown notification-dropdown">
                                        <a href="#" class="dropdown-toggle nk-quick-nav-icon" data-toggle="dropdown">
                                            <div class="position-relative" id="ni-bell">
                                                <em class="icon ni ni-bell"></em>
                                                <span class="badge badge-primary position-absolute top-0 start-100 translate-middle" id="notificationCount">
                                                    {{ $notificationCount ?? 0 }}
                                                </span>
                                            </div>
                                        </a>

                                        <div class="dropdown-menu dropdown-menu-xl dropdown-menu-right overflow-hidden" id="noticationDropdown">
                                            <div class="dropdown-head">
                                                <span class="sub-title nk-dropdown-title">Notifications</span>
                                                @if($notificationCount > 0)
                                                <a href="#" id="mark_all_as_read">Mark All as Read</a>
                                                @endif
                                            </div>
                                            <div class="dropdown-body">
                                                <div class="nk-notification" id="nk-notification">
                                                    @if(!empty($notificationList))
                                                    @foreach($notificationList as $notification)

                                                    <!-- Example notification item (unread) -->
                                                    <div class="notification-item nk-tb-list ml-2 mt-2" @if(empty($notification->read_at)) style="color: #1818a8;" @endif>
                                                        <strong class="notification-title">{{ ucfirst($notification->notification_title) ?? ''}}</strong>
                                                        <p class="notification-description">{{ ucfirst($notification->notification_description) ?? ''}}</p>
                                                        <span class="notification-time">{{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() ?? ''}}</span>
                                                    </div>
                                                    <hr>
                                                    @endforeach
                                                    @endif
                                                </div><!-- .nk-notification -->
                                            </div><!-- .nk-dropdown-body -->
                                            <div class="dropdown-foot center">
                                                <a href="{{ url('/notification') }}">View All</a>
                                            </div>
                                        </div>
                                    </li>

                                    <li class="dropdown user-dropdown">
                                        <a href="#" class="dropdown-toggle mr-n1" data-toggle="dropdown">
                                            <div class="user-toggle">
                                                <div class="user-avatar sm">
                                                    <img src="{{ $profileDetails->profile_path }}" alt="Your Profile Image">
                                                </div>
                                                <div class="ml-2">
                                                    <span style="color: black; font-weight: bold;">{{$profileDetails->profile_name}}</span><br>
                                                    <span>({{$profileDetails->role_name}})</span>
                                                </div>
                                                <div class="user-info d-none d-xl-block">
                                                    {{-- <div class="user-status user-status-unverified">Unverified</div> --}}
                                                    <div class="user-name dropdown-indicator">{{ ucfirst(\Session::get('name')) }}</div>
                                                </div>
                                            </div>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-md dropdown-menu-right">
                                            <div class="dropdown-inner user-card-wrap bg-lighter d-none d-md-block">
                                                <div class="user-card">
                                                    <div class="user-avatar sm ">
                                                        <img src="{{ $profileDetails->profile_path }}" alt="Your Profile Image">
                                                    </div>
                                                    <div class="ml-2">
                                                        <span style="color: black; font-weight: bold;">{{$profileDetails->profile_name}}</span><br>
                                                        <span>({{$profileDetails->role_name}})</span>
                                                    </div>
                                                    <div class="user-info">
                                                        <span class="lead-text">{{ ucfirst(\Session::get('name')) }}</span>
                                                        <span class="sub-text">{{ \Session::get('email') }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="dropdown-inner">
                                                <ul class="link-list">
                                                    <li><a href="{{ url('profile') }}"><em class="icon ni ni-user-alt"></em><span>View Profile</span></a></li>
                                                    <li><a href="{{ url('profile/setting') }}"><em class="icon ni ni-setting-alt"></em><span>Account Setting</span></a></li>

                                                </ul>
                                            </div>
                                            <div class="dropdown-inner">
                                                <ul class="link-list">
                                                    <li>
                                                        <a href="{{ route('logout') }}" onclick="event.preventDefault();
                                                         document.getElementById('logout-form').submit();"><em class="icon ni ni-signout"></em><span>Sign out</span></a>
                                                        <form id="logout-form" action="{{ route('post-logout') }}" method="POST" style="display: none;">
                                                            @csrf
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div><!-- .nk-header-wrap -->
                    </div><!-- .container-fliud -->
                </div>
                <div class="nk-content ">
                    <div class="container-fluid">
                        <div class="nk-content-inner">
                            <div class="nk-content-body">
                                @if (session()->has('message'))
                                <div class="alert alert-success alert-icon alert-dismissible">
                                    <em class="icon ni ni-check-circle"></em>
                                    {{ session('message') }}<button class="close" data-dismiss="alert"></button>
                                </div>
                                @endif
                                @if (session()->has('error'))
                                <div class="alert alert-danger alert-icon alert-dismissible">
                                    <em class="icon ni ni-cross-circle"></em>
                                    {{ session('error') }}<button class="close" data-dismiss="alert"></button>
                                </div>
                                @endif
                                @if($errors->any())
                                <div class="alert alert-danger alert-icon alert-dismissible">
                                    <em class="icon ni ni-cross-circle"></em>
                                    @forelse ($errors->all() as $validation_error)
                                    <p>{{ $validation_error }}</p>
                                    @empty
                                    {{-- empty expr --}}
                                    @endforelse
                                </div>
                                @endif

                                @yield('content')

                            </div>
                        </div>
                    </div>
                </div>
                <!-- footer @s -->
                <div class="nk-footer">
                    <div class="container-fluid">
                        <div class="nk-footer-wrap">
                            <div class="nk-footer-copyright"> &copy; {{ date('Y') }} Parivaar.
                            </div>
                            <div class="nk-footer-links">
                                <ul class="nav nav-sm">
                                    <li class="nav-item"><a class="nav-link" href="{{ url('terms') }}">Terms</a></li>
                                    <li class="nav-item"><a class="nav-link" href="{{ url('privacy') }}">Privacy</a></li>
                                    <li class="nav-item"><a class="nav-link" href="{{ url('help') }}">Help</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- footer @e -->
            </div>
            <!-- wrap @e -->
        </div>
        <!-- main @e -->
    </div>
    <script src="{{url('js/bundle.js?ver=1.9.0')}}"></script>
    <script src="{{url('js/scripts.js?ver=1.9.0')}}"></script>
    {{-- <script src="{{url('js/chart-ecommerce.js')}}"></script> --}}
    <script src="{{url('js/parsley.min.js')}}"></script>
    <script src="{{url('js/app.js')}}"></script>
    <script src="{{url('js/top-notifications.js')}}"></script>
    @stack('footerScripts')
    <script src="{{url('js/common.js?t='.time())}}"></script>
    <script src="{{url('js/jquery.simplePagination.js')}}"></script>
    <script type="text/javascript">
        $(document).on({
            ajaxStart: function() {
                $("body").addClass("loading");
            },
            ajaxStop: function() {
                $("body").removeClass("loading");
            }
        });
        $(document).ready(function() {
            $('#system_organization').change(function() {
                var root_url = "<?php echo Request::root(); ?>";
                var org_id = $(this).val();
                var org_name = $("#system_organization option:selected").text();

                $.ajax({
                    url: root_url + '/user/set-organization/?org_name=' + org_name + '&org_id=' + org_id,
                    data: {},
                    method: "GET",
                    cache: false,
                    success: function(response) {
                        if (response.success) {
                            location.reload();
                        }
                    }
                });
            });


        });
        $('#mark_all_as_read').on('click', function(e) {
            $.ajax({
                url: "{{url('/notification-mark-as-read')}}",
                method: "GET",
                success: function(response) {
                    if (response.status == 'success') {
                        $('.nk-header-tools').load(window.location.href + ' .nk-header-tools');
                    } else {
                        toastr.error(response.message);
                    }
                }
            });
        });
    </script>
</body>

</html>