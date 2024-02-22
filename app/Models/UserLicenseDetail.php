<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLicenseDetail extends Model
{
    use HasFactory;

    protected $table = 'user_license_details';
    protected $guard_name = 'web';
    protected $fillable = ['user_id', 'license_number', 'dl_type_id', 'expiry_date', 'license_image_path', 'created_at', 'updated_at', 'deleted_at'];
}
