<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;
    protected $table = 'documents';
    protected $guard_name = 'web';


    protected $fillable = [
        'id', 'name', 'path', 'folder_id', 'user_id', 'created_by', 'updated_by', 'created_at', 'updated_at', 'deleted_at'
    ];
}
