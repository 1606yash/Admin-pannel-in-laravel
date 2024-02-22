<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AmbulanceUserMapping extends Model
{
    use HasFactory;
    protected $table = 'ambulance_user_mappings';
    protected $guard_name = 'web';
    protected $fillable = ['user_id', 'ambulance_id', 'shift_type_id', 'created_at', 'updated_at', 'deleted_at'];

    public static function getAmbulanceStaffByAmbulanceId($ambulanceId, $perPage, $skip, $filters)
    {
        $query = self::where('ambulance_id', $ambulanceId)
            ->select('ambulance_user_mappings.*','ambulance_shifts.*', \DB::raw("CONCAT(users.first_name, ' ', users.last_name) AS name"), 'users.employee_id', 'shift_types.shift_name')
            ->leftJoin('users', 'ambulance_user_mappings.user_id', '=', 'users.id')
            ->leftJoin('shift_types', 'ambulance_user_mappings.shift_type_id', '=', 'shift_types.id')
            ->join('ambulance_shifts', 'ambulance_user_mappings.id', '=', 'ambulance_shifts.ambulance_mapping_id');

        if (!empty($filters['year'])) {
            $query->whereYear('ambulance_shifts.date', $filters['year']);
        }
        
        if (!empty($filters['month'])) {
            $query->whereMonth('ambulance_shifts.date', $filters['month']);
        }
        $query->skip($skip)->take($perPage);
        return $query->get();    
    }
}
