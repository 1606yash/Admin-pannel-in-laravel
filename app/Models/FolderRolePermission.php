<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FolderRolePermission extends Model
{
    use HasFactory;
    protected $table = 'folder_role_permissions';
    protected $guard_name = 'web';


    protected $fillable = [
        'id', 'role_id', 'folder_id', 'permission_category_id', 'created_by', 'updated_by'
    ];

}
