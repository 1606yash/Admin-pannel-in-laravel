<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use DB;

class Attendance extends Model
{
    use HasFactory;
    protected $table = 'attendances';
    protected $guard_name = 'web';
    protected $fillable = ['user_id', 'shift_type_id','attendance_date', 'login_time', 'login_meter_reading', 'logout_time', 'logout_meter_reading', 'duration', 'km_run','', 'login_meter_photo', 'logout_meter_photo', 'station_location', 'login_location', 'login_latitude', 'login_longitude', 'logout_location', 'logout_latitude', 'logout_longitude', 'login_status'];

    protected $casts = [
        'login_time' => 'string',
        'logout_time' => 'string',
        // Add other fields here if needed
    ];

     /**
     * @name validationRules
     * @desc validation rules
     * @return array
     */
    public static function validationRules() {
        return [
            'attendance_date' => 'required_if:type,checkin',
            'login_time' => 'required_if:type,checkin',
            'login_location' => 'required_if:type,checkin',
            'login_latitude' => 'required_if:type,checkin',
            'login_longitude' => 'required_if:type,checkin',
            'logout_time' => 'required_if:type,checkout',
            'logout_location' => 'required_if:type,checkout',
            'logout_latitude' => 'required_if:type,checkout',
            'logout_longitude' => 'required_if:type,checkout'
        ];
    }

    /**
     * @leave Validation messages
     * @var array
     */
    public static $validationMessages = [
        'attendance_date.required_if'=> 'Attendance Date is required',
        'login_time.required_if' => 'Login Time is required',
        'login_location.required_if' => 'Login Location is required',
        'login_latitude.required_if' => 'Login Latitude is required',
        'login_longitude.required_if' => 'Login Longitude is required',
        'logout_time.required_if' => 'Logout Time is required',
        'logout_location.required_if' => 'Logout Location is required',
        'logout_latitude.required_if' => 'Logout Latitude is required',
        'logout_longitude.required_if' => 'Logout Longitude is required',
    ]; 

    public static function getUserAttendanceLogsForDate($userId, $attendanceDate)
    {
        $query = self::where('user_id', $userId)->leftJoin('shift_types', 'attendances.shift_type_id', '=', 'shift_types.id') // Adjust the join condition based on your database schema
            ->select('attendances.*', 'shift_types.shift_name as shift_name')
            ->where('attendance_date', $attendanceDate);
        $attendanceLogs =  $query->get();
        return $attendanceLogs;
    }

    public static function getUserPresentDaysCountForMonth($userId, $firstDayOfMonth, $lastDayOfMonth)
    {
        $query = self::where('user_id', $userId)
            ->whereBetween('attendance_date', [$firstDayOfMonth, $lastDayOfMonth])
            ->where('login_status', Config::get('constants.LOGIN_STATUS.Present'));
        $totalPresentDays =  $query->count();
        return $totalPresentDays;
    }

    public static function getUserAttendanceStatusForDate($userId, $attendanceDate)
    {
        $query = self::where('user_id', $userId)->where('attendance_date', $attendanceDate);
        $currentDayStatus =  $query->first();
        return $currentDayStatus;
    }

    public static function getAttendanceStatusByShift($userId,$date,$shiftId){
        $query = self::where('user_id', $userId)->where('shift_type_id',$shiftId)->where('attendance_date',$date)->first();
        if(empty($query)){
            $assignedShift = AmbulanceShift::where('user_id', $userId)->where('shift_type_id',$shiftId)->first();
            if(!empty($assignedShift)){
                $attendanceStatus = Config::get('constants.ATTENDENCE_STATUS.Absent');
            }else{
                $attendanceStatus = '';
            }
            
        }else{
            $attendanceStatus = Config::get('constants.ATTENDENCE_STATUS.Present');
        }

        return $attendanceStatus;
    }

    public static function getEmployeeAttendance($userId, $perPage, $skip, $filters)
    {
        $fromDate = $filters['year'] . "-" . $filters['month'] . "-01";
        $toDate = date("Y-m-t", strtotime($fromDate));

        $rawResults = DB::select('CALL GenerateAttendanceReport(?, ?)', [$fromDate, $toDate]);

        $filteredAndPaginatedResults = collect($rawResults)
            ->filter(function ($attendanceRecord) use ($userId) {
                return $attendanceRecord->user_id == $userId;
            })
            ->slice($skip)
            ->take($perPage);

        $formattedResults = [];
        foreach ($filteredAndPaginatedResults as $attendanceRecord) {
            $formattedResults[] = [
                'user_id' => $attendanceRecord->user_id,
                'user_name' => $attendanceRecord->user_name,
                'role_id' => $attendanceRecord->role_id,
                'district_id' => $attendanceRecord->district_id,
                'shift_type_id' => $attendanceRecord->shift_type_id,
                'employee_id' => $attendanceRecord->employee_id,
                'district_name' => $attendanceRecord->district_name,
                'role_name' => $attendanceRecord->role_name,
                'shift_name' => $attendanceRecord->shift_name,
                'attendance_status' => $attendanceRecord->attendance_status,
                'attendance_date' => $attendanceRecord->attendance_date,
                'login_time' => $attendanceRecord->login_time,
                'login_location' => $attendanceRecord->login_location,
                'logout_time' => $attendanceRecord->logout_time,
                'logout_location' => $attendanceRecord->logout_location,
                'login_meter_reading' => $attendanceRecord->login_meter_reading,
                'logout_meter_reading' => $attendanceRecord->logout_meter_reading,
                'duration' => $attendanceRecord->duration,
                'km_run' => $attendanceRecord->km_run,
            ];
        }
        return $formattedResults;
    }

}
