<?php

namespace App\Http\Controllers;

use App\Models\District;
use App\Models\Role;
use App\Models\State;
use App\Models\User;
use Illuminate\Http\Request;
use Str;

class CommonController extends Controller
{
    public function getState(Request $request)
    {
        try {
            $districtId = $request->district_id;

            // Using value() to get a single value directly
            $stateId = District::where('id', $districtId)->value('state_id');

            // Check if $stateId is not null before proceeding
            if ($stateId !== null) {
                // Using value() to get a single value directly
                $state = State::where('id', $stateId)->value('state_name');

                return response()->json(['status' => 'success', 'state' => $state,'stateId' => $stateId]);
            } else {
                return response()->json(['status' => 'fail', 'message' => 'State not found for the given district']);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 'fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
        }
    }

    public function getReportingManagerForDriver(Request $request)
    {
        $reportingManagers = [];
        $roleId = $request->role_id;
        $districtId = $request->district_id;
        $role = Role::getRoleByID($roleId);
        if ((Str::lower($role->role_name) == 'driver') || (Str::lower($role->role_name) == 'attendant')) {
            $districtAnchors = User::getDistrictAnchor($districtId);
            foreach ($districtAnchors as $districtAnchor) {
                $reportingManager = []; // Initialize an array for each reporting manager

                $reportingManager['employee_name'] = $districtAnchor['district_anchor_name'];
                $reportingManager['user_id'] = $districtAnchor['user_id'];

                $reportingManagers[] = $reportingManager; // Add the reporting manager to the array
            }
        } elseif (Str::lower($role->role_name) == 'district anchor') {
            $getSubAdminAndSuperAdmin = User::getSubAdminAndSuperAdmin();
            foreach ($getSubAdminAndSuperAdmin as $admin) {
                $reportingManager = []; // Initialize an array for each reporting manager

                $reportingManager['employee_name'] = $admin['district_anchor_name'];
                $reportingManager['user_id'] = $admin['user_id'];

                $reportingManagers[] = $reportingManager; // Add the reporting manager to the array
            }
        }else{
            $getSubAdminAndSuperAdmin = User::getSubAdminAndSuperAdmin();
            foreach ($getSubAdminAndSuperAdmin as $admin) {
                $reportingManager = []; // Initialize an array for each reporting manager

                $reportingManager['employee_name'] = $admin['district_anchor_name'];
                $reportingManager['user_id'] = $admin['user_id'];

                $reportingManagers[] = $reportingManager; // Add the reporting manager to the array
            }
        }
        return response()->json(['reportingManager' => $reportingManagers]);
    }

    public function getUsersByRoleId(Request $request){
        $roleId = $request->role_id;
        $users = User::select('id', \DB::raw('COALESCE(CONCAT(users.first_name, " ", users.last_name)) as username'))->where('role_id', $roleId)->get()->toArray();
        return response()->json(['users' => $users]); 
    }
}
