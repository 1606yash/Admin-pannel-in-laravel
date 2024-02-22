<?php

namespace Modules\Administration\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\User\Entities\User;
use Modules\User\Entities\RetailerMapping;
use DB;
use Image;
use DataTables;
use Modules\User\Entities\City;
use Modules\User\Entities\District;
use Modules\User\Entities\State;
use Modules\User\Entities\RetailerCategories;
use Modules\Administration\Entities\NotificationTemplate;
use SendNotification;
use App\Jobs\SendNotificationJob;

class MappingController extends Controller
{

    public function __construct()
    {

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
        $userPermission = \Session::get('userPermission');
        if(!isset($userPermission[\Config::get('constants.FEATURES.MAPPING')])){
            return view('error/403');
        }

        $dsps =     User::from('users as u')
            ->select('u.id', 'u.name', 'u.last_name', 'u.email', 'u.file', \DB::Raw('sum(case when m.dsp_id is not null then 1 else 0 end) AS retailers'))
            ->leftJoin('model_has_roles as mr', 'mr.model_id', '=', 'u.id')
            ->leftJoin('roles as r', 'r.id', '=', 'mr.role_id')
            ->leftJoin('retailer_mapping as m', 'm.dsp_id', '=', 'u.id')
            ->where('r.name', \Config::get('constants.ROLES.SP'))
            ->groupBy('u.id')
            ->get();

        if ($request->ajax()) {
            return Datatables::of($dsps)
                ->addIndexColumn()
                ->addColumn('name', function ($row) use ($userPermission) {

                    $username = $row->name . ' ' . $row->last_name;

                    if (!is_null($row->file)) {
                        $file = public_path('uploads/users/') . $row->file;
                    }

                    if (!is_null($row->file) && file_exists($file)){
                        $avatar = "<img src=" . url('uploads/users/' . $row->file) . ">";
                    }
                    else{
                        $avatar = "<span>" . \Helpers::getAcronym($username) . "</span>";
                    }

                    if(isset($userPermission['mapping']) && ($userPermission['mapping']['edit_all'] || $userPermission['mapping']['edit_own'])){
                        $edit = url('/') . '/administration/mapping/map-buyers/' . $row->id;
                    }else{
                        $edit = '#';
                    }
                    return $name = '<a href="' . $edit . '">
                                            <div class="user-card">
                                                <div class="user-avatar bg-primary">
                                                    ' . $avatar . '
                                                </div>
                                                <div class="user-info">
                                                    <span class="tb-lead">' . $username . ' <span class="dot dot-success d-md-none ml-1"></span></span>
                                                </div>
                                            </div>
                                        </a>';
                })
                ->addColumn('action', function ($row) use ($userPermission) {

                    if(isset($userPermission['mapping']) && ($userPermission['mapping']['edit_all'] || $userPermission['mapping']['edit_own'])){
                        $edit = url('/') . '/administration/mapping/map-buyers/' . $row->id;
                        $btn = '<a href="' . $edit . '" class="btn btn-primary d-none d-md-inline-flex"><span>Map Buyers</span></a>';
                    }else{
                        $btn = '';
                    }

                    return $btn;
                })
                ->rawColumns(['name', 'action'])
                ->make(true);
        }

        return view('administration::mapping/index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function mapBuyers(Request $request, $dsp)
    {
        $authUser = \Auth::user();

        $retailerCategories =   RetailerCategories::all();
        $districts          =   District::orderBy('name', 'asc')->get();
        $cities             =   City::orderBy('name', 'asc')->get();
        $states             =   State::all();

        $dspDetails =    User::from('users as u')
            ->select('u.name', 'u.last_name', 'u.id')
            ->where('u.id', $dsp)
            ->first();

        $dspRetailers =     RetailerMapping::select(\DB::raw('group_concat(retailer_id) as retailer_id'))
            ->where('dsp_id', $dsp)
            ->groupBy('dsp_id')
            ->first();
        if ($dspRetailers) {
            $dspRetailers = explode(',', $dspRetailers->retailer_id);
        } else {
            $dspRetailers  = array();
        }

        $mappedRetailers = User::from('users as u')
            ->select('u.id', 'u.name', 'u.last_name', 'u.shop_name', 'u.email', 'u.file', 'u.phone_number', 'c.name as city', 'd.name as district')
            ->leftJoin('retailer_mapping as m', 'u.id', '=', 'm.retailer_id')
            ->leftJoin('cities as c', 'u.city', '=', 'c.id')
            ->leftJoin('districts as d', 'u.district', '=', 'd.id')
            ->where('dsp_id', $dsp)
            ->orderby('u.id', 'desc')
            ->groupBy('u.id')
            ->get();

        $unmappedRetailers = User::from('users as u')
            ->select('u.id', 'u.name', 'u.last_name', 'u.shop_name', 'u.email', 'u.file', 'u.phone_number', 'ob.organization_id as obid', 'c.name as city', 'd.name as district')
            ->leftJoin('model_has_roles as mr', 'mr.model_id', '=', 'u.id')
            ->leftJoin('roles as r', 'mr.role_id', '=', 'r.id')
            ->leftJoin('organization_buyer as ob', 'u.id', '=', 'ob.buyer_id')
            ->leftJoin('cities as c', 'u.city', '=', 'c.id')
            ->leftJoin('districts as d', 'u.district', '=', 'd.id')
            ->where('r.name', \Config::get('constants.ROLES.BUYER'))
            // ->where('ob.organization_id', $authUser->organization_id)
            ->where('ob.status',1)
            ->where('u.is_approved',1)
            ->whereNotIn('u.id', $dspRetailers)
            ->where(function ($query) use ($request) {
                if (!empty($request->toArray())) {
                    if ($request->get('state') != '') {
                        $query->where('u.state', $request->get('state'));
                    }
                    if ($request->get('district') != '') {
                        $query->where('u.district', $request->get('district'));
                    }
                    if ($request->get('city') != '') {
                        $query->where('u.city', $request->get('city'));
                    }
                    if ($request->get('category') != '') {
                        $query->where('ob.buyer_category', $request->get('category'));
                    }
                }
            })
            ->orderby('u.id', 'desc')
            ->groupBy('u.id')
            ->get();

        $unmappedRetailersCount = count($unmappedRetailers);

        if ($request->ajax()) {
            return Datatables::of($unmappedRetailers)
                ->addIndexColumn()
                ->addColumn('name', function ($row) {
                    $detailLink = url('user/detail/' . $row->id);
                    $username = $row->name . ' ' . $row->last_name;
                    
                    if(!is_null($row->file)){
                        $file = public_path('uploads/users/') . $row->file;
                    }

                    if (!is_null($row->file) && file_exists($file))
                        $avatar = "<img src=" . url('uploads/users/' . $row->file) . ">";
                    else
                        $avatar = "<span>" . \Helpers::getAcronym($username) . "</span>";

                    $name = '<a href="' . $detailLink . '">
                            <div class="user-card">
                                <div class="user-avatar bg-primary">
                                    ' . $avatar . '
                                </div>
                                <div class="user-info">
                                    <span class="tb-lead">' . $row->shop_name . ' <span class="dot dot-success d-md-none ml-1"></span></span>
                                    <span>' . $username . ' </span>
                                </div>
                            </div>
                        </a>';
                    return $name;
                })
                ->addColumn('city', function ($row) {
                    return $row->city;
                })
                ->addColumn('district', function ($row) {
                    return $row->district;
                })
                ->rawColumns(['name', 'district', 'city'])
                ->make(true);
        }

        return view('administration::mapping/map')->with(compact('dspRetailers', 'dspDetails', 'mappedRetailers', 'unmappedRetailersCount', 'retailerCategories', 'districts', 'cities','states'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request, $dsp)
    {
        $retailers = $request->buyers;
        if (empty($retailers)) {
            flash("Please select atleast one buyer to map.")->warning();
            return redirect('administration/mapping/map-buyers/' . $dsp);
        }

        $authUser = \Auth::user();

        foreach ($retailers as $key => $retailer) {
            $mapData[] =  array(
                'dsp_id' => $dsp,
                'retailer_id' => $retailer,
                'created_by' => $authUser->id,
            );
        }

        if (!empty($mapData)) {
            RetailerMapping::insert($mapData);
            $dspDetails = User::findorfail($dsp);

            $notificationData = NotificationTemplate::where('organization_id',$authUser->organization_id)
                        ->where('event_name','notifications.sp.buyer_assigned')
                        ->first();

            $chanels = $notificationData->via;
            $shortCodes = json_decode($notificationData->shortcodes,true);

            foreach ($retailers as $key => $retailer) {
                $retailerDetails = User::select(\DB::Raw('CONCAT(name," ", last_name) AS username'),'shop_name','phone_number')->where('id',$retailer)->first();
                
                $bodies = array();
                $pushNotificationDetails = array();
                $codeData = array(
                                'name' => ucfirst($retailerDetails->username),
                                'mobile_no' => $retailerDetails->phone_number,
                                'shop_name' => ucfirst($retailerDetails->shop_name),
                                'sp_name' => ucfirst($dspDetails->name.' '.$dspDetails->last_name),
                            );

                if($retailerDetails){
                }
                if(isset($notificationData->via)){
                    if(!empty($notificationData->body)){
                        foreach ($notificationData->body as $key => $body) {
                            if(in_array($key, $chanels)){
                                foreach ($shortCodes as $code => $shortcode) {
                                    $searchKey = '{'.$code.'}';
                                    if(isset($codeData[$code])){
                                        $replaceWith = $codeData[$code];
                                    }else{
                                        $replaceWith = "";
                                    }
                                    $body = str_replace($searchKey, $replaceWith, $body);
                                    $bodies[$key] = $body;
                                }
                            }
                        }
                    }
                }

                if(in_array('database', $chanels)){
                    $pushNotificationDetails = [
                        'title' => 'New Buyer Assigned',
                        'body' => $bodies['database'],
                        'user_id' => $dsp,
                        'fcm_token' => $dspDetails->fcm_token,
                        'organization_id' => $authUser->organization_id,
                    ];
                }
                $mailSubject = $notificationData->email_subject;

                $jobData =  array(
                            'receiver' => $dspDetails,
                            'bodies' => $bodies,
                            'channels' => $chanels,
                            'mailSubject' => $mailSubject,
                            'details' => $pushNotificationDetails,
                        );

                $emailJob = (new SendNotificationJob($jobData))->delay(\Carbon\Carbon::now()->addSeconds(3));
                dispatch($emailJob);
                // \Helpers::sendNotifications($dspDetails,$bodies,$chanels,$mailSubject,$pushNotificationDetails);

            }

            return redirect('administration/mapping/map-buyers/' . $dsp)->with('message', trans('messages.BUYERS_MAPPED'));
        } else {
            return redirect('administration/mapping/map-buyers/' . $dsp)->with('error', trans('messages.SOMETHING_WENT_WRONG'));
        }
    }

    public function unmapBuyers(Request $request, $dsp)
    {
        $retailers = $request->unmapped;

        if ($retailers == "") {
            return redirect('administration/mapping/map-buyers/' . $dsp)->with('error', trans('messages.SELECT_BUYER_MAP'));
        }

        $retailers = explode(',', $retailers);

        $unmap =    RetailerMapping::where('dsp_id', $dsp)
            ->whereIn('retailer_id', $retailers)
            ->delete();

        return redirect('administration/mapping/map-buyers/' . $dsp)->with('message', trans('messages.BUYERS_UNMAPPED'));
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('administration::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function checkmapping()
    {

        $retailers = RetailerMapping::from('retailer_mapping as m')
            ->select('u.id', 'u.name', 'm.id as mid')
            ->leftJoin('users as u', 'u.id', '=', 'm.retailer_id')
            ->get();

        $removeRetailers = array();

        if(!empty($retailers->toArray())){
            foreach ($retailers as $key => $retailer) {
                if($retailer->id == ""){
                    $removeRetailers[] = $retailer->mid;
                }
            }
        }

        if(!empty($removeRetailers)){
            $mapping = RetailerMapping::whereIn('id',$removeRetailers)->forceDelete();
            if($mapping){
                echo "deleted";
            }
            die;
        }

    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }
}
