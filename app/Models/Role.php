<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Passport\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class Role extends Authenticatable implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;
    use HasApiTokens, Notifiable, HasRoles;



    protected $table = 'roles';
    protected $guard_name = 'web';


    protected $fillable = [
        'id', 'role_name', 'role_description', 'is_self_registration', 'approved_by_role_id', 'created_by', 'updated_by',
        'created_at', 'updated_at', 'deleted_at', 'is_active','role_slug', 'parent_id', 'department_id'
    ];

    public static function getAllRoles()
    {
        return self::where('role_name', '<>', 'Super Admin')->get();
    }
    public static function getRoleByID($id)
    {
        return self::where('id', $id)->first();
    }
    public static function deleteRoleByID($id)
    {
        $role = self::where('id', $id)->first();
        if ($role) {
            $role->delete();
            return true;
        }
        return false;
    }

    public static function allRoles($roleName = null)
    {
        return self::select('id as role_id', 'role_name', 'role_description')->get();
        // if ($roleName == 'Super Admin') {
        //     return self::select('id as role_id', 'role_name', 'role_description')->get();
        // }
        // if ($roleName == 'HR') {
        //     return self::where('role_name', '!=', 'Super Admin')
        //         ->where('role_name', '!=', 'HR')
        //         ->select('id as role_id', 'role_name', 'role_description')->get();
        // } else {
        //     return self::where('role_name', '!=', 'Super Admin')
        //         ->where('role_name', '!=', 'HR')
        //         ->where('role_name', '!=', $roleName)
        //         ->select('id as role_id', 'role_name', 'role_description')->get();
        // }
    }
    public static function getRoleIDByName($name)
    {
        return self::where('role_name', $name)->id;
    }

    public static function getRoleIdBySlug($slug){
        return self::where('role_slug', $slug)->id;
    }

    public static function getRoleIDByUserID($user_id){
        $roleId = User::select('role_id')->where('id',$user_id)->first();
        return $roleId;
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions', 'role_id', 'permission_id');
    }
}
