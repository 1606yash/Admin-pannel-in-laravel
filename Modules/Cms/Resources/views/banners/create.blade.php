@push('headerScripts')
    <link rel="stylesheet" href="{{url('css/editor/summernote.css?ver=1.9.0')}}">
@endpush
@extends('layouts.app')

@section('content')
	<div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title"><a href="javascript:history.back()" class="pt-3"><em class="icon ni ni-chevron-left back-icon"></em> </a> @if(isset($banner)) Edit @else Add @endif Bannner</h3>
            </div><!-- .nk-block-head-content -->
        </div>
    </div>
    <form role="form" method="post" enctype="multipart/form-data" >
        @csrf
        <div class="nk-block">
            <div class="card card-bordered sp-plan">
                <div class="row no-gutters">
                    <div class="col-md-3">
                        <div class="sp-plan-action card-inner">
                            <div class="icon">
                                <em class="icon ni ni-box fs-36px o-5"></em>
                                <h5 class="o-5">Basic <br> Information</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="sp-plan-info card-inner">
                            <div class="row g-3 align-center">
                                <div class="col-lg-5">
                                    <x-inputs.verticalFormLabel label="Upload Image" for="default-06" suggestion="Upload the image for the banner." required="true" />
                                </div>
                                <div class="col-lg-7">
                                    <div class="form-group">
                                        <div class="form-control-wrap">
                                            <div class="custom-file">
                                                <input type="file" multiple class="custom-file-input" id="customFile" name="banner">
                                                <label class="custom-file-label" for="customFile">Choose file</label>
                                                @if ($errors->has('banner'))
                                                    <span class="text-danger">{{ $errors->first('banner') }}</span>
                                                @endif
                                            </div>
                                            @if(isset($banner) && !is_null($banner->file))
                                            <div class="media_box">
                                                <img height="100" width="100" src="{{url('uploads/banners/'.$banner->file)}}">
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-3 align-center">
                                <div class="col-lg-5">
                                    <x-inputs.verticalFormLabel label="Title" for="title" required="true" suggestion="Specify the title of the banner." required="true" />
                                </div>
                                <div class="col-lg-7">
                                    <x-inputs.text value="{{ isset($banner) ? $banner->title : old('title') }}"  for="title" icon="user" required="true" placeholder="title" name="title"/>
                                    @if ($errors->has('title'))
                                        <span class="text-danger">{{ $errors->first('title') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="row g-3 align-center">
                                <div class="col-lg-5">
                                    <x-inputs.verticalFormLabel label="Link" for="link" suggestion="Specify the link of the banner." />
                                </div>
                                <div class="col-lg-7">
                                    <x-inputs.text value="{{ isset($banner) ? $banner->link : old('link') }}"  for="link" icon="link" placeholder="link" name="link"/>
                                    @if ($errors->has('link'))
                                        <span class="text-danger">{{ $errors->first('link') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="row g-3 align-center">
                                <div class="col-lg-5">
                                    <x-inputs.verticalFormLabel label="Link Target" for="target" suggestion="Specify the target tab of the banner." required="true" />
                                </div>
                                <div class="col-lg-7">
                                    <select class="form-select" data-parsley-errors-container=".targetParsley" data-placeholder="Select target" required="true" name="target">
                                        <option value="_self">Same Tab</option>
                                        <option value="_blank">New Tab</option>
                                    </select>
                                </div>
                                <div class="targetParsley"></div>
                            </div>
                            <div class="row g-3 align-center">
                                <div class="col-lg-5">
                                    <div class="form-group">
                                        <label class="form-label" for="default-06">Status</label>
                                        <span class="form-note">select the status of the banner.</span>
                                    </div>
                                </div>
                                <div class="col-lg-7">
                                    @if(isset($banner) && $banner->status == 1)
                                        <x-inputs.switch for="status" size="md" name="status" checked='checked'/>
                                    @else
                                        <x-inputs.switch for="status" size="md" name="status"/>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- .nk-block -->
        <div class="nk-block">
            <div class="card card-bordered sp-plan">
                <div class="row no-gutters">
                    <div class="col-md-3">
                        <div class="sp-plan-action card-inner">
                            <div class="icon">
                                <em class="icon ni ni-card-view fs-36px o-5"></em>
                                <h5 class="o-5">Description</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="sp-plan-info card-inner">
                            <div class="row g-3 align-center">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <div class="form-control-wrap">
                                            {{-- <div class="summernote-basic" name="description" maxlength="500"></div> --}}
                                            @if(isset($banner))
                                                <textarea name="description" class="summernote-basic" maxlength="500">{{ $banner->description }}</textarea>
                                            @else
                                                <textarea name="description" class="summernote-basic" maxlength="500"></textarea>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- .nk-block -->

        <div class="nk-block">
            <div class="row">
                <div class="col-md-12">
                    <div class="sp-plan-info pt-0 pb-0 card-inner">  
                            <div class="row">
                                <div class="col-lg-7 text-right offset-lg-5">
                                    <div class="form-group">
                                        <a href="javascript:history.back()" class="btn btn-outline-light">Cancel</a>
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                </div>
                            </div>
                    </div><!-- .sp-plan-info -->
                </div><!-- .col -->
            </div><!-- .row -->
        </div>
    </form>
@endsection
@push('footerScripts')
<script src="{{url('js/editor/summernote.min.js?ver=1.9.0')}}"></script>
<script src="{{url('js/editor/editors.js?ver=1.9.0')}}"></script>
@endpush