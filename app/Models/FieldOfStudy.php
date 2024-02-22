<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Passport\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use DB;
use App\Notifications\EmailVerificationMail;
use Illuminate\Support\Facades\Crypt;

class FieldOfStudy extends Authenticatable implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    use HasApiTokens, Notifiable, HasRoles;



    protected $table = 'field_of_study_types';
    protected $guard_name = 'web';


    protected $fillable = [
        'id', 'type','created_at', 'updated_at'
    ];
    public static function getAllFieldOfStudy(){
        return self::all();
    }
    public static function allFieldOfStudyTypes(){
        return self::select('id as field_of_study_type_id','type as field_of_study_type')
        ->orderBy('type','asc')
        ->get();
    }
    
}
