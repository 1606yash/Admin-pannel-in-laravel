<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoutePoint extends Model
{
    use HasFactory;
    protected $table = 'route_points';
    protected $guard_name = 'web';
    protected $fillable = ['case_id', 'driver_id', 'latitude', 'longitude', 'created_at', 'updated_at'];
}
