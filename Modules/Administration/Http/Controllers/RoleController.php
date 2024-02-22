<?php

namespace Modules\Administration\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\User\Entities\Role;
use Modules\User\Entities\ModelRole;
use Illuminate\Support\Str;
use DB;

class RoleController extends Controller
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
        if(!isset($userPermission[\Config::get('constants.FEATURES.ROLES')]))
            return view('error/403');

        $authUser = \Auth::user();
        
        $perPage = \Config::get('constants.PAGE.PER_PAGE');
        
        if(isset($request->perPage) && $request->perPage > 0){
            $perPage = $request->perPage;
        }

        $defaultRoles =Role::select(DB::Raw('sum(case when (u.role_id!="") then 1 else 0 end) AS count'),'roles.name','roles.id','roles.label','roles.created_at','roles.updated_at','roles.is_default')
                ->leftJoin('model_has_roles as u','roles.id','=','u.role_id')
                ->where('organization_id',$authUser->organization_id)
                ->where('roles.name','!=',\Config::get('constants.ROLES.SUPERUSER'))
                ->where('is_default',1)
                ->groupBy('roles.id')
                ->orderBy('roles.name')
                ->get();

        
        $roles =Role::select(DB::Raw('sum(case when (u.role_id!="") then 1 else 0 end) AS count'),'roles.name','roles.id','roles.label','roles.created_at','roles.updated_at','roles.is_default')
                ->leftJoin('model_has_roles as u','roles.id','=','u.role_id')
                ->where('organization_id',$authUser->organization_id)->orderBy('name','ASC')
                ->where('is_default',0)
                ->where(function ($query) use ($request) {
                    if (isset($request->search) && $request->search!='' ) {
                        $query->where('name', 'like', '%' . $request->search . '%');
                    }
                })
                ->groupBy('roles.id')
                ->orderBy('roles.name')
                ->paginate($perPage);

        return view('administration::roles/index',['defaultRoles' => $defaultRoles,'roles' => $roles]);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function map()
    {
        return view('administration::mapping/map');
    }

    public function getRole(Request $request)
    {
        $id = $request->input("id");
        $role   =   Role::where('id',$id)->first();  
        
        if(!empty($role->toArray())){
            return array('role' => $role,'success'=>true);
        }else{
            return array('success'=>false,'model'=>array());
        }
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
        /*echo '<pre>';
        print_r(request()->all());die;*/
        $user = \Auth::user();
        try {
            if($request->input("role_id") && $request->input("role_id")!='0' && $request->input("role_id")!=''){
                $rules = array(
                    'name' => 'required|unique:roles,name,'.$request->input("role_id"),
                    'label' => 'required|unique:roles,label,'.$request->input("role_id")      
                );

            }else{

                $rules = array(
                    'name' => 'required|unique:roles,name',
                    'label' => 'required|unique:roles,label'      
                );
            }
            $organizationId=$user->organization_id;
            $validator = \Validator::make($request->all(), $rules);


            if ($validator->fails()) {
                $messages = $validator->messages();
                // return redirect('administration/roles')->with('error', $messages);
                return redirect('administration/roles')->withErrors($validator);
            } else {

                if (!preg_match('/^[a-z0-9_]{3,20}$/', $request->name)) {
                    return redirect('administration/roles')->with('error', trans('messages.ROLE_NAME'));
                }

                if($request->input("role_id") && $request->input("role_id")!='0' && $request->input("role_id")!=''){
                    $role = Role::find($request->input("role_id"));
                    $msg = trans('messages.ROLE_UPDATED');
                }else{
                   $isExists = Role::where('organization_id',$organizationId)->where('name',$request->name)->get()->toArray();
                
                    if(!empty($isExists)){
                        return redirect('administration/roles')->with('error', trans('messages.ROLE_NAME_UNIQUE'));
                    }
                
                    $role = new Role();
                    $msg = trans('messages.ROLE_ADDED');
                }
                
                    
                    $role->name = $request->input("name");
                    $role->label = $request->input("label");
                    $role->organization_id = $organizationId;

                    if($role->save()){
                        return redirect('administration/roles')->with('message',$msg);
                        
                    }else{
                        return redirect('administration/roles')->with('error', trans('messages.SOMETHING_WENT_WRONG'));
                    }
                
            }
            
        } catch (Exception $e) {
            return redirect('administration/roles')->with('error', trans('messages.SOMETHING_WENT_WRONG'));
        }
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
    public function edit($id)
    {
        return view('administration::edit');
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
        //$user = Auth::user();
        $item = Role::findOrfail($id);

        $check = ModelRole::where('role_id', $id)->count();

        if ($check > 0) {
            if($check>1){
                $value='users are ';
            }else{
                $value='user is ';
            }
            return redirect('administration/roles')->with('error', trans('messages.ROLE_NOT_DELETED',['userCount' => $check]));
        }

        if ($item->delete()) {
            return redirect('administration/roles')->with('message', trans('messages.ROLE_DELETED'));
        }
        else{
            return redirect('administration/roles')->with('error', trans('messages.SOMETHING_WENT_WRONG'));
        }
    }
}
