<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserWorkExperience extends Model
{
    use HasFactory;
    protected $table = 'user_work_experiences';
    protected $guard_name = 'web';
    protected $fillable = ['user_id', 'company_name', 'designation', 'location', 'start_date', 'end_date', 'document_image_path', 'created_at', 'updated_at', 'deleted_at'];
}
