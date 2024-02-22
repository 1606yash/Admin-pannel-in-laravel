<!DOCTYPE html>
<html lang="zxx" class="js">

<head>
    <base href="../../../">
    <meta charset="utf-8">
    <meta name="author" content="Softnio">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="A powerful and conceptual apps base dashboard template that especially build for developers and programmers.">
    <!-- Fav Icon  -->
    <link rel="shortcut icon" href="./images/favicon.png">
    <!-- Page Title  -->
    <title>Login | Parivaar</title>
    <!-- StyleSheets  -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{url('css/dashlite.css')}}">
    <link id="skin-default" rel="stylesheet" href="{{url('css/theme.css')}}">
    <link href="{{url('css/toastr.min.css')}}" rel="stylesheet" type="text/css" />
</head>

<body class="nk-body bg-login npc-default pg-auth">
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
    </style>
    <div class="nk-app-root">
        <!-- main @s -->
        <div class="nk-main ">
            <!-- wrap @s -->
            <div class="nk-wrap nk-wrap-nosidebar">
                <!-- content @s -->
                <div class="nk-content ">
                    <div class="login-wrap">
                        <div class="login-box">
                            <div class="login-box-row">
                                <div class="login-box-col-left">
                                    <div class="login-form">
                                        <div class="brand-logo">
                                            <a href="javascript:window.location.reload(true)" class="logo-link">
                                                <img class="logo-light logo-img logo-img-lg" src="{{url('images/parivaar.png')}}" srcset="{{url('images/parivaar.png')}}" alt="logo">
                                                <img class="logo-dark logo-img logo-img-lg" src="{{url('images/parivaar.png')}}" srcset="{{url('images/parivaar.png')}}" alt="logo-dark">
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="login-box-col-right">
                                    <div class="login-form">
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
                                        <form method="POST" enctype="multipart/form-data" id="LoginForm">
                                            @csrf
                                            <div class="form-group">
                                                <div class="form-label-group">
                                                    <label class="form-label" for="default-01">{{ __('Email Address') }} <span class="text-danger">*</span></label>
                                                </div>

                                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" autocomplete="email" autofocus data-parsley-excluded="true" placeholder="Enter email">
                                                @error('email')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                                @enderror
                                            </div><!-- .foem-group -->
                                            <div class="form-group">
                                                <div class="form-label-group">
                                                    <label class="form-label" for="password">{{ __('Password') }} <span class="text-danger">*</span></label>

                                                </div>
                                                <div class="form-control-wrap">
                                                    <a tabindex="-1" href="#" class="form-icon form-icon-right passcode-switch" data-target="password">
                                                        <em class="passcode-icon icon-show icon ni ni-eye-off"></em>
                                                        <em class="passcode-icon icon-hide icon ni ni-eye"></em>
                                                    </a>
                                                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" autocomplete="current-password" data-parsley-excluded="true" placeholder="Enter password">
                                                    @error('password')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                    @enderror
                                                </div>
                                                <div class="form-label-group">
                                                    <label class="form-label" for="password"><span class="text-danger"></span></label>
                                                    @if (Route::has('password.request'))
                                                    <a class="btn-link" href="{{ route('password.request') }}">
                                                        {{ __('Forgot Your Password?') }}
                                                    </a>
                                                    @endif
                                                </div>

                                            </div><!-- .foem-group -->
                                            <div class="form-group">
                                                <input type="submit" class="btn btn-lg btn-orange btn-block" value="{{ __('Login') }}" id="login">
                                            </div>
                                        </form><!-- form -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- wrap @e -->
            </div>
            <!-- content @e -->
        </div>
        <!-- main @e -->
    </div>
    <script src="{{url('js/jquery-3.5.1.min.js')}}"></script>
    <script src="{{url('js/bundle.js')}}"></script>
    <script src="{{url('js/parsley.min.js')}}"></script>
    <script src="{{url('js/scripts.js')}}"></script>
    <script src="{{url('js/toastr.min.js')}}"></script>
    <script>
        $(document).ready(function() {
            if ($("#LoginForm").length > 0) {
                $("#LoginForm").validate({
                    rules: {
                        email: {
                            required: true,
                            maxlength: 255,
                            email: true
                        },
                        password: {
                            required: true,
                            maxlength: 255,
                        },
                    },
                    messages: {
                        email: {
                            required: "Please enter email.",
                            maxlength: "Your email maxlength should not be more than 255 characters long."
                        },
                        password: {
                            required: "Please enter password.",
                            maxlength: "Your password maxlength should not be more than 255 characters long."
                        }
                    },
                    submitHandler: function(form) {
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });

                        // disabled submit button during process running status
                        $("#login").attr("disabled", true);
                        $(".overlay").show();
                        $.ajax({
                            type: "POST",
                            url: "{{ url('post-login') }}",
                            data: $('#LoginForm').serialize(),
                            success: function(response) {
                                $("#login").attr("disabled", false);
                                $(".overlay").hide();
                                if (response.status == 'success') {

                                    sessionStorage.setItem('toastrNotification', JSON.stringify({
                                        type: 'success',
                                        message: response.message
                                    }));

                                    window.location.href = "{{ url('dashboard') }}";
                                    // toastr.success(response.message);
                                } else {
                                    toastr.error(response.message);
                                }
                            }
                        });
                    }
                });
            }
        });
    </script>
</body>

</html>