<?php

namespace Modules\Ambulance\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ApiBaseController;
use Illuminate\Support\Facades\Config;
use App\Models\Expense;
use App\Models\ExpenseType;
use Helpers;
use Illuminate\Support\Facades\Auth as FacadesAuth;

class AmbulanceExpenseController extends ApiBaseController
{
    /**
     * @OA\Get(
     *     path="/api/v1/expenses",
     *     tags={"Expense"},
     *     security={
     *      {"bearerAuth": {}},
     *     },
     *     description="API to get expenses of ambulance",
     *     operationId="indexad",
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\Schema(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function getAmbulanceExpenses(Request $request)
    {
        try {
            $ambulanceId = $request->input('ambulance_id');
            $perPage = $request->input('per_page', 10);
            $currentPage = $request->input('page', 1);
            $skip = ($currentPage - 1) * $perPage;
            $search = $request->input('search');
            $filters = [
                'entry_types' => $request->input('entry_types', []),
                'date' => $request->input('date', []),
                'status' => $request->input('status'),
                'expense_type_id' => $request->input('expense_type_id')
            ];
            $expenses = Expense::getAmbulanceExpenses($ambulanceId, $perPage, $skip, $filters, $search);
            $entryTypeFilters = Helpers::createFilter('ENTRY_TYPE', [Config::get('constants.ENTRY_TYPE.Claim'), Config::get('constants.ENTRY_TYPE.Record')]);
            $reimbursementStatusFilters = Helpers::createFilter('REIMBURSEMENT_STATUS', [Config::get('constants.REIMBURSEMENT_STATUS.Pending'), Config::get('constants.REIMBURSEMENT_STATUS.Approved'), Config::get('constants.REIMBURSEMENT_STATUS.Rejected')]);
            $expenseTypeFilters = ExpenseType::select('id', 'name')->get();

            $response = [
                'expenses' => $expenses,
                'filters' => [
                    'entry_type' => array_map('current', $entryTypeFilters),
                    'status' => array_map('current', $reimbursementStatusFilters),
                    'expense_type' => $expenseTypeFilters,
                ],
            ];
            if ($expenses->isEmpty()) {
                return $this->sendSuccessResponse((object)[], 200, Config::get('constants.APIMESSAGES.NO_DATA_FOUND'));
            } else {
                return $this->sendSuccessResponse($response, 200, Config::get('constants.APIMESSAGES.EXPENSES_RETRIVED_SUCCESSFULLY'));
            }    
        } catch (\Exception $exception) {
            DB::rollback();
            return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.SOMETHING_WENT_WRONG'));
        }
    }

    public function updateAmbulanceExpenseStatus(Request $request)
    {
        try {
            DB::beginTransaction();
            $userId = FacadesAuth::user()->id;
            $expense = Expense::findOrFail($request->id);
            if ($expense->entry_type === Config::get('constants.ENTRY_TYPE.Claim')) {
                $expense->claim_status = $request->status;
            } else {
                $expense->reimbursement_status = $request->status;
            }
            $expense->rejection_reason = $request->rejection_reason;
            $expense->approved_by = $userId;
            if($expense->save()) {
                DB::commit();                                
                return $this->sendSuccessResponse($expense, 200, Config::get('constants.APIMESSAGES.AMBULANCE_EXPENSE_STATUS_UPDATED_SUCCESSFULLY'));
            } else {
                DB::rollback();
                return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.SOMETHING_WENT_WRONG'));
            }
        } catch (ModelNotFoundException $ex) {
            return $this->sendFailureResponse($ex->getMessage());
        } catch (\Exception $exception) {
            DB::rollback();
            return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.SOMETHING_WENT_WRONG'));
        }
    }
}
