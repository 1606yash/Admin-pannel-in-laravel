<?php

namespace Modules\Attendance\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiBaseController;
use App\Models\Attendance;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use DB;
use App\Models\Holiday;
use App\Models\Leave;
use App\Models\LeaveType;

class AttendanceController extends ApiBaseController
{
    public function logAttendance(Request $request)
    {
        try {
            DB::beginTransaction();
            $validations = Attendance::validationRules();
            $validator = Validator::make($request->all(),$validations, Attendance::$validationMessages);
            if($validator->fails()) {
                $errors = $validator->errors();
                $errorsMsg = "";

                if ($errors->first()) {
                    $errorsMsg .= " " . $errors->first();
                }

                return $this->sendFailureResponse($errorsMsg);
            } else {
                $userId = FacadesAuth::user()->id;
                if ($request->has('type') && $request->type == "checkin") {
                    $attendance = new Attendance();
                    $attendance->user_id = $userId;
                    $attendance->attendance_date = $request->attendance_date;
                    $attendance->login_time = $request->login_time;
                    $attendance->login_location = $request->login_location;
                    $attendance->login_latitude = $request->login_latitude;
                    $attendance->login_longitude = $request->login_longitude;
                    if ($request->has('shift_type_id')) {
                        $checkinData->shift_type_id = $request->shift_type_id;
                    }
                    $attendance->login_meter_reading = $request->login_meter_reading;
                    $attendance->save();
                    if ($request->hasFile('login_meter_photo_attachment')) {
                        $folderName = 'attendance_attachments';
                        $attachmentUrl = Helpers::uploadAttachment($request->file('login_meter_photo_attachment'), $folderName, $attendance->id);
                        $attendance->login_meter_photo = $attachmentUrl;
                    }
                    if($attendance->save()) {
                        DB::commit();                                
                        return $this->sendSuccessResponse($attendance, 200, Config::get('constants.APIMESSAGES.CHECK_IN_SUCCESSFULLY'));
                    } else {
                        DB::rollback();
                        return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.SOMETHING_WENT_WRONG'));
                    }
                } elseif ($request->has('type') && $request->type == "checkout") {
                    $checkinData = Attendance::where('user_id', $userId)->where('attendance_date', $request->attendance_date)->first();
                    if ($checkinData) {
                        $checkinData->logout_time = $request->logout_time;
                        $checkinData->logout_location = $request->logout_location;
                        $checkinData->logout_latitude = $request->logout_latitude;
                        $checkinData->logout_longitude = $request->logout_longitude;
                        $checkinData->shift_type_id = $request->shift_type_id;
                        $checkinData->login_status = Config::get('constants.LOGIN_STATUS.Present');
                        $loginTime = new \DateTime($checkinData->login_time);
                        $logoutTime = new \DateTime($request->logout_time);
                        // Calculate the difference
                        $duration = $loginTime->diff($logoutTime);
                        // Format the duration
                        $shiftDuration = $duration->format('%H:%I:%S');
                        $checkinData->duration = $shiftDuration;
                        $checkinData->logout_meter_reading = $request->logout_meter_reading;
                        if ($request->hasFile('logout_meter_photo_attachment')) {
                            $folderName = 'attendance_attachments';
                            $attachmentUrl = Helpers::uploadAttachment($request->file('logout_meter_photo_attachment'), $folderName, $checkinData->id);
                            $checkinData->logout_meter_photo = $attachmentUrl;
                        }
                        if($checkinData->save()) {
                            DB::commit();                                
                            return $this->sendSuccessResponse($checkinData, 200, Config::get('constants.APIMESSAGES.CHECK_OUT_SUCCESSFULLY'));
                        } else {
                            DB::rollback();
                            return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.SOMETHING_WENT_WRONG'));
                        }
                    } else {
                        DB::rollback();
                        return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.CHECK_IN_DATA_NOT_FOUND'));
                    }
                } else {
                    DB::rollback();
                    return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.INVALID_LOG_ATTENDANCE_REQUEST'));
                }
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.SOMETHING_WENT_WRONG'));
        }
    }

    public function getAttendanceLogDetails($attendanceDate)
    {
        try {
            $userId = FacadesAuth::user()->id;
            $firstDayOfMonth = date('Y-m-01', strtotime($attendanceDate));
            $lastDayOfMonth = date('Y-m-t', strtotime($attendanceDate));

            $attendanceLogs = Attendance::getUserAttendanceLogsForDate($userId, $attendanceDate);
            $totalPresentDays = Attendance::getUserPresentDaysCountForMonth($userId, $firstDayOfMonth, $lastDayOfMonth);
            $totalHolidays = Holiday::getHolidaysCountForMonth($firstDayOfMonth, $lastDayOfMonth);
            $holiday = Holiday::getHolidayForDay($attendanceDate);
            $currentDayStatus = Attendance::getUserAttendanceStatusForDate($userId, $attendanceDate);
            $totalLeaves = Leave::getUserLeaveCountBetweenDates($userId, $firstDayOfMonth, $lastDayOfMonth);
            $currentDate = now()->format('Y-m-d');            
            if ($holiday > 0) {
                $attendanceStatus = Config::get('constants.ATTENDENCE_STATUS.Holiday');
            } else {
                $leaveStatus = Leave::getUserLeaveStatusForDay($userId, $attendanceDate);
                if ($leaveStatus) {
                    $leaveType = LeaveType::find($leaveStatus->leave_type_id)->name;
                    $attendanceStatus = "on $leaveType";    
                } else {
                    if ($attendanceDate < $currentDate && count($attendanceLogs) === 0) {
                        // For previous dates without logs, mark as absent
                        $attendanceStatus = Config::get('constants.ATTENDENCE_STATUS.Absent');
                    } elseif ($attendanceDate == $currentDate) {
                        // For the current date, check if the user is checked in, not logged in, or checked out
                        $attendanceStatus = $currentDayStatus ? ($currentDayStatus->logout_time ? Config::get('constants.ATTENDENCE_STATUS.CheckedOut') : Config::get('constants.ATTENDENCE_STATUS.CheckedIn')) : Config::get('constants.ATTENDENCE_STATUS.NotLoggedIn');
                    } elseif ($attendanceDate > $currentDate) {
                        // For future dates, show "empty status"
                        //$attendanceStatus = '';
                        $attendanceStatus = $currentDayStatus ? ($currentDayStatus->logout_time ? Config::get('constants.ATTENDENCE_STATUS.CheckedOut') : Config::get('constants.ATTENDENCE_STATUS.CheckedIn')) : Config::get('constants.ATTENDENCE_STATUS.NotLoggedIn');
                    } else {
                        // For previous dates with logs, mark as present
                        $attendanceStatus = Config::get('constants.ATTENDENCE_STATUS.Present');
                    }
                }
            }
                
            $response = [
                'attendanceLogs' => $attendanceLogs,
                'totalPresentDays' => $totalPresentDays,
                'totalHolidays' => $totalHolidays,
                'totalLeaves' => $totalLeaves,
                'attendanceStatus' => $attendanceStatus,
            ];
            return $this->sendSuccessResponse($response, 200, Config::get('constants.APIMESSAGES.ATTENDANCE_LOG_DETAILS_RETRIVED_SUCCESSFULLY'));
        } catch (\Exception $e) {
            return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.SOMETHING_WENT_WRONG'));
        }
    }

}
