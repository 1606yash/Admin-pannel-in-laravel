<?php

namespace Modules\Dashboard\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use URL;
use Auth;
use DB;
use Helpers;
use Modules\Saas\Entities\Organization;
use Modules\User\Entities\User;
use Modules\User\Entities\OrganizationBuyer;
use Modules\User\Entities\RetailerCategories;




class DashboardController extends Controller
{

    public function __construct() {

        /* Execute authentication filter before processing any request */
        $this->middleware('auth');

        if (\Auth::check()) {
            return redirect('/');
        }

    }
    
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $organizationId=$user->organization_id;
        
        $month = date('m');
        $year = date('Y');
        if(isset($request->month)){
            $month = $request->month;
        }
        if(isset($request->year)){
            $year = $request->year;
        }

        $orderByStatus =    array(
                                    'unapproved'    =>  array(
                                                            'amount' => 0,
                                                            'orders' => 0
                                                        ),
                                    'accepted'      =>  array(
                                                            'amount' => 0,
                                                            'orders' => 0
                                                        ),
                                    'processing'    =>  array(
                                                            'amount' => 0,
                                                            'orders' => 0
                                                        ),
                                    'declined'      =>  array(
                                                            'amount' => 0,
                                                            'orders' => 0
                                                        ),
                                    'cancelled'     =>  array(
                                                            'amount' => 0,
                                                            'orders' => 0
                                                        ),
                                    'invoiced'      =>  array(
                                                            'amount' => 0,
                                                            'orders' => 0
                                                        ),
                                    'shipped'       =>  array(
                                                            'amount' => 0,
                                                            'orders' => 0
                                                        ),
                                    'completed'     =>  array(
                                                            'amount' => 0,
                                                            'orders' => 0
                                                        )
                                );

        // $statusOrders =     Order::select(\DB::Raw('count(*) as totalOrders'),
        //                             \DB::Raw('COALESCE(SUM(amount),0) as totalSales'),'status')
        //                             // ->where('organization_id',$user->organization_id)
        //                             ->whereMonth('created_at',$month)
        //                             ->whereYear('created_at',$year)
        //                             ->groupBy('status')
        //                             ->get();
        // if(!empty($statusOrders->toArray())){
        //     foreach ($statusOrders as $key => $o) {
        //            $orderByStatus[$o->status]['amount'] = $o->totalSales;
        //            $orderByStatus[$o->status]['orders'] = $o->totalOrders;
        //        }   
        // }
        
        // $orders =   Order::select(\DB::Raw('count(*) as totalOrders'),\DB::Raw('COALESCE(SUM(amount),0) as totalSales'),\DB::Raw('COALESCE(ROUND(AVG(amount),2),0) AS averageOrder'))
        //             ->where('status', 'completed')
        //             ->first();

        // $today_order =  Order::select(\DB::Raw('count(*) as todayOrders'),\DB::Raw('COALESCE(SUM(amount),0) as todayAmount'))
        //                 ->where('status','completed')
        //                 ->whereDate('created_at', \Carbon\Carbon::today())->get()
        //                 ->first();


        // $week_order =  Order::select(\DB::Raw('count(*) as weekOrders'),\DB::Raw('COALESCE(SUM(amount),0) as weekAmount'))
        //             ->where('status','completed')
        //             ->where('created_at', '>', \Carbon\Carbon::now()->startOfWeek())
        //             ->where('created_at', '<', \Carbon\Carbon::now()->endOfWeek())
        //             ->first();

        // $month_order =  Order::select(\DB::Raw('count(*) as monthOrders'),\DB::Raw('COALESCE(SUM(amount),0) as monthAmount'))
        //                 ->where('status','completed')
        //                 ->whereMonth('created_at',$month)
        //                 ->whereYear('created_at',$year)
        //                 ->first();

        // $totalDsp =  User::from('users as u')
        //             ->join('model_has_roles as mr','mr.model_id','=','u.id')
        //             ->join('roles as r','r.id','=','mr.role_id')
        //             ->where('r.name',\Config::get('constants.ROLES.SP'))
        //             ->count();
        // $dspOrders =   Order::from('ecommerce_orders as o')
        //                 ->select(
        //                     DB::Raw('sum(o.amount) AS totalAmount'),
        //                     DB::Raw('sum(case when (o.id!="") then 1 else 0 end) AS totalOrders'),
        //                     DB::Raw('group_concat(o.created_by) AS dsps')
        //                 )
        //                 ->join('users as u','u.id','=','o.created_by')
        //                 ->join('model_has_roles as mr','mr.model_id','=','u.id')
        //                 ->join('roles as r','r.id','=','mr.role_id')
        //                 ->where('r.name',\Config::get('constants.ROLES.SP'))
        //                 ->whereMonth('o.created_at',$month)
        //                 ->whereYear('o.created_at',$year)
        //                 ->orderBy('o.created_at','desc')
        //                 ->first();

        // $dsp_today_order =  Order::from('ecommerce_orders as o')
        //                     ->select(\DB::Raw('count(*) as todayOrders'),\DB::Raw('COALESCE(SUM(amount),0) as todayAmount'))
        //                     ->join('users as u','u.id','=','o.created_by')
        //                     ->join('model_has_roles as mr','mr.model_id','=','u.id')
        //                     ->join('roles as r','r.id','=','mr.role_id')
        //                     ->where('r.name',\Config::get('constants.ROLES.SP'))
        //                     ->where('o.status','completed')
        //                     ->whereDate('o.created_at', \Carbon\Carbon::today())->get()
        //                     ->first();


        // $dsp_week_order =   Order::from('ecommerce_orders as o')
        //                     ->select(\DB::Raw('count(*) as weekOrders'),\DB::Raw('COALESCE(SUM(amount),0) as weekAmount'))
        //                     ->join('users as u','u.id','=','o.created_by')
        //                     ->join('model_has_roles as mr','mr.model_id','=','u.id')
        //                     ->join('roles as r','r.id','=','mr.role_id')
        //                     ->where('r.name',\Config::get('constants.ROLES.SP'))
        //                     ->where('o.status','completed')
        //                     ->where('o.created_at', '>', \Carbon\Carbon::now()->startOfWeek())
        //                     ->where('o.created_at', '<', \Carbon\Carbon::now()->endOfWeek())
        //                     ->first();

        // if($dspOrders){
        //     $totalorderdsps = count(array_unique(explode(',', $dspOrders->dsps)));
        //     $averageDspOrder = $dspOrders->totalAmount/$totalorderdsps;
        //     $dspOrders->averageDspOrder = number_format((float) $averageDspOrder, 2, '.', '');
        //     if($totalDsp > $totalorderdsps){
        //         $dspOrders->zeroBillingDsp = $totalDsp-$totalorderdsps;
        //     }else{
        //         $dspOrders->zeroBillingDsp = 0;
        //     }
        // }else{
        //     $dspOrders->zeroBillingDsp = $totalDsp;
        //     $dspOrders->averageDspOrder = 0;
        // }

        // $targets =  Target::from('targets as t')
        //             ->select('t.user_id','t.month','t.year','t.total_sales','t.total_line_items','t.total_orders','r.name as roleName',
        //                 DB::Raw('CONCAT(u.name," ", COALESCE(u.last_name,"")) AS username'),
        //                 DB::Raw('sum(case when (o.created_by!="") then 1 else 0 end) AS achivedOrders'),
        //                 DB::Raw('sum(case when (o.created_by!="") then o.amount else 0 end) AS achivedSales'),
        //                 \DB::Raw('SUM((select count(ecommerce_order_items.id) from ecommerce_order_items where ecommerce_order_items.order_id = o.id )) as achivedItems')
        //             )
        //             ->join('users as u','u.id','=','t.user_id')
        //             ->join('model_has_roles as mr','mr.model_id','=','u.id')
        //             ->join('roles as r','mr.role_id','=','r.id')
        //             ->leftJoin('ecommerce_orders as o', function($join)
        //             {
        //                 $join->on('t.user_id','=','o.created_by');
        //                 $join->on('t.month','=',\DB::Raw('MONTH(o.created_at)'));
        //                 $join->on('t.year','=',\DB::Raw('YEAR(o.created_at)'));
        //             })
        //             ->where('r.name',\Config::get('constants.ROLES.SP'))
        //             // ->where('t.organization_id',$user->organization_id)
        //             ->where('t.month',$month)
        //             ->where('t.year',$year)
        //             ->groupBy('u.id')
        //             ->get();

        // $targetsAchieved = $targetsFailed = 0; 

        // if(!empty($targets->toArray())){
        //     foreach ($targets as $key => $target) {
        //         if($target->achivedSales >= $target->total_sales || $target->achivedItems >= $target->total_line_items || $target->achivedOrders >= $target->total_orders){
        //             $targetsAchieved ++;
        //         }else{
        //             $targetsFailed ++;
        //         }
        //     }
        // }

        $totalBuyers =  0;
        // $buyerOrders =   Order::from('ecommerce_orders as o')
        //                 ->select(
        //                     DB::Raw('sum(o.amount) AS totalAmount'),
        //                     DB::Raw('sum(case when (o.id!="") then 1 else 0 end) AS totalOrders'),
        //                     // DB::Raw('sum(case when (i.status="paid") then i.total else 0 end) AS amountInvoiced'),
        //                     DB::Raw('group_concat(o.user_id) AS buyers')
        //                 )
        //                 ->join('users as u','u.id','=','o.user_id')
        //                 ->join('model_has_roles as mr','mr.model_id','=','u.id')
        //                 ->join('roles as r','r.id','=','mr.role_id')
        //                 // ->leftJoin('accounts_invoices as i','o.id','=','i.order_id')
        //                 ->where('r.name',\Config::get('constants.ROLES.BUYER'))
        //                 ->whereMonth('o.created_at',$month)
        //                 ->whereYear('o.created_at',$year)
        //                 ->orderBy('o.created_at','desc')
        //                 ->first();

        // if($buyerOrders){
        //     if(!empty($buyerOrders->buyers)){
        //         $totalorderbuyers = count(array_unique(explode(',', $buyerOrders->buyers)));
        //         $averageBuyerOrder = $buyerOrders->totalAmount/$totalorderbuyers;
        //     }else{
        //         $totalorderbuyers = 0;
        //         $averageBuyerOrder = 0;
        //     }

        //     $buyerOrders->averageBuyerOrder = number_format((float) $averageBuyerOrder, 2, '.', '');
        //     if($totalBuyers > $totalorderbuyers){
        //         $buyerOrders->zeroBillingBuyer = $totalBuyers-$totalorderbuyers;
        //     }else{
        //         $buyerOrders->zeroBillingBuyer = 0;
        //     }
        // }else{
        //     $buyerOrders->zeroBillingBuyer = $totalDsp;
        //     $buyerOrders->averageBuyerOrder = 0;
        // }

        $monthRetailers =    0;

        $weekRetailers =   0;

        $todayRetailers =   0;

        $categoryBuyers =  0;

        // foreach ($categoryBuyers as $key => $categoryBuyer) {
        //     $categoryBuyer->totalAmount = 0;
        //     $categoryBuyer->totalOrders = 0;
        //     $catBuyers = explode(',', $categoryBuyer->buyers);

        //     if(!is_null($categoryBuyer->buyers)){
        //         $catOrders =   Order::from('ecommerce_orders as o')
        //                 ->select(
        //                     DB::Raw('COALESCE(sum(o.amount),0) AS totalAmount'),
        //                     DB::Raw('COALESCE(sum(case when (o.id!="") then 1 else 0 end),0) AS totalOrders'),
        //                 )
        //                 ->whereIn('o.user_id',$catBuyers)
        //                 ->first();
        //         if($catOrders){
        //             $categoryBuyer->totalAmount = $catOrders->totalAmount;
        //             $categoryBuyer->totalOrders = $catOrders->totalOrders;
        //         }
        //     }
        // }

        // $weekvisits =    Visit::from('visits as v')
        //                 // ->where('v.plan_type',"ONFIELD")
        //                 ->where('planned_date', '>=', \Carbon\Carbon::now()->startOfWeek())
        //                 ->where('planned_date', '<=', \Carbon\Carbon::now()->endOfWeek())
        //                 ->whereNotNull('approved_at')
        //                 ->count();

        // $todayvisits =  Visit::from('visits as v')
        //                 // ->where('v.plan_type',"ONFIELD")
        //                 ->whereDate('planned_date', \Carbon\Carbon::today())->get()
        //                 ->whereNotNull('approved_at')
        //                 ->count();

        // $monthvisitsdata    =   Visit::from('visits as v')
        //                     ->select('v.*')
        //                     ->whereMonth('planned_date', $month)
        //                     ->whereYear('planned_date', $year)
        //                     ->whereNotNull('approved_at')
        //                     ->get();

        // $monthvisits = array(
        //             "totalVisits" => 0,
        //             "totalOnFieldVisits" => 0,
        //             "totalOffFieldVisits" => 0,
        //             "completedOnFieldVisits" => 0,
        //             "cancelledOnFieldVisits" => 0,
        //             "pendingOnFieldVisits" => 0,
        //             "holidays" => 0,
        //             "fullDayOffs" => 0,
        //             "halfDayOffs" => 0,
        //             "ho" => 0,
        //             "productive" => 0,
        //             "nonProductive" => 0,
        //         );

        // if(!empty($monthvisitsdata->toArray())){
        //     $monthvisits['totalVisits'] = count($monthvisitsdata);
        //     foreach ($monthvisitsdata as $key => $visit) {

        //         $productive = $nonProductive = 0;

        //         if($visit->plan_type == 'ONFIELD'){
        //             $monthvisits['totalOnFieldVisits']++;
        //             if($visit->checked_out_at != ""){
        //                 $monthvisits['completedOnFieldVisits']++;
        //                 $checkOrder  =  Order::from('ecommerce_orders as o')
        //                                 ->select(DB::raw("DATE(created_at) as date"),'id','user_id')
        //                                 ->whereDate('created_at',$visit->planned_date)
        //                                 ->where('user_id',$visit->buyer)
        //                                 ->where('o.created_by', $visit->dsp)
        //                                 ->count();
        //                 if($checkOrder > 0){
        //                     $monthvisits['productive']++;
        //                 }else{
        //                     $monthvisits['nonProductive']++;
        //                 }
        //             }

        //             if($visit->cancelled_at != ""){
        //                 $monthvisits['cancelledOnFieldVisits']++;
        //             }
        //             if($visit->checked_in_at == "" && $visit->checked_out_at == "" && $visit->cancelled_at == ""){
        //                 $monthvisits['pendingOnFieldVisits']++;
        //             }

        //         }else{
        //             $monthvisits['totalOffFieldVisits']++;

        //             if($visit->plan == 'FULL_DAY_LEAVE'){
        //                 $monthvisits['fullDayOffs']++;
        //             }
        //             if($visit->plan == 'HOLIDAY'){
        //                 $monthvisits['holidays']++;
        //             }
        //             if($visit->plan == 'HALF_DAY_LEAVE'){
        //                 $monthvisits['halfDayOffs']++;
        //             }
        //             if($visit->plan == 'HO'){
        //                 $monthvisits['ho']++;
        //             }
        //         }
        //     }
        // }

        // $monthvisits = json_decode(json_encode($monthvisits), FALSE);


        // $allDspVisits =   Visit::from('visits as v')
        //                 ->select('v.*',DB::Raw('CONCAT(d.name," ", d.last_name) AS dspName'),)
        //                 ->leftJoin('users as d','d.id','=','v.dsp')
        //                 ->whereMonth('planned_date', $month)
        //                 ->whereYear('planned_date', $year)
        //                 ->whereNotNull('v.approved_at')
        //                 ->get();

        // $allDspVisitsData = array();
        // if(!empty($allDspVisits->toArray())){


        //     foreach ($allDspVisits as $key => $dspVisit) {

        //         if(!isset($allDspVisitsData[$dspVisit->dsp])){
        //             $allDspVisitsData[$dspVisit->dsp] = array(
        //                     "sp_id" => $dspVisit->dsp,
        //                     "dspName" => $dspVisit->dspName,
        //                     "totalVisits" => 0,
        //                     "totalOnFieldVisits" => 0,
        //                     "totalOffFieldVisits" => 0,
        //                     "completedOnFieldVisits" => 0,
        //                     "cancelledOnFieldVisits" => 0,
        //                     "pendingOnFieldVisits" => 0,
        //                     "holidays" => 0,
        //                     "fullDayOffs" => 0,
        //                     "halfDayOffs" => 0,
        //                     "ho" => 0,
        //                     "productive" => 0,
        //                     "nonProductive" => 0,
        //                 );
        //         }

        //         $allDspVisitsData[$dspVisit->dsp]['totalVisits'] += 1;

        //         $productive = $nonProductive = 0;
        //         if($dspVisit->plan_type == 'ONFIELD'){
        //             $allDspVisitsData[$dspVisit->dsp]['totalOnFieldVisits']++;
        //             if($dspVisit->checked_out_at != ""){
        //                 $allDspVisitsData[$dspVisit->dsp]['completedOnFieldVisits']++;
        //                 $checkOrder  =  Order::from('ecommerce_orders as o')
        //                                 ->select(DB::raw("DATE(created_at) as date"),'id','user_id')
        //                                 ->whereDate('created_at',$dspVisit->planned_date)
        //                                 ->where('user_id',$dspVisit->buyer)
        //                                 ->where('o.created_by', $dspVisit->dsp)
        //                                 ->count();
        //                 if($checkOrder > 0){
        //                     $allDspVisitsData[$dspVisit->dsp]['productive']++;
        //                 }else{
        //                     $allDspVisitsData[$dspVisit->dsp]['nonProductive']++;
        //                 }
        //             }

        //             if($dspVisit->cancelled_at != ""){
        //                 $allDspVisitsData[$dspVisit->dsp]['cancelledOnFieldVisits']++;
        //             }
        //             if($dspVisit->checked_in_at == "" && $dspVisit->checked_out_at == "" && $dspVisit->cancelled_at == ""){
        //                 $allDspVisitsData[$dspVisit->dsp]['pendingOnFieldVisits']++;
        //             }

        //         }else{
        //             $allDspVisitsData[$dspVisit->dsp]['totalOffFieldVisits']++;

        //             if($dspVisit->plan == 'FULL_DAY_LEAVE'){
        //                 $allDspVisitsData[$dspVisit->dsp]['fullDayOffs']++;
        //             }
        //             if($dspVisit->plan == 'HOLIDAY'){
        //                 $allDspVisitsData[$dspVisit->dsp]['holidays']++;
        //             }
        //             if($dspVisit->plan == 'HALF_DAY_LEAVE'){
        //                 $allDspVisitsData[$dspVisit->dsp]['halfDayOffs']++;
        //             }
        //             if($dspVisit->plan == 'HO'){
        //                 $allDspVisitsData[$dspVisit->dsp]['ho']++;
        //             }
        //         }
        //     }
        // }
        // $dspVisits = json_decode(json_encode($allDspVisitsData), FALSE);
        
        // $categoryurl=URL::to('/').'/uploads/category/';
        // $topCategories =    ProductCategory::from('ecommerce_category_product as cp')
        //                     ->select('c.name',
        //                         DB::Raw('SUM(quantity) AS totalQuantity'),
        //                         \DB::Raw('SUM(amount*quantity) AS totalAmount'),
        //                         \DB::Raw('case when (c.file!="") then CONCAT("'.$categoryurl.'",c.file) else 0 end AS image'),
        //                         \DB::Raw('(select count(prc.category_id) from ecommerce_category_product as prc where prc.category_id = cp.category_id group by prc.category_id) as items')
        //                     )
        //                     ->leftJoin('categories as c','c.id','=','cp.category_id')
        //                     ->leftJoin('ecommerce_sku as s','s.product_id','=','cp.product_id')
        //                     ->join('ecommerce_order_items as oi','oi.sku_code','=','s.code')
        //                     // ->leftJoin('ecommerce_category_product as pro','c.id','=','pro.category_id')
        //                     ->whereMonth('oi.created_at', $month)
        //                     ->whereYear('oi.created_at', $year)
        //                     ->groupBy('cp.category_id')
        //                     ->orderBy('totalQuantity','desc')
        //                     ->take(5)
        //                     ->get();

        // $brandurl        =   URL::to('/').'/uploads/brands/';
        // $topBrands  =   ProductBrand::from('ecommerce_product_brand as pb')
        //                     ->select('b.name',
        //                         \DB::Raw('SUM(quantity) AS totalQuantity'),
        //                         \DB::Raw('SUM(amount*quantity) AS totalAmount'),
        //                         \DB::Raw('case when (b.file!="") then CONCAT("'.$brandurl.'",b.file) else "" end AS image'),
        //                         \DB::Raw('(select count(prb.brand_id) from ecommerce_product_brand as prb where prb.brand_id = pb.brand_id group by prb.brand_id) as items')
        //                     )
        //                     ->leftJoin('ecommerce_brands as b','b.id','=','pb.brand_id')
        //                     ->leftJoin('ecommerce_sku as s','s.product_id','=','pb.product_id')
        //                     ->join('ecommerce_order_items as oi','oi.sku_code','=','s.code')
        //                     // ->where('b.organization_id',$user->organization_id)
        //                     ->whereMonth('oi.created_at', $month)
        //                     ->whereYear('oi.created_at', $year)
        //                     ->groupBy('pb.brand_id')
        //                     ->orderBy('totalQuantity','desc')
        //                     ->take(5)
        //                     ->get();

        // $buyerurl=URL::to('/').'/uploads/users/';
        // $topBuyers =  Order::from('ecommerce_orders as o')
        //     ->select('u.phone_number as user_mobile','u.shop_name',
        //         DB::Raw('sum(o.amount) AS totalAmount'),
        //         DB::Raw('sum(case when (o.id!="") then 1 else 0 end) AS totalOrders'),
        //         DB::Raw('CONCAT(u.name," ", COALESCE(u.last_name,"")) AS username'),
        //         DB::Raw('case when (u.file!="") then CONCAT("'.$buyerurl.'",u.file) else 0 end AS picture')
        //     )
        //     ->join('users as u','u.id','=','o.user_id')
        //     // ->join('ecommerce_order_items as oi','o.id','=','oi.order_id')
        //     ->join('model_has_roles as mr','mr.model_id','=','u.id')
        //     ->join('roles as r','r.id','=','mr.role_id')
        //     // ->where('o.organization_id',$user->organization_id)
        //     ->where('r.name',\Config::get('constants.ROLES.BUYER'))
        //     ->whereMonth('o.created_at', $month)
        //     ->whereYear('o.created_at', $year)
        //     ->orderBy('totalAmount','desc')
        //     ->groupBy('u.id')
        //     ->take(5)
        //     ->get();

        // $spurl=URL::to('/').'/uploads/users/';
        // $topsalesPersons =  Order::from('ecommerce_orders as o')
        //     ->select('u.phone_number as user_mobile',
        //         DB::Raw('sum(o.amount) AS totalAmount'),
        //         DB::Raw('sum(case when (o.id!="") then 1 else 0 end) AS totalOrders'),
        //         DB::Raw('CONCAT(u.name," ", COALESCE(u.last_name,"")) AS username'),
        //         DB::Raw('case when (u.file!="") then CONCAT("'.$spurl.'",u.file) else "" end AS picture')
        //     )
        //     ->join('users as u','u.id','=','o.created_by')
        //     // ->join('ecommerce_order_items as oi','o.id','=','oi.order_id')
        //     ->join('model_has_roles as mr','mr.model_id','=','u.id')
        //     ->join('roles as r','r.id','=','mr.role_id')
        //     ->leftJoin('accounts_invoices as i','o.id','=','i.order_id')
        //     // ->where('o.organization_id',$user->organization_id)
        //     ->where('r.name',\Config::get('constants.ROLES.SP'))
        //     ->whereMonth('o.created_at', $month)
        //     ->whereYear('o.created_at', $year)
        //     ->orderBy('totalAmount','desc')
        //     ->groupBy('u.id')
        //     ->take(5)
        //     ->get();

        // $collections =  PaymentCollection::from('payment_collection as pc')
        //                 ->select(
        //                     DB::Raw('sum(amount) AS collectionAmount'),
        //                 )
        //                 ->whereMonth('created_at', $month)
        //                 ->whereYear('created_at', $year)
        //                 ->first();
        // if($collections){
        //     $collection = $collections->collectionAmount;
        // }else{
        //     $collection = 0;
        // }

        // $ledgerData =   Ledger::select(
        //                             \DB::Raw('COALESCE(sum(case when (transaction_type="DR") then amount else 0 end),0) AS totalDebit'),
        //                             \DB::Raw('COALESCE(sum(case when (transaction_type="CR") then amount else 0 end),0) AS totalCredit'),
        //                         )
        //                         ->whereMonth('created_at', $month)
        //                         ->whereYear('created_at', $year)
        //                         ->first();

        // if($ledgerData){
        //     $outstanding = $ledgerData->totalDebit-$ledgerData->totalCredit;
        // }else{
        //     $outstanding = 0;
        // }

        $orders = 0;
        $today_order = 0;
        $week_order = 0;
        $month_order = 0;
        $dspOrders = 0;
        $dsp_week_order = 0;
        $dsp_today_order = 0;
        $targetsFailed = 0;
        $targetsAchieved = 0;
        $targets = array();
        $buyerOrders = 0;
        $todayvisits = 0;
        $weekvisits = 0;
        $monthvisits = 0;
        $dspVisits = array();
        $topCategories = array();
        $topBrands = array();
        $topBuyers = array();
        $topsalesPersons = array();
        $collection = 0;
        $orderByStatus['completed']['amount'] = 0;
        $outstanding = 0;

        return view('dashboard::index')->with(compact('orders','today_order','week_order','month_order','dspOrders','dsp_week_order','dsp_today_order','targetsFailed','targetsAchieved','targets','buyerOrders','todayRetailers','weekRetailers','monthRetailers','categoryBuyers','todayvisits','weekvisits','monthvisits','dspVisits','topCategories','topBrands','topBuyers','topsalesPersons','collection','orderByStatus','outstanding'));
    }

}
