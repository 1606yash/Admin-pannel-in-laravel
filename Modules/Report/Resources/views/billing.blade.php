@extends('layouts.app')
@section('content')
<div class="nk-block-head nk-block-head-sm">
    <div class="nk-block-between">
        <div class="nk-block-head-content">
            <h3 class="nk-block-title page-title">Billing</h3>
        </div><!-- .nk-block-head-content -->
        <div class="nk-block-head-content">
            <div class="toggle-wrap nk-block-tools-toggle">
                <a href="#" class="btn btn-icon btn-trigger toggle-expand mr-n1" data-target="more-options"><em class="icon ni ni-more-v"></em></a>
                <div class="toggle-expand-content" data-content="more-options">
                    <ul class="nk-block-tools g-3">
                        <li>
                            <div class="form-wrap 50px mr-2">
                                <input class="form-control" value="" placeholder="Order No." name="label" />
                            </div>
                            <div class="form-wrap 50px mr-2">
                                <x-inputs.datePicker for="orderDate" size="sm" placeholder="Order Date" name="orderDate" icon="calendar"/>
                            </div>
                            <div class="form-wrap 50px mr-2">
                                <input class="form-control" value="" placeholder="Order Value" name="label" />
                            </div>
                            <div class="btn-wrap">
                                <span class="d-none d-md-block"><button class="btn btn-primary" name="submit_btn" id="mass-update" onclick="updateMassItems()">Apply</button></span>
                                <span class="d-md-none"><button class="btn btn-dim btn-outline-light btn-icon disabled"><em class="icon ni ni-arrow-right"></em></button></span>
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
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Order Date</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Order By</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Buyer Name</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">State</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">District</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">City</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Order No</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Order Value</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Order Qty</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Invoice Value</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Invoice Date</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Cancelled by</span></th>
                    <th class="nk-tb-col tb-col-mb"><span class="sub-text">Cancelled Date</span></th>
                </tr>
            </thead>
            <tbody>
                <tr class="nk-tb-item odd" role="row">
                    <td class="nk-tb-col tb-col-lg">21 Jan 2021</td>
                    <td class="nk-tb-col tb-col-lg">Test</td>
                    <td class="nk-tb-col tb-col-lg">Test11</td>
                    <td class="nk-tb-col tb-col-lg">State</td>
                    <td class="nk-tb-col tb-col-lg">District</td>
                    <td class="nk-tb-col tb-col-lg">City</td>
                    <td class="nk-tb-col tb-col-lg">123654</td>
                    <td class="nk-tb-col tb-col-lg">500.00</td>
                    <td class="nk-tb-col tb-col-lg">20</td>
                    <td class="nk-tb-col tb-col-lg">500.00</td>
                    <td class="nk-tb-col tb-col-lg">21 Jan 2021</td>
                    <td class="nk-tb-col tb-col-lg">Test</td>
                    <td class="nk-tb-col tb-col-lg">21 Jan 2021</td>
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
                                <x-inputs.verticalFormLabel label="Date Range" for="Create" suggestion="Specify the range." />
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
                                <div class="row p-1 align-center">
                                    <div class="col-lg-12 text-center fw-bold">
                                        Or
                                    </div>
                                </div>
                                <div>
                                    <x-inputs.select  size="sm" name="range" for="range" placeholder="Select">
                                        <option value="">Select</option>
                                        <option value="Last 7 Days" selected>Last 7 Days</option>
                                        <option value="Last 30 Days">Last 30 Days</option>
                                        <option value="Current Quarter">Current Quarter</option>
                                        <option value="Last Quarter">Last Quarter</option>
                                        <option value="Current Month">Current Month</option>
                                        <option value="Last Month">Last Month</option>
                                    </x-inputs.select>
                                </div>
                            </div>
                        </div>
                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <x-inputs.verticalFormLabel label="Buyer" for="buyer" suggestion="Select the buyer." />
                            </div>
                            <div class="col-lg-7">
                                <x-inputs.select  size="sm" name="buyer" for="buyer" placeholder="Buyer">
                                    <option value="">Select</option>
                                </x-inputs.select>
                            </div>
                        </div>
                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <x-inputs.verticalFormLabel label="Sales Person" for="salesPerson" suggestion="Select the sales person." />
                            </div>
                            <div class="col-lg-7">
                                <x-inputs.select  size="sm" name="salesPerson" for="salesPerson" placeholder="Sales Person">
                                    <option value="">Select</option>
                                </x-inputs.select>
                            </div>
                        </div>
                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <x-inputs.verticalFormLabel label="State" for="state" suggestion="Specify the state." />
                            </div>
                            <div class="col-lg-7">
                                <x-inputs.select  size="sm" name="state" for="state" placeholder="State">
                                    <option value="">Select</option>
                                </x-inputs.select>
                            </div>
                        </div>
                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <x-inputs.verticalFormLabel label="District" for="district" suggestion="Specify the district." />
                            </div>
                            <div class="col-lg-7">
                                <x-inputs.select  size="sm" name="district" for="district" placeholder="District">
                                    <option value="">Select</option>
                                </x-inputs.select>
                            </div>
                        </div>
                        <div class="row g-3 align-center">
                            <div class="col-lg-5">
                                <x-inputs.verticalFormLabel label="City" for="city" suggestion="Specify the city." />
                            </div>
                            <div class="col-lg-7">
                                <x-inputs.select  size="sm" name="city" for="city" placeholder="City">
                                    <option value="">Select</option>
                                </x-inputs.select>
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