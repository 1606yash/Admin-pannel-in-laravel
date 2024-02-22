<?php

namespace Modules\Ambulance\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ApiBaseController;
use Illuminate\Support\Facades\Config;
use App\Models\AmbulanceShift;
use App\Models\Attendance;
use Illuminate\Support\Facades\Validator;
use App\Models\AmbulanceUserMapping;

class AmbulanceStaffController extends ApiBaseController
{
    /**
     * @OA\Get(
     *     path="/api/v1/ambulances",
     *     tags={"AmbulanceShift"},
     *     security={
     *      {"bearerAuth": {}},
     *     },
     *     description="API to get ambulances",
     *     operationId="indexad",
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\Schema(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function getAmbulanceStaff(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10);
            $currentPage = $request->input('page', 1);
            $skip = ($currentPage - 1) * $perPage;
            $filters = [
                'year' => $request->input('year'),
                'month' => $request->input('month')
            ];
            $ambulancesShift = AmbulanceUserMapping::getAmbulanceStaffByAmbulanceId($request->input('ambulance_id'), $perPage, $skip, $filters);
            foreach ($ambulancesShift as $staff) {
                $userId = $staff->user_id;
                $totalWorkingDays = Attendance::where('user_id', $userId)->whereYear('attendance_date', $filters['year'])
                    ->whereMonth('attendance_date', $filters['month'])
                    ->count();
                $staff->total_working_days = $totalWorkingDays;
            }
            if ($ambulancesShift->isEmpty()) {
                return $this->sendSuccessResponse($ambulancesShift, 200, Config::get('constants.APIMESSAGES.NO_DATA_FOUND'));
            } else {
                return $this->sendSuccessResponse($ambulancesShift, 200, Config::get('constants.APIMESSAGES.AMBULANCE_SHIFT_RETRIVED_SUCCESSFULLY'));
            }
        } catch (\Exception $exception) {
            DB::rollback();
            return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.SOMETHING_WENT_WRONG'));
        }
    }

    public function addAmbulanceStaff(Request $request)
    {
        try {
            DB::beginTransaction();
            $validations = AmbulanceShift::validationRules();
            $validator = Validator::make($request->all(),$validations, AmbulanceShift::$validationMessages);
            if($validator->fails()) {
                $errors = $validator->errors();
                $errorsMsg = "";

                if ($errors->first()) {
                    $errorsMsg .= " " . $errors->first();
                }

                return $this->sendFailureResponse($errorsMsg);
            } else {
                $processedRecords = [];

                foreach ($request->input('data') as $data) {
                    $key = implode('-', [
                        $data['date'],
                        $data['shift_type_id'],
                        $data['ambulance_id'],
                        $data['user_type']
                    ]);

                    if (in_array($key, $processedRecords)) {
                        DB::rollback();
                        return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.AMBULANCE_SHIFT_STAFF_CONFLICT'));
                    }

                    $processedRecords[] = $key;

                    $existingRecord = AmbulanceUserMapping::join('ambulance_shifts', 'ambulance_shifts.ambulance_mapping_id', '=', 'ambulance_user_mappings.id')
                        ->where('date', $data['date'])
                        ->where('shift_type_id', $data['shift_type_id'])
                        ->where('ambulance_id', $data['ambulance_id'])
                        ->where('user_type', $data['user_type'])
                        ->first();

                    if ($existingRecord) {
                        DB::rollback();
                        return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.AMBULANCE_SHIFT_STAFF_ALREADY_ASSIGNED'));
                    }
                     
                    $ambulanceUserMapping = [
                        'ambulance_id' => $data['ambulance_id'],
                        'shift_type_id' => $data['shift_type_id'],
                        'user_id' => $data['user_id']
                    ];
                    $newRecord = AmbulanceUserMapping::create($ambulanceUserMapping);
                    $ambulanceShift = [ 
                        'ambulance_mapping_id' => $newRecord['id'],
                        'user_type' => $data['user_type'],
                        'type' => $data['type'],
                        'start_time' => $data['start_time'],
                        'end_time' => $data['end_time'],
                        'date' => $data['date']
                    ];

                    $ambulanceNewRecord = AmbulanceShift::create($ambulanceShift);
                    $successData[] = $ambulanceNewRecord;
                }
                DB::commit();
                return $this->sendSuccessResponse($successData, 200, Config::get('constants.APIMESSAGES.AMBULANCE_SHIFT_ADDED_SUCCESSFULLY'));        
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.SOMETHING_WENT_WRONG'));
        }
    }

}
