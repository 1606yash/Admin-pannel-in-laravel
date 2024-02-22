@extends('layouts.app')
@section('content')
<div class="nk-block-head nk-block-head-sm">
    <div class="nk-block-between">
        <div class="nk-block-head-content">
            <h3 class="nk-block-title page-title">Activity Logs</h3>
            <p>You have total 1 activity.</p>
        </div><!-- .nk-block-head-content -->
        <div class="nk-block-head-content">
            <div class="toggle-wrap nk-block-tools-toggle">
                <a href="#" class="btn btn-icon btn-trigger toggle-expand mr-n1" data-target="more-options"><em class="icon ni ni-more-v"></em></a>
                <div class="toggle-expand-content" data-content="more-options">
                    <ul class="nk-block-tools g-3">
                        <li>
                            <div class="form-inline flex-nowrap input-group gx-3">
                                <select class="form-select" data-search="off" data-placeholder="Bulk Action" id="mass-status">
                                    <option value="" selected disabled>Bulk Action</option>
                                    <option value="1">Approve</option>
                                    <option value="0">Unapprove</option>
                                </select>
                                <div class="input-group-prepend">
                                    <span class="d-none d-md-block"><button class="btn btn-primary" name="submit_btn" id="mass-update" onclick="updateMassItems()">Apply</button></span>
                                    <span class="d-md-none"><button class="btn btn-dim btn-outline-light btn-icon disabled"><em class="icon ni ni-arrow-right"></em></button></span>
                                </div>
                            </div>
                        </li>
                        <li>
                            <a href="#" class="btn btn-trigger btn-icon dropdown-toggle" data-toggle="modal" title="filter" data-target="#modalFilterorder">
                                <div class="dot dot-primary"></div>
                                <em class="icon ni ni-filter-alt"></em>
                            </a>
                        </li>
                        <li>
                            <div class="dropdown">
                                <a href="#" class="btn btn-trigger btn-icon dropdown-toggle" data-toggle="dropdown">
                                    <em class="icon ni ni-setting"></em>
                                </a>
                                <div class="dropdown-menu dropdown-menu-xs dropdown-menu-right">
                                    <ul class="link-check">
                                        <li><span>Actions</span></li>
                                        {{-- <li><a href="#"><em class="icon ni ni-download m-r10"></em> Export</a></li> --}}
                                        <li><a href="#"><em class="icon ni ni-upload m-r10"></em> Import</a></li>
                                    </ul>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div><!-- .nk-block-between -->
</div><!-- .nk-block-head -->
<div class="nk-block table-compact">
    <div class="nk-tb-list is-separate is-medium mb-3">
        <table class="products-init nowrap nk-tb-list is-separate" data-auto-responsive="false">
            <thead>
                <tr class="nk-tb-item nk-tb-head">
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Organization Name</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Organization Tin</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Organization Address</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Owner Name</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Assigned Brands</span></th>
                    <th class="nk-tb-col nk-tb-col-tools text-right">
                        <span class="sub-text">Action</span>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr class="nk-tb-item odd" role="row">
                    <td class="nk-tb-col tb-col-lg">User</td>
                    <td class="nk-tb-col tb-col-lg">500</td>
                    <td class="nk-tb-col tb-col-lg">Test</td>
                    <td class="nk-tb-col tb-col-lg">Test</td>
                    <td class="nk-tb-col tb-col-lg">Test</td>
                    <td class="nk-tb-col tb-col-lg text-right">
                        <ul class="nk-tb-actions gx-1">
                            <li class="nk-tb-action-hidden">
                                <a href="{{url('/user/organization-create')}}" class="btn btn-trigger btn-icon" data-toggle="tooltip" data-placement="top" title="Edit">
                                    <em class="icon ni ni-edit"></em>
                                </a>
                            </li>
                        </ul>
                    </td>
                </tr>
            </tbody>
        </table>
    </div><!-- .nk-tb-list -->
</div><!-- .nk-block -->
<div class="modal fade zoom" tabindex="-1" id="modalFilterorder">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Filter</h5>
                <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
            </div>
            <form role="form" class="mb-0" method="get" action="#">
                @csrf
                <div class="modal-body modal-body-lg">
                    <div class="gy-3">
                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <x-inputs.verticalFormLabel label="Organization Name" for="organizationName" suggestion="Specify the organization name." />
                            </div>
                            <div class="col-lg-7">
                                <x-inputs.text value="" for="organizationName" icon="user" placeholder="Organization Name" name="organizationName" />
                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" id="userId" name="user_id" value="0">
                <div class="modal-footer bg-light">
                    <div class="row">
                        <div class="col-lg-12 p-0 text-right">
                            <button class="btn btn-outline-light" data-dismiss="modal" aria-label="Close">Cancel</button>
                            <button class="btn btn-danger resetFilter" data-dismiss="modal" aria-label="Close">Clear Filter</button>
                            <button class="btn btn-primary submitBtn" type="button">Submit</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection