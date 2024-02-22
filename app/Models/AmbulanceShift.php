<?php

namespace App\Models;

use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\SoftDeletes;

class AmbulanceShift extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
   // use SoftDeletes;

    protected $table = 'ambulance_shifts';
    protected $guard_name = 'web';
    protected $fillable = ['ambulance_mapping_id', 'user_type','type', 'service_area_id', 'station_area', 'created_by', 'updated_by', 'created_at', 'updated_at', 'deleted_at', 'start_time', 'end_time', 'date'];

    /**
     * @name validationRules
     * @desc validation rules
     * @return array
    */
    public static function validationRules() {
        return [
            'data.*.ambulance_id' => 'required|exists:ambulances,id',
            'data.*.user_id' => 'required|exists:users,id',
            'data.*.user_type' => 'required',
            'data.*.type' => 'required',
            'data.*.shift_type_id' => 'required',
            'data.*.start_time' => 'required',
            'data.*.end_time' => 'required',
            'data.*.date' => 'required'
        ];
    }

    /**
     * @leave Validation messages
     * @var array
    */
    public static $validationMessages = [
        'data.*.ambulance_id.required'=> 'Ambulance id is required',
        'data.*.ambulance_id.exists' => 'Selected Ambulance id is invalid.',
        'data.*.user_id.required'=> 'User id is required',
        'data.*.user_id.exists' => 'Selected User id is invalid.',
        'data.*.user_type.required' => 'User Type is required',
        'data.*.type.required' => 'Type is required',
        'data.*.shift_type_id.required' => 'Shift Type Id is required',
        'data.*.start_time.required' => 'Start Time is required',
        'data.*.end_time.required' => 'End Time is required',
        'data.*.date.required' => 'Start Date is required',
    ]; 

}
