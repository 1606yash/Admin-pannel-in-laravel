<?php

namespace Modules\Ambulance\Http\Controllers;

use App\Models\Ambulance as ModelsAmbulance;
use App\Models\AmbulanceDetail as ModelsAmbulanceDetail;
use App\Models\District as ModelsDistrict;
use App\Models\Bank as ModelsBank;
use App\Models\User as ModelsUser;
use App\Models\Expense as ModelsExpense;
use App\Models\ExpenseType as ModelsExpenseType;
use App\Models\ShiftType as ModelsShiftType;
use App\Models\AmbulanceInventory as ModelsAmbulanceInventory;
use App\Models\InventoryManagement as ModelsInventoryManagement;
use App\Models\AmbulanceUserMapping as ModelsAmbulanceUserMapping;
use App\Models\AmbulanceShift as ModelsAmbulanceShift;
use App\Models\ServiceArea as ModelsServiceArea;
use App\Models\PatientRegistration as ModelsPatientRegistration;
use App\Models\Role;
use Yajra\Datatables\Datatables;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Helpers;
use Carbon\CarbonPeriod;

class AmbulanceController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $districts = ModelsDistrict::getAllDistricts();
        $chassisNumbers = ModelsAmbulance::getAllChassisNumber();

        return view('ambulance::index', ['districts' => $districts, 'chassisNumbers' => $chassisNumbers]);
    }

    public function getAmbulanceList(Request $request)
    {
        $ambulanceData = ModelsAmbulance::select(
            'ambulances.id as ambulance_id',
            'ambulances.ambulance_no',
            'ambulances.chassis_no',
            'districts.district_name',
            'ambulances.inauguration_date',
            'ambulances.status',
        )
            ->leftJoin('districts', 'ambulances.district_id', '=', 'districts.id');

        $ambulanceData = $ambulanceData->where(function ($query) use ($request) {
            if (!empty($request->toArray())) {
                if (isset($request->district_id) && !empty($request->district_id)) {
                    $query->where('ambulances.district_id', $request->district_id);
                }
                if (isset($request->chassis_no) && !empty($request->chassis_no)) {
                    $query->where('ambulances.chassis_no', $request->chassis_no);
                }
                if (isset($request->status) && $request->status !== '') {
                    $query->where('ambulances.status', $request->status);
                }
                if (isset($request->inaugration_date) && !empty($request->inaugration_date)) {
                    $this->filterByInaugrationDate($query, $request->inaugration_date);
                }
                // Add more conditions as needed
            }
        });

        $ambulanceData = $ambulanceData->orderBy('ambulance_id', 'desc')->get();

        // If the request is AJAX, return the data as DataTable response
        if ($request->ajax()) {
            return Datatables::of($ambulanceData)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '';
                    $btn .= "<ul class='nk-tb-actions gx-1'><li><div class='drodown mr-n1'><a href='#' class='dropdown-toggle btn btn-icon btn-trigger' data-toggle='dropdown'><em class='icon ni ni-more-h'></em></a><div class='dropdown-menu dropdown-menu-right'><ul class='link-list-opt no-bdr'>";
                    if (true) {
                        $btn .= '<li>
                                    <a href="' . url("ambulance/view-ambulance-details/$row->ambulance_id/") . '" data-id="' . $row->ambulance_id . '" data-status ="' . $row->status . '" class="view-user toggle">
                                        <em class="icon ni ni-eye"></em> <span>View</span>
                                    </a>
                                </li>';
                    }
                    if (true) {
                        $btn .= '<li>
                                    <a href="#" class="eg-swal-av3" data-id="' . $row->ambulance_id . '" data-status ="' . $row->status . '">
                                        <em class="icon ni ni-star"></em> <span>Update Status</span>
                                    </a>
                                </li>';
                    }
                    // $confirmMsg = 'Are you sure, you want to delete this ambulance?';
                    // if (true) {
                    //     $btn .= "<li>
                    //             <a href='#' data-id='" . $row->ambulance_id . "' class='delete-ambulance' >
                    //                     <em class='icon ni ni-trash'></em> <span>Delete</span>
                    //                 </a>
                    //             </li>";
                    // }
                    $btn .= "</ul></div></div></li></ul>";
                    return $btn;
                })
                ->editColumn('status', function ($row) {
                    if ($row->status == 'Running') {
                        return 'Active';
                    } else {
                        return 'Inactive';
                    }
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
    }

    public function addAmbulance()
    {
        $districts = ModelsDistrict::getAllDistricts();
        $banks = ModelsBank::getAllBanks();
        return view('ambulance::add_ambulance', ['districts' => $districts, 'banks' => $banks]);
    }

    public function storeAmbulance(Request $request)
    {
        try {
            DB::beginTransaction();
            $data = [];
            $ambulanceDetails = [];
            $checkAmbulance = ModelsAmbulance::where('chassis_no', $request->chassis_number)->orWhere('ambulance_no', $request->ambulance_number)->first();

            if ($checkAmbulance) {
                return response()->json(['status' => 'fail', 'message' => trans('messages.AMBULANCE_ALREADY_EXIST')]);
            }

            $data['registration_number_available'] = $request->registration_number_availability ? $request->registration_number_availability : null;
            $data['make'] = $request->make ? $request->make : null;
            $data['inauguration_date'] = $request->inaugration_date ? \Carbon\Carbon::createFromFormat('d/m/Y', $request->inaugration_date)->format('Y-m-d') : null;
            $data['status'] = $request->status ? $request->status : null;
            $data['number_plate_available'] = $request->number_plate_availability ? $request->number_plate_availability : null;
            $data['chassis_no'] = $request->chassis_number ? $request->chassis_number : null;
            $data['district_id'] = $request->district_id ? $request->district_id : null;
            $data['ambulance_no'] = $request->ambulance_number ? $request->ambulance_number : null;
            $data['registration_certificate_available'] = $request->registration_certificate_availability ? $request->registration_certificate_availability : null;
            $data['purchase_paper_available'] = $request->purchase_paper_availability ? $request->purchase_paper_availability : null;
            $data['fastags_available'] = $request->fastag_availability ? $request->fastag_availability : null;
            $data['sponsor_name'] = $request->sponsor_name ? $request->sponsor_name : null;
            $data['invoice_no'] = $request->invoice_number ? $request->invoice_number : null;
            $data['invoice_date'] = $request->invoice_date ? \Carbon\Carbon::createFromFormat('d/m/Y', $request->invoice_date)->format('Y-m-d') : null;
            $data['date_of_delivery'] = $request->date_of_delivery ? \Carbon\Carbon::createFromFormat('d/m/Y', $request->date_of_delivery)->format('Y-m-d') : null;
            $data['registration_date'] = $request->registration_date ? \Carbon\Carbon::createFromFormat('d/m/Y', $request->registration_date)->format('Y-m-d') : null;
            $data['entry_date'] = $request->entry_date ? \Carbon\Carbon::createFromFormat('d/m/Y', $request->entry_date)->format('Y-m-d') : null;
            $data['additional_notes'] = $request->additional_notes ? $request->additional_notes : null;
            $data['created_by'] = Auth::user()->id ?? null;

            if ($request->hasFile('number_plate_doc')) {
                $folderName = 'number_plate_doc';
                $numberPlateAttachmentUrl = Helpers::uploadAttachment($request->file('number_plate_doc'), $folderName, time() . rand(100, 100000));
                $data['number_plate_image_path'] = $numberPlateAttachmentUrl;
            }

            if ($request->hasFile('registration_certificate_doc')) {
                $folderName = 'registration_certificate_doc';
                $registrationCertificateAttachmentUrl = Helpers::uploadAttachment($request->file('registration_certificate_doc'), $folderName, time() . rand(100, 100000));
                $data['registration_certificate_path'] = $registrationCertificateAttachmentUrl;
            }

            if ($request->hasFile('purchase_paper_doc')) {
                $folderName = 'purchase_paper_doc';
                $purchasePaperAttachmentUrl = Helpers::uploadAttachment($request->file('purchase_paper_doc'), $folderName, time() . rand(100, 100000));
                $data['purchase_paper_path'] = $purchasePaperAttachmentUrl;
            }

            if ($request->hasFile('fastag_doc')) {
                $folderName = 'fastag_doc';
                $fastagAttachmentUrl = Helpers::uploadAttachment($request->file('fastag_doc'), $folderName, time() . rand(100, 100000));
                $data['fastags_image_path'] = $fastagAttachmentUrl;
            }

            $createAmbulance = ModelsAmbulance::create($data);

            if ($createAmbulance) {
                $ambulanceDetails['ambulance_id'] = $createAmbulance->id ?? null;
                $ambulanceDetails['insurance_available'] = $request->insurance_availability ? $request->insurance_availability : null;
                $ambulanceDetails['policy_company'] = $request->policy_company ? $request->policy_company : null;
                $ambulanceDetails['policy_number'] = $request->policy_number ? $request->policy_number : null;
                $ambulanceDetails['insurance_start_date'] = $request->current_insurance_start_date ? \Carbon\Carbon::createFromFormat('d/m/Y', $request->current_insurance_start_date)->format('Y-m-d') : null;
                $ambulanceDetails['insurance_valid_upto'] = $request->insurance_valid_date ? \Carbon\Carbon::createFromFormat('d/m/Y', $request->insurance_valid_date)->format('Y-m-d') : null;
                $ambulanceDetails['puc_available'] = $request->puc_availability ? $request->puc_availability : null;
                $ambulanceDetails['puc_certificate_validity'] = $request->puc_certificate_valid_date ? \Carbon\Carbon::createFromFormat('d/m/Y', $request->puc_certificate_valid_date)->format('Y-m-d') : null;
                $ambulanceDetails['fitness_available'] = $request->fitness_certificate_availability ? $request->fitness_certificate_availability : null;
                $ambulanceDetails['fitness_certificate_validity'] = $request->fitness_certificate_valid_date ? \Carbon\Carbon::createFromFormat('d/m/Y', $request->fitness_certificate_valid_date)->format('Y-m-d') : null;
                $ambulanceDetails['supplier_name'] = $request->supplier_name ? $request->supplier_name : null;
                $ambulanceDetails['bank_id'] = $request->payment_bank_id ? $request->payment_bank_id : null;
                $ambulanceDetails['payment_date'] = $request->payment_date ? \Carbon\Carbon::createFromFormat('d/m/Y', $request->payment_date)->format('Y-m-d') : null;

                if ($request->hasFile('insurance_doc')) {
                    $folderName = 'insurance_doc';
                    $numberPlateAttachmentUrl = Helpers::uploadAttachment($request->file('insurance_doc'), $folderName, time() . rand(100, 100000));
                    $ambulanceDetails['insurance_upload_path'] = $numberPlateAttachmentUrl;
                }
                if ($request->hasFile('puc_certificate_doc')) {
                    $folderName = 'puc_certificate_doc';
                    $numberPlateAttachmentUrl = Helpers::uploadAttachment($request->file('puc_certificate_doc'), $folderName, time() . rand(100, 100000));
                    $ambulanceDetails['puc_certificates_path'] = $numberPlateAttachmentUrl;
                }
                if ($request->hasFile('fitness_certificate_doc')) {
                    $folderName = 'fitness_certificate_doc';
                    $numberPlateAttachmentUrl = Helpers::uploadAttachment($request->file('fitness_certificate_doc'), $folderName, time() . rand(100, 100000));
                    $ambulanceDetails['fitness_certificate_upload_path'] = $numberPlateAttachmentUrl;
                }

                $createAmbulanceDetails = ModelsAmbulanceDetail::create($ambulanceDetails);

                if ($createAmbulanceDetails) {
                    DB::Commit();
                    return response()->json(['status' => 'success', 'message' => trans('messages.AMBULANCE_ADDED')]);
                }
                DB::rollback();
                return response()->json(['status' => 'fail', 'message' => trans('messages.AMBULANCE_NOT_CREATED')]);
            }


            DB::rollback();
            return response()->json(['status' => 'fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
        } catch (Exception $e) {
            return response()->json(['status' => 'fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
        }
    }

    public function updateAmbulanceInfo(Request $request)
    {
        try {
            DB::beginTransaction();
            $data = [];
            $ambulanceDetails = [];
            $checkAmbulanceRecord = ModelsAmbulance::where('id', $request->ambulance_id)->first();
            if (empty($checkAmbulanceRecord)) {
                return response()->json(['status' => 'fail', 'message' => trans('messages.AMBULANCE_NOT_EXIST')]);
            }

            $data['make'] = $request->ambulance_make ? $request->ambulance_make : null;
            $data['inauguration_date'] = $request->inaugration_date ? \Carbon\Carbon::createFromFormat('d/m/Y', $request->inaugration_date)->format('Y-m-d') : null;
            $data['status'] = $request->ambulance_status ? $request->ambulance_status : null;
            $data['number_plate_available'] = $request->number_plate_availability ? $request->number_plate_availability : null;
            $data['chassis_no'] = $request->chassis_number ? $request->chassis_number : null;
            $data['district_id'] = $request->ambulance_district_id ? $request->ambulance_district_id : null;
            $data['ambulance_no'] = $request->ambulance_no ? $request->ambulance_no : null;
            $data['registration_certificate_available'] = $request->registration_certificate_availability ? $request->registration_certificate_availability : null;
            $data['purchase_paper_available'] = $request->purchase_paper_availability ? $request->purchase_paper_availability : null;
            $data['fastags_available'] = $request->fastag_availability ? $request->fastag_availability : null;
            $data['sponsor_name'] = $request->sponsor_name ? $request->sponsor_name : null;
            $data['invoice_no'] = $request->invoice_number ? $request->invoice_number : null;
            $data['invoice_date'] = $request->invoice_date ? \Carbon\Carbon::createFromFormat('d/m/Y', $request->invoice_date)->format('Y-m-d') : null;
            $data['date_of_delivery'] = $request->date_of_delivery ? \Carbon\Carbon::createFromFormat('d/m/Y', $request->date_of_delivery)->format('Y-m-d') : null;
            $data['registration_date'] = $request->registration_date ? \Carbon\Carbon::createFromFormat('d/m/Y', $request->registration_date)->format('Y-m-d') : null;
            $data['entry_date'] = $request->entry_date ? \Carbon\Carbon::createFromFormat('d/m/Y', $request->entry_date)->format('Y-m-d') : null;
            $data['additional_notes'] = $request->additional_notes ? $request->additional_notes : null;
            $data['created_by'] = Auth::user()->id ?? null;

            if ($request->hasFile('number_plate_doc')) {
                $folderName = 'number_plate_doc';
                $numberPlateAttachmentUrl = Helpers::uploadAttachment($request->file('number_plate_doc'), $folderName, time() . rand(100, 100000));
                $data['number_plate_image_path'] = $numberPlateAttachmentUrl;
            }

            if ($request->hasFile('registration_certificate_doc')) {
                $folderName = 'registration_certificate_doc';
                $registrationCertificateAttachmentUrl = Helpers::uploadAttachment($request->file('registration_certificate_doc'), $folderName, time() . rand(100, 100000));
                $data['registration_certificate_path'] = $registrationCertificateAttachmentUrl;
            }

            if ($request->hasFile('purchase_paper_doc')) {
                $folderName = 'purchase_paper_doc';
                $purchasePaperAttachmentUrl = Helpers::uploadAttachment($request->file('purchase_paper_doc'), $folderName, time() . rand(100, 100000));
                $data['purchase_paper_path'] = $purchasePaperAttachmentUrl;
            }

            if ($request->hasFile('fastag_doc')) {
                $folderName = 'fastag_doc';
                $fastagAttachmentUrl = Helpers::uploadAttachment($request->file('fastag_doc'), $folderName, time() . rand(100, 100000));
                $data['fastags_image_path'] = $fastagAttachmentUrl;
            }

            $updateAmbulance = $checkAmbulanceRecord->update($data);

            if ($updateAmbulance) {
                $checkAmbulanceDetails  = ModelsAmbulanceDetail::where('ambulance_id', $request->ambulance_id)->first();
                if ($checkAmbulanceDetails) {
                    $ambulanceDetails['insurance_available'] = $request->insurance_availability ? $request->insurance_availability : null;
                    $ambulanceDetails['policy_company'] = $request->policy_company ? $request->policy_company : null;
                    $ambulanceDetails['policy_number'] = $request->policy_number ? $request->policy_number : null;
                    $ambulanceDetails['insurance_start_date'] = $request->current_insurance_start_date ? \Carbon\Carbon::createFromFormat('d/m/Y', $request->current_insurance_start_date)->format('Y-m-d') : null;
                    $ambulanceDetails['insurance_valid_upto'] = $request->insurance_valid_date ? \Carbon\Carbon::createFromFormat('d/m/Y', $request->insurance_valid_date)->format('Y-m-d') : null;
                    $ambulanceDetails['puc_available'] = $request->puc_availability ? $request->puc_availability : null;
                    $ambulanceDetails['puc_certificate_validity'] = $request->puc_certificate_valid_date ? \Carbon\Carbon::createFromFormat('d/m/Y', $request->puc_certificate_valid_date)->format('Y-m-d') : null;
                    $ambulanceDetails['fitness_available'] = $request->fitness_certificate_availability ? $request->fitness_certificate_availability : null;
                    $ambulanceDetails['fitness_certificate_validity'] = $request->fitness_certificate_valid_date ? \Carbon\Carbon::createFromFormat('d/m/Y', $request->fitness_certificate_valid_date)->format('Y-m-d') : null;
                    $ambulanceDetails['supplier_name'] = $request->supplier_name ? $request->supplier_name : null;
                    $ambulanceDetails['bank_id'] = $request->payment_bank_id ? $request->payment_bank_id : null;
                    $ambulanceDetails['payment_date'] = $request->payment_date ? \Carbon\Carbon::createFromFormat('d/m/Y', $request->payment_date)->format('Y-m-d') : null;

                    if ($request->hasFile('insurance_doc')) {
                        $folderName = 'insurance_doc';
                        $numberPlateAttachmentUrl = Helpers::uploadAttachment($request->file('insurance_doc'), $folderName, time() . rand(100, 100000));
                        $ambulanceDetails['insurance_upload_path'] = $numberPlateAttachmentUrl;
                    }
                    if ($request->hasFile('puc_certificate_doc')) {
                        $folderName = 'puc_certificate_doc';
                        $numberPlateAttachmentUrl = Helpers::uploadAttachment($request->file('puc_certificate_doc'), $folderName, time() . rand(100, 100000));
                        $ambulanceDetails['puc_certificates_path'] = $numberPlateAttachmentUrl;
                    }
                    if ($request->hasFile('fitness_certificate_doc')) {
                        $folderName = 'fitness_certificate_doc';
                        $numberPlateAttachmentUrl = Helpers::uploadAttachment($request->file('fitness_certificate_doc'), $folderName, time() . rand(100, 100000));
                        $ambulanceDetails['fitness_certificate_upload_path'] = $numberPlateAttachmentUrl;
                    }

                    $updateAmbulanceDetails =  $checkAmbulanceDetails->update($ambulanceDetails);
                    if ($updateAmbulanceDetails) {
                        DB::Commit();
                        return response()->json(['status' => 'success', 'message' => trans('messages.AMBULANCE_UPDATED')]);
                    }
                }
                DB::rollback();
                return response()->json(['status' => 'fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
            }

            DB::rollback();
            return response()->json(['status' => 'fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
        } catch (Exception $e) {
            return response()->json(['status' => 'fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
        }
    }

    public function viewAmbulanceDetails(Request $request)
    {
        $id = $request->ambulance_id;
        $ambulanceDetails = ModelsAmbulance::ambulanceDetails($id);
        $expenseTypes = ModelsExpenseType::getAllExpenseType();
        $ambulances = ModelsAmbulance::getAllAmbulances();
        $districts = ModelsDistrict::getAllDistricts();
        $banks = ModelsBank::getAllBanks();
        $shiftTypes = ModelsShiftType::getAllShifts();
        $serviceArea = ModelsServiceArea::where('district_id', $ambulanceDetails->district_id)->get();
        $masterEvent = '';
        $workshops = [];
        $workshop = '';
        return view('ambulance::view_ambulance', ['districts' => $districts, 'ambulanceDetails' => $ambulanceDetails, 'expenseTypes' => $expenseTypes, 'ambulances' => $ambulances, 'banks' => $banks, 'masterEvent' => $masterEvent,  'workshops' => $workshops,  'workshop' => $workshop, 'shiftTypes' => $shiftTypes, 'serviceAreas' => $serviceArea]);
    }

    public function deleteAmbulance(Request $request)
    {

        $id = $request->input("id");
        $item = ModelsAmbulance::where('id', $id)->delete();
        if ($item) {
            return array('success' => true, 'msg' => 'success');
        } else {
            return array('success' => false, 'msg' => 'fails');
        }
    }

    public function viewExpensesByAmbulanceId(Request $request)
    {
        $ambulanceId = $request->ambulance_id;
        $expenseData = ModelsExpense::select(
            'ambulances.ambulance_no',
            'ambulances.chassis_no',
            'expense_entries.entry_type',
            'expense_types.name as expense_type_name',
            'expense_entries.amount',
            DB::raw('DATE_FORMAT(expense_entries.expense_date, "%d/%m/%Y") as expense_date'),
            'expense_entries.claim_status',
            'expense_entries.reimbursement_status',
            'expense_entries.id as expense_id',
            'expense_entries.expense_type_id',
            'expense_entries.ambulance_id',
            DB::raw('CONCAT(users.first_name, " ", users.last_name) as created_by_name')
        )
            ->leftJoin('ambulances', 'expense_entries.ambulance_id', '=', 'ambulances.id')
            ->leftJoin('expense_types', 'expense_entries.expense_type_id', '=', 'expense_types.id')
            ->leftJoin('users', 'expense_entries.created_by', '=', 'users.id')
            ->where('ambulances.id', $ambulanceId);

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
        $expenseData = $expenseData->get();

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

    public function viewInventoryByAmbulanceId(Request $request)
    {
        $ambulanceId = $request->ambulance_id;
        $inventoryData = ModelsAmbulanceInventory::select('id', 'name', 'unit_of_measurement', 'capacity', 'quantity', DB::raw("DATE_FORMAT(updated_at, '%d %b %Y') AS formatted_updated_at"))
            ->where('ambulance_id', $ambulanceId)
            ->get();

        if ($request->ajax()) {
            return Datatables::of($inventoryData)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '';
                    $btn .= "<ul class='nk-tb-actions gx-1'><li><div class='drodown mr-n1'><a href='#' class='dropdown-toggle btn btn-icon btn-trigger' data-toggle='dropdown'><em class='icon ni ni-more-h'></em></a><div class='dropdown-menu dropdown-menu-right'><ul class='link-list-opt no-bdr'>";
                    $btn .= "<li>
                        <a href='#' class='nav-link view-inventory' onclick='updateInventory($row->id);updateInventoryItemInfo($row->id);'>
                            <em class='icon ni ni-eye'></em> <span>View</span>
                        </a>
                    </li>";
                    $btn .= "</ul></div></div></li></ul>";
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function storeInventory(Request $request)
    {
        try {
            DB::beginTransaction();
            $ambulanceId = $request->ambulance_id;
            $data = [];
            $data['ambulance_id'] = $ambulanceId ?? null;
            $data['name'] = $request->item_name ?? null;
            $data['unit_of_measurement'] = $request->unit_of_measurement ?? null;
            $data['capacity'] = $request->capacity ?? null;
            $data['quantity'] = $request->quantity ?? null;
            $data['created_by'] = Auth::user()->id ?? null;

            $createInventory = ModelsAmbulanceInventory::create($data);

            if ($createInventory) {
                $inventoryManagement = [];
                $inventoryManagement['inventory_id'] = $createInventory->id;
                $inventoryManagement['type'] = 'Added';
                $inventoryManagement['quantity'] = $request->quantity ?? null;
                $inventoryManagement['unit_of_measurement'] = $request->unit_of_measurement ?? null;
                $inventoryManagement['created_by'] = Auth::user()->id ?? null;;
                $createInventoryManagement = ModelsInventoryManagement::create($inventoryManagement);

                if ($createInventoryManagement) {
                    DB::Commit();
                    return response()->json(['status' => 'success', 'message' => trans('messages.INVENTORY_ADDED')]);
                }
                DB::rollback();
                return response()->json(['status' => 'fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
            }

            DB::rollback();
            return response()->json(['status' => 'fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
        } catch (Exception $e) {
            return response()->json(['status' => 'fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
        }
    }

    public function inventoryHistoryList(Request $request)
    {
        $inventoryId = $request->inventory_id;
        $inventoryHistoryData = ModelsInventoryManagement::select(DB::raw("DATE_FORMAT(inventory_managements.updated_at, '%d %b %Y') AS formatted_updated_date"), 'inventory_managements.type', 'inventory_managements.quantity', 'inventory_managements.unit_of_measurement', DB::raw('CONCAT(users.first_name, " ", users.last_name) as changed_by'))
            ->leftJoin('ambulance_inventories', 'inventory_managements.inventory_id', '=', 'ambulance_inventories.id')
            ->leftJoin('users', 'inventory_managements.created_by', '=', 'users.id')
            ->where('inventory_id', $inventoryId)
            ->get();
        if ($request->ajax()) {
            return Datatables::of($inventoryHistoryData)
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function getInventoryItemInfo(Request $request)
    {
        try {
            $inventoryId = $request->inventoryId;
            $inventoryData = ModelsAmbulanceInventory::select('id', 'name', 'unit_of_measurement', 'capacity', 'quantity', DB::raw("DATE_FORMAT(updated_at, '%d %b %Y') AS formatted_updated_at"))->where('id', $inventoryId)->first();
            if (!empty($inventoryData)) {
                return response()->json(['status' => 'success', 'inventoryData' => $inventoryData, 'message' => trans('messages.INVENTORY_SUCCESS')]);
            }
            return response()->json(['status' => 'fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
        } catch (Exception $e) {
            return response()->json(['status' => 'fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
        }
    }

    public function updateInventory(Request $request)
    {
        try {
            $inventoryId = $request->inventory_id;
            $changeType = $request->update_inventory_change_type ?? null;
            $quantity = $request->update_inventory_quantity ?? null;
            $dateOfChange = $request->date_of_change ? \Carbon\Carbon::createFromFormat('d/m/Y', $request->date_of_change)->format('Y-m-d') : null;
            $updatedBy = Auth::user()->id ?? null;

            DB::beginTransaction();
            if ($changeType == 'Added') {
                $inventoryData = ModelsAmbulanceInventory::where('id', $inventoryId)->first();
                if ($inventoryData) {
                    $previousQuantity = $inventoryData->quantity;
                    $capacity = $inventoryData->capacity;
                    if (($previousQuantity + $quantity) > $capacity) {
                        DB::rollback();
                        return response()->json(['status' => 'fail', 'message' => trans('messages.CANNOT_EXCEED_CAPACITY')]);
                    }
                    $inventoryData->quantity = $previousQuantity + $quantity;
                    $inventoryData->updated_by = $updatedBy;
                    $inventoryData->date = $dateOfChange;
                    $updatedinventoryData = $inventoryData->update();

                    if ($updatedinventoryData) {
                        $data = [];
                        $data['inventory_id'] = $inventoryId;
                        $data['type'] = 'Added';
                        // $data['quantity'] = $previousQuantity + $quantity;
                        $data['quantity'] = $quantity;
                        $data['date'] = $dateOfChange;
                        $data['unit_of_measurement'] = $inventoryData->unit_of_measurement;
                        $data['created_by'] = $updatedBy;

                        $createInventoryManagement = ModelsInventoryManagement::create($data);
                        if ($createInventoryManagement) {
                            DB::Commit();
                            return response()->json(['status' => 'success', 'message' => trans('messages.INVENTORY_UPDATED')]);
                        }
                        DB::rollback();
                        return response()->json(['status' => 'fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
                    }
                    DB::rollback();
                    return response()->json(['status' => 'fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
                }
                DB::rollback();
                return response()->json(['status' => 'fail', 'message' => trans('messages.INVENTORY_NOT_FOUND')]);
            } else {
                $inventoryData = ModelsAmbulanceInventory::where('id', $inventoryId)->first();
                if ($inventoryData) {
                    $previousQuantity = $inventoryData->quantity;
                    if (($previousQuantity - $quantity) < 0) {
                        DB::rollback();
                        return response()->json(['status' => 'fail', 'message' => trans('messages.NOT_ENOUGH_CAPACITY')]);
                    }
                    $inventoryData->quantity = $previousQuantity - $quantity;
                    $inventoryData->updated_by = $updatedBy;
                    $inventoryData->date = $dateOfChange;
                    $updatedinventoryData = $inventoryData->update();

                    if ($updatedinventoryData) {
                        $data = [];
                        $data['inventory_id'] = $inventoryId;
                        $data['type'] = 'Consumed';
                        // $data['quantity'] = $previousQuantity - $quantity;
                        $data['quantity'] = $quantity;
                        $data['date'] = $dateOfChange;
                        $data['unit_of_measurement'] = $inventoryData->unit_of_measurement;
                        $data['created_by'] = $updatedBy;

                        $createInventoryManagement = ModelsInventoryManagement::create($data);
                        if ($createInventoryManagement) {
                            DB::Commit();
                            return response()->json(['status' => 'success', 'message' => trans('messages.INVENTORY_UPDATED')]);
                        }
                        DB::rollback();
                        return response()->json(['status' => 'fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
                    }
                    DB::rollback();
                    return response()->json(['status' => 'fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
                }
                DB::rollback();
                return response()->json(['status' => 'fail', 'message' => trans('messages.INVENTORY_NOT_FOUND')]);
            }
            DB::rollback();
            return response()->json(['status' => 'fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
        } catch (Exception $e) {
            return response()->json(['status' => 'fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
        }
    }


    public function calendarView(Request $request)
    {
        $masterEvent = '';
        $workshops = [];
        $workshop = '';
        return view('ambulance::calendar', compact('masterEvent', 'workshops', 'workshop'));
    }

    public function updateAmbulanceStatus(Request $request)
    {
        $ambulanceId = $request->id ?? null;
        $status = $request->status ?? "Not Running";

        if ($status == 'Not Running') {
            $ambulanceStatus = ModelsPatientRegistration::where('ambulance_id', $ambulanceId)->where('case_status', 'ongoing')->first();
            if ($ambulanceStatus) {
                $url = url("call-center/get-case-details/{$ambulanceStatus->id}");
                return response()->json(['success' => false, 'data' => array(), 'msg' => trans('messages.AMBULANCE_STATUS_NOT_CHANGED'),'url'=> $url, 'info' => 'View Case'], 200, [], JSON_UNESCAPED_SLASHES);
            }

            $alreadyShiftAssigned = ModelsAmbulanceUserMapping::leftJoin('ambulance_shifts', 'ambulance_shifts.ambulance_mapping_id', '=', 'ambulance_user_mappings.id')
                ->where('ambulance_user_mappings.ambulance_id', $ambulanceId)
                ->whereDate('ambulance_shifts.date', '>=', now()->toDateString())
                ->first();

            if ($alreadyShiftAssigned) {
                $url = url("human-resource/employees/" . strtolower($alreadyShiftAssigned->user_type) . "/view-user/{$alreadyShiftAssigned->user_id}#shift_details");
                return response()->json(['success' => false, 'data' => array(), 'msg' => trans('messages.AMBULANCE_STATUS_NOT_UPDATED'), 'url' => $url,'info'=> 'Unassign Shift'], 200, [], JSON_UNESCAPED_SLASHES);
            }
        }

        $ambulanceData = ModelsAmbulance::where('id', $ambulanceId)->first();
        $ambulanceData->status = $status;
        $updateAmbulanceStatus = $ambulanceData->update();

        if ($updateAmbulanceStatus) {
            return response()->json(['data' => array(), 'success' => true, 'msg' => 'success']);
        } else {
            return response()->json(['success' => false, 'data' => array(), 'msg' => 'fails']);
        }
    }

    public function getUserList(Request $request)
    {
        try {
            $userType = $request->userType;
            $shiftType = $request->shiftType;
            $shiftId = $request->shift_id;
            $shiftdate = $request->shift_date ? \Carbon\Carbon::createFromFormat('d/m/Y', $request->shift_date)->format('Y-m-d') : null;
            $startDate = $request->shift_start_date ? \Carbon\Carbon::createFromFormat('d/m/Y', $request->shift_start_date)->format('Y-m-d') : null;
            $endDate = $request->shift_end_date ? \Carbon\Carbon::createFromFormat('d/m/Y', $request->shift_end_date)->format('Y-m-d') : null;

            $role = Role::where('role_slug', 'LIKE', '%' . $userType . '%')->first();

            if (!$role) {
                return response()->json(['usersList' => [], 'message' => 'Role not found!']);
            }

            $roleId = $role->id;
            $users = []; // Initialize the $users variable

            if ($shiftType == 'Permanent') {
                if ($startDate && $endDate) {
                    $users = DB::table('users')
                        ->select('users.id', DB::raw('CONCAT(users.first_name, " ", users.last_name) as user_name'))
                        ->where('users.role_id', $roleId)
                        ->where('users.is_active', 1)
                        ->where('users.is_verified',1)
                        ->whereNotExists(function ($query) use ($shiftId, $startDate, $endDate) {
                            $query->select(DB::raw(1))
                                ->from('ambulance_shifts')
                                ->join('ambulance_user_mappings', 'ambulance_user_mappings.id', '=', 'ambulance_shifts.ambulance_mapping_id')
                                ->where('ambulance_user_mappings.shift_type_id', $shiftId)
                                ->whereRaw('ambulance_user_mappings.user_id = users.id')
                                ->whereBetween('ambulance_shifts.date', [$startDate, $endDate]);
                        })
                        ->get();
                }
            } else {
                $users = DB::table('users')
                    ->select('users.id', DB::raw('CONCAT(users.first_name, " ", users.last_name) as user_name'))
                    ->where('users.role_id', $roleId)
                    ->where('users.is_active', 1)
                    ->where('users.is_verified', 1)
                    ->whereNotExists(function ($query) use ($shiftId, $shiftdate) {
                        $query->select(DB::raw(1))
                            ->from('ambulance_shifts')
                            ->join('ambulance_user_mappings', 'ambulance_user_mappings.id', '=', 'ambulance_shifts.ambulance_mapping_id')
                            ->where('ambulance_user_mappings.shift_type_id', $shiftId)
                            ->whereRaw('ambulance_user_mappings.user_id = users.id')
                            ->whereBetween('ambulance_shifts.date', [$shiftdate, $shiftdate]);
                    })
                    ->get();
            }

            // Check if $users is not empty
            if (!empty($users)) {
                return response()->json(['usersList' => $users, 'message' => 'Users fetched successfully!']);
            } else {
                return response()->json(['usersList' => [], 'message' => 'No records found!']);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 'fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
        }
    }


    public function assignShift(Request $request)
    {
        try {
            $userType = $request->userType;
            $shiftType = $request->shiftType;
            $shiftId = $request->shift_id;
            $shiftdate = $request->shift_date ? \Carbon\Carbon::createFromFormat('d/m/Y', $request->shift_date)->format('Y-m-d') : null;
            $startDate = $request->shift_start_date ? \Carbon\Carbon::createFromFormat('d/m/Y', $request->shift_start_date)->format('Y-m-d') : null;
            $endDate = $request->shift_end_date ? \Carbon\Carbon::createFromFormat('d/m/Y', $request->shift_end_date)->format('Y-m-d') : null;
            $ambulanceId = $request->ambulance_id;
            $userId = $request->assigned_user;
            $serviceArea = $request->service_area_id;
            $stationArea = $request->station_area;

            $checkUserProfileStatus = ModelsUser::where('id', $userId)->first();
            if ($checkUserProfileStatus) {
                if ($checkUserProfileStatus->is_active != '1') {
                    DB::rollback();
                    return response()->json([
                        'status' => 'Fail',
                        'message' => trans('messages.PROFILE_NOT_ACTIVE'),
                    ]);
                }

                if ($checkUserProfileStatus->is_verified != '1') {
                    DB::rollback();
                    return response()->json([
                        'status' => 'Fail',
                        'message' => trans('messages.PROFILE_NOT_VERIFIED'),
                    ]);
                }
            }
            
            $checkAmbulanceStatus = ModelsAmbulance::where('id', $ambulanceId)->first();
            if($checkAmbulanceStatus->status == 'Not Running'){
                return response()->json([
                    'status' => 'Fail',
                    'message' => "The shift cannot be assigned to this " .  $userType . " as the current status of the ambulance is inactive. Please activate the status before assigning the shift"
                ]);
            }
            

            //dd($request->all());
            DB::beginTransaction();
            $checkAmbulanceMapping = ModelsAmbulanceUserMapping::where('ambulance_id', $ambulanceId)->where('shift_type_id', $shiftId)->where('user_id', $userId)->first();
            if ($checkAmbulanceMapping) {
                $mappingId = $checkAmbulanceMapping->id;
            } else {
                $data = [];
                $data['ambulance_id'] = $ambulanceId;
                $data['shift_type_id'] = $shiftId;
                $data['user_id'] = $userId;
                $createAmbulanceMapping = ModelsAmbulanceUserMapping::create($data);
                if ($createAmbulanceMapping) {
                    $mappingId = $createAmbulanceMapping->id;
                }
            }
            if ($shiftType == 'Permanent') {
                if ($startDate && $endDate) {
                    $period = CarbonPeriod::create($startDate, $endDate);

                    foreach ($period as $date) {
                        $checkAmbulanceShift = ModelsAmbulanceShift::leftJoin('ambulance_user_mappings', 'ambulance_user_mappings.id', '=', 'ambulance_shifts.ambulance_mapping_id')
                            ->leftJoin('shift_types', 'shift_types.id', '=', 'ambulance_user_mappings.shift_type_id')
                            ->where('ambulance_user_mappings.ambulance_id', $ambulanceId)
                            ->where('ambulance_user_mappings.shift_type_id', $shiftId)
                            ->where('ambulance_shifts.user_type', $userType)
                            ->whereDate('ambulance_shifts.date', $date)
                            ->first();

                        $checkUserAvailabilty = ModelsAmbulanceShift::leftJoin('ambulance_user_mappings', 'ambulance_user_mappings.id', '=', 'ambulance_shifts.ambulance_mapping_id')
                            ->leftJoin('shift_types', 'shift_types.id', '=', 'ambulance_user_mappings.shift_type_id')
                            ->where('ambulance_user_mappings.user_id', $userId)
                            ->where('ambulance_user_mappings.shift_type_id', $shiftId)
                            ->whereDate('ambulance_shifts.date', $date)
                            ->first();

                        //dd($checkAmbulanceShift);
                        if ($checkAmbulanceShift) {
                            $shift = ModelsShiftType::where('id', $shiftId)->first();
                            $shiftName = $shift->shift_name;

                            DB::rollback();
                            return response()->json([
                                'status' => 'Fail',
                                'message' => "This shift for this date: " . $date->format('d/m/Y') . " and for the " . $shiftName . " already assigned. Please unassign the previous shift first."
                            ]);
                        } else {
                            if ($checkUserAvailabilty) {
                                $shift = ModelsShiftType::where('id', $shiftId)->first();
                                $shiftName = $shift->shift_name;

                                DB::rollback();
                                return response()->json([
                                    'status' => 'Fail',
                                    'message' => "This user already assigned on other ambulance for this date: " . $date->format('d/m/Y') . " and for the " . $shiftName . ". Please unassign the previous shift first."
                                ]);
                            }
                            $ambulanceShiftDetails = [];
                            $ambulanceShiftDetails['ambulance_mapping_id'] = $mappingId;
                            $ambulanceShiftDetails['user_type'] = $userType;
                            $ambulanceShiftDetails['type'] = 'Permanent';
                            $ambulanceShiftDetails['service_area_id'] = $serviceArea;
                            $ambulanceShiftDetails['station_area'] = $stationArea;
                            $ambulanceShiftDetails['date'] = $date->format('Y-m-d');
                            $ambulanceShiftDetails['created_by'] = Auth::user()->id ?? null;
                            $createAmbulanceShiftDetails = ModelsAmbulanceShift::create($ambulanceShiftDetails);
                        }
                    }
                    if ($createAmbulanceShiftDetails) {
                        DB::Commit();
                        return response()->json(['status' => 'success', 'message' => trans('messages.SHIFT_ASSIGNED_SUCCESS')]);
                    }
                }
            } else {
                $checkAmbulanceShift = ModelsAmbulanceShift::leftJoin('ambulance_user_mappings', 'ambulance_user_mappings.id', '=', 'ambulance_shifts.ambulance_mapping_id')
                    ->leftJoin('shift_types', 'shift_types.id', '=', 'ambulance_user_mappings.shift_type_id')
                    ->where('ambulance_user_mappings.ambulance_id', $ambulanceId)
                    ->where('ambulance_user_mappings.shift_type_id', $shiftId)
                    ->where('ambulance_shifts.user_type', $userType)
                    ->whereDate('ambulance_shifts.date', $shiftdate)
                    ->first();

                //dd($checkAmbulanceShift);
                if ($checkAmbulanceShift) {
                    $shift = ModelsShiftType::where('id', $shiftId)->first();
                    $shiftName = $shift->shift_name;

                    DB::rollback();
                    return response()->json([
                        'status' => 'Fail',
                        'message' => "This shift for this date: " . $request->shift_date . " and for the " . $shiftName . " already assigned. Please unassign this shift first."
                    ]);
                } else {
                    $ambulanceShiftDetails = [];
                    $ambulanceShiftDetails['ambulance_mapping_id'] = $mappingId;
                    $ambulanceShiftDetails['user_type'] = $userType;
                    $ambulanceShiftDetails['type'] = 'Temporary';
                    $ambulanceShiftDetails['service_area_id'] = $serviceArea;
                    $ambulanceShiftDetails['station_area'] = $stationArea;
                    $ambulanceShiftDetails['date'] = $shiftdate;
                    $ambulanceShiftDetails['created_by'] = Auth::user()->id ?? null;
                    $createAmbulanceShiftDetails = ModelsAmbulanceShift::create($ambulanceShiftDetails);
                    if ($createAmbulanceShiftDetails) {
                        DB::Commit();
                        return response()->json(['status' => 'success', 'message' => trans('messages.SHIFT_ASSIGNED_SUCCESS')]);
                    }
                }
            }
            DB::rollback();
            return response()->json(['status' => 'Fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
        } catch (Exception $e) {
            return response()->json(['status' => 'fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
        }
    }


    public function getAssignStaff(Request $request)
    {
        try {
            $month = $request->month_id;
            $year = $request->year_id;
            $ambulanceId = $request->ambulance_id;
            $staffData = ModelsAmbulanceShift::leftJoin('ambulance_user_mappings', 'ambulance_user_mappings.id', '=', 'ambulance_shifts.ambulance_mapping_id')
                ->leftJoin('users', 'users.id', '=', 'ambulance_user_mappings.user_id')
                ->leftJoin('users as created_by', 'created_by.id', '=', 'ambulance_shifts.created_by')
                ->leftJoin('shift_types', 'shift_types.id', '=', 'ambulance_user_mappings.shift_type_id')
                ->whereMonth('ambulance_shifts.date', '=', $month)
                ->whereYear('ambulance_shifts.date', '=', $year)
                ->where('ambulance_user_mappings.ambulance_id', $ambulanceId)
                ->groupBy('ambulance_user_mappings.user_id', 'users.first_name', 'users.last_name', 'users.profile_path', 'users.employee_id', 'ambulance_shifts.user_type', 'ambulance_shifts.type', 'created_by.first_name', 'created_by.last_name', 'shift_types.shift_name')
                ->select(
                    'ambulance_user_mappings.user_id',
                    DB::raw('CONCAT(users.first_name, " ", users.last_name) as user_name'),
                    'users.profile_path',
                    'users.employee_id',
                    'ambulance_shifts.user_type',
                    'ambulance_shifts.type',
                    DB::raw('CONCAT(created_by.first_name, " ", created_by.last_name) as created_by_user_name'),
                    'shift_types.shift_name',
                    'ambulance_user_mappings.shift_type_id',
                    DB::raw('SUM(1) as shift_count'),
                    DB::raw('COUNT(DISTINCT ambulance_shifts.date) as day_count')
                )
                ->get();

            $formattedDate = \Carbon\Carbon::create($year, $month, 1)->format('F Y');

            if (!empty($staffData)) {
                return response()->json(['staffList' => $staffData, 'date' => $formattedDate, 'message' => 'Users fetched successfully!']);
            } else {
                return response()->json(['staffList' => [], 'message' => 'No records found!']);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 'fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
        }
    }


    /**
     * Filter the query based on the provided creation date range.
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @param string $creationDate
     */
    protected function filterByInaugrationDate($query, $inaugrationDate)
    {
        // Check the provided creationDate and apply the corresponding filter to the query
        if ($inaugrationDate == 'LastThreeMonth') {
            $query->where('ambulances.inauguration_date', '>=', \Carbon\Carbon::now()->subMonths(3));
        } elseif ($inaugrationDate == 'LastSixMonth') {
            $query->where('ambulances.inauguration_date', '>=', \Carbon\Carbon::now()->subMonths(6));
        } elseif ($inaugrationDate == 'CurrentYear') {
            $query->whereYear('ambulances.inauguration_date', \Carbon\Carbon::now()->year);
        } elseif ($inaugrationDate == 'LastYear') {
            $query->whereYear('ambulances.inauguration_date', \Carbon\Carbon::now()->subYear()->year);
        } elseif ($inaugrationDate == 'LastThreeYear') {
            $query->where('ambulances.inauguration_date', '<', \Carbon\Carbon::now()->subYears(3));
        }
    }

    public function getAssignStaffForEvents(Request $request)
    {
        try {
            $month = $request->month_id;
            $year = $request->year_id;
            $ambulanceId = $request->ambulance_id;
            $userId = $request->user_id;
            $shiftTypeId = $request->shift_type_id;
            $query = ModelsAmbulanceShift::leftJoin('ambulance_user_mappings', 'ambulance_user_mappings.id', '=', 'ambulance_shifts.ambulance_mapping_id')
                ->leftJoin('users', 'users.id', '=', 'ambulance_user_mappings.user_id')
                ->leftJoin('users as created_by', 'created_by.id', '=', 'ambulance_shifts.created_by')
                ->leftJoin('shift_types', 'shift_types.id', '=', 'ambulance_user_mappings.shift_type_id')
                ->whereMonth('ambulance_shifts.date', '=', $month)
                ->whereYear('ambulance_shifts.date', '=', $year)
                ->where('ambulance_user_mappings.ambulance_id', $ambulanceId);

            if ($shiftTypeId) {
                $query->where('ambulance_user_mappings.shift_type_id', $shiftTypeId);
            }

            if ($userId) {
                $query->where('ambulance_user_mappings.user_id', $userId);
            }

            $staffData = $query->select(
                'ambulance_user_mappings.user_id',
                'ambulance_shifts.user_type',
                'ambulance_shifts.type',
                'shift_types.shift_name',
                'ambulance_shifts.date',
                'ambulance_user_mappings.shift_type_id',
            )
                ->get();
            $formattedDate = \Carbon\Carbon::create($year, $month, 1)->format('F Y');

            if (!empty($staffData)) {
                return response()->json(['staffList' => $staffData, 'date' => $formattedDate, 'shift_type_id' => $shiftTypeId, 'message' => 'Users fetched successfully!']);
            } else {
                return response()->json(['staffList' => [], 'message' => 'No records found!']);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 'fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
        }
    }
}
