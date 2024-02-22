@extends('layouts.app')

@section('content')
    <?php /*
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h4 class="nk-block-title page-title">Dashboard</h4>
            </div><!-- .nk-block-head-content -->
        </div><!-- .nk-block-between -->
    </div><!-- .nk-block-head -->
    */?>
    <div class="nk-block">
        <div class="row g-gs">
            <div class="col-xxl-4 col-md-6">
                <div class="card is-dark h-100">
                    <div class="nk-ecwg nk-ecwg1">
                        <div class="card-inner">
                            <div class="card-title-group">
                                <div class="card-title">
                                    <h6 class="title">Total Sales <span>/ Orders</span></h6>
                                </div>
                                <div class="card-tools">
                                    <a href="#" class="link">View Report</a>
                                </div>
                            </div>
                            <div class="data">
                                <div class="amount">₹{{number_format($orders->totalSales,2)}} <span>/ 5</span></div>
                                <div class="info"><strong>₹{{number_format($last_orders->totalSales,2)}} / 5</strong> in last month</div>
                            </div>
                            <div class="data">
                                
                                @php
                                    $arrowType = 'up';
                                    $percentChange = 0;
                                    if($last_orders->totalSales > 0 && $last_orders->totalSales = 0){
                                        $percentChange = (1 - $last_orders->totalSales / $orders->totalSales) * 100;
                                        $percentChange = number_format($percentChange,2);


                                        if($last_orders->totalSales > $orders->totalSales){
                                            $arrowType = 'down';
                                        }
                                    }

                                @endphp 

                                <h6 class="sub-title">This week so far</h6>
                                <div class="data-group">
                                    <div class="amount">₹{{number_format($week->totalSales,2)}} <span>/ 5</span></div>
                                    <div class="info text-right"><span class="change up text-danger"><em class="icon ni ni-arrow-long-{{ $arrowType }}"></em>{{ $percentChange }}%</span><br><span>vs. last week</span></div>
                                </div>
                            </div>
                        </div><!-- .card-inner -->
                        <div class="nk-ecwg1-ck">
                            <canvas class="ecommerce-line-chart-s1" id="totalSales"></canvas>
                        </div>
                    </div><!-- .nk-ecwg -->
                </div><!-- .card -->
            </div><!-- .col -->
            <div class="col-xxl-4 col-md-6">
                <div class="card h-100">
                    <div class="nk-ecwg nk-ecwg2">
                        <div class="card-inner">
                            <div class="card-title-group mt-n1">
                                <div class="card-title">
                                    <h6 class="title">Average order</h6>
                                </div>
                                <div class="card-tools mr-n1">
                                    {{-- <div class="dropdown">
                                        <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                                        <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                                            <ul class="link-list-opt no-bdr">
                                                <li><a href="#" class="active"><span>15 Days</span></a></li>
                                                <li><a href="#"><span>30 Days</span></a></li>
                                                <li><a href="#"><span>3 Months</span></a></li>
                                            </ul>
                                        </div>
                                    </div> --}}
                                </div>
                            </div>
                            <div class="data">
                                <div class="data-group">
                                    <div class="amount">₹{{number_format($orders->averageOrder,2)}}</div>

                                    @php

                                        $arrowType = 'up';
                                        $percentChange = 0;
                                        if($last_orders->averageOrder = 0 && $last_orders->averageOrder = 0){
                                            $percentChange = (1 - $last_orders->averageOrder / $orders->averageOrder) * 100;
                                            $percentChange = number_format($percentChange,2);


                                            if($last_orders->averageOrder > $orders->averageOrder){
                                                $arrowType = 'down';
                                            }
                                        }

                                    @endphp

                                    <div class="info text-right"><span class="change up text-danger"><em class="icon ni ni-arrow-long-{{ $arrowType }}"></em>{{ $percentChange }}%</span><br><span>vs. last week</span></div>
                                </div>
                            </div>
                            <h6 class="sub-title">Orders over time</h6>
                        </div><!-- .card-inner -->
                        <div class="nk-ecwg2-ck">
                            <canvas class="ecommerce-bar-chart-s1" id="averargeOrder"></canvas>
                        </div>
                    </div><!-- .nk-ecwg -->
                </div><!-- .card -->
            </div><!-- .col -->
            <div class="col-xxl-4">
                <div class="row g-gs">
                    <div class="col-xxl-12 col-md-6">
                        <div class="card">
                            <div class="nk-ecwg nk-ecwg3">
                                <div class="card-inner pb-0">
                                    <div class="card-title-group">
                                        <div class="card-title">
                                            <h6 class="title">Orders</h6>
                                        </div>
                                    </div>
                                    <div class="data">
                                        <div class="data-group">
                                            <div class="amount">{{ $orders->totalOrders }}</div>
                                            {{-- <div class="info text-right"><span class="change up text-danger"><em class="icon ni ni-arrow-long-up"></em>4.63%</span><br><span>vs. last week</span></div> --}}
                                        </div>
                                    </div>
                                </div><!-- .card-inner -->
                                <div class="nk-ecwg3-ck">
                                    <canvas class="ecommerce-line-chart-s1" id="totalOrders"></canvas>
                                </div>
                            </div><!-- .nk-ecwg -->
                        </div><!-- .card -->
                    </div><!-- .col -->
                    <div class="col-xxl-12 col-md-6">
                        <div class="card">
                            <div class="nk-ecwg nk-ecwg3">
                                <div class="card-inner pb-0">
                                    <div class="card-title-group">
                                        <div class="card-title">
                                            <h6 class="title">Customers</h6>
                                        </div>
                                    </div>
                                    <div class="data">
                                        <div class="data-group">
                                            <div class="amount">{{ $retailers }}</div>
                                            {{-- <div class="info text-right"><span class="change up text-danger"><em class="icon ni ni-arrow-long-up"></em>4.63%</span><br><span>vs. last week</span></div> --}}
                                        </div>
                                    </div>
                                </div><!-- .card-inner -->
                                <div class="nk-ecwg3-ck">
                                    <canvas class="ecommerce-line-chart-s1" id="totalCustomers"></canvas>
                                </div>
                            </div><!-- .nk-ecwg -->
                        </div><!-- .card -->
                    </div><!-- .col -->
                </div><!-- .row -->
            </div><!-- .col -->
            <div class="col-xxl-8">
                <div class="card card-full">
                    <div class="card-inner">
                        <div class="card-title-group">
                            <div class="card-title">
                                <h6 class="title">Recent Orders</h6>
                            </div>
                        </div>
                    </div>
                    <div class="nk-tb-list mt-n2">
                        <div class="nk-tb-item nk-tb-head">
                            <div class="nk-tb-col"><span>Order No.</span></div>
                            <div class="nk-tb-col tb-col-sm"><span>Customer</span></div>
                            <div class="nk-tb-col tb-col-md"><span>Date</span></div>
                            <div class="nk-tb-col"><span class="d-none d-sm-inline">Status</span></div>
                            <div class="nk-tb-col"><span>Amount</span></div>
                            <div class="nk-tb-col text-right"><span>Action</span></div>
                        </div>
                        
                        @forelse ($recentOrders as $order)
                            <div class="nk-tb-item">
                                <div class="nk-tb-col">
                                    @php
                                        $detailUrl =URL::to('ecommerce/orders/detail/'.$order->order_id);
                                    @endphp

                                    <span class="tb-lead"><a href="{{ $detailUrl }}">#{{ $order->order_number }}</a></span>
                                </div>
                                <div class="nk-tb-col tb-col-sm">
                                    <div class="user-card">

                                        @php

                                            if(!is_null($order->file)){
                                                $file = public_path('uploads/users/') . $order->file;
                                            }

                                            if(!is_null($order->file) && file_exists($file))
                                                $avatar = "<img src=".url('uploads/users/'.$order->file).">";
                                            else
                                                $avatar = "<span>".\Helpers::getAcronym($order->buyerName)."</span>";
                                        @endphp     

                                        <div class="user-avatar sm bg-purple-dim">
                                            <span>{!! $avatar !!}</span>
                                        </div>
                                        <div class="user-info">
                                            <span class="tb-lead">{{ $order->buyerName }}</span>
                                            <!-- <span>{{ $order->buyerName }}</span> -->
                                        </div>
                                    </div>
                                </div>
                                <div class="nk-tb-col tb-col-md">
                                    <span class="tb-sub">{{ date(\Config::get('constants.DATE.DATE_FORMAT') , strtotime($order->orderDate)) }}</span>
                                </div>
                                <div class="nk-tb-col">
                                    <span class="badge badge-success">{{ ucfirst($order->status) }}</span>
                                </div>
                                <div class="nk-tb-col">
                                    <span class="tb-sub tb-amount"><span>₹</span>{{ $order->amount }}</span>
                                </div>
                                <div class="nk-tb-col text-right nk-tb-col-tools">
                                    <ul class="nk-tb-actions gx-1">
                                        <li class="nk-tb-action-hidden">
                                            <a href="{{ $detailUrl }}" class='viewItem btn btn-trigger btn-icon' data-toggle='tooltip' data-placement='top' title='View'>
                                                <em class='icon ni ni-eye'></em>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        @empty
                            <p>No Recent Orders</p>
                        @endforelse
                    </div>
                </div><!-- .card -->
            </div>
            <div class="col-xxl-4 col-md-6">
                <div class="card h-100">
                    <div class="card-header bg-white">
                        <div class="card-title-group mt-1">
                            <div class="card-title">
                                <h6 class="title">Top 5 Products</h6>
                            </div>
                            <div class="card-tools">
                                {{-- <div class="dropdown">
                                    <a href="#" class="dropdown-toggle link link-light link-sm dropdown-indicator" data-toggle="dropdown">Weekly</a>
                                    <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                                        <ul class="link-list-opt no-bdr">
                                            <li><a href="#"><span>Daily</span></a></li>
                                            <li><a href="#" class="active"><span>Weekly</span></a></li>
                                            <li><a href="#"><span>Monthly</span></a></li>
                                        </ul>
                                    </div>
                                </div> --}}
                            </div>
                        </div>
                    </div>
                    <div class="card-inner">
                        <ul class="nk-top-products">
                            @forelse ($products as $product)
                                <li class="item">
                                    <div class="user-avatar md shadow1 mr-2 bg-purple-dim">
                                        <span>
                                            @php

                                                if(!is_null($product->file) && $product->file != ""){
                                                    $file = public_path('uploads/users/') . $product->file;
                                                }

                                                if(!is_null($product->file) && file_exists($file))
                                                    $src = URL::to('/').'/uploads/products/'.$product->organization_id.'/'.$product->file;
                                                else
                                                    $src = "./images/product/a.png";
                                            @endphp
                                            <img src="{{ $src }}" alt="">
                                        </span>
                                    </div>
                                    <div class="info">
                                        <div class="title">{{ $product->item_name }}</div>
                                        <div class="price">₹{{ $product->sale_price }}</div>
                                    </div>
                                    <div class="total">
                                        <h6 class="amount1">₹{{ $product->TotalAmount }}</h6>
                                        <div class="count">{{ $product->TotalQuantity }} Sold</div>
                                    </div>
                                </li>
                            @empty
                                {{-- empty expr --}}
                            @endforelse
                        </ul>
                    </div><!-- .card-inner -->
                </div><!-- .card -->
            </div><!-- .col -->
            <div class="col-xxl-4 col-md-6">
                <div class="card h-100">
                    <div class="card-inner">
                        <div class="card-title-group mb-2">
                            <div class="card-title">
                                <h6 class="title">Top 5 Categories</h6>
                            </div>
                            <div class="card-tools">
                                {{-- <div class="dropdown">
                                    <a href="#" class="dropdown-toggle link link-light link-sm dropdown-indicator" data-toggle="dropdown">Weekly</a>
                                    <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                                        <ul class="link-list-opt no-bdr">
                                            <li><a href="#"><span>Daily</span></a></li>
                                            <li><a href="#" class="active"><span>Weekly</span></a></li>
                                            <li><a href="#"><span>Monthly</span></a></li>
                                        </ul>
                                    </div>
                                </div> --}}
                            </div>
                        </div>
                        <ul class="nk-top-products">

                            @forelse ($topCategories as $category)
                                <li class="item">
                                    <div class="user-avatar md shadow1 mr-2 bg-purple-dim">
                                        <span>
                                            @php
                                            if(!is_null($category->file) && $category->file != ""){
                                                $file = public_path('uploads/users/') . $category->file;
                                            }

                                            if(!is_null($category->file) && file_exists($file))
                                                $src = URL::to('/').'/uploads/category/'.$category->file;
                                            else
                                                $src = "./images/product/a.png";
                                            @endphp
                                            <img src="{{ $src }}" alt="">
                                        </span>
                                    </div>
                                    <div class="info">
                                        <div class="title">{{ $category->name }}</div>
                                    </div>
                                    <div class="total">
                                        <h6 class="amount1">₹{{ $category->TotalAmount }}</h6>
                                        <div class="count">{{ $category->TotalQuantity }} Products  Sold</div>
                                    </div>
                                </li>
                            @empty
                                {{-- empty expr --}}
                            @endforelse
                        </ul>
                    </div><!-- .card-inner -->
                </div><!-- .card -->
            </div><!-- .col -->
            <div class="col-xxl-8 col-md-8">
                <div class="card h-100">
                    <div class="card-inner">
                        <div class="card-title-group mb-2">
                            <div class="card-title">
                                <h6 class="title">Store Statistics</h6>
                            </div>
                        </div>
                        <ul class="nk-store-statistics">
                            <li class="item">
                                <div class="info">
                                    <div class="title">Orders</div>
                                    <div class="count">{{ $statistics['orders'] }}</div>
                                </div>
                                <em class="icon bg-primary-dim ni ni-bag"></em>
                            </li>
                            <li class="item">
                                <div class="info">
                                    <div class="title">Customers</div>
                                    <div class="count">{{ $statistics['customers'] }}</div>
                                </div>
                                <em class="icon bg-info-dim ni ni-users"></em>
                            </li>
                            <li class="item">
                                <div class="info">
                                    <div class="title">Products</div>
                                    <div class="count">{{ $statistics['products'] }}</div>
                                </div>
                                <em class="icon bg-pink-dim ni ni-box"></em>
                            </li>
                            <li class="item">
                                <div class="info">
                                    <div class="title">Categories</div>
                                    <div class="count">{{ $statistics['categories'] }}</div>
                                </div>
                                <em class="icon bg-purple-dim ni ni-server"></em>
                            </li>
                        </ul>
                    </div><!-- .card-inner -->
                </div><!-- .card -->
            </div><!-- .col -->
            {{-- <div class="col-xxl-6 col-lg-6">
                <div class="card h-100">
                    <div class="nk-ecwg nk-ecwg5">
                        <div class="card-inner">
                            <div class="card-title-group align-start pb-3 g-2">
                                <div class="card-title">
                                    <h6 class="title">Orders</h6>
                                </div>
                                <div class="card-tools">
                                    <em class="card-hint icon ni ni-help" data-toggle="tooltip" data-placement="left" title="Users of this month"></em>
                                </div>
                            </div>
                            <div class="data-group">
                                <div class="data">
                                    <div class="title">This Month</div>
                                    <div class="amount amount-sm">1075</div>
                                </div>
                                <div class="data">
                                    <div class="title">This Week</div>
                                    <div class="amount amount-sm">320</div>
                                </div>
                                <div class="data">
                                    <div class="title">Today</div>
                                    <div class="amount amount-sm">40</div>
                                </div>
                            </div>
                            <div class="card-title-group align-start pb-3 g-2">
                                <div class="card-title">
                                    <h6 class="title">Orders Total Amount</h6>
                                </div>
                                <div class="card-tools">
                                    <em class="card-hint icon ni ni-help" data-toggle="tooltip" data-placement="left" title="Users of this month"></em>
                                </div>
                            </div>
                            <div class="data-group">
                                <div class="data">
                                    <div class="title">This Month</div>
                                    <div class="amount amount-sm"><em class="icon ni ni-sign-inr"></em> 9.28K</div>
                                </div>
                                <div class="data">
                                    <div class="title">This Week</div>
                                    <div class="amount amount-sm"><em class="icon ni ni-sign-inr"></em> 2.69K</div>
                                </div>
                                <div class="data">
                                    <div class="title">Today</div>
                                    <div class="amount amount-sm"><em class="icon ni ni-sign-inr"></em> 0.94K</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div><!-- .card -->
            </div> --}}
        </div><!-- .row -->
    </div><!-- .nk-block -->
@endsection
@push('footerScripts')
<script src="{{url('js/APIDataTable.js')}}"></script>
<script src="{{url('js/chart-analytics.js')}}"></script>
<script src="{{url('js/jqvmap.js')}}"></script>
<script src="{{url('js/example-chart.js')}}"></script>
<script src="{{url('js/gd-default.js')}}"></script>
<script src="{{url('js/chart-ecommerce.js')}}"></script>
@endpush