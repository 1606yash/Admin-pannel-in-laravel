<?php

namespace Modules\Administration\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\User\Entities\Role;
use Modules\User\Entities\Address;
use Modules\User\Entities\User;
use Modules\User\Entities\RetailerCategories;
use Modules\User\Entities\State;
use Modules\User\Entities\City;
use Modules\User\Entities\District;
use Modules\User\Entities\OrganizationBuyer;
use Modules\Administration\Http\Requests\OrganiztionRequest;
use DB;
use Image;
use Auth;
use DataTables;
use Modules\User\Entities\ModelRole;
use Modules\Saas\Entities\Organization;
use App\Models\Audit;

class OrganizationController extends Controller
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
        $authUser = \Auth::user();

        $data = Organization::from('organizations as o')
                ->select('o.*')
                ->where('parent_id',$authUser->organization_id)
                ->where(function ($query) use ($request) {
                    if (!empty($request->toArray())) {
                        if ($request->get('name') != '') {
                            $query->where('name', 'like', '%' . $request->name . '%');
                        }
                        if ($request->get('owner_name') != '') {
                            $query->where('owner_name', 'like', '%' . $request->owner_name . '%');
                        }
                        if ($request->get('tin') != '') {
                            $query->where('tin', 'like', '%' . $request->tin . '%');
                        }
                    }
                })
                ->orderby('o.id','desc')
                ->get();
        
        $count = 0;
        if(!empty($data->toArray())){
            $count = count($data);
        }

        if ($request->ajax()) {
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('name', function($row) {
                            $name = '<div class="user-card">
                                                <div class="user-info">
                                                    <span class="tb-lead">'.$row->name.' <span class="dot dot-success d-md-none ml-1"></span></span>
                                                    <span>'.$row->owner_name.' </span>
                                                </div>
                                            </div>';
                            return $name;
                    })
                    ->addColumn('status', function ($row) {
                        if($row->status == 'active'){
                            $statusValue = 'Active';
                        }else{
                            $statusValue = 'Inactive';
                        }

                        $value = ($row->status == 'active') ? 'badge badge-success' : 'badge badge-danger';
                        $status = '
                            <span class="tb-sub">
                                <span class="'.$value.'">
                                    '.$statusValue.'
                                </span>
                            </span>
                        ';
                        return $status;
                    })
                    ->addColumn('action', function($row) {
                        $edit = url('/').'/administration/organization-edit/'.$row->id;
                        $delete = url('/').'/administration/organization-delete/'.$row->id;
                        $confirm = '"Are you sure, you want to delete it?"';
                        $editBtn = "<li class='nk-tb-action-hidden'>
                                    <a href='".$edit."' class='btn btn-trigger btn-icon' data-toggle='tooltip' data-placement='top' title='Edit'>
                                        <em class='icon ni ni-edit'></em>
                                    </a>
                                </li>";

                        $deleteBtn = "<li class='nk-tb-action-hidden'>
                                            <a href='".$delete."' onclick='return confirm(".$confirm.")'  class='btn btn-trigger btn-icon delete' data-toggle='tooltip' data-placement='top' title='Delete'>
                                                <em class='icon ni ni-trash'></em>
                                            </a>
                                        </li>";

                        $logbtn = '<li class="nk-tb-action-hidden"><a data-toggle="tooltip" data-placement="top" title="Audit Logs" href="#" data-resourceId="'.$row->id.'" class="audit_logs"><em class="icon ni ni-list"></em><span></span></a></li>';

                       $btn = "<ul class='nk-tb-actions gx-1'>
                                    ".$editBtn."
                                    ".$deleteBtn."
                                    ".$logbtn."
                                    <li>
                                        &nbsp;
                                    </li>
                                </ul>
                            ";
                        return $btn;
                    })
                    ->addColumn('created_at', function ($row) {
                        return date(\Config::get('constants.DATE.DATE_FORMAT') , strtotime($row->created_at));
                    })
                    ->rawColumns(['action','status','created_at','name'])
                    ->make(true);
        }

        return view('administration::organization/index')->with(compact('count'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function organizationCreate()
    {
        $states = State::all();
        return view('administration::organization/create',['states' => $states]);
    }

    public function store(OrganiztionRequest $request)
    {   
        $authUser = \Auth::user();

        $organization = new Organization();
        $organization->parent_id = $authUser->organization_id;
        $organization->name = $request->organizationName;
        $organization->tin = $request->organizationTin;
        $organization->owner_name = $request->ownerName;
        $organization->street_1 = $request->address1;
        $organization->street_2 = $request->address2;
        $organization->country = $request->country;
        $organization->state = $request->state;
        $organization->district = $request->district;
        $organization->city = $request->city;
        $organization->pincode = $request->pincode;

        if($organization->save()){
            return redirect('administration/organization')->with('message', trans('messages.ORGANIZATION_ADDED'));
        } else{
            return redirect('administration/organization')->with('error', trans('messages.SOMETHING_WENT_WRONG'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        try {
            $authUser = \Auth::user();
            $organization   = Organization::findorfail($id);
            $states         = State::all();
            $districts      = District::where('state_id',$organization->state)->orderby('name','asc')->get();
            $cities         = City::where('district_id',$organization->district)->orderby('name','asc')->get();  

            return view('administration::organization/create',['states' => $states,'organization' => $organization,'districts' => $districts,'cities' => $cities]);
        } catch (Exception $e) {
            return redirect('administration/organization')->with('error', $exception->getMessage());           
        }
    }

    public function update(OrganiztionRequest $request, $id)
    {   
        try {
            $authUser = \Auth::user();
            $organization   = Organization::findorfail($id);
            $organization->parent_id = $authUser->organization_id;
            $organization->name = $request->organizationName;
            $organization->tin = $request->organizationTin;
            $organization->owner_name = $request->ownerName;
            $organization->street_1 = $request->address1;
            $organization->street_2 = $request->address2;
            $organization->country = $request->country;
            $organization->state = $request->state;
            $organization->district = $request->district;
            $organization->city = $request->city;
            $organization->pincode = $request->pincode;

            if($organization->save()){
                return redirect('administration/organization')->with('message', trans('messages.ORGANIZATION_UPDATED'));
            } else{
                return redirect('administration/organization')->with('error', trans('messages.SOMETHING_WENT_WRONG'));
            }
        } catch (Exception $e) {
            return redirect('user')->with('error', $exception->getMessage());           
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function bulkUpdate(Request $request)
    {
        $authUser = \Auth::user();


        foreach ($request->ids as $key => $id) {
            $update = Organization::where('parent_id',$authUser->organization_id)->where('id',$id)->first();
            $update->status = $request->status;
            $update->save();
        }

        // $update = Organization::where('parent_id',$authUser->organization_id)->whereIn('id',$request->ids)->update(['status' => $request->status]);
        if($update){
            return array('success'=>true,'item' => array(),'msg'=>'true');
        }else{
            return array('success'=>false,'item'=>array(),'msg'=>trans('messages.NO_UPDATE_REQUIRED'));
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        $organization = Organization::findorfail($id);
        if($organization->forceDelete()){
            return redirect('administration/organization')->with('message', trans('messages.ORGANIZATION_DELETED'));
        }else{
            return redirect('administration/organization')->with('error', trans('messages.SOMETHING_WENT_WRONG'));
        }
    }

    public function logs($id)
    {
        $logs = Audit::from('audits as a')
                ->select('a.*',
                    DB::Raw('CONCAT(u.name," ", COALESCE(u.last_name,"")) AS username')
                )
                ->join('users as u','u.id','=','a.user_id')
                ->where('auditable_type','Modules\Saas\Entities\Organization')
                ->where('auditable_id',$id)
                ->orderby('a.id','desc')
                ->get();
        $logData = array();

        if(!empty($logs->toArray())){
            foreach ($logs as $key => $log) {

                $newdata = json_decode($log->new_values);
                $olddata = json_decode($log->old_values);
                if ($log->event == 'created') {
                    $detail = 'Organization created with Active status.';

                    $logData[] =    '<li class="timeline-item">
                            <div class="timeline-status bg-primary is-outline"></div>
                            <div class="timeline-date logDate">'.date(\Config::get('constants.DATE.DATE_FORMAT') , strtotime($log->created_at)).' <em class="icon ni ni-alarm-alt"></em></div>
                            <div class="timeline-data">
                                <div class="timeline-des">
                                    <p class="logText">'.$detail.'</p>
                                    <span class="text-muted fs-10px">by <span class="logBy">'.$log->username.'</span> at <span class="logTime">'.date(\Config::get('constants.DATE.TIME_FORMAT') , strtotime($log->created_at)).'</span></span>
                                </div>
                            </div>
                        </li>';
                }elseif ($log->event == 'updated') {
                    $fields = array();
                    foreach ($newdata as $key => $value) {
                        $fields[] = str_replace('_', ' ', $key);
                    }
                    $detail = 'Organization data updated. Updated fields are : '.implode(', ', $fields).'.';

                    if(isset($newdata->status)){
                        if($newdata->status == 1){
                            $status = 'Active';
                        }else{
                            $status = 'Inactive';
                        }
                        $detail .= 'Status updated to '.$status;                            
                    }


                    $logData[] =    '<li class="timeline-item">
                        <div class="timeline-status bg-primary is-outline"></div>
                        <div class="timeline-date logDate">'.date(\Config::get('constants.DATE.DATE_FORMAT') , strtotime($log->created_at)).' <em class="icon ni ni-alarm-alt"></em></div>
                        <div class="timeline-data">
                            <div class="timeline-des">
                                <p class="logText">'.$detail.'</p>
                                <span class="text-muted fs-10px">by <span class="logBy">'.$log->username.'</span> at <span class="logTime">'.date(\Config::get('constants.DATE.TIME_FORMAT') , strtotime($log->created_at)).'</span></span>
                            </div>
                        </div>
                    </li>';
                }
            }
            if(!empty($logData)){
                return array('success'=>true,'logs' => $logData,'msg'=>'true');
            }else{
                return array('success'=>false,'logs'=>array(),'msg'=>trans('messages.NO_LOGS_FOUND'));
            }
        }else{
            return array('success'=>false,'logs'=>array(),'msg'=>trans('messages.NO_LOGS_FOUND'));
        }
    }
}
