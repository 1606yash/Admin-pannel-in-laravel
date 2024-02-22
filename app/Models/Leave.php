<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class Leave extends Model
{
    use HasFactory;
    protected $table = 'leaves';
    protected $guard_name = 'web';
    protected $fillable = ['user_id', 'leave_type_id', 'from_date', 'to_date', 'applying_to', 'leave_reason', 'reject_reason', 'attachment', 'status', 'approved_by', 'approved_on', 'created_by', 'updated_by'];

    /**
     * @name validationRules
     * @desc validation rules
     * @return array
    */
    public static function validationRules() {
        return [
            'leave_type_id' => 'required|exists:leave_types,id',
            'from_date' => 'required',
            'to_date' => 'required',
            'applying_to' => 'required'
        ];
    }

    /**
     * @leave Validation messages
     * @var array
    */
    public static $validationMessages = [
        'leave_type_id.required'=> 'Leave Type id is required',
        'leave_type_id.exists' => 'Selected Leave Type is invalid.',
        'from_date.required' => 'From Date is required',
        'to_date.required' => 'To Date is required',
        'applying_to.required' => 'Applying To is required',
    ]; 

    /**
     * @name getLeavesvalidationRules
     * @desc validation rules
     * @return array
    */
    public static function getLeavesValidationRules() {
        return [
            'user_id' => 'required'
        ];
    }

    /**
     * @leave Validation messages
     * @var array
    */
    public static $getLeavesValidationMessages = [
        'user_id.required'=> 'User id is required'
    ];

    public static function getLeaves($userId, $perPage, $skip, $filters)
    {
        $query = self::where('user_id', $userId);
        if (!empty($filters['year'])) {
            $query->whereYear('created_at', $filters['year']);
        }
        
        if (!empty($filters['month'])) {
            $query->whereMonth('created_at', $filters['month']);
        }
        $query->skip($skip)->take($perPage);
        return $query->get();
    }

    public static function getUserLeaveCountBetweenDates($userId, $firstDayOfMonth, $lastDayOfMonth)
    {
        $query = self::where('user_id', $userId)->whereBetween('from_date', [$firstDayOfMonth, $lastDayOfMonth])
            ->whereBetween('to_date', [$firstDayOfMonth, $lastDayOfMonth])
            ->where('status', Config::get('constants.LEAVE_STATUS.Approved'));
        $totalLeaves =  $query->count();
        return $totalLeaves;
    }

    public static function getUserLeaveStatusForDay($userId, $attendanceDate)
    {
        $query = self::where('user_id', $userId)->whereDate('from_date', '<=', $attendanceDate)->whereDate('to_date', '>=', $attendanceDate)
            ->where('status', Config::get('constants.LEAVE_STATUS.Approved'));
        $leaveStatus =  $query->first();
        return $leaveStatus;
    }

    public static function getUserTotalLeaveTaken($userId)
    {
        $query = self::where('user_id', $userId)->where('status', Config::get('constants.LEAVE_STATUS.Approved'));
        $totalLeaveTaken =  $query->count();
        return $totalLeaveTaken;
    }

    public static function getUserExistingLeaves($userId, $fromDate, $toDate)
    {
        $query = self::where('user_id', $userId)
        ->where(function ($query) use ($fromDate, $toDate) {
            $query->whereBetween('from_date', [$fromDate, $toDate])
                ->orWhereBetween('to_date', [$fromDate, $toDate])
                ->orWhere(function ($q) use ($fromDate, $toDate) {
                    $q->where('from_date', '<=', $fromDate)
                        ->where('to_date', '>=', $toDate);
                });
        });
        $existingLeaves =  $query->first();
        return $existingLeaves;
    }

    public static function getEmployeesLeaveRequests($userId, $perPage, $skip, $filters)
    {
        $query = self::select('leaves.*', \DB::raw("CONCAT(users.first_name, ' ', users.last_name) AS name"), 'roles.role_name', \DB::raw("CONCAT(applying_to_user.first_name, ' ', applying_to_user.last_name) AS applying_to_user"),
        )->leftJoin('users', 'leaves.user_id', '=', 'users.id')->leftJoin('roles', 'users.role_id', '=', 'roles.id')->leftJoin('users as applying_to_user', 'leaves.applying_to', '=', 'applying_to_user.id')

        ->where('users.reporting_manager_id', $userId);

        if (!empty($filters['year'])) {
            $query->whereYear('leaves.created_at', $filters['year']);
        }
        
        if (!empty($filters['month'])) {
            $query->whereMonth('leaves.created_at', $filters['month']);
        }
        if (!empty($filters['role_id'])) {
            $query->where('users.role_id', $filters['role_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('leaves.status', $filters['status']);
        }
        
        if (!empty($filters['creation_date_range'])) {
            switch ($filters['creation_date_range']) {
                case 'current_month':
                    $query->whereMonth('leaves.created_at', now()->month);
                    break;
                case 'last_month':
                    $query->whereMonth('leaves.created_at', now()->subMonth()->month);
                    break;
                case 'last_3_months':
                    $query->where('leaves.created_at', '>=', now()->subMonths(3));
                    break;
                case 'last_6_months':
                    $query->where('leaves.created_at', '>=', now()->subMonths(6));
                    break;
                case 'current_year':
                    $query->whereYear('leaves.created_at', now()->year);
                    break;
                case 'last_year':
                    $query->whereYear('leaves.created_at', now()->subYear()->year);
                    break;
                case 'last_3_years':
                    $query->where('leaves.created_at', '>=', now()->subYears(3));
                    break;
                default:
                    break;
            }
        }
        
        $query->skip($skip)->take($perPage);
        return $query->get();
    }

    
}
