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
    <title>Forgot Password | Parivaar</title>
    <!-- StyleSheets  -->
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
                                    <div class="nk-block-head">
                                        <div class="nk-block-head-content">
                                            <h5 class="nk-block-title">Reset password</h5>
                                            {{-- <div class="nk-block-des">
                                                    <p>If you forgot your password, well, then we’ll email you instructions to reset your password.</p>
                                                </div> --}}
                                        </div>
                                    </div><!-- .nk-block-head -->
                                    @if (session()->has('message'))
                                    <div class="alert alert-success alert-icon alert-dismissible">
                                        <em class="icon ni ni-check-circle"></em>
                                        {{ session('message') }}<button class="close" data-dismiss="alert"></button>
                                    </div>
                                    @endif
                                    @if (session()->has('email_error'))
                                    <div class="alert alert-success alert-icon alert-dismissible">
                                        <em class="icon ni ni-cross-circle"></em>
                                        {{ session('email_error') }}<button class="close" data-dismiss="alert"></button>
                                    </div>
                                    @endif
                                    <form method="POST" enctype="multipart/form-data" id="resetPasswordForm">
                                        @csrf
                                        <div class="form-group">
                                            <div class="form-label-group">
                                                <label class="form-label" for="default-01">New Password</label>
                                                {{-- <a class="link link-primary link-sm" href="#">Need Help?</a> --}}
                                            </div>
                                            <input id="new_password" type="password" class="form-control form-control-lg @error('new_password') is-invalid @enderror" name="new_password" value="{{ old('new_password') }}" required autofocus id="default-01" placeholder="Enter new password" minlength="8" maxlength="16">

                                            @error('new_password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <div class="form-label-group">
                                                <label class="form-label" for="default-01">Confirm Password</label>
                                                {{-- <a class="link link-primary link-sm" href="#">Need Help?</a> --}}
                                            </div>
                                            <input id="confirm_password" type="password" class="form-control form-control-lg @error('confirm_password') is-invalid @enderror" name="confirm_password" value="{{ old('confirm_password') }}" required autofocus id="default-01" placeholder="Confirm new password" minlength="8" maxlength="16">

                                            @error('confirm_password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>

                                        <input type="hidden" name="token" value="{{ $token }}">

                                        <div class="form-group">
                                            <input type="submit" class="btn btn-lg btn-primary btn-block" value="Reset Password" id="resetPassword">
                                        </div>
                                    </form><!-- form -->
                                    <div class="form-note-s2 pt-5">
                                        <a href="{{ url('login') }}"><strong>Return to login</strong></a>
                                    </div>
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
    <script src="{{url('js/jquery-3.5.1.min.js')}}"></script>
    <script src="{{url('js/bundle.js')}}"></script>
    <script src="{{url('js/parsley.min.js')}}"></script>
    <script src="{{url('js/scripts.js')}}"></script>
    <script src="{{url('js/toastr.min.js')}}"></script>
    <script>
        $(document).ready(function() {
            $('#resetPassword').on('click', function(e) {
                var form = $('#resetPasswordForm')[0];
                var data = new FormData(form);
                e.preventDefault();
                $(".overlay").show();
                $.ajax({
                    type: "POST",
                    enctype: 'multipart/form-data',
                    url: "{{ url('password/reset-password') }}",
                    data: data,
                    processData: false,
                    contentType: false,
                    cache: false,
                    timeout: 800000, // in milliseconds
                    success: function(response) {
                        $(".overlay").hide();
                        if (response.status == 'success') {
                            form.reset();
                            window.location.href = "{{ url('/login') }}";
                            toastr.success(response.message);
                        } else {
                            toastr.error(response.message);
                        }
                    }
                });
            });
        });
    </script>

    <!-- <script src="./assets/js/charts/chart-ecommerce.js?ver=1.9.0"></script> -->
</body>

</html>