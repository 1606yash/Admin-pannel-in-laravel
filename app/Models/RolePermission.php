<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Auth;

class RolePermission extends Model
{

    protected $fillable = ["id"
    ,"role_id"
    ,"permission_id"
    ,"created_at"
    ,"created_by"
    ,"updated_at"
    ,"updated_by"];
    
    protected $table = 'role_permissions';
    protected $primaryKey = "id";



}
