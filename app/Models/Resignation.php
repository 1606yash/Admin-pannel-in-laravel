<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class Resignation extends Model
{
    use HasFactory;
    protected $table = 'resignations';
    protected $guard_name = 'web';
    protected $fillable = ['user_id', 'resignation_date', 'last_working_day', 'resignation_reasons_id', 'applying_to', 'remark', 'attachment', 'approved_by', 'rejection_reason', 'status', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * @name validationRules
     * @desc validation rules
     * @return array
     */
    public static function validationRules() {
        return [
            'resignation_reasons_id' => 'required',
            'applying_to' => 'required'
        ];
    }

    /**
     * @leave Validation messages
     * @var array
     */
    public static $validationMessages = [
        'resignation_reasons_id.required'=> 'Resignation Reason id is required',
        'applying_to.required' => 'Applying to is required'
    ];

    /**
     * @name getResignationValidationRules
     * @desc validation rules
     * @return array
    */
    public static function getResignationValidationRules() {
        return [
            'user_id' => 'required'
        ];
    }

    /**
     * @leave Validation messages
     * @var array
    */
    public static $getResignationValidationMessages = [
        'user_id.required'=> 'User id is required'
    ];
    
    public static function getResignation($userId)
    {
        return $resignations = self::join('resignation_reasons', 'resignations.resignation_reasons_id', '=', 'resignation_reasons.id')
            ->join('users', 'resignations.applying_to', '=', 'users.id')
            ->where('resignations.user_id', $userId)
            ->select('resignations.*', 'resignation_reasons.reason as resignation_reason',\DB::raw("CONCAT(users.first_name, ' ', users.last_name) AS applying_to_name"), \DB::raw(Config::get('constants.NOTICE_PERIOD') . " as notice_period"))
            ->get();
    }
    
}
