<?php

namespace Modules\Expense\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use App\Models\Expense as ModelsExpense;
use App\Models\ExpenseType as ModelsExpenseType;
use App\Models\Ambulance as ModelsAmbulance;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB as FacadesDB;
use Illuminate\Routing\Controller;
use App\Exports\ExpenseExport;
use App\Models\Notification;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $expenseTypes = ModelsExpenseType::getAllExpenseType();
        $ambulances = ModelsAmbulance::getAllAmbulances();
        return view('expense::index', ['expenseTypes' => $expenseTypes, 'ambulances' => $ambulances]);
    }

    public function getExpenseList(Request $request){
        $expenseData = ModelsExpense::select(
            'ambulances.ambulance_no',
            'ambulances.chassis_no',
            'expense_entries.entry_type',
            'expense_types.name as expense_type_name',
            'expense_entries.amount',
            FacadesDB::raw('DATE_FORMAT(expense_entries.expense_date, "%d/%m/%Y") as expense_date'),
            'expense_entries.claim_status',
            'expense_entries.reimbursement_status',
            'expense_entries.id as expense_id',
            'expense_entries.expense_type_id',
            'expense_entries.ambulance_id'
        )
            ->leftJoin('ambulances', 'expense_entries.ambulance_id', '=', 'ambulances.id')
            ->leftJoin('expense_types', 'expense_entries.expense_type_id', '=', 'expense_types.id');

        $expenseData = $expenseData->where(function ($query) use ($request) {
            if (!empty($request->toArray())) {
                if (isset($request->entry_type) && !empty($request->entry_type)) {
                    $query->where('expense_entries.entry_type', 'LIKE', '%' . $request->entry_type . '%');
                }
                if (isset($request->vehicle_id) && !empty($request->vehicle_id)) {
                    $query->where('expense_entries.ambulance_id', $request->vehicle_id);
                }
                if (isset($request->reimbursment_status) && !empty($request->reimbursment_status)) {
                    $query->where('expense_entries.reimbursement_status', 'LIKE', '%' . $request->reimbursment_status . '%');
                }
                if (isset($request->status) && !empty($request->status)) {
                    $query->where('expense_entries.claim_status', $request->status);
                }
                if (isset($request->expense_type_id) && !empty($request->expense_type_id)) {
                    $query->where('expense_entries.expense_type_id', $request->expense_type_id);
                }
                if (!empty($request->fromDate) && !empty($request->toDate)) {
                    $query->whereRaw("expense_entries.expense_date between '" . \Carbon\Carbon::createFromFormat('m/d/Y', $request->fromDate)->format('Y-m-d') . "' AND '" . \Carbon\Carbon::createFromFormat('m/d/Y', $request->toDate)->format('Y-m-d') . "'");
                }
                //  Add more conditions as needed
            }
        });

        $expenseData = $expenseData->orderBy('expense_id','desc')->get();

        if ($request->ajax()) {
            return Datatables::of($expenseData)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '';
                    $btn .= "<ul class='nk-tb-actions gx-1'><li><div class='drodown mr-n1'><a href='#' class='dropdown-toggle btn btn-icon btn-trigger' data-toggle='dropdown'><em class='icon ni ni-more-h'></em></a><div class='dropdown-menu dropdown-menu-right'><ul class='link-list-opt no-bdr'>";
                    // Use regular PHP syntax to generate the URL
                    $btn .= "<li>
                        <a href='" . url('human-resource/expenses/expense-details/' . $row->expense_id) . "' class='nav-link'>
                            <em class='icon ni ni-eye'></em> <span>View</span>
                        </a>
                    </li>";
                    $btn .= "</ul></div></div></li></ul>";
                    return $btn;
                })
                ->addColumn('vechicle_no', function ($row) {
                    if ($row['ambulance_no']) {
                        return $row['ambulance_no'];
                    } elseif ($row['chassis_no']) {
                        return $row['chassis_no'];
                    } else {
                        return 'NA';
                    }
                })
                ->rawColumns(['action', 'vechicle_no'])
                ->make(true);
        }
    }

    public function viewExpenseDetails(Request $request)
    {
        $id = $request->id;
        $expenseData = ModelsExpense::select(
            'ambulances.ambulance_no',
            'ambulances.chassis_no',
            'expense_entries.entry_type',
            'expense_types.name as expense_type_name',
            'expense_entries.amount',
            'expense_entries.fuel_type',
            FacadesDB::raw('DATE_FORMAT(expense_entries.expense_date, "%d/%m/%Y") as expense_date'),
            'expense_entries.claim_status',
            'expense_entries.reimbursement_status',
            'expense_entries.id as expense_id',
            'expense_entries.expense_type_id',
            'expense_entries.ambulance_id',
            'expense_entries.quantity',
            'vendors.name as vendor_name',
            'expense_entries.non_vendor',
            'expense_entries.description',
            'expense_entries.km_reading',
            'expense_entries.rejection_reason',
            FacadesDB::raw('DATE_FORMAT(expense_entries.claim_date, "%d/%m/%Y") as claim_date'),
        )
            ->leftJoin('ambulances', 'expense_entries.ambulance_id', '=', 'ambulances.id')
            ->leftJoin('expense_types', 'expense_entries.expense_type_id', '=', 'expense_types.id')
            ->leftJoin('vendors', 'expense_entries.vendor_id', '=', 'vendors.id')
            ->where('expense_entries.id', $id)->first();

        return view('expense::view_expense_details', ['expenseData' => $expenseData]);
    }

    public function approveReimbursement(Request $request)
    {
        $id = $request->expense_id;
        $requestStatus = $request->request_status;

        FacadesDB::beginTransaction();

        $expenseData = ModelsExpense::where('id', $id)->first();
        if ($expenseData) {
            if ($requestStatus == 'approved') {
                $expenseData->reimbursement_status = 'Approved';
                $expenseData->claim_status = 'Approved';
                $statusUpdate = $expenseData->update();
                $notificationData['related_resource_id'] = $id ?? null;
                $notificationData['related_resource_user_id'] = $expenseData->user_id ?? null;
                $notificationData['related_resource_type'] = 'expenses/expense-details/'. $id ?? null;
                $notificationData['notification_title'] = 'Reimbursement Approved';
                $notificationData['notification_description'] = 'Your Reimbursement has been approved.';
                $notificationData['created_by'] = auth()->id() ?? null;
                $notification = Notification::create($notificationData);
            } elseif ($requestStatus == 'complete') {
                $expenseData->reimbursement_status = 'Completed';
                $expenseData->claim_status = 'Approved';
                $statusUpdate = $expenseData->update();
                $notificationData['related_resource_id'] = $id ?? null;
                $notificationData['related_resource_user_id'] = $expenseData->user_id ?? null;
                $notificationData['related_resource_type'] = 'expenses/expense-details/' . $id ?? null;
                $notificationData['notification_title'] = 'Reimbursement Completed';
                $notificationData['notification_description'] = 'Your Reimbursement has been completed.';
                $notificationData['created_by'] = auth()->id() ?? null;
                $notification = Notification::create($notificationData);
            } else {
                $statusUpdate='';
            }

            if($statusUpdate){
                
                FacadesDB::commit();
                return response()->json(['status' => 'success', 'message' => trans('messages.REIMBURSEMENT_STATUS')]);
            }
            FacadesDB::rollback();
            return response()->json(['status' => 'Fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
        }
        FacadesDB::rollback();
        return response()->json(['status' => 'Fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
    }

    public function rejectReimbursement(Request $request)
    {
        $id = $request->expense_id;
        $reason = $request->reason;
        FacadesDB::beginTransaction();

        $expenseData = ModelsExpense::where('id', $id)->first();
        if ($expenseData) {
            $expenseData->reimbursement_status = 'Rejected';
            $expenseData->claim_status = 'Rejected';
            $expenseData->rejection_reason = $reason;
            $statusUpdate = $expenseData->update();
            if ($statusUpdate) {
                $notificationData = [];
                $notificationData['related_resource_id'] = $id ?? null;
                $notificationData['related_resource_user_id'] = $expenseData->user_id ?? null;
                $notificationData['related_resource_type'] = 'expenses/expense-details/' . $id ?? null;
                $notificationData['notification_title'] = 'Reimbursement Rejected';
                $notificationData['notification_description'] = 'Your Reimbursement has been rejected.';
                $notificationData['created_by'] = auth()->id() ?? null;
                $notification = Notification::create($notificationData);
                FacadesDB::commit();
                return response()->json(['status' => 'success', 'message' => trans('messages.REIMBURSEMENT_STATUS')]);
            }
            FacadesDB::rollback();
            return response()->json(['status' => 'Fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
        }
        FacadesDB::rollback();
        return response()->json(['status' => 'Fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);

    }

    public function exportGrid(Request $request){
        
        $options = ['filename' => 'Expense Table' . time()];
        return \Excel::download(new ExpenseExport($request), $options['filename'] . '.xlsx');
    }
}
