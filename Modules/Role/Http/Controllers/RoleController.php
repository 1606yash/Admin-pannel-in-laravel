<?php

namespace Modules\Role\Http\Controllers;

use App\Models\Permission;
use App\Models\PermissionCategory;
use App\Models\Role as ModelsRole;
use App\Models\RolePermission;
use App\Models\User;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Role\Entities\Role;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    /**
     * Display a listing of roles with optional filtering.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */

    public function index()
    {
        return view('role::index');
    }

    public function roleList(Request $request)
    {
        // Retrieve role data based on request parameters
        $roleData = ModelsRole::select('roles.*')->where(function ($query) use ($request) {
            if (!empty($request->toArray())) {
                if (isset($request->rolename) && !empty($request->rolename)) {
                    // Filter roles by role name if 'rolename' is provided in the request
                    $query->where('role_name', 'LIKE', '%' . $request->rolename . '%');
                }
            }
        })
            ->get();

        // Calculate the number of staff members for each role
        foreach ($roleData as $role) {
            $role->no_of_staff = User::where('role_id', $role->id)
                ->whereNull('deleted_at')
                ->count();
        }

        // If the request is AJAX, return the data as JSON for Datatables
        if ($request->ajax()) {
            return Datatables::of($roleData)
                ->addIndexColumn()
                ->addColumn('updated_at', function ($row) {
                    return date('d/m/Y', strtotime($row->updated_at));
                })
                ->addColumn('action', function ($row) {
                    $btn = '';
                    $btn .= "<ul class='nk-tb-actions gx-1'><li><div class='drodown mr-n1'><a href='#' class='dropdown-toggle btn btn-icon btn-trigger' data-toggle='dropdown'><em class='icon ni ni-more-h'></em></a><div class='dropdown-menu dropdown-menu-right'><ul class='link-list-opt no-bdr'>";

                    // Edit role action
                    if (true) {
                        $btn .= "<li>
                                    <a href='#' data-target='addRole' data-id='" . $row->id . "' class='editItem toggle'>
                                        <em class='icon ni ni-edit'></em> <span>Edit</span>
                                    </a>
                                </li>";
                    }

                    // Manage Permissions action
                    if (true) {
                        $btn .= "<li>
                                        <a href='/role/manage-permission/" . $row->id . "/' data-id='" . $row->id . "' class='nav-link'>
                                            <em class='icon ni ni-note-add'></em> <span>Manage Permissions</span>
                                        </a>
                                    </li>";
                    }

                    $btn .= "</ul></div></div></li></ul>";
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }


    /**
     * Store or update a role based on the provided data.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeRole(Request $request)
    {
        try {
            // Validation rules for the request
            $rules = array(
                'role_name' => 'required',
                'role_description' => 'required',
            );

            // Create a validator instance
            $validator = \Validator::make($request->all(), $rules);

            // Check if validation fails
            if ($validator->fails()) {
                $messages = $validator->messages();
                return redirect('role')->withErrors($validator);
            } else {
                // Check if the role ID is provided for update
                if ($request->input("id") && $request->input("id") != '0' && $request->input("id") != '') {
                    // Check if the role name already exists for another role (excluding the current role)
                    $checkRoleName = Role::where('role_name', $request->input("role_name"))
                        ->where('id', '!=', $request->input("id"))
                        ->first();

                    // Return failure response if the role name already exists
                    if ($checkRoleName) {
                        return response()->json(['status' => 'Fail', 'message' => trans('messages.ROLE_ALREADY_EXISTS')]);
                    }

                    // Update the existing role
                    $role = Role::find($request->input("id"));
                    $role->role_name = ucwords($request->input("role_name"));
                    $role->role_description = $request->input("role_description");
                } else {
                    // Check if the role name already exists for a new role
                    $checkRoleName = Role::where('role_name', $request->input("role_name"))->first();

                    // Return failure response if the role name already exists
                    if ($checkRoleName) {
                        return response()->json(['status' => 'Fail', 'message' => trans('messages.ROLE_ALREADY_EXISTS')]);
                    }

                    // Create a new role
                    $role = new Role();
                    $role->role_name = ucwords($request->input("role_name"));
                    $role->role_description = $request->input("role_description");
                    $role->role_slug = Str::slug($request->input("role_name"));
                }

                // Save the role data
                if ($role->save()) {
                    // Return success response based on whether it's an update or new role
                    if ($request->input("id") && $request->input("id") != '0' && $request->input("id") != '') {
                        return response()->json(['status' => 'success', 'message' => trans('messages.ROLE_UPDATED')]);
                    } else {
                        return response()->json(['status' => 'success', 'message' => trans('messages.ROLE_ADDED')]);
                    }
                } else {
                    // Return failure response if saving fails
                    return response()->json(['status' => 'Fail', 'message' => trans('messages.ROLE_NOT_ADDED')]);
                }
            }
        } catch (Exception $e) {
            // Return failure response for unexpected exceptions
            return response()->json(['status' => 'Fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
        }
    }

    /**
     * Delete a role by its ID.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function destroyRole(Request $request)
    {
        // Retrieve the role ID from the request
        $id = $request->input("id");

        // Attempt to delete the role using a custom method (deleteRoleByID)
        $item = ModelsRole::deleteRoleByID($id);

        // Check if the role deletion was successful
        if ($item) {
            // Return a success response
            return array('roles' => array(), 'success' => true, 'msg' => 'success');
        } else {
            // Return a failure response if the role deletion fails
            return array('success' => false, 'roles' => array(), 'msg' => 'fails');
        }
    }


    /**
     * Get role details by its ID.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function getRole(Request $request)
    {
        // Retrieve the role ID from the request
        $id = $request->input("id");

        // Attempt to get role details using a custom method (getRoleByID)
        $role = ModelsRole::getRoleByID($id ?? null);

        // Check if role details were successfully retrieved
        if (!empty($role->toArray())) {
            // Return a success response with role details
            return array('role' => $role, 'success' => true);
        } else {
            // Return a failure response if role details are not found
            return array('success' => false, 'role' => array());
        }
    }


    /**
     * Display the permission management view for a specific role.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function managePermission(Request $request)
    {
        // Retrieve the role ID from the request
        $roleId = $request->role_id;

        // Retrieve all permission categories
        $permissionCategory = PermissionCategory::getAllPermissionCategories() ?? [];

        // Retrieve role permissions based on the role ID
        $permissions = RolePermission::select('role_permissions.role_id', 'role_permissions.permission_id', 'roles.role_name', 'permission_categories.category_name', 'permissions.permission_name')
            ->leftJoin('roles', 'role_permissions.role_id', '=', 'roles.id')
            ->leftJoin('permissions', 'permissions.id', '=', 'role_permissions.permission_id')
            ->leftJoin('permission_categories', 'permissions.permission_category_id', '=', 'permission_categories.id')
            ->where('role_permissions.role_id', $roleId)
            ->groupBy('permission_categories.id')
            ->get();

        // Retrieve role details by its ID
        $role = ModelsRole::getRoleByID($roleId);

        // Get the role name from the retrieved role details
        $roleName = $role->role_name;

        // Return the view with relevant data
        return view('role::viewPermission', ['roleId' => $roleId, 'roleName' => $roleName, 'permissionCategories' => $permissionCategory, 'permissions' => $permissions]);
    }

    /**
     * Store or update permissions for a specific role.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storePermission(Request $request)
    {
        try {
            // Decode JSON data from the request
            $permissionData = json_decode($request->categoryData, true);

            // Retrieve role ID from the request
            $roleId = $request->role_id;

            // Fetch existing permissions for the role and category
            $getPermissions = RolePermission::select('role_permissions.role_id', 'role_permissions.permission_id', 'roles.role_name', 'permission_categories.category_name', 'permissions.permission_name')
                ->leftJoin('roles', 'role_permissions.role_id', '=', 'roles.id')
                ->leftJoin('permissions', 'permissions.id', '=', 'role_permissions.permission_id')
                ->leftJoin('permission_categories', 'permissions.permission_category_id', '=', 'permission_categories.id')
                ->where('role_permissions.role_id', $roleId)
                ->get();

            // If existing permissions are found, delete them
            if (!empty($getPermissions)) {
                $deletePermissions = RolePermission::leftJoin('roles', 'role_permissions.role_id', '=', 'roles.id')
                    ->leftJoin('permissions', 'permissions.id', '=', 'role_permissions.permission_id')
                    ->leftJoin('permission_categories', 'permissions.permission_category_id', '=', 'permission_categories.id')
                    ->where('role_permissions.role_id', $roleId)
                    ->delete();
            }

            // Iterate through permission data and assign permissions
            if (!empty($permissionData)) {
                foreach ($permissionData as $permission) {
                    if ($permission['categoryName'] === 'masterToggle') {
                        continue; // Skip to the next iteration
                    }
                    // Assign full access to all permissions in the category
                    if ($permission['categoryPermission'] == 'full-access') {
                        // Fetch all permission IDs in the category
                        $getPermissionIds = Permission::where('permission_category_id', $permission['categoryId'])->get();
                        if (!empty($getPermissionIds)) {
                            // Assign the permissions to the role
                            foreach ($getPermissionIds as $getPermission) {
                                RolePermission::create(['role_id' => $roleId, 'permission_id' => $getPermission->id, 'created_by' => 1, 'updated_by' => 1]);
                            }
                        } else {
                            // Return failure response if permission IDs are not found
                            return response()->json(['status' => 'Fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
                        }
                    } else {
                        // Return failure response for unsupported category permission
                        return response()->json(['status' => 'Fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
                    }
                }
            }

            // Return success response
            return response()->json(['status' => 'success', 'message' => trans('messages.PERMISSION_UPDATED')]);
        } catch (Exception $e) {
            // Return failure response for unexpected exceptions
            return response()->json(['status' => 'Fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
        }
    }
}
