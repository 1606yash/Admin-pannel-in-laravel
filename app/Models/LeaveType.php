<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    use HasFactory;

    public static function allLeaveTypes() {
        return self::select('id as leave_type_id','name')
        ->orderBy('id','asc')
        ->get();
    }
}
