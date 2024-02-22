<?php

namespace Modules\HumanResource\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ApiBaseController;
use App\Models\Salary;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;

class SalaryController extends ApiBaseController
{
    /**
     * @OA\Get(
     *     path="/api/v1/salary-slips",
     *     tags={"Salary"},
     *     security={
     *      {"bearerAuth": {}},
     *     },
     *     description="API to get salary slips of user",
     *     operationId="indexad",
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\Schema(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function getSalarySlip(Request $request)
    {
        try {
            $validations = Salary::getSalaryValidationRules();
            $validator = Validator::make($request->all(),$validations, Salary::$getSalaryValidationMessages);
            if($validator->fails()) {
                $errors = $validator->errors();
                $errorsMsg = "";

                if ($errors->first()) {
                    $errorsMsg .= " " . $errors->first();
                }

                return $this->sendFailureResponse($errorsMsg);
            } else {
                $userId = $request->input('user_id');
                $filters = [
                    'year' => $request->input('year'),
                    'month' => $request->input('month')
                ];
                $salarySlip = Salary::getSalarySlip($userId, $filters);
                if ($salarySlip->isEmpty()) {
                    return $this->sendSuccessResponse($salarySlip, 200, Config::get('constants.APIMESSAGES.NO_DATA_FOUND'));
                } else {
                    return $this->sendSuccessResponse($salarySlip, 200, Config::get('constants.APIMESSAGES.SALARYSLIP_RETRIVED_SUCCESSFULLY'));
                }
            }
        } catch (\Exception $exception) {
            DB::rollback();
            return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.SOMETHING_WENT_WRONG'));
        }
    }
}
