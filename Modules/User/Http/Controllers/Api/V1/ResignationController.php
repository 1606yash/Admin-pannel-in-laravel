<?php

namespace Modules\User\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ApiBaseController;
use App\Models\Resignation;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Helpers;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ResignationController extends ApiBaseController
{
    public function addResignation(Request $request)
    {
        try {
            DB::beginTransaction();
            $validations = Resignation::validationRules();
            $validator = Validator::make($request->all(),$validations, Resignation::$validationMessages);
            if($validator->fails()) {
                $errors = $validator->errors();
                $errorsMsg = "";

                if ($errors->first()) {
                    $errorsMsg .= " " . $errors->first();
                }

                return $this->sendFailureResponse($errorsMsg);
            } else {
                $resignation = new Resignation();
                $userId = FacadesAuth::user()->id;
                $resignation->user_id = $userId;
                $resignation->resignation_reasons_id = $request->resignation_reasons_id;
                $resignation->applying_to = $request->applying_to;
                $resignation->remark = $request->remark;
                if ($request->has('resignation_date')) {
                    $resignation->resignation_date = $request->resignation_date;
                } else {
                    $resignation->resignation_date = now()->format('Y-m-d');
                }
                if ($request->has('last_working_day')) {
                    $resignation->last_working_day = $request->last_working_day;
                } else {
                    $resignation->resignation_date = now()->format('Y-m-d');
                    $noticePeriodConstant = Config::get('constants.NOTICE_PERIOD');
                    $lastWorkingDate = now()->addDays($noticePeriodConstant);
                    $resignation->last_working_day = $lastWorkingDate->format('Y-m-d');
                }
                $resignation->save();
                 // Check if the 'attachment' key exists in the request
                if ($request->hasFile('attachment')) {
                    $folderName = 'resignation_attachments';
                    $attachmentUrl = Helpers::uploadAttachment($request->file('attachment'), $folderName, $resignation->id);
                    $resignation->attachment = $attachmentUrl;
                }
                if($resignation->save()) {
                    DB::commit();                                
                    return $this->sendSuccessResponse($resignation, 200, Config::get('constants.APIMESSAGES.RESIGNATION_ADDED_SUCCESSFULLY'));
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

    public function getUserResignation(Request $request)
    {
        try {
            $validations = Resignation::getResignationValidationRules();
            $validator = Validator::make($request->all(),$validations, Resignation::$getResignationValidationMessages);
            if($validator->fails()) {
                $errors = $validator->errors();
                $errorsMsg = "";

                if ($errors->first()) {
                    $errorsMsg .= " " . $errors->first();
                }

                return $this->sendFailureResponse($errorsMsg);
            } else {
                $userId = $request->input('user_id');
                $resignation = Resignation::getResignation($userId);
                if ($resignation->isEmpty()) {
                    return $this->sendSuccessResponse($resignation, 200, Config::get('constants.APIMESSAGES.NO_DATA_FOUND'));
                } else {
                    return $this->sendSuccessResponse($resignation, 200, Config::get('constants.APIMESSAGES.RESIGNATION_RETRIVED_SUCCESSFULLY'));    
                }
            }
        } catch (\Exception $exception) {
            DB::rollback();
            return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.SOMETHING_WENT_WRONG'));
        }
    }

    public function withdrawResignation($id)
    {
        try {
            DB::beginTransaction();
            $resignation = Resignation::findOrFail($id);
            $resignation->status = Config::get('constants.RESIGNATION_STATUS.Withdrawn');
            if($resignation->save()) {
                DB::commit();                                
                return $this->sendSuccessResponse($resignation, 200, Config::get('constants.APIMESSAGES.RESIGNATION_WITHDRAW_SUCCESSFULLY'));
            } else {
                DB::rollback();
                return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.SOMETHING_WENT_WRONG'));
            }
        } catch (ModelNotFoundException $ex) {
            return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.SOMETHING_WENT_WRONG'));
        } catch (\Exception $exception) {
            DB::rollback();
            return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.SOMETHING_WENT_WRONG'));
        }
    }
   
}
