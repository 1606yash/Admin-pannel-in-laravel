<?php

namespace App\Models;
use Illuminate\Notifications\Notifiable;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Passport\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class State extends Authenticatable implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;
    use HasApiTokens, Notifiable, HasRoles;



    protected $table = 'states';
    protected $guard_name = 'web';


    protected $fillable = [
        'id', 'state_name', 'created_by', 'updated_by', 'created_at', 'updated_at', 'deleted_at'
    ];

    public static function getStateByID($id){
        return self::where('id',$id)->first();
    }
    public static function getStateByDistrictId($district_id){
        $state=District::leftJoin('states', 'states.id', '=', 'districts.state_id')
            ->where('districts.id', '=', $district_id ?? null)
            ->select(['states.id as state_id', 'states.state_name'])->get();
            return $state;
    }
    public static function getAllStates(){
        return self::all();
    }
    public static function allStates(){
        return self::select('id as state_id','state_name')
        ->orderBy('state_name','asc')
        ->get();
    }
}
