<?php

namespace Modules\Role\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiBaseController;
use App\Models\Role;
use Illuminate\Support\Facades\Config;

class PermissionController extends ApiBaseController
{
    public function getPermissionsByRoleId(Request $request)
    {
        try {
            $role = Role::with(['permissions.category:id,category_name'])->findOrFail($request->role_id);
            $permissionsByCategory = $role->permissions->groupBy('category.category_name');
            $permissionsByCategory = $permissionsByCategory->map(function ($permissions) {
                return $permissions->map(function ($permission) {
                    return [
                        'id' => $permission['id'],
                        'permission_category_id' => $permission['permission_category_id'],
                        'permission_name' => $permission['permission_name'],
                        'created_by' => $permission['created_by'],
                        'updated_by' => $permission['updated_by'],
                        'created_at' => $permission['created_at'],
                        'updated_at' => $permission['updated_at'],
                    ];
                });
            });
            if($permissionsByCategory) {
                return $this->sendSuccessResponse($permissionsByCategory, 200, Config::get('constants.APIMESSAGES.PERMISSIONS_RETRIVED_SUCCESSFULLY'));
            } else {
                return $this->sendSuccessResponse($permissionsByCategory, 200, Config::get('constants.APIMESSAGES.NO_DATA_FOUND'));
            }
        } catch (\Exception $exception) {
            return $this->sendFailureResponse($exception->getMessage());
        }
    }
}
