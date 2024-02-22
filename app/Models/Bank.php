<?php

namespace App\Models;
use Illuminate\Notifications\Notifiable;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Passport\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class Bank extends Authenticatable implements Auditable
{
    use \OwenIt\Auditing\Auditable;
  
    use HasApiTokens, Notifiable, HasRoles;



    protected $table = 'bank_lists';
    protected $guard_name = 'web';


    protected $fillable = [
        'id', 'bank_name', 'created_at', 'updated_at'
    ];

    public static function getAllBanks(){
        return self::all();
    }
    public static function allBanks(){
        return self::select('id as bank_id','bank_name')
        ->orderBy('bank_name','asc')
        ->get();
    }
}
