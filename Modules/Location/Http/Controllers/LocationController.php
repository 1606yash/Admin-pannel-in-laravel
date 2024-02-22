<?php

namespace Modules\Location\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Maatwebsite\Excel\HeadingRowImport;
use Modules\Location\Entities\Country;
use Modules\Location\Entities\State;
use Modules\Location\Entities\City;
use Modules\Saas\Entities\Organization;
use Auth;
use DataTables;
use DB;
use URL;

class LocationController extends Controller
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

        $userPermission = \Session::get('userPermission');

        if(!isset($userPermission[\Config::get('constants.FEATURES.BRANDS')]))
            return view('error/403');

        $user = Auth::user();
        $organizationId=$user->organization_id;
        $currentOrganization = \Session::get('currentOrganization');

        $data = Country::orderBy('name','ASC')
            ->get();

        if ($request->ajax()) {
            /*echo '<pre>';
            print_r($data->toArray());die;*/
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row) use ($userPermission){    
                        $btn = '';
                        $btn .= "<ul class='nk-tb-actions gx-1'><li><div class='drodown mr-n1'><a href='#' class='dropdown-toggle btn btn-icon btn-trigger' data-toggle='dropdown'><em class='icon ni ni-more-h'></em></a><div class='dropdown-menu dropdown-menu-right'><ul class='link-list-opt no-bdr'>";
                        if(isset($userPermission['brands']) && ($userPermission['brands']['edit_all'] || $userPermission['brands']['edit_own'])){

                            $btn .= "<li>
                                        <a href='#' data-target='addCountry' data-id='".$row->id."' class='editItem toggle'>
                                            <em class='icon ni ni-edit'></em> <span>Edit</span>
                                        </a>
                                    </li>";
                        }
                        $confirmMsg = 'Are you sure, you want to delete it?';
                        if(isset($userPermission['brands']) && ($userPermission['brands']['delete_all'] || $userPermission['brands']['delete_own'])){
                            $btn .= "<li>
                                        <a href='#' data-id='".$row->id."' class='eg-swal-av3'>
                                            <em class='icon ni ni-trash'></em> <span>Delete</span>
                                        </a>
                                    </li>";
                        }

                        $btn .= '<li><a href="#" data-resourceId="'.$row->id.'" class="audit_logs"><em class="icon ni ni-list"></em><span>Audit Logs</span></a></li>';
                        $btn .= "</ul></div></div></li></ul>";

                        return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }                           
        
        $organizations = Organization::where('parent_id',$organizationId)->where('status','active')->get();
    
        return view('location::index',['countries'=>$data,'organizations'=>$organizations]);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('location::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        try {

            $rules = array(
                'name' => 'required',     
                'code' => 'required',
            );
            $user = \Auth::user();
            $organizationId=$user->organization_id;
            $validator = \Validator::make($request->all(), $rules);


            if ($validator->fails()) {
                $messages = $validator->messages();
                // return redirect('ecommerce/brands')->with('validator_error', $messages);
                return redirect('location/country')->withErrors($validator);
            } else {
                
                $code='';
                if(!$request->code || $request->code==''){
                    $code = Str::slug($request->name, "-");
                }else{
                    $code = Str::slug($request->code, "-");
                }

                if (strlen($code) > \Config::get('constants.SLUG_CHARACTER_LIMIT'))
                    $code = substr($code, 0, \Config::get('constants.SLUG_CHARACTER_LIMIT'));

                if($code || $code!=''){
                    if (!preg_match('/^[a-zA-Z0-9_-]{1,255}$/', $code)) {
                        return redirect('location/country')->with('error', trans('messages.SLUG_CHARACTER_LIMIT',['slug_character_limit'=> \Config::get('constants.SLUG_CHARACTER_LIMIT')]));
                    }
                }

                if($request->input("id") && $request->input("id")!='0' && $request->input("id")!=''){

                    $isExists = Country::where('id','!=',$request->input("id"))
                                ->where(function($query) use ($request,$code){
                                    $query->where('name',$request->input("name"));
                                    $query->orWhere('code',$code);
                                })
                                ->get()->toArray();

                    if(!empty($isExists)){

                        if($isExists[0]['slug'] == $request->code){

                            if(!$request->code || $request->code==''){
                                $code = Str::slug($request->name.'-'.$request->input("id"), "-");
                            }else{
                                return redirect('location/country')->with('error', trans('messages.CODE_ALREADY_EXISTS'));
                            }
                        }else{
                            return redirect('location/country')->with('error', trans('messages.BRAND_TITLE'));
                        }

                    }

                    $country = Country::find($request->input("id"));
                    $country->code = $code;
                    $msg = trans('messages.COUNTRY_UPDATED');
                }else{

                    $isExists = Country::where('name',$request->input("name"))->orWhere('code',$code)->get()->toArray();

                    if(!empty($isExists)){

                        if($isExists[0]['slug'] == $request->code){
                            if(!$request->code || $request->code==''){
                                $code = Str::slug($request->name.'-'.$request->name, "-");
                            }
                            // return redirect('ecommerce/brands')->with('error', 'Slug already exists');
                        }else{
                            return redirect('location/country')->with('error', trans('messages.BRAND_TITLE'));
                        }

                    }
                
                    $country = new Country();
                    $country->code = $code;
                    $msg = trans('messages.COUNTRY_ADDED');
                }
                
                $country->name = $request->input("name");
                $country->code = $request->input("code");
                                

                // $organization_type = \Session::get('organization_type');
                // if(isset($organization_type) && $organization_type == 'MULTIPLE'){
                //     $brand->organization_id = $request->input("organization");
                // }else{
                //     $brand->organization_id = $organizationId;
                // }



                if($country->save()){
                    return redirect('location/country')->with('message', $msg);
                }else{
                    return redirect('location/country')->with('error', trans('messages.SOMETHING_WENT_WRONG'));
                }
            }
            
        } catch (Exception $e) {
            return redirect('location/country')->with('error', trans('messages.SOMETHING_WENT_WRONG'));
        }
    }

    public function storeState(Request $request)
    {
        try {

            $rules = array(
                'name' => 'required',     
                'country_code' => 'required',
            );
            $user = \Auth::user();
            $organizationId=$user->organization_id;
            $validator = \Validator::make($request->all(), $rules);


            if ($validator->fails()) {
                $messages = $validator->messages();
                // return redirect('ecommerce/brands')->with('validator_error', $messages);
                return redirect('location/state')->withErrors($validator);
            } else {

                if($request->input("id") && $request->input("id")!='0' && $request->input("id")!=''){

                    $state = State::find($request->input("id"));
                    $state->name = $request->input("name");
                    $state->country_id = $request->input("country_code");
                    $state->state_code = $request->input("state_code");
                    $state->status = $request->input("status");
                    $msg = trans('messages.STATE_UPDATED');

                }else{
                
                    $state = new State();
                    $state->name = $request->input("name");
                    $state->country_id = $request->input("country_code");
                    $state->state_code = $request->input("state_code");
                    $state->status = $request->input("status");
                    $msg = trans('messages.STATE_ADDED');
                }

                if($state->save()){
                    return redirect('location/state')->with('message', $msg);
                }else{
                    return redirect('location/state')->with('error', trans('messages.SOMETHING_WENT_WRONG'));
                }
            }
            
        } catch (Exception $e) {
            return redirect('location/country')->with('error', trans('messages.SOMETHING_WENT_WRONG'));
        }
    }

    public function storeCity(Request $request)
    {
        try {

            $rules = array(
                'name' => 'required',     
                'state_id' => 'required',
            );
            $user = \Auth::user();
            $organizationId=$user->organization_id;
            $validator = \Validator::make($request->all(), $rules);


            if ($validator->fails()) {
                $messages = $validator->messages();
                // return redirect('ecommerce/brands')->with('validator_error', $messages);
                return redirect('location/city')->withErrors($validator);
            } else {

                if($request->input("id") && $request->input("id")!='0' && $request->input("id")!=''){

                    $city = City::find($request->input("id"));
                    $city->name = $request->input("name");
                    $city->state_id = $request->input("state_id");
                    $city->status = $request->input("status")=='on' ? 1 : 0;
                    $msg = trans('messages.CITY_UPDATED');
                }else{
                
                    $city = new City();
                    $city->name = $request->input("name");
                    $city->state_id = $request->input("state_id");
                    $city->status = $request->input("status")=='on' ? 1 : 0;
                    $msg = trans('messages.CITY_ADDED');
                }
                
                if($city->save()){
                    return redirect('location/city')->with('message', $msg);
                }else{
                    return redirect('location/city')->with('error', trans('messages.SOMETHING_WENT_WRONG'));
                }
            }
            
        } catch (Exception $e) {
            return redirect('location/city')->with('error', trans('messages.SOMETHING_WENT_WRONG'));
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        $id = $request->input("id");

        $item = Country::findOrfail($id);

        /*$check   =   ProductBrand::where('brand_id',$id)->count();
        if ($check > 0) {
            if($check>1){
                $value='products are ';
            }else{
                $value='product is ';
            }
            return array('success'=>false,'brand'=>array(),'msg'=>"Brand can't be deleted. ".$check.' '.$value."   associated with it.");
        }*/

        if ($item->delete()) {
            return array('country' =>array(),'success'=>true,'msg'=>'success');
        }
        else{
            return array('success'=>false,'country'=>array(),'msg'=>'fails');
        }
    }

    public function destroyState($id)
    {
        $id = $request->input("id");

        $item = State::findOrfail($id);

        /*$check   =   ProductBrand::where('brand_id',$id)->count();
        if ($check > 0) {
            if($check>1){
                $value='products are ';
            }else{
                $value='product is ';
            }
            return array('success'=>false,'brand'=>array(),'msg'=>"Brand can't be deleted. ".$check.' '.$value."   associated with it.");
        }*/

        if ($item->delete()) {
            return array('country' =>array(),'success'=>true,'msg'=>'success');
        }
        else{
            return array('success'=>false,'country'=>array(),'msg'=>'fails');
        }
    }

    public function destroyCity($id)
    {
        $id = $request->input("id");

        $item = City::findOrfail($id);

        /*$check   =   ProductBrand::where('brand_id',$id)->count();
        if ($check > 0) {
            if($check>1){
                $value='products are ';
            }else{
                $value='product is ';
            }
            return array('success'=>false,'brand'=>array(),'msg'=>"Brand can't be deleted. ".$check.' '.$value."   associated with it.");
        }*/

        if ($item->delete()) {
            return array('city' =>array(),'success'=>true,'msg'=>'success');
        }
        else{
            return array('success'=>false,'city'=>array(),'msg'=>'fails');
        }
    }


    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function stateList(Request $request)
    {

        $userPermission = \Session::get('userPermission');

        if(!isset($userPermission[\Config::get('constants.FEATURES.BRANDS')]))
            return view('error/403');

        $user = Auth::user();
        $organizationId=$user->organization_id;
        $currentOrganization = \Session::get('currentOrganization');

        $data = State::select('states.*','c.name as country_name')->leftjoin('countries as c','c.code','states.country_id')
            ->orderBy('name','ASC')
            ->get();

        if ($request->ajax()) {
            /*echo '<pre>';
            print_r($data->toArray());die;*/
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row) use ($userPermission){    
                        $btn = '';
                        $btn .= "<ul class='nk-tb-actions gx-1'><li><div class='drodown mr-n1'><a href='#' class='dropdown-toggle btn btn-icon btn-trigger' data-toggle='dropdown'><em class='icon ni ni-more-h'></em></a><div class='dropdown-menu dropdown-menu-right'><ul class='link-list-opt no-bdr'>";
                        if(isset($userPermission['brands']) && ($userPermission['brands']['edit_all'] || $userPermission['brands']['edit_own'])){

                            $btn .= "<li>
                                        <a href='#' data-target='addState' data-id='".$row->id."' class='editItem toggle'>
                                            <em class='icon ni ni-edit'></em> <span>Edit</span>
                                        </a>
                                    </li>";
                        }
                        $confirmMsg = 'Are you sure, you want to delete it?';
                        if(isset($userPermission['brands']) && ($userPermission['brands']['delete_all'] || $userPermission['brands']['delete_own'])){
                            $btn .= "<li>
                                        <a href='#' data-id='".$row->id."' class='eg-swal-av3'>
                                            <em class='icon ni ni-trash'></em> <span>Delete</span>
                                        </a>
                                    </li>";
                        }

                        $btn .= "</ul></div></div></li></ul>";

                        return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }                           
        
        $organizations = Organization::where('parent_id',$organizationId)->where('status','active')->get();
        $countries     = Country::get();  
    
        return view('location::stateList',['states'=>$data,'countries'=>$countries,'organizations'=>$organizations]);
    }

    public function cityList(Request $request)
    {

        $userPermission = \Session::get('userPermission');

        if(!isset($userPermission[\Config::get('constants.FEATURES.BRANDS')]))
            return view('error/403');

        $user = Auth::user();
        $organizationId=$user->organization_id;
        $currentOrganization = \Session::get('currentOrganization');

        $data = City::select('cities.*','s.name as state_name','c.name as country_name','s.country_id as country_code')->leftjoin('states as s','s.id','cities.state_id')->leftjoin('countries as c','c.code','s.country_id')
            ->where(function ($query) use ($request) {
                    if (!empty($request->toArray())) {
                        if(isset($request->status) && (!empty($request->status) ) ){
                            $status = 0;
                            if($request->status=='active'){$status = 1;}
                            $query->where('cities.status',$status);
                        }

                        if(isset($request->organizationFilter) && (!empty($request->organizationFilter) || $request->organizationFilter != 0)){
                            $query->where('ecommerce_brands.organization_id',$request->input('organizationFilter'));
                        }
                    }
                })
            ->orderBy('name','ASC')
            ->get();

        if ($request->ajax()) {
            /*echo '<pre>';
            print_r($data->toArray());die;*/
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('status', function ($row) {
                        $class = ($row->status == 1) ? 'badge badge-success' : 'badge badge-danger';
                        $value = ($row->status == 1) ? 'Active' : 'InActive';
                        $status = '
                            <span class="tb-sub">
                                <span class="'.$class.'">
                                    '.ucfirst($value).'
                                </span>
                            </span>
                        ';
                        return $status;
                    })
                    ->addColumn('action', function($row) use ($userPermission){    
                        $btn = '';
                        $btn .= "<ul class='nk-tb-actions gx-1'><li><div class='drodown mr-n1'><a href='#' class='dropdown-toggle btn btn-icon btn-trigger' data-toggle='dropdown'><em class='icon ni ni-more-h'></em></a><div class='dropdown-menu dropdown-menu-right'><ul class='link-list-opt no-bdr'>";
                        if(isset($userPermission['brands']) && ($userPermission['brands']['edit_all'] || $userPermission['brands']['edit_own'])){

                            $btn .= "<li>
                                        <a href='#' data-target='addCity' data-id='".$row->id."' class='editItem toggle'>
                                            <em class='icon ni ni-edit'></em> <span>Edit</span>
                                        </a>
                                    </li>";
                        }
                        $confirmMsg = 'Are you sure, you want to delete it?';
                        if(isset($userPermission['brands']) && ($userPermission['brands']['delete_all'] || $userPermission['brands']['delete_own'])){
                            $btn .= "<li>
                                        <a href='#' data-id='".$row->id."' class='eg-swal-av3'>
                                            <em class='icon ni ni-trash'></em> <span>Delete</span>
                                        </a>
                                    </li>";
                        }

                        $btn .= '<li><a href="#" data-resourceId="'.$row->id.'" class="audit_logs"><em class="icon ni ni-list"></em><span>Audit Logs</span></a></li>';
                        $btn .= "</ul></div></div></li></ul>";

                        return $btn;
                    })
                    ->rawColumns(['action','status'])
                    ->make(true);
        }                           
        
        $organizations = Organization::where('parent_id',$organizationId)->where('status','active')->get();
        $countries     = Country::get();
        $states        = State::get();
    
        return view('location::cityList',['cities'=>$data,'states'=>$states,'countries'=>$countries,'organizations'=>$organizations]);
    }
    
    /**
     * Get the specified resource.
     * @return Renderable
     */
    public function getCountry(Request $request)
    {
        $id = $request->input("id");
        $country   =   Country::where('id',$id)->first();  

        if(!empty($country->toArray())){
            return array('country' => $country,'success'=>true);
        }else{
            return array('success'=>false,'country'=>array());
        }
    }

    public function getState(Request $request)
    {
        $id = $request->input("id");
        $state = State::where('id',$id)->first();  

        if(!empty($state->toArray())){
            return array('state' => $state,'success'=>true);
        }else{
            return array('success'=>false,'state'=>array());
        }
    }

    public function getCity(Request $request)
    {
        $id = $request->input("id");
        $city = City::select('cities.*','s.name as state_name','c.name as country_name','s.country_id as country_code')->leftjoin('states as s','s.id','cities.state_id')->leftjoin('countries as c','c.code','s.country_id')->where('cities.id',$id)->first();  

        if(!empty($city->toArray())){
            return array('city' => $city,'success'=>true);
        }else{
            return array('success'=>false,'city'=>array());
        }
    }

    public function massUpdate(Request $request)
    {
        try {
 
            if( empty($request->input("ids"))){
                return array('success'=>false,'item'=>array(),'msg'=>trans('messages.SELECT_AN_ITEM'));
                
            }

            if($request->input("status") == '0' ){
                return array('success'=>false,'item'=>array(),'msg'=>trans('messages.SELECT_BULK_STATUS'));
            }
            
            $i=0;    
            foreach($request->input("ids") as $key=>$value ){
                $city = City::find($value);
                $city->status = $request->input("status")=='active' ? 1 : 0;
                $city->save();
                $i++;
            }


            if($i>0){
                return array('success'=>true,'item' => array(),'msg'=>'true');
            }else{
                return array('success'=>false,'item'=>array(),'msg'=>trans('messages.NO_UPDATE_REQUIRED'));
            }
                            
        } catch (Exception $e) {
            
            return array('success'=>false,'item'=>array(),'msg'=>trans('messages.SOMETHING_WENT_WRONG'));
        }
    }

    public function import(Request $request)
    {
        return view('location::import');
    }

    public function getStates(Request $request)
    {
        $states = State::where('country_id',$request->input('country_id'))->get();
        if(!empty($states->toArray())){
            return array('states' => $states,'success'=>true,'msg'=>'success');
        }
        else{
            return array('success'=>false,'states'=>array(),'msg'=>'fails');
        }
    }
}
