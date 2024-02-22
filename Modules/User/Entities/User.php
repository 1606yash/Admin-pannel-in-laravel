<?php

namespace Modules\User\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Passport\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use DB;
use App\Notifications\EmailVerificationMail;

class User extends Authenticatable implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    // use SoftDeletes;
    use HasApiTokens, Notifiable, HasRoles;



    protected $table = 'users';
    protected $guard_name = 'web';


    protected $auditInclude = [
        'id', 'organization_id', 'name', 'last_name', 'email', 'password', 'phone_number', 'confirmed_at', 'confirmation_code', 'remember_token', 'created_by', 'updated_by', 'deleted_at', 'created_at', 'updated_at', 'shop_name', 'gst', 'retailer_category', 'status', 'address1', 'address2', 'country', 'state', 'pincode', 'district', 'city', 'is_synced_in_tally', 'tally_customer_id', 'source', 'credit_limit', 'used_limit'
    ];


    /*public function role()
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }*/

    public static function getUserPermissionsViaRoles($id){
        $user = User::find($id);
        $permissions = $user->getPermissionsViaRoles();
        $permissionList = [];
        if($permissions && count($permissions) > 0) {
            foreach ($permissions as $permission){
                array_push($permissionList, trim($permission->name));
            }
        }
        return $permissionList;
    }

    public static function getUserRoles($id){
        $roles = self::findOrfail($id)->getRoleNames();
        return $roles->toArray();
    }

    public function getFullNameAttribute()
    {
        return $this->name . ' ' . $this->last_name;
    }


}
