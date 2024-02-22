<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResignationReason extends Model
{
    use HasFactory;

    public static function allResignationReasons() {
        return self::select('id as resignation_reason_id','reason')
        ->orderBy('id','asc')
        ->get();
    }
}
