<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Passport\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class ProfileInformation extends Authenticatable implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;
    use HasApiTokens, Notifiable, HasRoles;



    protected $table = 'profile_informations';
    protected $guard_name = 'web';


    protected $fillable = [
        'id', 'user_id', 'gender_id', 'date_of_birth', 'phone_no', 'email', 'address',
        'highest_qualification_id', 'year_of_completion', 'field_of_study_id', 'license_number', 'license_expiry_date',
        'license_type_id', 'last_company_name', 'designation', 'start_date', 'end_date', 'bank_id', 'account_number', 'ifsc_code',
        'ambulance_id', 'shift_id', 'attendent_id', 'date_of_joining', 'reporting_manager_id', 'state_id', 'district_id', 'city_id',
        'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by', 'profile', 'role_id','employee_id',
        'service_area' , 'station_area', 'middle_name','pan_no', 'aadhar_no'
    ];

    public static function addProfileInformation($data)
    {
        return self::create($data);
    }
    public static function updateProfileInformation($userId, $data)
    {
        $profileInfo = self::where('user_id', $userId)->first();
        if ($profileInfo) {
            return $profileInfo->update($data);
        } else {
            return self::create($data);
        }
    }
    public static function profileDoc($userId)
    {
        return $profileInfo = self::where('user_id', $userId ?? null)->first();
    }
    public static function getPanAadharByUserId($userId)
    {
        $data= self::where('user_id', $userId)->first();
        return  $data;
    }
    public static function findSuperiorUser($userId)
    {
        return ProfileInformation::leftJoin('users', 'users.id', 'profile_informations.user_id')
        ->where('profile_informations.user_id', $userId ?? null)
        ->select(['profile_informations.reporting_manager_id', 'profile_informations.user_id', 'users.first_name', 'users.email'])->first();
    }
  
}
