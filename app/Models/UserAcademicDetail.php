<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAcademicDetail extends Model
{
    use HasFactory;
    protected $table = 'user_academic_details';
    protected $guard_name = 'web';
    protected $fillable = ['user_id', 'highest_qualification_id', 'year_of_completion', 'field_of_study_id', 'marksheet_file_path', 'other_qualification', 'updated_at', 'deleted_at', 'created_at'];
}
