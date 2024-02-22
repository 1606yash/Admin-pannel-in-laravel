<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Auth;

class Permission extends Model
{

    protected $fillable = ["permission_category_id"
    ,"permission_name"
    ,"created_at"
    ,"created_by"
    ,"updated_at"
    ,"updated_by"];
    protected $table = 'permissions';
    protected $primaryKey = "id";

    public function permissionCategory()
    {
        return $this->belongsTo(PermissionCategory::class);
    }

    public static function getPermissionsByRoleId($roleId)
    {
        return self::where('id', $id)->first();
    }

    public function category()
    {
        return $this->belongsTo(PermissionCategory::class, 'permission_category_id');
    }
}
