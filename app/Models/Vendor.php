<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;

    public static function allVendors() {
        return self::select('id as vendor_id','name')
        ->orderBy('id','asc')
        ->get();
    }
}
