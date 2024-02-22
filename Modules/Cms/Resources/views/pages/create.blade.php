@push('headerScripts')
    <link rel="stylesheet" href="{{url('css/editor/summernote.css?ver=1.9.0')}}">
@endpush
@extends('layouts.app')

@section('content')
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title"><a href="javascript:history.back()" class="pt-3"><em class="icon ni ni-chevron-left back-icon"></em> </a> @if(isset($page)) Edit @else Add @endif Page</h3>
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
                            <em class="icon ni ni-template fs-36px o-5"></em>
                            <h5 class="o-5">Basic <br> Information</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="sp-plan-info card-inner">
                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <x-inputs.verticalFormLabel label="Title" for="title" required="true" suggestion="Specify the title of the page." required="true" />
                            </div>
                            <div class="col-lg-7">
                                <x-inputs.text value="{{ isset($page) ? $page->title : old('title') }}" for="title" icon="list" required="true" placeholder="title" name="title"/>
                                @if ($errors->has('title'))
                                    <span class="text-danger">{{ $errors->first('title') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <x-inputs.verticalFormLabel label="Slug" for="slug" required="true" suggestion="Specify the slug of the page." required="true" />
                            </div>
                            <div class="col-lg-7">
                                <x-inputs.text value="{{ isset($page) ? $page->slug : old('slug') }}" for="slug" icon="label" required="true" placeholder="Slug" name="slug"/>
                                @if ($errors->has('slug'))
                                    <span class="text-danger">{{ $errors->first('slug') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <x-inputs.verticalFormLabel label="Meta Keywords" for="MetaKeywords" required="false" suggestion="Specify the meta keywords of the page." />
                            </div>
                            <div class="col-lg-7">
                                <textarea class="form-control" name="MetaKeywords" data-parsley-errors-container=".parsley-container-MetaKeywords" spellcheck="false">{{ isset($page) ? $page->meta_keywords : old('MetaKeywords') }}</textarea>
                            </div>
                        </div>
                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <x-inputs.verticalFormLabel label="Meta Description" for="Metadescription" required="false" suggestion="Specify the meta description of the page." />
                            </div>
                            <div class="col-lg-7">
                                <textarea class="form-control" name="Metadescription" data-parsley-errors-container=".parsley-container-Metadescription" spellcheck="false">{{ isset($page) ? $page->meta_description : old('Metadescription') }}</textarea>
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
                                        @if(isset($page))
                                            <textarea name="description" class="summernote-basic" maxlength="500">{{ $page->description }}</textarea>
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
        <div class="card card-bordered sp-plan">
            <div class="row no-gutters">
                <div class="col-md-3">
                    <div class="sp-plan-action card-inner">
                        <div class="icon">
                            <em class="icon ni ni-eye fs-36px o-5"></em>
                            <h5 class="o-5">Page <br> Visiblity</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="sp-plan-info card-inner">
                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <x-inputs.verticalFormLabel label="Status" for="title" required="true" suggestion="Select to the publish the page." required="true" />
                            </div>
                            <div class="col-lg-7">
                                @php
                                    if(isset($page) && $page->status == 1){
                                        $active = 'checked';
                                        $inactive = '';
                                    }else{
                                        $active = '';
                                        $inactive = 'checked';
                                    }
                                @endphp

                                <span class="mr-2"><x-inputs.radio checked="{{ $active }}" for="Active" size="md" label="Active" name="status" value='1'/></span>
                                <x-inputs.radio for="Inactive" checked="{{ $inactive }}" size="md" label="Inactive" name="status" value='0'/>
                                @if ($errors->has('status'))
                                    <span class="text-danger">{{ $errors->first('status') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <x-inputs.verticalFormLabel label="Visiblity" for="title" required="true" suggestion="Select to the visiblity the page." required="true" />
                            </div>
                            <div class="col-lg-7">

                                @php
                                    if(isset($page) && $page->visiblity == 1){
                                        $public = 'checked';
                                        $private = '';
                                    }else{
                                        $public = '';
                                        $private = 'checked';
                                    }
                                @endphp

                                <span class="mr-2"><x-inputs.radio for="Public" checked="{{ $public }}" size="md" label="Public" name="visiblity" value='1'/></span>
                                <x-inputs.radio for="Private" checked="{{ $private }}" size="md" label="Private" name="visiblity" value='2'/>
                                @if ($errors->has('visiblity'))
                                    <span class="text-danger">{{ $errors->first('visiblity') }}</span>
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
                            <em class="icon ni ni-file-img fs-36px o-5"></em>
                            <h5 class="o-5">Image</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="sp-plan-info card-inner">
                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <x-inputs.verticalFormLabel label="Featured Image" for="FeaturedImage" required="true" suggestion="Select the image for the page." required="true" />
                            </div>
                            <div class="col-lg-7">
                                <div class="form-group">
                                    <div class="form-control-wrap">
                                        <div class="custom-file">
                                            <input type="file" multiple name="file" class="custom-file-input" id="customFile">
                                            <label class="custom-file-label" for="customFile">Choose file</label>
                                        </div>
                                        @if ($errors->has('file'))
                                            <span class="text-danger">{{ $errors->first('file') }}</span>
                                        @endif
                                    </div>
                                    @if(isset($page) && !is_null($page->file))
                                        @if($page->file != "")
                                            <div class="media_box">
                                                <img height="100" width="100" src="{{url('uploads/pages/'.$page->file)}}">
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row g-3 align-center">
                            <div class="col-lg-12 text-center fw-bold">
                                Or
                            </div>
                            
                        </div>
                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <x-inputs.verticalFormLabel label="Image Link" for="FeaturedImagelink" required="true" suggestion="Select the image for the page." required="true" />
                            </div>
                            <div class="col-lg-7">
                                <x-inputs.text value="{{ isset($page) ? $page->image_link : old('image_link') }}" for="FeaturedImagelink" icon="link" placeholder="Image Link" name=" image_link"/>
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