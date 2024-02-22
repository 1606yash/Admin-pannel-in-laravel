@extends('layouts.app')
@section('content')
<div class="nk-block-head nk-block-head-sm">
    <div class="nk-block-between">
        <div class="nk-block-head-content">
            <h3 class="nk-block-title page-title">Order Received from SP or Stuff role</h3>
        </div><!-- .nk-block-head-content -->
        <div class="nk-block-head-content">
            <div class="toggle-wrap nk-block-tools-toggle">
                <a href="#" class="btn btn-icon btn-trigger toggle-expand mr-n1" data-target="more-options"><em class="icon ni ni-more-v"></em></a>
                <div class="toggle-expand-content" data-content="more-options">
                    <ul class="nk-block-tools g-3">
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
                                        <li><a href="#"><em class="icon ni ni-download m-r10"></em> Export</a></li>
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
                    <th class="nk-tb-col nk-tb-col-check">
                        <div class="custom-control custom-control-sm custom-checkbox notext">
                            <input type="checkbox" class="custom-control-input" id="check-all" name="check_all"><label class="custom-control-label" for="check-all"></label>
                        </div>
                    </th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Role</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Employee Name</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Total no of Orders Taken</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Order Amount (INR)</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Invoiced Amount</span></th>
                    <th class="nk-tb-col nk-tb-col-tools text-right">
                        <span class="sub-text">Action</span>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr class="nk-tb-item odd" role="row">
                    <td class="nk-tb-col tb-col-lg nk-tb-col-check sorting_1">
                        <div class="custom-control custom-control-sm custom-checkbox notext">
                            <input type="checkbox" class="custom-control-input cb-check" id="cb-19" value="19" name="checked_items[]">
                            <label class="custom-control-label" for="cb-19"></label>
                        </div>
                    </td>
                    <td class=" nk-tb-col tb-col-lg">User</td>
                    <td class=" nk-tb-col tb-col-lg">Test</td>
                    <td class=" nk-tb-col tb-col-lg">100</td>
                    <td class=" nk-tb-col tb-col-lg">200.00</td>
                    <td class=" nk-tb-col tb-col-lg">200.00</td>
                    <td class=" nk-tb-col tb-col-lg text-right nk-tb-col-tools">
                        <ul class="nk-tb-actio ns gx-1">
                        <li>
                            <div class="drodown mr-n1">
                                <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-toggle="dropdown" aria-expanded="false"><em class="icon ni ni-more-h"></em></a>
                                <div class="dropdown-menu dropdown-menu-right" style="">
                                    <ul class="link-list-opt no-bdr">
                                        <li><a href="#"><em class="icon ni ni-undo"></em><span>Notify Buyer</span></a></li>
                                        <li><a href="#" data-toggle="modal" data-target="#modalOrderLogs"><em class="icon ni ni-list"></em><span>Audit Logs</span></a></li>
                                    </ul>
                                </div>
                            </div>
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
                                <x-inputs.verticalFormLabel label="Date Range" for="Create" suggestion="Specify the date." />
                            </div>
                            <div class="col-lg-7">
                                <div class="row">
                                    <div class="col-md-6">
                                        <x-inputs.datePicker for="fromDate" size="sm" placeholder="From date" name="fromDate" icon="calendar"/>
                                    </div>
                                    <div class="col-md-6">
                                        <x-inputs.datePicker for="toDate" size="sm" placeholder="To date" name="toDate" icon="calendar"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <x-inputs.verticalFormLabel label="State" for="state" suggestion="Specify the state." />
                            </div>
                            <div class="col-lg-7">
                                <x-inputs.text value="" for="state" placeholder="State" name="state"/>
                            </div>
                        </div>
                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <x-inputs.verticalFormLabel label="District" for="district" suggestion="Specify the district." />
                            </div>
                            <div class="col-lg-7">
                                <x-inputs.text value="" for="district" placeholder="District" name="district"/>
                            </div>
                        </div>
                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <x-inputs.verticalFormLabel label="City" for="city" suggestion="Specify the city." />
                            </div>
                            <div class="col-lg-7">
                                <x-inputs.text value="" for="city" placeholder="City" name="city"/>
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