<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FolderPermissionCategory extends Model
{
    use HasFactory;
    protected $table = 'folder_permission_categories';
    protected $guard_name = 'web';


    protected $fillable = [
        'id', 'category_name', 'created_by', 'updated_by'
    ];

}
