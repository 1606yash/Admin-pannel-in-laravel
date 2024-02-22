<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLocation extends Model
{
    use HasFactory;
    protected $table = 'user_locations';
    protected $guard_name = 'web';
    protected $fillable = ['user_id', 'latitude', 'longitude', 'created_at', 'updated_at'];
}
