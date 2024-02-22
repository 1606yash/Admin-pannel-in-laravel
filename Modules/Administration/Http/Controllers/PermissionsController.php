<?php

namespace Modules\Administration\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\User\Entities\Role;
use Modules\User\Entities\User;
use Modules\User\Entities\ModuleFeature;
use Modules\User\Entities\OrganizationPermission;
use Auth;
use DB;

class PermissionsController extends Controller
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
    public function index($role_id = 0)
    {
        $userPermission = \Session::get('userPermission');
        if(!isset($userPermission[\Config::get('constants.FEATURES.PERMISSIONS')]))
            return view('error/403');

        $user = Auth::user();
        $getRoles = [\Config::get('constants.ROLES.BUYER'),\Config::get('constants.ROLES.OWNER'),\Config::get('constants.ROLES.SUPERUSER')];
        $roles              =   Role::where('organization_id',$user->organization_id)
                                ->whereNotIn('name',$getRoles)
                                ->orderby('name','ASC')
                                ->get();

        $modules = ModuleFeature::all();

        if($role_id != 0){
            $permissions = OrganizationPermission::where('role_id',$role_id)->get()->toArray();
        }else{
            $permissions = array();
        }

        $features = array();
        foreach ($modules as $key => $module) {
            $features[$module->module][$module->id] = $module->feature;
        }

        return view('administration::permissions/index',['features' => $features,'roles' => $roles,'role_id' => $role_id,'permissions' => $permissions]);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function map()
    {
        return view('administration::mapping/map');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request,$role_id = 0)
    {
        try {
            DB::beginTransaction();
            $user = Auth::user();
            $featureData = array();
            $commit = 0;
            $moduleNotFound = 1;

            $removePermissoions = OrganizationPermission::where('role_id',$request->input('role_id'))->forceDelete();

            $modules = ModuleFeature::select('module')->distinct('module')->get();
            foreach ($modules as $key => $module) {
                $moduleName = $module->module;
                $moduleName = str_replace(' ', '_', $moduleName);
                if($request->exists($moduleName)){
                    $moduleNotFound = 0;
                    $moduleFetures = $request->input($moduleName);
                    foreach ($moduleFetures as $featureName => $features) {
                        $featureArr = explode('__', $featureName);
                        $featureId = $featureArr[1];
                        foreach ($features as $key => $feature) {

                            if(isset($featureData[$featureId])){
                                $featureData[$featureId][$feature] = 1;
                            }else{
                                $featureData[$featureId] =  array(
                                                    'feature_id' => $featureId,
                                                    'role_id' => $request->input('role_id'),
                                                    'created_by' => $user->id,
                                                    'is_active' => 1,
                                                    $feature => 1,
                                                );
                            }
                        }
                    }
                }
            }

            if($moduleNotFound){
                DB::commit();
                return redirect('administration/permissions/'.$request->input('role_id'))->with('message', trans('messages.PERMISSION_UPDATED'));
            }

            foreach ($featureData as $i => $values) {
                if(OrganizationPermission::insert($values)){
                    $commit = 1;
                }else{
                    $commit = 0;
                    DB::rollback();
                    return redirect('administration/permissions')->with('error', trans('messages.SOMETHING_WENT_WRONG'));
                }
            }

            if(($commit == 1)){
                DB::commit();
                return redirect('administration/permissions/'.$request->input('role_id'))->with('message', trans('messages.Permission Added'));
            }else{
                DB::rollback();
                return redirect('administration/permissions/'.$request->input('role_id'))->with('error', trans('messages.SELECT_PERMISSION'));    
            }

            
        } catch (Exception $e) {
            DB::rollback();
            return redirect('administration/permissions')->with('error', trans('messages.SOMETHING_WENT_WRONG'));
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
        //
    }
}
