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
    <title>Login | Profitley</title>
    <!-- StyleSheets  -->
    <link rel="stylesheet" href="{{url('css/dashlite.css')}}">
    <link id="skin-default" rel="stylesheet" href="{{url('css/theme.css')}}">
</head>
<body class="nk-body bg-white npc-default pg-auth">
    <div class="nk-app-root">
            <!-- main @s -->
        <div class="nk-main ">
            <!-- wrap @s -->
            <div class="nk-wrap nk-wrap-nosidebar">
                <!-- content @s -->
                <div class="nk-content ">
                    <div class="nk-split nk-split-page nk-split-md">
                        <div class="nk-split-content nk-block-area nk-block-area-column nk-auth-container bg-white">
                            <div class="absolute-top-right d-lg-none p-3 p-sm-5">
                                <a href="#" class="toggle btn-white btn btn-icon btn-light" data-target="athPromo"><em class="icon ni ni-info"></em></a>
                            </div>
                            <div class="nk-block nk-block-middle nk-auth-body">
                                <div class="brand-logo pb-5">
                                    <a href="javascript:window.location.reload(true)" class="logo-link">
                                        <img class="logo-light logo-img logo-img-lg" src="{{url('images/softude.png')}}" srcset="{{url('images/softude.png')}}" alt="logo">
                                        <img class="logo-dark logo-img logo-img-lg" src="{{url('images/softude.png')}}" srcset="{{url('images/softude.png')}}" alt="logo-dark">
                                    </a>
                                </div>
                                <div class="nk-block-head">
                                    <div class="nk-block-head-content">
                                        <h5 class="nk-block-title">You are associated with {{ count($userOrganizations) }} organizations please select one.</h5>
                                    </div>
                                </div><!-- .nk-block-head -->
                                <form method="post">
                                	@csrf
	                                <select class="form-select" data-placeholder="Select Organization" data-parsley-errors-container=".proCatParsley" name="organization" id="organization">
	                                    <option value="" selected disabled>Select Organization</option>
	                                    @foreach ($userOrganizations as $key => $userOrganization)
	                                    <option 
	                                    value="{{ $userOrganization['id'] }}">{{ $userOrganization['name'] }}</option>
	                                    @endforeach
	                                </select>
                                </form>
                            </div><!-- .nk-block -->
                        </div><!-- .nk-split-content -->
                        <div class="nk-split-content nk-split-stretch bg-lighter d-flex toggle-break-lg toggle-slide toggle-slide-right" data-content="athPromo" data-toggle-screen="lg" data-toggle-overlay="true">
                            <div class="slider-wrap w-100 w-max-550px p-3 p-sm-5 m-auto">
                                <div class="slider-init" data-slick='{"dots":true, "arrows":false}'>
                                    <div class="slider-item">
                                        <div class="nk-feature nk-feature-center">
                                            <div class="nk-feature-img">
                                                <img class="round" src="{{url('images/slides/promo-a.png')}}" srcset="./images/slides/promo-a2x.png 2x" alt="">
                                            </div>
                                            <div class="nk-feature-content py-4 p-sm-5">
                                                <h4>Profitley</h4>
                                                <p>-</p>
                                            </div>
                                        </div>
                                    </div><!-- .slider-item -->
                                    <div class="slider-item">
                                        <div class="nk-feature nk-feature-center">
                                            <div class="nk-feature-img">
                                                <img class="round" src="{{url('images/slides/promo-b.png')}}" srcset="./images/slides/promo-b2x.png 2x" alt="">
                                            </div>
                                            <div class="nk-feature-content py-4 p-sm-5">
                                                <h4>Profitley</h4>
                                                <p>-</p>
                                            </div>
                                        </div>
                                    </div><!-- .slider-item -->
                                    <div class="slider-item">
                                        <div class="nk-feature nk-feature-center">
                                            <div class="nk-feature-img">
                                                <img class="round" src="{{url('images/slides/promo-c.png')}}" srcset="./images/slides/promo-c2x.png 2x" alt="">
                                            </div>
                                            <div class="nk-feature-content py-4 p-sm-5">
                                                <h4>Profitley</h4>
                                                <p>-</p>
                                            </div>
                                        </div>
                                    </div><!-- .slider-item -->
                                </div><!-- .slider-init -->
                                <div class="slider-dots"></div>
                                <div class="slider-arrows"></div>
                            </div><!-- .slider-wrap -->
                        </div><!-- .nk-split-content -->
                    </div><!-- .nk-split -->
                </div>
                <!-- wrap @e -->
            </div>
            <!-- content @e -->
        </div>
        <!-- main @e -->
    </div>
    <script src="{{url('js/bundle.js')}}"></script>
        <script src="{{url('js/parsley.min.js')}}"></script>
        <script src="{{url('js/scripts.js')}}"></script>
        <!-- <script src="./assets/js/charts/chart-ecommerce.js?ver=1.9.0"></script> -->
        <script>
            $(document).ready(function(){
                $('form').parsley();

                $('#organization').change(function(){

                });

            });
        </script>
</body>
</html>