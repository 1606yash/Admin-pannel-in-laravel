<?php

namespace Modules\Report\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use DB;
use Modules\Saas\Entities\Organization;
use Modules\User\Entities\User;
use Modules\User\Entities\State;
use Modules\User\Entities\City;
use Modules\User\Entities\District;
use URL;
use Auth;
use Helpers;
use Modules\Administration\Entities\NotificationTemplate;
use Image;
use Illuminate\Support\Str;
use DataTables;
use Yajra\DataTables\Services\DataTable;
use Modules\Report\Exports\SalesBySalesPersonExport;
use Modules\Report\Exports\SalesByBuyerExport;
use Modules\Report\Exports\TopProductExport;
use Modules\Report\Exports\TopCategoryExport;
use Modules\Report\Exports\SalesByProductCategoriesExport;
use Modules\Report\Exports\ZeroBillingSalesPersonExport;
use Modules\Report\Exports\ZeroBillingBuyersExport;
use Modules\Report\Exports\SalesPersonTargetAchievementExport;
use Modules\Report\Exports\BuyerTargetAchievementExport;

class ReportController extends Controller
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
    public function index()
    {
        return view('report::index');
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function salesBySalesPerson(Request $request)
    {
        $user = Auth::user();
        $organizationId=$user->organization_id;

        return view('report::sales_by_sales_person');
    }

    public function salesBySalesPersonExport(Request $request,$fileType = 'xls')
    {

        $user = Auth::user();
        $organizationId=$user->organization_id;

        $orders =   array();
                    // Order::from('ecommerce_orders as o')
                    // ->select('u.phone_number as user_mobile',
                    //     DB::Raw('sum(case when (oi.order_id!="") then 1 else 0 end) AS totalItems'),
                    //     DB::Raw('sum(o.amount) AS totalAmount'),
                    //     DB::Raw('sum(case when (o.id!="") then 1 else 0 end) AS totalOrders'),
                    //     DB::Raw('CONCAT(u.name," ", u.last_name) AS username'),
                    //     DB::Raw('sum(case when (i.status="paid") then i.total else 0 end) AS amountInvoiced')
                    // )
                    // ->join('users as u','u.id','=','o.created_by')
                    // ->join('ecommerce_order_items as oi','o.id','=','oi.order_id')
                    // ->join('model_has_roles as mr','mr.model_id','=','u.id')
                    // ->join('roles as r','r.id','=','mr.role_id')
                    // ->leftJoin('accounts_invoices as i','o.id','=','i.order_id')
                    // ->where('o.organization_id',$user->organization_id)
                    // ->where('r.name',\Config::get('constants.ROLES.SP'))
                    // ->where(function ($query) use ($request) {
                    //     if (!empty($request->toArray())) {
                    //         if(isset($request->month) && (!empty($request->month) ) ){
                    //             $query->whereMonth('o.created_at', $request->month);
                    //         }

                    //         if(isset($request->year) && (!empty($request->year) ) ){
                    //             $query->whereYear('o.created_at', $request->year);
                    //         }
                    //     }
                    // })
                    // ->orderBy('o.created_at','desc')
                    // ->groupBy('o.created_by')
                    // ->get()->toArray();

        $ordersData = array();
        if (!empty($orders)) {
            foreach ($orders as $key => $order) {
                $ordersData[] =    array(
                    'username'          => $order['username'],
                    'totalOrders'       => $order['totalOrders'],
                    'totalItems'        => $order['totalItems'],
                    'totalAmount'       => $order['totalAmount'],
                    'amountInvoiced'    => $order['amountInvoiced']
                );
            }


            $fileName = 'SalesBySalesPerson' . date('m-d-Y');

            if ($fileType == 'xlsx') {
                return (new SalesBySalesPersonExport($ordersData))->download($fileName . '.xlsx', \Maatwebsite\Excel\Excel::XLSX);
            } elseif ($fileType == 'xls') {
                return (new SalesBySalesPersonExport($ordersData))->download($fileName . '.xls', \Maatwebsite\Excel\Excel::XLS);
            } elseif ($fileType == 'csv') {
                return (new SalesBySalesPersonExport($ordersData))->download($fileName . '.csv', \Maatwebsite\Excel\Excel::CSV, [
                    'Content-Type' => 'text/csv',
                ]);
            } else {
                return (new SalesBySalesPersonExport($ordersData))->download($fileName . '.xlsx', \Maatwebsite\Excel\Excel::XLSX);
            }

        } else {
            return redirect('report/sales-by-sales-person')->with('error', 'Orders not found');
        }
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function salesByBuyers(Request $request)
    {   
        $user = Auth::user();
        $organizationId=$user->organization_id;
        return view('report::sales_by_buyers');
    }

    public function salesByBuyersExport(Request $request,$fileType = 'xls')
    {

        $user = Auth::user();
        $organizationId=$user->organization_id;

        $orders =   array();
                    // Order::from('ecommerce_orders as o')
                    // ->select('u.phone_number as user_mobile',
                    //     DB::Raw('sum(case when (oi.order_id!="") then 1 else 0 end) AS totalItems'),
                    //     DB::Raw('sum(o.amount) AS totalAmount'),
                    //     DB::Raw('sum(case when (o.id!="") then 1 else 0 end) AS totalOrders'),
                    //     DB::Raw('CONCAT(u.name," ", u.last_name) AS username'),
                    //     DB::Raw('sum(case when (i.status="paid") then i.total else 0 end) AS amountInvoiced')
                    // )
                    // ->join('users as u','u.id','=','o.created_by')
                    // ->join('ecommerce_order_items as oi','o.id','=','oi.order_id')
                    // ->join('model_has_roles as mr','mr.model_id','=','u.id')
                    // ->join('roles as r','r.id','=','mr.role_id')
                    // ->leftJoin('accounts_invoices as i','o.id','=','i.order_id')
                    // ->where('o.organization_id',$user->organization_id)
                    // ->where('r.name',\Config::get('constants.ROLES.BUYER'))
                    // ->where(function ($query) use ($request) {
                    //     if (!empty($request->toArray())) {
                    //         if(isset($request->month) && (!empty($request->month) ) ){
                    //             $query->whereMonth('o.created_at', $request->month);
                    //         }

                    //         if(isset($request->year) && (!empty($request->year) ) ){
                    //             $query->whereYear('o.created_at', $request->year);
                    //         }
                    //     }
                    // })
                    // ->orderBy('o.created_at','desc')
                    // ->groupBy('o.created_by')
                    // ->get()->toArray();

        $ordersData = array();
        if (!empty($orders)) {
            foreach ($orders as $key => $order) {
                $ordersData[] =    array(
                    'username'          => $order['username'],
                    'totalOrders'       => $order['totalOrders'],
                    'totalItems'        => $order['totalItems'],
                    'totalAmount'       => $order['totalAmount'],
                    'amountInvoiced'    => $order['amountInvoiced']
                );
            }


            $fileName = 'SalesByBuyer' . date('m-d-Y');

            if ($fileType == 'xlsx') {
                return (new SalesByBuyerExport($ordersData))->download($fileName . '.xlsx', \Maatwebsite\Excel\Excel::XLSX);
            } elseif ($fileType == 'xls') {
                return (new SalesByBuyerExport($ordersData))->download($fileName . '.xls', \Maatwebsite\Excel\Excel::XLS);
            } elseif ($fileType == 'csv') {
                return (new SalesByBuyerExport($ordersData))->download($fileName . '.csv', \Maatwebsite\Excel\Excel::CSV, [
                    'Content-Type' => 'text/csv',
                ]);
            } else {
                return (new SalesByBuyerExport($ordersData))->download($fileName . '.xlsx', \Maatwebsite\Excel\Excel::XLSX);
            }

        } else {
            return redirect('report/sales-by-buyers')->with('error', 'Orders not found');
        }
    }

}
