@push('headerScripts')
    <link rel="stylesheet" href="{{url('css/editor/summernote.css?ver=1.9.0')}}">
@endpush
@extends('layouts.app')
<style type="text/css">
    .table th,
    .table td {
        vertical-align: middle;
    }
</style>
@section('content')
@php
    if (in_array('mail', $notiTemplate->via)) {
        $checkedEmail= "checked";
    }else{
        $checkedEmail= "";
    }

    if (in_array('web', $notiTemplate->via) || in_array('database', $notiTemplate->via)) {
        $checkedWebPush= "checked";
    }else{
        $checkedWebPush= "";
    }

    if (in_array('wa', $notiTemplate->via)) {
        $checkedWa= "checked";
    }else{
        $checkedWa= "";
    }

@endphp
<form role="form" method="post" action="{{ url('administration/notification/templates/update') }}" enctype="multipart/form-data">
@csrf
<input type="hidden" name="id" value="{{ $notiTemplate->id }}"/>
<div class="nk-block-head nk-block-head-sm">
    <div class="nk-block-between">
        <div class="nk-block-head-content">
            <h3 class="nk-block-title page-title"><a href="javascript:history.back()" class="pt-3"><em class="icon ni ni-chevron-left back-icon"></em> </a> Edit "{{ $notiTemplate->name }}" Template</h3>
        </div><!-- .nk-block-head-content -->

    </div><!-- .nk-block-between -->
</div><!-- .nk-block-head -->
<div class="nk-block">
    <div class="card card-bordered">
        <div class="row no-gutters">
            <div class="col-md-12">
                <div class="sp-plan-info card-inner">
                    <div class="row g-3 align-center">
                        <div class="col-lg-5">
                            <x-inputs.verticalFormLabel label="Via" for="Via" suggestion="Select the via of the notification." required="true" />
                        </div>
                        <div class="col-lg-7">
                            <x-inputs.checkbox for="Via" size="md" label="E-mail" placeholder="E-mail" name="email"  value='1' checked="{{ $checkedEmail }}"/>
                            <x-inputs.checkbox for="web" size="md" label="Web/Push" placeholder="Web" name="web" value='1' checked="{{ $checkedWebPush }}"/>
                            <x-inputs.checkbox for="wa" size="md" label="WhatsApp" placeholder="WhatsApp" name="whatsApp" value='1' checked="{{ $checkedWa }}"/>
                            <span class="form-note">Notification Channels. If 'User Preferences' was chosen, notification will be sent for both forced and user preferred channels.</span>
                        </div>
                    </div>

                    <div class="row g-3 align-center">
                        <div class="col-lg-5">
                            <x-inputs.verticalFormLabel label="Name" for="Name" suggestion="Specify the name of the template." required="true" />
                        </div>
                        <div class="col-lg-7">
                            <div class="form-group">
                                <div class="form-control-wrap">
                                    <div class="form-icon form-icon-left "><em class="icon ni ni-list-round"></em></div>
                                    <input type="text" class="form-control" required="true" value="{{ $notiTemplate->friendly_name }}" name="name">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 align-center">
                        <div class="col-lg-5">
                            <x-inputs.verticalFormLabel label="Code" for="Code" suggestion="Code name of the notificattion."  />
                        </div>
                        <div class="col-lg-7">
                            <div class="form-group">
                                <div class="form-control-wrap">
                                    <label>{{ $notiTemplate->name }}</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row g-3 align-center">
                        <div class="col-lg-5">
                            <x-inputs.verticalFormLabel label="Send notification to Users (BCC)" for="roles" suggestion="Select the users to send the notificattion." />
                        </div>
                        <div class="col-lg-7">
                            <div class="form-group">
                                <div class="form-control-wrap">
                                    <select class="form-select" data-search="on" data-placeholder="Select Users" name="users[]" multiple="">
                                        <option></option>
                                            @forelse($users as $key  => $u)
                                            <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                                            @empty
                                                <option></option>
                                            @endforelse
                                    </select>
                                    <span class="form-note">BCC will be sent to selected users.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row g-3 align-center">
                        <div class="col-lg-5">
                            <x-inputs.verticalFormLabel label="To Custom E-mail" for="roles" suggestion="Select the custom email to send the notificattion." />
                        </div>
                        <div class="col-lg-7">
                            <div class="form-group">
                                <div class="form-control-wrap">
                                    <x-inputs.text value="" for="customEmails" icon="list-round" placeholder="Email with comma seapreated." name="customEmails" />
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
        <div class="col-md-8">
            <div class="card">
                <div class="card-inner card-preview">
                    <ul class="nav nav-tabs mt-n3">

                        <li class="nav-item emailTab" @if($checkedEmail == "") style="display: none;" @endif>
                            <a class="nav-link active" data-toggle="tab" href="#emails">Email</a>
                        </li>

                        <li class="nav-item webTab" @if($checkedWebPush == "") style="display: none;" @endif>
                            <a class="nav-link" data-toggle="tab" href="#webss">Web/Push</a>
                        </li>
                        <li class="nav-item waTab" @if($checkedWa == "") style="display: none;" @endif>
                            <a class="nav-link" data-toggle="tab" href="#wasss">WhatsApp</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        
                        <div class="tab-pane active emailTab" id="emails" @if($checkedEmail == "") style="display: none;" @endif>
                            <div class="row">
                                <div class="col-md-12">
                                    <x-inputs.text value="{{ $notiTemplate->email_subject }}" for="Email Subject" icon="list-round" label="Email Subject" name="email_subject" />
                                    <label class="form-label" for="Email Body">Email Body</label>
                                    <div class="form-group">
                                        <div class="form-control-wrap">
                                            <textarea class="summernote-basic" name="mail">{{ !empty($notiTemplate->body['mail']) ? $notiTemplate->body['mail'] : '' }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane webTab" id="webss" @if($checkedWebPush == "") style="display: none;" @endif>
                            <div class="row">
                                <div class="col-md-12">
                                
                                    <div class="form-group">
                                        <div class="form-control-wrap">
                                            <textarea class="form-control" name="database" for="webPush" size="sm" id=""  maxlength="500"  placeholder="Message"  data-parsley-errors-container=".parsley-container-database">{{ !empty($notiTemplate->body['database']) ? $notiTemplate->body['database'] : '' }}</textarea>
                                        </div>
                                        <div class="parsley-container-database"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane waTab" id="wasss" @if($checkedWa == "") style="display: none;" @endif>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <div class="form-control-wrap">
                                            <textarea class="form-control" name="wa" for="wa" size="sm" id=""  maxlength="500"  placeholder="Message"  data-parsley-errors-container=".parsley-container-wa">{{ !empty($notiTemplate->body['wa']) ? $notiTemplate->body['wa'] : '' }}</textarea>
                                        </div>
                                        <div class="parsley-container-wa"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-inner">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label">Notification Parameters</label>
                                <span class="form-note"> copy and use parameters in body and title.</span>
                            </div>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th scope="col">Parameter</th>
                                        <th scope="col">Description</th>
                                    </tr>
                                </thead>
                                @php

                                    if($notiTemplate->shortcodes != ""){
                                        $shortcodes = json_decode($notiTemplate->shortcodes);
                                    }
                                @endphp
                                <tbody>
                                    @if(isset($shortcodes) && !empty($shortcodes))
                                    @foreach ($shortcodes as $code => $shortcode)
                                        <tr>
                                            <th scope="row">{{ '{'.$code.'}' }} <a href="#"><em class="icon ni ni-copy"></em></a></th>
                                            <td>{{ $shortcode }}</td>
                                        </tr>
                                    @endforeach

                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="nk-block">
    <div class="row">
        <div class="col-md-12">
            <div class="sp-plan-info pt-0 pb-0 card-inner">
                <div class="row">
                    <div class="col-lg-7 text-right offset-lg-5">
                        <div class="form-group">
                            <a href="javascript:history.back()" class="btn btn-outline-light">Cancel</a>
                            <button type="submit" class="btn btn-primary" name="submit">Update</button>
                        </div>
                    </div>
                </div>
            </div><!-- .sp-plan-info -->
        </div><!-- .col -->
    </div><!-- .row -->
</div>
@endsection


@push('footerScripts')
<script src="{{url('js/editor/summernote.min.js?ver=1.9.0')}}"></script>
<script src="{{url('js/editor/editors.js?ver=1.9.0')}}"></script>

<script type="text/javascript">
    $(document).ready(function(){
        $('#Via').click(function() {
            if(!$(this).is(':checked')){
                $('.emailTab').hide();
            }else{
                $('.emailTab').show();
            }
        });

        $('#web').click(function() {
            
            if(!$(this).is(':checked')){
                $('.webTab').hide();
            }else{
                $('.webTab').show();
            }
        });

        $('#wa').click(function() {
            
            if(!$(this).is(':checked')){
                $('.waTab').hide();
            }else{
                $('.waTab').show();
            }
        });
    });
</script>

@endpush