<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Auth;

class PermissionCategory extends Model
{

    protected $fillable = ["category_name"
    ,"created_at"
    ,"created_by"
    ,"updated_at"
    ,'updated_by'];
    protected $table = 'permission_categories';
    protected $guard_name = 'web';
    protected $primaryKey = "id";


    public function permissions()
    {
        return $this->hasMany(Permission::class, 'permission_category_id');
    }

    public static function getPermissionCategoriesWithPermissions() {
        return self::with('permissions')->get();
    }

    public static function getAllPermissionCategories(){
        return self::select('id','category_name')->get();
    }
}
