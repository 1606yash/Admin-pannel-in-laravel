<?php

namespace App\Models;
use Illuminate\Notifications\Notifiable;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Passport\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class ShiftType extends Authenticatable implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasApiTokens, Notifiable, HasRoles;



    protected $table = 'shift_types';
    protected $guard_name = 'web';


    protected $fillable = [
        'id', 'shift_name','state_id','created_at', 'updated_at'
    ];

    public static function getAllShifts(){
        return self::all();
    }
    public static function allShifts(){
        return self::select('id as shift_id','shift_name')
        ->orderBy('shift_name','asc')
        ->get();
    }

    public static function getShiftByName($shiftName){
        return self::where('shift_name',$shiftName)->first();
        
    }
}
