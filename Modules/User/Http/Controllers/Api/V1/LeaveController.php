<?php

namespace Modules\User\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ApiBaseController;
use App\Models\Leave;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Helpers;

class LeaveController extends ApiBaseController
{
    /**
     * @OA\Get(
     *     path="/api/v1/leaves",
     *     tags={"Leave"},
     *     security={
     *      {"bearerAuth": {}},
     *     },
     *     description="API to get leaves of user",
     *     operationId="indexad",
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\Schema(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function getUserLeaves(Request $request)
    {
        try {
            DB::beginTransaction();
            $validations = Leave::getLeavesValidationRules();
            $validator = Validator::make($request->all(),$validations, Leave::$getLeavesValidationMessages);
            if($validator->fails()) {
                $errors = $validator->errors();
                $errorsMsg = "";

                if ($errors->first()) {
                    $errorsMsg .= " " . $errors->first();
                }

                return $this->sendFailureResponse($errorsMsg);
            } else { 
                $userId = $request->input('user_id');
                $perPage = $request->input('per_page', 10);
                $currentPage = $request->input('page', 1);
                $skip = ($currentPage - 1) * $perPage;
                $filters = [
                    'year' => $request->input('year'),
                    'month' => $request->input('month')
                ];
                $leaves = Leave::getLeaves($userId, $perPage, $skip, $filters);
                if ($leaves->isEmpty()) {
                    return $this->sendSuccessResponse($leaves, 200, Config::get('constants.APIMESSAGES.NO_DATA_FOUND'));
                } else {
                    return $this->sendSuccessResponse($leaves, 200, Config::get('constants.APIMESSAGES.LEAVES_RETRIVED_SUCCESSFULLY'));
                }
            }
        } catch (\Exception $exception) {
            DB::rollback();
            return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.SOMETHING_WENT_WRONG'));
        }
    }

    public function addLeave(Request $request)
    {
        try {
            DB::beginTransaction();
            $validations = Leave::validationRules();
            $validator = Validator::make($request->all(),$validations, Leave::$validationMessages);
            if($validator->fails()) {
                $errors = $validator->errors();
                $errorsMsg = "";

                if ($errors->first()) {
                    $errorsMsg .= " " . $errors->first();
                }

                return $this->sendFailureResponse($errorsMsg);
            } else {
                $userId = FacadesAuth::user()->id;
                $fromDate = $request->from_date;
                $toDate = $request->to_date;
                $existingLeaves = Leave::getUserExistingLeaves($userId, $fromDate, $toDate);

                if ($existingLeaves) {
                    return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.LEAVES_OVERLAPPING'));
                }
                $leave = new Leave();
                $leave->user_id = $userId;
                $leave->leave_type_id = $request->leave_type_id;
                $leave->from_date = $fromDate;
                $leave->to_date = $toDate;
                $leave->leave_reason = $request->leave_reason;
                $leave->applying_to = $request->applying_to;
                $leave->save();
                 // Check if the 'attachment' key exists in the request
                if ($request->hasFile('attachment')) {
                    $folderName = 'leave_attachments';
                    $attachmentUrl = Helpers::uploadAttachment($request->file('attachment'), $folderName, $leave->id);
                    $leave->attachment = $attachmentUrl;
                }
                if($leave->save()) {
                    DB::commit();                                
                    return $this->sendSuccessResponse($leave, 200, Config::get('constants.APIMESSAGES.LEAVES_ADDED_SUCCESSFULLY'));
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

    public function getLeaveBalance()
    {
        try {
            $userId = FacadesAuth::user()->id;
            $totalLeaveTaken = Leave::getUserTotalLeaveTaken($userId);
            $remainingLeave = Config::get('constants.DISTRICT_ANCHOR_LEAVE') - $totalLeaveTaken;
            return $this->sendSuccessResponse($remainingLeave, 200, Config::get('constants.APIMESSAGES.REMAINING_LEAVES'));
        } catch (\Exception $exception) {
            DB::rollback();
            return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.SOMETHING_WENT_WRONG'));
        }
    }
}
