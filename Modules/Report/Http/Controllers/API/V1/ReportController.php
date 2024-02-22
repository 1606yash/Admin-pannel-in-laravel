<?php

namespace Modules\Report\Http\Controllers\API\V1;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use DB;
use Modules\Saas\Entities\Organization;
use Modules\Ecommerce\Entities\Product;
use Modules\Ecommerce\Entities\Category;
use Modules\Ecommerce\Entities\ProductCategory;
use Modules\Ecommerce\Entities\Target;
use Modules\Ecommerce\Entities\Order;
use Modules\Ecommerce\Entities\OrderItem;
use Modules\User\Entities\User;
use Modules\User\Entities\State;
use Modules\User\Entities\City;
use Modules\User\Entities\District;
use URL;
use Auth;
use Helpers;
use Modules\Report\Exports\SalesBySalesPersonExport;
use Modules\Report\Exports\SalesByBuyerExport;
use Modules\Report\Exports\TopProductExport;
use Modules\Report\Exports\TopCategoryExport;
use Modules\Report\Exports\SalesByProductCategoriesExport;
use Modules\Report\Exports\ZeroBillingSalesPersonExport;
use Modules\Report\Exports\ZeroBillingBuyersExport;
use Modules\Report\Exports\SalesPersonTargetAchievementExport;
use Modules\Report\Exports\BuyerTargetAchievementExport;
use Modules\Ecommerce\Entities\Visit;
use App\Http\Controllers\ApiBaseController;
use Modules\Saas\Entities\Settings;


class ReportController extends ApiBaseController
{

    public function __construct() {
        $this->success =  '200';
        $this->ok =  '200';
        $this->accessDenied =  '400';
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function topProducts(Request $request,$organizationId = 0)
    {
        echo "sds";die;
        $user = Auth::user();
        if($organizationId == 0){
            $organizationId= \Session::get('currentOrganization');
        }

        $products = Product::from('ecommerce_products as p')
                        ->select('p.id','p.created_at','p.organization_id','p.name','p.status','s.sale_price as price','s.code as sku_code','b.name as brand',
                            DB::raw('group_concat(DISTINCT cat.name) as categories'),
                            DB::Raw('SUM(quantity) AS TotalQuantity')
                        )
                        ->leftJoin('ecommerce_category_product as pro_cat','pro_cat.product_id','=','p.id')
                        ->leftJoin('categories as cat','cat.id','=','pro_cat.category_id')
                        ->leftJoin('ecommerce_sku as s','s.product_id','=','p.id')
                        ->join('ecommerce_order_items as oi','oi.sku_code','=','s.code')
                        ->leftJoin('ecommerce_product_brand as pb','pb.product_id','=','p.id')
                        ->leftJoin('ecommerce_brands as b','b.id','=','pb.brand_id')
                        ->where('p.organization_id',$organizationId)
                        ->whereNull('p.deleted_at')
                        ->groupBy('oi.sku_code')
                        ->orderBy('TotalQuantity','desc')
                        ->take(10)
                        ->get();

        $reportData = $products->toArray();
        // $reportData = $reportData['data'];
        // $pagination = $this->pagination($products);


        $data['data'] = $reportData;
        // $data['meta'] = $pagination;
        return $this->sendSuccessResponse($data, $this->success);
    }


    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function salesBySalesPerson(Request $request,$organizationId = 0)
    {
        $user = Auth::user();
        if($organizationId == 0){
            $organizationId= \Session::get('currentOrganization');
        }

        $orders =   Order::from('ecommerce_orders as o')
                    ->select('u.phone_number as user_mobile',
                        // DB::Raw('sum(case when (oi.order_id!="") then 1 else 0 end) AS totalItems'),
                        \DB::Raw('SUM((select count(ecommerce_order_items.id) from ecommerce_order_items where ecommerce_order_items.order_id = o.id )) as totalItems'),
                        DB::Raw('sum(o.amount) AS totalAmount'),
                        DB::Raw('sum(case when (o.id!="") then 1 else 0 end) AS totalOrders'),
                        DB::Raw('CONCAT(u.name," ", COALESCE(u.last_name,"")) AS username'),
                        DB::Raw('sum(case when (i.status="paid") then i.total else 0 end) AS amountInvoiced')
                    )
                    ->join('users as u','u.id','=','o.created_by')
                    // ->join('ecommerce_order_items as oi','o.id','=','oi.order_id')
                    ->join('model_has_roles as mr','mr.model_id','=','u.id')
                    ->join('roles as r','r.id','=','mr.role_id')
                    ->leftJoin('accounts_invoices as i','o.id','=','i.order_id')
                    ->where('o.organization_id',$organizationId)
                    ->where('r.name',\Config::get('constants.ROLES.SP'))
                    ->where(function ($query) use ($request) {
                        if (!empty($request->toArray())) {
                            if(isset($request->month) && (!empty($request->month) ) ){
                                $query->whereMonth('o.created_at', $request->month);
                            }

                            if(isset($request->year) && (!empty($request->year) ) ){
                                $query->whereYear('o.created_at', $request->year);
                            }
                        }
                    })
                    ->orderBy('o.created_at','desc')
                    ->groupBy('o.created_by')
                    ->paginate();

        $reportData = $orders->toArray();
        $reportData = $reportData['data'];

        $pagination = $this->pagination($orders);


        $data['data'] = $reportData;
        $data['meta'] = $pagination;
        return $this->sendSuccessResponse($data, $this->success);
    }

    public function salesByBuyers(Request $request,$organizationId = 0)
    {   
        $user = Auth::user();
        if($organizationId == 0){
            $organizationId= \Session::get('currentOrganization');
        }

        $orders =   Order::from('ecommerce_orders as o')
                    ->select('u.phone_number as user_mobile','u.shop_name',
                        // DB::Raw('sum(case when (oi.order_id!="") then 1 else 0 end) AS totalItems'),
                        \DB::Raw('SUM((select count(ecommerce_order_items.id) from ecommerce_order_items where ecommerce_order_items.order_id = o.id )) as totalItems'),
                        DB::Raw('sum(o.amount) AS totalAmount'),
                        DB::Raw('sum(case when (o.id!="") then 1 else 0 end) AS totalOrders'),
                        DB::Raw('CONCAT(u.name," ", COALESCE(u.last_name,"")) AS username'),
                        DB::Raw('sum(case when (i.status="paid") then i.total else 0 end) AS amountInvoiced')
                    )
                    ->join('users as u','u.id','=','o.user_id')
                    // ->join('ecommerce_order_items as oi','o.id','=','oi.order_id')
                    ->join('model_has_roles as mr','mr.model_id','=','u.id')
                    ->join('roles as r','r.id','=','mr.role_id')
                    ->leftJoin('accounts_invoices as i','o.id','=','i.order_id')
                    ->where('o.organization_id',$organizationId)
                    ->where('r.name',\Config::get('constants.ROLES.BUYER'))
                    ->where(function ($query) use ($request) {
                        if (!empty($request->toArray())) {
                            if(isset($request->month) && (!empty($request->month) ) ){
                                $query->whereMonth('o.created_at', $request->month);
                            }

                            if(isset($request->year) && (!empty($request->year) ) ){
                                $query->whereYear('o.created_at', $request->year);
                            }
                        }
                    })
                    ->orderBy('o.created_at','desc')
                    ->groupBy('o.user_id')
                    ->paginate();


        $reportData = $orders->toArray();
        $reportData = $reportData['data'];
        $pagination = $this->pagination($orders);


        $data['data'] = $reportData;
        $data['meta'] = $pagination;
        return $this->sendSuccessResponse($data, $this->success);
    }

    public function topCategories(Request $request,$organizationId = 0)
    {

        $user = Auth::user();
        if($organizationId == 0){
            $organizationId= \Session::get('currentOrganization');
        }

        $categories =   ProductCategory::from('ecommerce_category_product as cp')
                        ->select('c.name','c.created_at','c.updated_at','pc.name as parent','c.status','c.parent_id',
                            DB::Raw('sum(case when (cp.product_id!="") then 1 else 0 end) AS totalProducts'),
                            DB::Raw('SUM(quantity) AS TotalQuantity')
                        )
                        ->leftJoin('categories as c','c.id','=','cp.category_id')
                        ->leftJoin('categories as pc','pc.id','=','c.parent_id')
                        ->leftJoin('ecommerce_sku as s','s.product_id','=','cp.product_id')
                        ->join('ecommerce_order_items as oi','oi.sku_code','=','s.code')
                        ->where('c.organization_id',$organizationId)
                        ->groupBy('cp.category_id')
                        ->orderBy('TotalQuantity','desc')
                        ->take(10)
                        ->paginate();

        $reportData = $categories->toArray();
        $reportData = $reportData['data'];
        // $pagination = $this->pagination($categories);


        $data['data'] = $reportData;
        // $data['meta'] = $pagination;
        return $this->sendSuccessResponse($data, $this->success);
    }

    public function salesByProductCategories(Request $request,$organizationId = 0)
    {
        $user = Auth::user();
        if($organizationId == 0){
            $organizationId= \Session::get('currentOrganization');
        };

        $categories =   ProductCategory::from('ecommerce_category_product as cp')
                        ->select('c.id','c.name','c.created_at','c.updated_at','c.status','c.parent_id',
                            DB::Raw('sum(case when (o.id!="") then 1 else 0 end) AS totalOrders'),
                            DB::Raw('sum(oi.amount*oi.quantity) AS orderAmount'),
                            DB::Raw('sum(case when (i.status="paid") then i.total else 0 end) AS amountInvoiced')
                        )
                        ->leftJoin('categories as c','c.id','=','cp.category_id')
                        ->leftJoin('ecommerce_sku as s','s.product_id','=','cp.product_id')
                        ->join('ecommerce_order_items as oi','oi.sku_code','=','s.code')
                        ->join('ecommerce_orders as o','oi.order_id','=','o.id')
                        ->leftJoin('accounts_invoices as i','o.id','=','i.order_id')
                        ->where('c.organization_id',$organizationId)
                        ->where(function ($query) use ($request) {
                            if (!empty($request->toArray())) {
                                if(isset($request->category) && (!empty($request->category) ) ){
                                    $query->where('c.id', $request->category);
                                }

                                if(isset($request->range) && (!empty($request->range) ) ){
                                    if($request->range == '7days'){
                                        $query->whereDate('o.created_at', \Carbon\Carbon::now()->subDays(7));
                                    }elseif($request->range == '30days'){
                                        $query->whereDate('o.created_at', \Carbon\Carbon::now()->subDays(30));
                                    }elseif($request->range == 'current_month'){
                                        $query->whereMonth('o.created_at', date('m'));
                                    }elseif($request->range == 'last_month'){
                                        $query->whereMonth('o.created_at', \Carbon\Carbon::now()->subMonth()->month);   
                                    }elseif ($request->range == 'current_quarter') {
                                        $dates = Helpers:: get_dates_of_quarter($quarter = 'current', $year = null, $format = 'Y-m-d');

                                        $dateFrom =  \Carbon\Carbon::createFromFormat('Y-m-d', $dates['startDate'])->format('Y-m-d');
                                        $dateTo =  \Carbon\Carbon::createFromFormat('Y-m-d', $dates['endDate'])->format('Y-m-d');

                                        $dateTo = date('Y-m-d', strtotime($dateTo . ' +1 day'));
                                        $query->whereBetween('o.created_at', array($dateFrom, $dateTo));
                                    }
                                    elseif ($request->range == 'last_quarter') {
                                        $dates = Helpers:: get_dates_of_quarter($quarter = 'previous', $year = null, $format = 'Y-m-d');

                                        $dateFrom =  \Carbon\Carbon::createFromFormat('Y-m-d', $dates['startDate'])->format('Y-m-d');
                                        $dateTo =  \Carbon\Carbon::createFromFormat('Y-m-d', $dates['endDate'])->format('Y-m-d');

                                        $dateTo = date('Y-m-d', strtotime($dateTo . ' +1 day'));
                                        $query->whereBetween('o.created_at', array($dateFrom, $dateTo));
                                    }

                                }

                                if (isset($request->dateFrom) || isset($request->dateTo)) {

                                    if (isset($request->dateFrom) && isset($request->dateTo)) {

                                        $dateFrom =  \Carbon\Carbon::createFromFormat('m/d/Y', $request->input("dateFrom"))->format('Y-m-d');
                                        $dateTo =  \Carbon\Carbon::createFromFormat('m/d/Y', $request->input("dateTo"))->format('Y-m-d');

                                        $dateTo = date('Y-m-d', strtotime($dateTo . ' +1 day'));
                                        $query->whereBetween('o.created_at', array($dateFrom, $dateTo));
                                    } elseif (isset($request->dateFrom)) {
                                        $dateFrom =  \Carbon\Carbon::createFromFormat('m/d/Y', $request->input("dateFrom"))->format('Y-m-d');
                                        $query->whereDate('o.created_at', '>=', $dateFrom);
                                    } elseif (isset($request->dateTo)) {
                                        $dateTo =  \Carbon\Carbon::createFromFormat('m/d/Y', $request->input("dateTo"))->format('Y-m-d');
                                        $query->whereDate('o.created_at', '<=', $dateTo);
                                    }
                                }
                            }
                        })
                        ->groupBy('c.id')
                        ->paginate();

        $reportData = $categories->toArray();
        $reportData = $reportData['data'];
        $pagination = $this->pagination($categories);


        $data['data'] = $reportData;
        $data['meta'] = $pagination;
        return $this->sendSuccessResponse($data, $this->success);

    }

    public function zeroBillingBuyers(Request $request,$organizationId = 0)
    {
        $authUser = Auth::user();
        if($organizationId == 0){
            $organizationId= \Session::get('currentOrganization');
        }

        $users =   User::from('users as u')
                    ->select('u.id','u.phone_number as user_mobile','u.email','u.file','u.phone_number','u.created_at','u.updated_at','ob.status',
                        DB::Raw('CONCAT(u.name," ", COALESCE(u.last_name,"")) AS username'),
                        DB::Raw('sum(case when (o.created_by!="") then 1 else 0 end) AS totalOrders'),
                    )
                    ->leftJoin('ecommerce_orders as o','u.id','=','o.user_id')
                    ->leftJoin('organization_buyer as ob','u.id','=','ob.buyer_id')
                    ->leftJoin('model_has_roles as mr','mr.model_id','=','u.id')
                    ->leftJoin('roles as r','r.id','=','mr.role_id')
                    ->where('ob.organization_id',$organizationId)
                    ->where('r.name',\Config::get('constants.ROLES.BUYER'))
                    ->where(function ($query) use ($request) {
                        if (!empty($request->toArray())) {
                            if(isset($request->buyer) && (!empty($request->buyer) ) ){
                                $query->where('u.id', $request->buyer);
                            }

                            if(isset($request->range) && (!empty($request->range) ) ){
                                if($request->range == '7days'){
                                    $query->whereDate('o.created_at', \Carbon\Carbon::now()->subDays(7));
                                }elseif($request->range == '30days'){
                                    $query->whereDate('o.created_at', \Carbon\Carbon::now()->subDays(30));
                                }elseif($request->range == 'current_month'){
                                    $query->whereMonth('o.created_at', date('m'));
                                }elseif($request->range == 'last_month'){
                                    $query->whereMonth('o.created_at', \Carbon\Carbon::now()->subMonth()->month);   
                                }elseif ($request->range == 'current_quarter') {
                                    $dates = Helpers:: get_dates_of_quarter($quarter = 'current', $year = null, $format = 'Y-m-d');

                                    $dateFrom =  \Carbon\Carbon::createFromFormat('Y-m-d', $dates['startDate'])->format('Y-m-d');
                                    $dateTo =  \Carbon\Carbon::createFromFormat('Y-m-d', $dates['endDate'])->format('Y-m-d');

                                    $dateTo = date('Y-m-d', strtotime($dateTo . ' +1 day'));
                                    $query->whereBetween('o.created_at', array($dateFrom, $dateTo));
                                }
                                elseif ($request->range == 'last_quarter') {
                                    $dates = Helpers:: get_dates_of_quarter($quarter = 'previous', $year = null, $format = 'Y-m-d');

                                    $dateFrom =  \Carbon\Carbon::createFromFormat('Y-m-d', $dates['startDate'])->format('Y-m-d');
                                    $dateTo =  \Carbon\Carbon::createFromFormat('Y-m-d', $dates['endDate'])->format('Y-m-d');

                                    $dateTo = date('Y-m-d', strtotime($dateTo . ' +1 day'));
                                    $query->whereBetween('o.created_at', array($dateFrom, $dateTo));
                                }

                            }

                            if (isset($request->dateFrom) || isset($request->dateTo)) {

                                if (isset($request->dateFrom) && isset($request->dateTo)) {

                                    $dateFrom =  \Carbon\Carbon::createFromFormat('m/d/Y', $request->input("dateFrom"))->format('Y-m-d');
                                    $dateTo =  \Carbon\Carbon::createFromFormat('m/d/Y', $request->input("dateTo"))->format('Y-m-d');

                                    $dateTo = date('Y-m-d', strtotime($dateTo . ' +1 day'));
                                    $query->whereBetween('o.created_at', array($dateFrom, $dateTo));
                                } elseif (isset($request->dateFrom)) {
                                    $dateFrom =  \Carbon\Carbon::createFromFormat('m/d/Y', $request->input("dateFrom"))->format('Y-m-d');
                                    $query->whereDate('o.created_at', '>=', $dateFrom);
                                } elseif (isset($request->dateTo)) {
                                    $dateTo =  \Carbon\Carbon::createFromFormat('m/d/Y', $request->input("dateTo"))->format('Y-m-d');
                                    $query->whereDate('o.created_at', '<=', $dateTo);
                                }
                            }
                        }
                    })
                    ->groupBy('u.id')
                    ->having('totalOrders', '<', 1)
                    ->paginate();


        if(!empty($users)){
            foreach ($users as $key => $user) {

                $detailLink = url('user/detail/'.$user->id);

                if(!is_null($user->file)){
                    $file = public_path('uploads/users/') . $user->file;
                }

                if(!is_null($user->file) && file_exists($file))
                    $avatar = "<img src=".url('uploads/users/'.$user->file).">";
                else
                    $avatar = "<span>".\Helpers::getAcronym($user->username)."</span>";
                $name = '<a href="'.$detailLink.'"><div class="user-card"><div class="user-avatar bg-primary">'.$avatar.'</div><div class="user-info"><span class="tb-lead">'.ucfirst($user->username).' <span class="dot dot-success d-md-none ml-1"></span></span><span>'.$user->email.' </span></div></div></a>';

                $users[$key]['name'] = $name;

                $users[$key]['created_at'] = date(\Config::get('constants.DATE.DATE_FORMAT') , strtotime($user->created_at));
                $users[$key]['updated_at'] = date(\Config::get('constants.DATE.DATE_FORMAT') , strtotime($user->updated_at));
                
                if($user->status == 1){
                    $statusValue = 'Approved';
                }else{
                    $statusValue = 'Not Approved';
                }

                $value = ($user->status == '1') ? 'badge badge-success' : 'badge badge-danger';
                
                $status = '<span class="tb-sub"><span class="'.$value.'">'.$statusValue.'</span></span>';
                $users[$key]['status'] = $status;

            }
        }

        $reportData = $users->toArray();
        $reportData = $reportData['data'];
        $pagination = $this->pagination($users);


        $data['data'] = $reportData;
        $data['meta'] = $pagination;
        return $this->sendSuccessResponse($data, $this->success);
    }

    public function zeroBillingSalesPerson(Request $request,$organizationId = 0)
    {
        $authUser = Auth::user();
        if($organizationId == 0){
            $organizationId= \Session::get('currentOrganization');
        }

        $users =   User::from('users as u')
                    ->select('u.id','u.phone_number as user_mobile','u.email','u.file','u.phone_number','u.created_at','u.updated_at','u.status',
                        DB::Raw('CONCAT(u.name," ", COALESCE(u.last_name,"")) AS username'),
                        DB::Raw('sum(case when (o.created_by!="") then 1 else 0 end) AS totalOrders')
                    )
                    ->leftJoin('ecommerce_orders as o','u.id','=','o.created_by')
                    ->leftJoin('model_has_roles as mr','mr.model_id','=','u.id')
                    ->leftJoin('roles as r','r.id','=','mr.role_id')
                    ->where('u.organization_id',$organizationId)
                    ->where('r.name',\Config::get('constants.ROLES.SP'))
                    ->where(function ($query) use ($request) {
                        if (!empty($request->toArray())) {
                            if(isset($request->sp) && (!empty($request->sp) ) ){
                                $query->where('u.id', $request->sp);
                            }

                            if(isset($request->range) && (!empty($request->range) ) ){
                                if($request->range == '7days'){
                                    $query->whereDate('o.created_at', \Carbon\Carbon::now()->subDays(7));
                                }elseif($request->range == '30days'){
                                    $query->whereDate('o.created_at', \Carbon\Carbon::now()->subDays(30));
                                }elseif($request->range == 'current_month'){
                                    $query->whereMonth('o.created_at', date('m'));
                                }elseif($request->range == 'last_month'){
                                    $query->whereMonth('o.created_at', \Carbon\Carbon::now()->subMonth()->month);   
                                }elseif ($request->range == 'current_quarter') {
                                    $dates = Helpers:: get_dates_of_quarter($quarter = 'current', $year = null, $format = 'Y-m-d');

                                    $dateFrom =  \Carbon\Carbon::createFromFormat('Y-m-d', $dates['startDate'])->format('Y-m-d');
                                    $dateTo =  \Carbon\Carbon::createFromFormat('Y-m-d', $dates['endDate'])->format('Y-m-d');

                                    $dateTo = date('Y-m-d', strtotime($dateTo . ' +1 day'));
                                    $query->whereBetween('o.created_at', array($dateFrom, $dateTo));
                                }
                                elseif ($request->range == 'last_quarter') {
                                    $dates = Helpers:: get_dates_of_quarter($quarter = 'previous', $year = null, $format = 'Y-m-d');

                                    $dateFrom =  \Carbon\Carbon::createFromFormat('Y-m-d', $dates['startDate'])->format('Y-m-d');
                                    $dateTo =  \Carbon\Carbon::createFromFormat('Y-m-d', $dates['endDate'])->format('Y-m-d');

                                    $dateTo = date('Y-m-d', strtotime($dateTo . ' +1 day'));
                                    $query->whereBetween('o.created_at', array($dateFrom, $dateTo));
                                }

                            }

                            if (isset($request->dateFrom) || isset($request->dateTo)) {

                                if (isset($request->dateFrom) && isset($request->dateTo)) {

                                    $dateFrom =  \Carbon\Carbon::createFromFormat('m/d/Y', $request->input("dateFrom"))->format('Y-m-d');
                                    $dateTo =  \Carbon\Carbon::createFromFormat('m/d/Y', $request->input("dateTo"))->format('Y-m-d');

                                    $dateTo = date('Y-m-d', strtotime($dateTo . ' +1 day'));
                                    $query->whereBetween('o.created_at', array($dateFrom, $dateTo));
                                } elseif (isset($request->dateFrom)) {
                                    $dateFrom =  \Carbon\Carbon::createFromFormat('m/d/Y', $request->input("dateFrom"))->format('Y-m-d');
                                    $query->whereDate('o.created_at', '>=', $dateFrom);
                                } elseif (isset($request->dateTo)) {
                                    $dateTo =  \Carbon\Carbon::createFromFormat('m/d/Y', $request->input("dateTo"))->format('Y-m-d');
                                    $query->whereDate('o.created_at', '<=', $dateTo);
                                }
                            }
                        }
                    })
                    ->groupBy('u.id')
                    ->having('totalOrders', '<', 1)
                    ->paginate();


        if(!empty($users)){
            foreach ($users as $key => $user) {

                $detailLink = url('user/staff/staff-detail/'.$user->id);

                if(!is_null($user->file)){
                    $file = public_path('uploads/users/') . $user->file;
                }

                if(!is_null($user->file) && file_exists($file))
                    $avatar = "<img src=".url('uploads/users/'.$user->file).">";
                else
                    $avatar = "<span>".\Helpers::getAcronym($user->username)."</span>";
                $name = '<a href="'.$detailLink.'"><div class="user-card"><div class="user-avatar bg-primary">'.$avatar.'</div><div class="user-info"><span class="tb-lead">'.ucfirst($user->username).' <span class="dot dot-success d-md-none ml-1"></span></span><span>'.$user->email.' </span></div></div></a>';

                $users[$key]['name'] = $name;

                $users[$key]['created_at'] = date(\Config::get('constants.DATE.DATE_FORMAT') , strtotime($user->created_at));
                $users[$key]['updated_at'] = date(\Config::get('constants.DATE.DATE_FORMAT') , strtotime($user->updated_at));
                
                if($user->status == 1){
                    $statusValue = 'Approved';
                }else{
                    $statusValue = 'Not Approved';
                }

                $value = ($user->status == '1') ? 'badge badge-success' : 'badge badge-danger';
                
                $status = '<span class="tb-sub"><span class="'.$value.'">'.$statusValue.'</span></span>';
                $users[$key]['status'] = $status;

            }
        }
        
        $reportData = $users->toArray();
        $reportData = $reportData['data'];
        $pagination = $this->pagination($users);


        $data['data'] = $reportData;
        $data['meta'] = $pagination;
        return $this->sendSuccessResponse($data, $this->success);
    }

    public function targetAchievementForBuyer(Request $request,$organizationId = 0)
    {
        $user = Auth::user();
        if($organizationId == 0){
            $currentOrganization = \Session::get('currentOrganization');
            $organizationId= \Session::get('currentOrganization');
        }

        $targets =  Target::from('targets as t')
                    ->select('t.user_id','t.month','t.year','t.total_sales','t.total_line_items','t.total_orders','s.name as state','c.name as city','d.name as district',
                        DB::Raw('CONCAT(u.name," ", COALESCE(u.last_name,"")) AS username'),
                        DB::Raw('sum(case when (o.user_id!="") then 1 else 0 end) AS achivedOrders'),
                        DB::Raw('sum(case when (o.user_id!="") then o.amount else 0 end) AS achivedSales'),
                        // DB::Raw('sum(case when (oi.order_id!="") then 1 else 0 end) AS achivedItems')
                        \DB::Raw('SUM((select sum(ecommerce_order_items.quantity) from ecommerce_order_items where ecommerce_order_items.order_id = o.id )) as achivedItems')
                    )
                    ->join('users as u','u.id','=','t.user_id')
                    ->join('model_has_roles as mr','mr.model_id','=','u.id')
                    ->join('roles as r','mr.role_id','=','r.id')
                    ->leftJoin('ecommerce_orders as o','u.id','=','o.user_id')
                    // ->leftJoin('ecommerce_order_items as oi','o.id','=','oi.order_id')
                    ->leftJoin('states as s','s.id','=','u.state')
                    ->leftJoin('cities as c','c.id','=','u.city')
                    ->leftJoin('districts as d','d.id','=','u.district')
                    ->where('r.name',\Config::get('constants.ROLES.BUYER'))
                    ->where('t.organization_id',$organizationId)

                    ->where(function ($query) use ($request) {
                        if(isset($request->month) || isset($request->year) || isset($request->buyer)|| isset($request->state)|| isset($request->district) || isset($request->city)) {
                            $this->targetFilters($request, $query);
                        }else{
                            $query->where('t.month',date('m'));
                            $query->where('t.year',date('Y'));
                            $query->whereMonth('o.created_at',date('m'));
                            $query->whereYear('o.created_at',date('Y'));
                        }
                    })

                    ->groupBy('u.id')
                    ->paginate();

        if(!empty($targets)){
            foreach ($targets as $key => $target) {
                $month = date('F',strtotime('2020-'.$target->month.'-01'));
                $targets[$key]['period'] = $month.'-'.$target->year;
            }
        }

        $reportData = $targets->toArray();
        $reportData = $reportData['data'];
        $pagination = $this->pagination($targets);

        if(isset($request->month)){
            $period = date('F-Y',strtotime($request->year.'-'.$request->month.'-01'));
            $data['note'] = 'Showing results for '.$period;
        }else{
            $period = date('F-Y');
            $data['note'] = 'Showing results for '.$period;
        }


        $data['data'] = $reportData;
        $data['meta'] = $pagination;
        return $this->sendSuccessResponse($data, $this->success);
    }

    public function targetchievementSetOfFieldTeam(Request $request,$organizationId = 0)
    {
        $user = Auth::user();
        if($organizationId == 0){
            $organizationId= \Session::get('currentOrganization');
        }
        $targets =  Target::from('targets as t')
                    ->select('t.user_id','t.month','t.year','t.total_sales','t.total_line_items','t.total_orders','s.name as state','c.name as city','d.name as district',
                        DB::Raw('CONCAT(u.name," ", COALESCE(u.last_name,"")) AS username'),
                        DB::Raw('sum(case when (o.created_by!="") then 1 else 0 end) AS achivedOrders'),
                        DB::Raw('sum(case when (o.created_by!="") then o.amount else 0 end) AS achivedSales'),
                        // DB::Raw('sum(case when (oi.order_id!="") then 1 else 0 end) AS achivedItems')
                        \DB::Raw('SUM((select sum(ecommerce_order_items.quantity) from ecommerce_order_items where ecommerce_order_items.order_id = o.id )) as achivedItems')
                    )
                    ->join('users as u','u.id','=','t.user_id')
                    ->join('model_has_roles as mr','mr.model_id','=','u.id')
                    ->join('roles as r','mr.role_id','=','r.id')
                    ->leftJoin('ecommerce_orders as o','u.id','=','o.created_by')
                    // ->leftJoin('ecommerce_order_items as oi','o.id','=','oi.order_id')
                    ->leftJoin('states as s','s.id','=','u.state')
                    ->leftJoin('cities as c','c.id','=','u.city')
                    ->leftJoin('districts as d','d.id','=','u.district')
                    ->where('r.name',\Config::get('constants.ROLES.SP'))
                    ->where('t.organization_id',$organizationId)

                    ->where(function ($query) use ($request) {
                        if(isset($request->month) || isset($request->year) || isset($request->salesPerson)|| isset($request->state)|| isset($request->district) || isset($request->city)) {
                            $this->targetFilters($request, $query);
                        }else{
                            $query->where('t.month',date('m'));
                            $query->where('t.year',date('Y'));
                            $query->whereMonth('o.created_at',date('m'));
                            $query->whereYear('o.created_at',date('Y'));
                        }
                    })
                    ->groupBy('u.id')
                    ->paginate();

        if(!empty($targets)){
            foreach ($targets as $key => $target) {
                $month = date('F',strtotime('2020-'.$target->month.'-01'));
                $targets[$key]['period'] = $month.'-'.$target->year;
            }
        }

        $reportData = $targets->toArray();
        $reportData = $reportData['data'];
        $pagination = $this->pagination($targets);

        if(isset($request->month)){
            $period = date('F-Y',strtotime($request->year.'-'.$request->month.'-01'));
            $data['note'] = 'Showing results for '.$period;
        }else{
            $period = date('F-Y');
            $data['note'] = 'Showing results for '.$period;
        }


        $data['data'] = $reportData;
        $data['meta'] = $pagination;
        return $this->sendSuccessResponse($data, $this->success);
    }

    public function targetFilters(Request $request,$query,$organizationId = 0)
    {
        if (isset($request->month)) {
            $query->where('t.month',$request->month);
        }

        if (isset($request->year)) {
            $query->where('t.year',$request->year);
        }

        if (isset($request->buyer)) {
            $query->where('t.user_id',$request->buyer);
        }

        if (isset($request->salesPerson)) {
            $query->where('t.user_id',$request->salesPerson);
        }

        if (isset($request->state)) {
            $query->where('u.state',$request->state);
        }

        if (isset($request->district)) {
            $query->where('u.district',$request->district);
        }

        if (isset($request->city)) {
            $query->where('u.city',$request->city);
        }

        return $query;
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function topBuyers(Request $request,$organizationId = 0)
    {
        $user = Auth::user();

        if($organizationId == 0){
            $organizationId= \Session::get('currentOrganization');
        }

        $buyers =   Order::from('ecommerce_orders as o')
                    ->select('o.user_id','u.phone_number as user_mobile',
                        // DB::Raw('sum(case when (oi.order_id!="") then 1 else 0 end) AS totalItems'),
                        DB::Raw('sum(o.amount) AS totalAmount'),
                        DB::Raw('sum(case when (o.id!="") then 1 else 0 end) AS totalOrders'),
                        DB::Raw('CONCAT(u.name," ", COALESCE(u.last_name,"")) AS username'),
                        DB::Raw('sum(case when (i.status="paid") then i.total else 0 end) AS amountInvoiced')
                    )
                    ->join('users as u','u.id','=','o.user_id')
                    // ->join('ecommerce_order_items as oi','o.id','=','oi.order_id')
                    ->join('model_has_roles as mr','mr.model_id','=','u.id')
                    ->join('roles as r','r.id','=','mr.role_id')
                    ->leftJoin('accounts_invoices as i','o.id','=','i.order_id')
                    ->where('o.organization_id',$organizationId)
                    ->where('r.name',\Config::get('constants.ROLES.BUYER'))
                    ->when(!empty($request->toArray()), function ($query) use ($request) {
                        if(isset($request->month) && (!empty($request->month) ) ){
                            $query->whereMonth('o.created_at', $request->month);
                        }

                        if(isset($request->year) && (!empty($request->year) ) ){
                            $query->whereYear('o.created_at', $request->year);
                        }
                    }, function ($query) {
                        $query->whereMonth('o.created_at', date('m'));
                        $query->whereYear('o.created_at', date('Y'));
                    })
                    ->orderBy('totalAmount','desc')
                    ->groupBy('u.id')
                    ->take(10)
                    ->get();

        $reportData = $buyers->toArray();
        // $reportData = $reportData['data'];
        // $pagination = $this->pagination($buyers);


        $data['data'] = $reportData;
        // $data['meta'] = $pagination;
        return $this->sendSuccessResponse($data, $this->success);
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function topSalesPerson(Request $request,$organizationId = 0)
    {
        $user = Auth::user();

        if($organizationId == 0){
            $organizationId= \Session::get('currentOrganization');
        }

        $salesPerson =   Order::from('ecommerce_orders as o')
                    ->select('o.created_by','u.phone_number as user_mobile',
                        // DB::Raw('sum(case when (oi.order_id!="") then 1 else 0 end) AS totalItems'),
                        DB::Raw('sum(o.amount) AS totalAmount'),
                        DB::Raw('sum(case when (o.id!="") then 1 else 0 end) AS totalOrders'),
                        DB::Raw('CONCAT(u.name," ", COALESCE(u.last_name,"")) AS username'),
                        DB::Raw('sum(case when (i.status="paid") then i.total else 0 end) AS amountInvoiced')
                    )
                    ->join('users as u','u.id','=','o.created_by')
                    // ->join('ecommerce_order_items as oi','o.id','=','oi.order_id')
                    ->join('model_has_roles as mr','mr.model_id','=','u.id')
                    ->join('roles as r','r.id','=','mr.role_id')
                    ->leftJoin('accounts_invoices as i','o.id','=','i.order_id')
                    ->where('o.organization_id',$organizationId)
                    ->where('r.name',\Config::get('constants.ROLES.SP'))
                    ->when(!empty($request->toArray()), function ($query) use ($request) {
                        if(isset($request->month) && (!empty($request->month) ) ){
                            $query->whereMonth('o.created_at', $request->month);
                        }

                        if(isset($request->year) && (!empty($request->year) ) ){
                            $query->whereYear('o.created_at', $request->year);
                        }
                    }, function ($query) {
                        $query->whereMonth('o.created_at', date('m'));
                        $query->whereYear('o.created_at', date('Y'));
                    })
                    ->orderBy('totalAmount','desc')
                    ->groupBy('u.id')
                    ->take(10)
                    ->paginate();

        $reportData = $salesPerson->toArray();
        $reportData = $reportData['data'];
        // $pagination = $this->pagination($salesPerson);


        $data['data'] = $reportData;
        // $data['meta'] = $pagination;
        return $this->sendSuccessResponse($data, $this->success);
    }


    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function zeroBillingItems(Request $request,$organizationId = 0)
    {

        $user = Auth::user();
        if($organizationId == 0){
            $organizationId= \Session::get('currentOrganization');
        }

        $products = Product::from('ecommerce_products as p')
                    ->select('p.organization_id as organization_id','p.type as type','p.id as id','p.name as name',DB::raw('group_concat(DISTINCT cat.name SEPARATOR ", ") as categories'),'sku.sale_price as price','sku.code as sku','p.status as status','p.created_at as created_at','brand.name as brand',
                        DB::Raw('IFNULL(SUM(quantity), 0) AS TotalQuantity')

                    )
                    ->leftJoin('ecommerce_category_product as pro_cat','pro_cat.product_id','=','p.id')
                    ->leftJoin('categories as cat','cat.id','=','pro_cat.category_id')
                    ->leftJoin('ecommerce_product_brand as pro_brand','pro_brand.product_id','=','p.id')
                    ->leftJoin('ecommerce_brands as brand','brand.id','=','pro_brand.brand_id')
                    ->leftJoin('ecommerce_sku as sku','sku.product_id','=','p.id')
                    ->leftJoin('ecommerce_order_items as oi','oi.sku_code','=','sku.code')
                    ->where('p.organization_id',$organizationId)
                    ->whereNull('p.deleted_at')
                    ->where(function ($query) use ($request) {
                        if (!empty($request->toArray())) {
                            if ($request->get('productName') != '') {
                                $query->where('p.name', 'like', '%' . $request->get('productName') . '%');
                            }
                            if ($request->get('status') != '') {
                                $query->where('p.status', $request->get('status'));
                            }
                            if ($request->get('brand') != '') {
                                $brand = implode(',', $request->brand);
                                if(!empty($brand)){
                                    $brand = explode(',', $brand);
                                    $query->whereIn('pro_brand.brand_id', $brand);
                                }
                            }
                            if ($request->get('model') != '') {
                                $model = implode(',', $request->model);
                                if(!empty($model)){
                                    $model = explode(',', $model);
                                    $query->whereIn('pro_model.model_id', $model);
                                }
                            }
                            if ($request->get('category') != '') {
                                $category = implode(',', $request->category);
                                if(!empty($category)){
                                    $category = explode(',', $category);
                                    $query->whereIn('pro_cat.category_id', $category);
                                }
                            }
                        }
                    })
                    ->groupBy('p.id')
                    ->having('TotalQuantity', '<', 1)
                    ->orderBy('p.id','DESC')
                    ->paginate();

        $reportData = $products->toArray();
        $reportData = $reportData['data'];
        $pagination = $this->pagination($products);


        $data['data'] = $reportData;
        $data['meta'] = $pagination;
        return $this->sendSuccessResponse($data, $this->success);
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function pendingSalesOrder(Request $request,$organizationId = 0)
    {

        $user = Auth::user();
        if($organizationId == 0){
            $organizationId= \Session::get('currentOrganization');
        }

        $orders = Order::from('ecommerce_orders as o')
        ->select(DB::Raw('CONCAT(u.name," ", COALESCE(u.last_name,"")) AS username'),'o.order_number','o.amount','o.status','o.user_id','u.shop_name','o.created_at as order_date','u.file')
        ->leftJoin('accounts_invoices as i','o.id','=','i.order_id')
        ->join('users as u','u.id','=','o.user_id')
        ->when(!empty($request->toArray()), function ($query) use ($request) {
            if(isset($request->month) && (!empty($request->month) ) ){
                $query->whereMonth('o.created_at', $request->month);
            }

            if(isset($request->year) && (!empty($request->year) ) ){
                $query->whereYear('o.created_at', $request->year);
            }
        }, function ($query) {
            $query->whereMonth('o.created_at', date('m'));
            $query->whereYear('o.created_at', date('Y'));
        })
        ->whereIn('o.status',['unapproved','accepted','processing'])
        ->orderBy('o.created_at','desc')
        ->paginate();

        if($orders->count() > 0){
            foreach ($orders as $key => $order) {
                $detailLink = url('user/detail/'.$order->user_id);

                if(!is_null($order->file)){
                    $file = public_path('uploads/users/') . $order->file;
                }

                if(!is_null($order->file) && file_exists($file))
                    $avatar = "<img src=".url('uploads/users/'.$order->file).">";
                else
                    $avatar = "<span>".\Helpers::getAcronym($order->username)."</span>";
                $name = '<a href="'.$detailLink.'"><div class="user-card"><div class="user-avatar bg-primary">'.$avatar.'</div><div class="user-info"><span class="tb-lead">'.ucfirst($order->username).' <span class="dot dot-success d-md-none ml-1"></span></span><span>'.$order->shop_name.' </span></div></div></a>';

                $orders[$key]['username'] = $name;
            }
        }

        $reportData = $orders->toArray();
        $reportData = $reportData['data'];
        $pagination = $this->pagination($orders);


        $data['data'] = $reportData;
        $data['meta'] = $pagination;
        return $this->sendSuccessResponse($data, $this->success);
    }

    public function onfieldVisits(Request $request)
    {
        $user = Auth::user();
        $planApprovalRequired = false;
        $setting =  Settings::select('code','value')
                    ->where('code','plan_approval_required')
                    ->first();

        if($setting->code == 'plan_approval_required' && $setting->value == 'true'){
            $planApprovalRequired = true;
        }
        $visits =    Visit::from('visits as v')
                        ->select('v.id','v.buyer', 'v.dsp', 'v.plan_type', 'v.plan', 'v.plan_comment', 'v.planned_date', 'v.checked_in_at', 'v.checked_out_at', 'v.cancelled_at', 'v.is_system_cancelled', 'v.is_system_checkout', 'r.shop_name', 'r.file',
                            DB::Raw('CONCAT(r.name," ", r.last_name) AS retailerName'),
                            DB::Raw('CONCAT(d.name," ", d.last_name) AS dspName'),
                            DB::Raw('case when (v.checked_in_at != "" AND v.checked_out_at != "") then "Completed" when (v.cancelled_at != "") then "Cancelled" when (v.checked_in_at != "" AND v.checked_out_at IS NULL) then "Checked In" else "Pending" end AS status'),
                            DB::Raw('case when (v.cancelled_at != "" AND v.is_system_cancelled = "1") then "System" when (v.cancelled_at != "" AND v.is_system_cancelled = "0") then "Self" else "" end AS cancelled_by'),
                            DB::Raw('case when (v.checked_out_at != "" AND v.is_system_checkout = "1") then "System" when (v.checked_out_at != "" AND v.is_system_checkout = "0") then "Self" else "" end AS checked_out_by'),
                            DB::Raw('case when (v.checkout_comment != "") then v.checkout_comment when (v.cancel_comment != "") then v.cancel_comment else "" end AS comment'),
                        )
                        ->leftJoin('users as r','r.id','=','v.buyer')
                        ->leftJoin('users as d','d.id','=','v.dsp')
                        ->where('v.plan_type','ONFIELD')
                        ->where(function ($query) use ($planApprovalRequired) {
                            if($planApprovalRequired){
                                $query->whereNotNull('v.approved_at');
                            }
                        })
                        ->where(function ($query) use ($request) {
                            if(isset($request->buyer) || isset($request->dsp) || isset($request->dateFrom) || isset($request->dateTo) || isset($request->range) || isset($request->status)) {
                                $this->visitFilters($request, $query);
                            }
                        })
                        ->orderBy('v.created_at','DESC')
                        ->paginate();

        foreach ($visits as $key => $visit) {
            $detailLink = url('user/detail/'.$visit->buyer);

            if(!is_null($visit->file)){
                $file = public_path('uploads/users/') . $visit->file;
            }

            if(!is_null($visit->file) && file_exists($file))
                $avatar = "<img src=".url('uploads/users/'.$visit->file).">";
            else
                $avatar = "<span>".\Helpers::getAcronym($visit->retailerName)."</span>";
            $name = '<a href="'.$detailLink.'"><div class="user-card"><div class="user-avatar bg-primary">'.$avatar.'</div><div class="user-info"><span class="tb-lead">'.ucfirst($visit->retailerName).' <span class="dot dot-success d-md-none ml-1"></span></span><span>'.$visit->shop_name.' </span></div></div></a>';

            $visits[$key]['retailerName'] = $name;
        }

        $reportData = $visits->toArray();
        $reportData = $reportData['data'];
        $pagination = $this->pagination($visits);

        $data['data'] = $reportData;
        $data['meta'] = $pagination;
        return $this->sendSuccessResponse($data, $this->success);
    }

    public function offfieldVisits(Request $request)
    {
        $user = Auth::user();
        $planApprovalRequired = false;
        $setting =  Settings::select('code','value')
                    ->where('code','plan_approval_required')
                    ->first();

        if($setting->code == 'plan_approval_required' && $setting->value == 'true'){
            $planApprovalRequired = true;
        }

        $visits =    Visit::from('visits as v')
                        ->select('v.id','v.buyer', 'v.dsp', 'v.plan_type', 'v.plan', 'v.plan_comment', 'v.planned_date', 'v.checked_in_at', 'v.checked_out_at', 'v.cancelled_at', 'v.is_system_cancelled', 'v.is_system_checkout',
                            DB::Raw('CONCAT(d.name," ", d.last_name) AS dspName'),
                            DB::Raw('case when (v.checked_in_at != "" AND v.checked_out_at != "") then "Completed" when (v.cancelled_at != "") then "Cancelled" when (v.checked_in_at != "" AND v.checked_out_at IS NULL) then "Checked In" else "Pending" end AS status'),
                            DB::Raw('case when (v.cancelled_at != "" AND v.is_system_cancelled = "1") then "System" when (v.cancelled_at != "" AND v.is_system_cancelled = "0") then "Self" else "" end AS cancelled_by'),
                            DB::Raw('case when (v.checked_out_at != "" AND v.is_system_checkout = "1") then "System" when (v.checked_out_at != "" AND v.is_system_checkout = "0") then "Self" else "" end AS checked_out_by'),
                            DB::Raw('case when (v.checkout_comment != "") then v.checkout_comment when (v.cancel_comment != "") then v.cancel_comment else "" end AS comment'),
                        )
                        ->leftJoin('users as d','d.id','=','v.dsp')
                        ->where('v.plan_type','OFFFIELD')
                        ->where(function ($query) use ($planApprovalRequired) {
                            if($planApprovalRequired){
                                $query->whereNotNull('v.approved_at');
                            }
                        })
                        ->where(function ($query) use ($request) {
                            if(isset($request->dsp) || isset($request->dateFrom) || isset($request->dateTo) || isset($request->range) || isset($request->plan)) {
                                $this->visitFilters($request, $query);
                            }
                        })
                        ->orderBy('v.created_at','DESC')
                        ->paginate();

        $reportData = $visits->toArray();
        $reportData = $reportData['data'];
        $pagination = $this->pagination($visits);

        $data['data'] = $reportData;
        $data['meta'] = $pagination;
        return $this->sendSuccessResponse($data, $this->success);
        return view('report::off_field_visit');
    }

    public function visitFilters(Request $request,$query)
    {
        if (isset($request->buyer)) {
            $query->where('v.buyer',$request->buyer);
        }

        if (isset($request->dsp)) {
            $query->where('v.dsp',$request->dsp);
        }

        if(isset($request->plan) && (!empty($request->plan)) ){
            $query->where('plan',$request->plan);
        }

        if(isset($request->status) && (!empty($request->status)) ){

            if($request->status == 'pending'){
                $query->whereNull('v.checked_in_at');
                $query->whereNull('v.checked_out_at');
                $query->whereNull('v.cancelled_at');
            }
            elseif($request->status == 'completed'){
                $query->whereNotNull('v.checked_in_at');
                $query->whereNotNull('v.checked_out_at');
            }
            elseif($request->status == 'checkedIn'){
                $query->whereNotNull('v.checked_in_at');
                $query->whereNull('v.checked_out_at');
            }
            elseif($request->status == 'cancelled'){
                $query->whereNotNull('v.cancelled_at');
            }

        }

        if (isset($request->dateFrom) || isset($request->dateTo)) {
            if (isset($request->dateFrom) && isset($request->dateTo)) {

                $dateFrom =  \Carbon\Carbon::createFromFormat('m/d/Y', $request->input("dateFrom"))->format('Y-m-d');
                $dateTo =  \Carbon\Carbon::createFromFormat('m/d/Y', $request->input("dateTo"))->format('Y-m-d');

                $dateTo = date('Y-m-d', strtotime($dateTo . ' +1 day'));
                $query->whereBetween('v.planned_date', array($dateFrom, $dateTo));
            } elseif (isset($request->dateFrom)) {
                $dateFrom =  \Carbon\Carbon::createFromFormat('m/d/Y', $request->input("dateFrom"))->format('Y-m-d');
                $query->whereDate('v.planned_date', '>=', $dateFrom);
            } elseif (isset($request->dateTo)) {
                $dateTo =  \Carbon\Carbon::createFromFormat('m/d/Y', $request->input("dateTo"))->format('Y-m-d');
                $query->whereDate('v.planned_date', '<=', $dateTo);
            }
        }

        if(isset($request->range) && (!empty($request->range) ) ){
            if($request->range == 'today'){
                $query->whereDate('v.planned_date', date('Y-m-d'));
            }elseif($request->range == 'this_week'){
                $query->where('v.planned_date', '>=', \Carbon\Carbon::now()->startOfWeek());
                $query->where('v.planned_date', '<=', \Carbon\Carbon::now()->endOfWeek());
            }elseif($request->range == 'this_month'){
                $query->whereMonth('v.planned_date', date('m'));
            }
        }

        return $query;
    }

    /**
     * Gather the meta data for the response.
     *
     * @param  LengthAwarePaginator  $paginated
     * @return array
     */
    public function pagination($paginated)
    {
        return [
            'pagination' => [
                'total' => $paginated->total(),
                'count' =>$paginated->count(),
                'per_page' => $paginated->perPage(),
                'current_page' => $paginated->currentPage(),
                'last' => $paginated->lastPage(),
                'links' =>  array(
                                'next' => $paginated->nextPageUrl(),
                                'prev' => $paginated->previousPageUrl(),
                            )
            ]
        ];
    }
}
