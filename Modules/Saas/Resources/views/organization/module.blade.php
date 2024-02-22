@extends('layouts.app')
@section('content')
<div class="nk-block-head nk-block-head-sm">
    <div class="nk-block-between">
        <div class="nk-block-head-content">
            <h3 class="nk-block-title page-title"><a href="javascript:history.back()" class="pt-3"><em class="icon ni ni-chevron-left back-icon"></em> </a> Edit Organization</h3>
        </div><!-- .nk-block-head-content -->
    </div><!-- .nk-block-between -->
</div><!-- .nk-block-head -->
<div class="card">
    <div class="card-inner pt-2 pl-2 pb-0">
        <ul class="nav nav-tabs mt-n3 bdr-btm-none">
            <li class="nav-item">
                <a class="nav-link " href="{{url('/saas/organization/edit')}}"><em class="icon ni ni-setting"></em> <span>Profile</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href=""><em class="icon ni ni-lock"></em><span>Module Access</span></a>
            </li>
        </ul>
    </div>
</div>
<div class="nk-block nk-block-lg pt-28">
    
        
            <div class="card">
                <div class="card-inner pl-0 pr-0">
                    <table class="table permission-table ">
                        <thead>
                            <tr>
                                <th>Module Name</th>
                                <th width="1%" nowrap="">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Ecommerce</td>
                                <td>
                                    <x-inputs.switch for="ecommerce" size="md" name="ecommerce"/>
                                </td>
                            </tr>
                            <tr>
                                <td>CMS</td>
                                <td>
                                    <x-inputs.switch for="cms" size="md" name="cms"/>
                                </td>
                            </tr>
                            <tr>
                                <td>Utility</td>
                                <td>
                                    <x-inputs.switch for="utility" size="md" name="utility"/>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="nk-block pt-28">
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
        
    
</div>
@endsection