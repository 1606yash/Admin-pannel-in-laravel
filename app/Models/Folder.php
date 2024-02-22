<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{
    use HasFactory;
    protected $guard_name = 'web';
    protected $fillable = ['user_id', 'name', 'parent_folder_id', 'created_by', 'updated_by'];

    public static function getFolders($userId, $perPage, $skip)
    {
        $query = self::with('documents');
        $query->skip($skip)->take($perPage);
        return $query->get();
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'folder_id');
    }
}
