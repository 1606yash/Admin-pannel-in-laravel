<?php

namespace Modules\Ambulance\Http\Controllers\Api\V1;

use App\Http\Controllers\ApiBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use App\Models\PatientRegistration;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth as FacadesAuth;

class CaseController extends ApiBaseController
{
    public function acceptRejectCase(Request $request)
    {
        try {
            DB::beginTransaction();
            $case = PatientRegistration::findOrFail($request->id);
            $case->request_status = $request->request_status;
            $case->reject_reason = $request->reject_reason;
            $case->user_id = $request->user_id;
            if($case->save()) {
                DB::commit();                                
                return $this->sendSuccessResponse($case, 200, Config::get('constants.APIMESSAGES.CASE_STATUS_UPDATED_SUCCESSFULLY'));
            } else {
                DB::rollback();
                return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.SOMETHING_WENT_WRONG'));
            }
        } catch (\Exception $exception) {
            DB::rollback();
            return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.SOMETHING_WENT_WRONG'));
        }
    }

    public function patientRegistration(Request $request)
    {
        try {
            DB::beginTransaction();
            $validations = PatientRegistration::validationRules();
            $validator = Validator::make($request->all(),$validations, PatientRegistration::$validationMessages);
            if($validator->fails()) {
                $errors = $validator->errors();
                $errorsMsg = "";

                if ($errors->first()) {
                    $errorsMsg .= " " . $errors->first();
                }

                return $this->sendFailureResponse($errorsMsg);
            } else {
                $patient = PatientRegistration::findOrFail($request->id);
                $patient->request_id = $request->request_id;
                $patient->requester_name = $request->requester_name;
                $patient->mobile_number = $request->mobile_number;
                $patient->relation = $request->relation;
                $patient->patient_name = $request->patient_name;
                $patient->age = $request->age;
                $patient->gender = $request->gender;
                $patient->reason = $request->reason;
                $patient->pickup_address = $request->pickup_address;
                $patient->drop_address = $request->drop_address;
                $patient->pickup_latitude = $request->pickup_latitude;
                $patient->pickup_longitude = $request->pickup_longitude;
                $patient->drop_latitude = $request->drop_latitude;
                $patient->drop_longitude = $request->drop_longitude;
                if($patient->save()) {
                    DB::commit();                                
                    return $this->sendSuccessResponse($patient, 200, Config::get('constants.APIMESSAGES.PATIENT_ADDED_SUCCESSFULLY'));
                } else {
                    DB::rollback();
                    return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.SOMETHING_WENT_WRONG'));
                }
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.SOMETHING_WENT_WRONG'));
        }
    }

    public function getCases(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10);
            $currentPage = $request->input('page', 1);
            $skip = ($currentPage - 1) * $perPage;
            $search = $request->input('search');
            $filters = [
                'date' => $request->input('date', []),
                'creation_date_range' => $request->input('creation_date_range'),
                'type' => $request->input('type'),
            ];
            $userId = FacadesAuth::user()->id;
            $cases = PatientRegistration::getCasesByUserId($userId, $perPage, $skip, $filters, $search);
            if ($cases->isEmpty()) {
                return $this->sendSuccessResponse($cases, 200, Config::get('constants.APIMESSAGES.NO_DATA_FOUND'));
            } else {
                return $this->sendSuccessResponse($cases, 200, Config::get('constants.APIMESSAGES.CASE_RETRIVED_SUCCESSFULLY'));
            }
        } catch (\Exception $exception) {
            DB::rollback();
            return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.SOMETHING_WENT_WRONG'));
        }
    }

    public function updateCaseStatus(Request $request)
    {
        try {
            DB::beginTransaction();
            $case = PatientRegistration::findOrFail($request->id);
            $caseStatus = $request->case_status;
            $case->case_status = $caseStatus;
            switch ($caseStatus) {
                case Config::get('constants.CASE_STATUS.Start'):
                    $case->start_meter_reading = $request->start_meter_reading;
                    break;
                case Config::get('constants.CASE_STATUS.Pickup'):
                    $case->pickup_meter_reading = $request->pickup_meter_reading;
                    break;
                case Config::get('constants.CASE_STATUS.Drop'):
                    $case->drop_meter_reading = $request->drop_meter_reading;
                    break;
                default:
                    $case->patient_status = $request->patient_status;
                    $case->service_duration = $request->service_duration;
                    $case->distance_covered = $request->distance_covered;
            }
            if($case->save()) {
                DB::commit();                                
                return $this->sendSuccessResponse($case, 200, Config::get('constants.APIMESSAGES.CASE_STATUS_UPDATED_SUCCESSFULLY'));
            } else {
                DB::rollback();
                return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.SOMETHING_WENT_WRONG'));
            }
        } catch (\Exception $exception) {
            DB::rollback();
            return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.SOMETHING_WENT_WRONG'));
        }
    }

    public function getCaseStatistics(Request $request)
    {
        try {
            $userId = $request->user_id;
            $currentCaseRequest = PatientRegistration::getCurrentCaseRequest($userId);
            $totalCases = PatientRegistration::getTotalCasesCount($userId);
            $todayCases = PatientRegistration::getTodayCasesCount($userId);
            $thisWeekCases = PatientRegistration::getThisWeekCasesCount($userId);
            $thisMonthCases = PatientRegistration::getThisMonthCasesCount($userId);
            $response = [
                'currentCaseRequest' => $currentCaseRequest,
                'total_cases' => $totalCases,
                'today_cases' => $todayCases,
                'this_week_cases' => $thisWeekCases,
                'this_month_cases' => $thisMonthCases,
            ]; 
            if ($response) {
                return $this->sendSuccessResponse($response, 200, Config::get('constants.APIMESSAGES.CASE_RETRIVED_SUCCESSFULLY'));
            } else {
                return $this->sendSuccessResponse($response, 200, Config::get('constants.APIMESSAGES.NO_DATA_FOUND'));
            }
        } catch (\Exception $exception) {
            DB::rollback();
            return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.SOMETHING_WENT_WRONG'));
        }
    }
}
