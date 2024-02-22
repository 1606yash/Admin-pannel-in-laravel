<?php

namespace Modules\Expense\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiBaseController;
use App\Models\Expense;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use DB;
use Helpers;
use App\Models\ExpenseType;
use App\Models\ExpenseAttachment;

class ExpenseController extends ApiBaseController
{
    /**
     * @OA\Get(
     *     path="/api/v1/expenses",
     *     tags={"Expense"},
     *     security={
     *      {"bearerAuth": {}},
     *     },
     *     description="API to get expenses of user",
     *     operationId="indexad",
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\Schema(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function getUserExpenses(Request $request)
    {
        try {
            $validations = Expense::getExpensesValidationRules();
            $validator = Validator::make($request->all(),$validations, Expense::$getExpensesValidationMessages);
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
                $search = $request->input('search');
                $filters = [
                    'entry_types' => $request->input('entry_types', []),
                    'date' => $request->input('date', []),
                    'status' => $request->input('status'),
                    'expense_type_id' => $request->input('expense_type_id'),
                    'ambulance_id' => $request->input('ambulance_id'),
                    'type' => $request->input('type')
                ];
                $expenses = Expense::getExpenses($userId, $perPage, $skip, $filters, $search);
                $entryTypeFilters = Helpers::createFilter('ENTRY_TYPE', [Config::get('constants.ENTRY_TYPE.Claim'), Config::get('constants.ENTRY_TYPE.Record')]);
                $reimbursementStatusFilters = Helpers::createFilter('REIMBURSEMENT_STATUS', [Config::get('constants.REIMBURSEMENT_STATUS.Pending'), Config::get('constants.REIMBURSEMENT_STATUS.Approved'), Config::get('constants.REIMBURSEMENT_STATUS.Rejected')]);
                $expenseTypeFilters = ExpenseType::select('id', 'name')->get();

                $response = [
                    'expenses' => $expenses,
                    'filters' => [
                        'entry_type' => array_map('current', $entryTypeFilters),
                        'status' => array_map('current', $reimbursementStatusFilters),
                        'expense_type' => $expenseTypeFilters
                    ],
                ];
                if ($expenses->isEmpty()) {
                    return $this->sendSuccessResponse((object)[], 200, Config::get('constants.APIMESSAGES.NO_DATA_FOUND'));
                } else {
                    return $this->sendSuccessResponse($response, 200, Config::get('constants.APIMESSAGES.EXPENSES_RETRIVED_SUCCESSFULLY'));
                }    
            }
        } catch (\Exception $exception) {
            DB::rollback();
            return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.SOMETHING_WENT_WRONG'));
        }
    }

    public function addExpense(Request $request)
    {
        try {
            DB::beginTransaction();
            $validations = Expense::validationRules();
            $validator = Validator::make($request->all(),$validations, Expense::$validationMessages);
            if($validator->fails()) {
                $errors = $validator->errors();
                $errorsMsg = "";

                if ($errors->first()) {
                    $errorsMsg .= " " . $errors->first();
                }

                return $this->sendFailureResponse($errorsMsg);
            } else {
                $expense = new Expense();
                $userId = $request->user_id;
                $expense->user_id = $userId;
                $expense->ambulance_id = $request->ambulance_id;
                $expense->expense_date = $request->expense_date;
                $expense->expense_type_id = $request->expense_type_id;
                $expense->entry_type = $request->entry_type;
                if ($request->entry_type === Config::get('constants.ENTRY_TYPE.Claim')) {
                    $expense->claim_date = now()->format('Y-m-d');
                    $expense->claim_status = Config::get('constants.CLAIM_STATUS.Pending');
                    $expense->reimbursement_status = Config::get('constants.CLAIM_STATUS.Pending');
                }             
                $expense->vendor_id = $request->vendor_id;
                $expense->non_vendor = $request->non_vendor;
                $expense->amount = $request->amount;
                $expense->description = $request->description;
                $expense->km_reading = $request->km_reading;
                $expense->quantity = $request->quantity;
                $expense->rate = $request->rate;
                if($expense->save()) {
                    if ($request->hasFile('attachments')) {
                        $folderName = 'expense_attachments';
                    
                        foreach ($request->file('attachments') as $file) {
                            $attachmentUrl = Helpers::uploadAttachment($file, $folderName, $expense->id);
                            $attachment = new ExpenseAttachment();
                            $attachment->expense_id = $expense->id;
                            $attachment->attachment_path = $attachmentUrl;
                            $attachment->save();
                        }
                    }
                    DB::commit();                                
                    return $this->sendSuccessResponse($expense, 200, Config::get('constants.APIMESSAGES.EXPENSE_ADDED_SUCCESSFULLY'));
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
}
