<?php

namespace Modules\Ambulance\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\District as ModelsDistrict;
use App\Models\Ambulance as ModelsAmbulance;
use App\Models\PatientRegistration as ModelsPatientRegistration;
use App\Models\AmbulanceShift as ModelsAmbulanceShift;
use App\Models\Notification;
use App\Models\UserLocation as ModelsUserLocation;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CaseController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $counts = [];
        $ambulances = ModelsAmbulance::getAllAmbulances();
        $districts = ModelsDistrict::getAllDistricts();

        $counts['total_cases'] = ModelsPatientRegistration::whereNull('deleted_at')->count();
        $counts['pending_cases'] = ModelsPatientRegistration::whereNull('deleted_at')->where('request_status', 'pending')->count();
        $counts['active_cases'] = ModelsPatientRegistration::whereNull('deleted_at')->where('request_status', 'ongoing')->count();
        $counts['completed_cases'] = ModelsPatientRegistration::whereNull('deleted_at')->where('request_status', 'completed')->count();
        $counts['cancelled_cases'] = ModelsPatientRegistration::whereNull('deleted_at')->where('request_status', 'cancelled')->count();

        return view('ambulance::case.index', compact('districts', 'ambulances', 'counts'));
    }


    public function getCases(Request $request)
    {
        $caseData = ModelsPatientRegistration::select('patient_registrations.id', 'patient_registrations.mobile_number', \DB::raw('DATE_FORMAT(patient_registrations.created_at, "%d/%m/%Y") as case_date'), 'districts.district_name', 'ambulances.ambulance_no', 'ambulances.chassis_no', 'patient_registrations.requester_name', 'patient_registrations.pickup_address', 'patient_registrations.drop_address', 'patient_registrations.case_status', 'patient_registrations.request_status', 'service_areas.service_area')
            ->leftJoin('users', 'patient_registrations.user_id', '=', 'users.id')
            ->leftJoin('service_areas', 'patient_registrations.service_area_id', '=', 'service_areas.id')
            ->leftJoin('ambulances', 'patient_registrations.ambulance_id', '=', 'ambulances.id')
            ->leftJoin('districts', 'patient_registrations.district_id', '=', 'districts.id');

        $caseData = $caseData->where(
            function ($query) use ($request) {
                if (!empty($request->toArray())) {
                    if (isset($request->vehicle_id) && !empty($request->vehicle_id)) {
                        $query->where('patient_registrations.ambulance_id', $request->vehicle_id);
                    }
                    if (isset($request->district_id) && !empty($request->district_id)) {
                        $query->where('patient_registrations.district_id', $request->district_id);
                    }
                    if (isset($request->case_status) && !empty($request->case_status)) {
                        $query->where('patient_registrations.case_status', $request->case_status);
                    }
                    if (isset($request->req_status) && !empty($request->req_status)) {
                        $query->where('patient_registrations.request_status', $request->req_status);
                    }
                    if (isset($request->creation_date) && !empty($request->creation_date)) {
                        $this->filterByCreationDate($query, $request->creation_date);
                    }
                }
            }
        );

        $caseData = $caseData->orderBy('patient_registrations.id', 'desc')->get();

        if ($request->ajax()) {
            return Datatables::of($caseData)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '';
                    $btn .= "<ul class='nk-tb-actions gx-1'><li><div class='drodown mr-n1'><a href='#' class='dropdown-toggle btn btn-icon btn-trigger' data-toggle='dropdown'><em class='icon ni ni-more-h'></em></a><div class='dropdown-menu dropdown-menu-right'><ul class='link-list-opt no-bdr'>";
                    if (true) {
                        $btn .=
                            "<li>
                                <a href='#' data-id='" . $row->id . "' class='nav-link' onclick='getCaseInfo(" . $row->id . ");'>
                                    <span>View Case Details</span>
                                </a>
                            </li>";
                    }
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

    protected function filterByCreationDate($query, $creationDate)
    {
        // Check the provided creationDate and apply the corresponding filter to the query
        if ($creationDate == 'LastThreeMonth') {
            $query->where('patient_registrations.created_at', '>=', Carbon::now()->subMonths(3));
        } elseif ($creationDate == 'LastSixMonth') {
            $query->where('patient_registrations.created_at', '>=', Carbon::now()->subMonths(6));
        } elseif ($creationDate == 'CurrentYear') {
            $query->whereYear('patient_registrations.created_at', Carbon::now()->year);
        } elseif ($creationDate == 'LastYear') {
            $query->whereYear('patient_registrations.created_at', Carbon::now()->subYear()->year);
        } elseif ($creationDate == 'LastThreeYear') {
            $query->where('patient_registrations.created_at', '<', Carbon::now()->subYears(3));
        }
    }

    public function createCase(Request $request)
    {
        $districts = ModelsDistrict::getAllDistricts();

        return view('ambulance::case.create', compact('districts'));
    }

    public function storeCase(Request $request)
    {
        try {
            $data = [];
            DB::beginTransaction();
            $data['requester_name'] = $request->requestor_name ?? '';
            $data['mobile_number'] = $request->phone_no ?? '';
            $data['district_id'] = $request->district_id ?? '';
            $data['pickup_address'] = $request->pickup_location ?? '';
            $data['user_id'] = Auth::user()->id;
            $data['service_area_id'] = $request->service_area_id;
            $data['request_status'] = 'pending';
            $data['case_status'] = 'pending';
            $createCase = ModelsPatientRegistration::create($data);

            $caseDetails = ModelsPatientRegistration::select('patient_registrations.id', 'patient_registrations.mobile_number', 'patient_registrations.requester_name', 'patient_registrations.pickup_address', 'service_areas.service_area', 'districts.district_name')
                ->leftJoin('service_areas', 'patient_registrations.service_area_id', '=', 'service_areas.id')
                ->leftJoin('districts', 'patient_registrations.district_id', '=', 'districts.id')
                ->where('patient_registrations.id', $createCase->id)->first();

            if ($createCase) {
                DB::Commit();
                return response()->json(['status' => 'success', 'case_details' => $caseDetails, 'message' => trans('messages.CASE_CREATED')]);
            }

            DB::rollback();
            return response()->json(['status' => 'Fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
        } catch (Exception $e) {
            return response()->json(['status' => 'fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
        }
    }

    public function viewCaseInfo(Request $request)
    {
        $caseId = $request->case_id;
        $caseDetails = ModelsPatientRegistration::select('patient_registrations.id', 'patient_registrations.mobile_number', 'patient_registrations.requester_name', 'patient_registrations.pickup_address', 'service_areas.service_area', 'districts.district_name', 'patient_registrations.request_status')
            ->leftJoin('service_areas', 'patient_registrations.service_area_id', '=', 'service_areas.id')
            ->leftJoin('districts', 'patient_registrations.district_id', '=', 'districts.id')
            ->where('patient_registrations.id', $caseId)->first();

        if ($caseDetails) {
            return response()->json(['status' => 'success', 'case_details' => $caseDetails]);
        }
        return response()->json(['status' => 'fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
    }

    public function getCaseInfo(Request $request)
    {
        $id = $request->id;
        $caseDetails = ModelsPatientRegistration::select('patient_registrations.id', 'patient_registrations.mobile_number', 'patient_registrations.requester_name', 'patient_registrations.pickup_address', 'service_areas.service_area', 'districts.district_name', 'patient_registrations.relation', 'patient_registrations.gender', 'patient_registrations.patient_name', 'patient_registrations.age', 'patient_registrations.reason', 'patient_registrations.request_status', 'patient_registrations.case_status', 'patient_registrations.pickup_latitude', 'patient_registrations.pickup_longitude', 'patient_registrations.drop_longitude', 'patient_registrations.drop_latitude', 'patient_registrations.drop_address', 'states.state_name', 'patient_registrations.district_id', 'patient_registrations.service_area_id', 'ambulances.ambulance_no', 'ambulances.chassis_no', 'patient_registrations.ambulance_id', \DB::raw('DATE_FORMAT(patient_registrations.created_at, "%d/%m/%Y") as case_date'))
            ->leftJoin('ambulances', 'patient_registrations.ambulance_id', '=', 'ambulances.id')
            ->leftJoin('service_areas', 'patient_registrations.service_area_id', '=', 'service_areas.id')
            ->leftJoin('districts', 'patient_registrations.district_id', '=', 'districts.id')
            ->leftJoin('states', 'districts.state_id', '=', 'states.id')
            ->where('patient_registrations.id', $id)->first();

        $districts = ModelsDistrict::getAllDistricts();

        return view('ambulance::case.update_case_info', compact('caseDetails', 'districts'));
    }

    public function updateCaseDetails(Request $request)
    {
        try {
            $caseId = $request->request_id;
            $caseDetails = ModelsPatientRegistration::where('patient_registrations.id', $caseId)->first();
            $data = [];
            $data['requester_name'] = $request->requestor_name ?? '';
            $data['mobile_mumber'] = $request->mobile_mumber ?? '';
            $data['relation'] = $request->relation ?? '';
            $data['patient_name'] = $request->patient_name ?? '';
            $data['age'] = $request->patient_age ?? '';
            $data['gender'] = $request->patient_gender ?? '';
            $data['reason'] = $request->reason ?? '';
            $data['pickup_address'] = $request->pickup_address ?? '';
            $data['drop_address'] = $request->drop_address ?? '';
            $data['district_id'] = $request->district_id ?? '';
            $data['service_area_id'] = $request->service_area_id ?? '';
            $data['ambulance_id'] = $request->ambulance_id ?? null;
            $data['created_at'] = $request->case_date ? \Carbon\Carbon::createFromFormat('d/m/Y', $request->case_date)->format('Y-m-d') : null;
            $caseUpdate = $caseDetails->update($data);

            if ($caseUpdate) {
                DB::Commit();
                return response()->json(['status' => 'success', 'message' => trans('messages.CASE_UPDATED')]);
            }

            DB::rollback();
            return response()->json(['status' => 'Fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
        } catch (Exception $e) {
            return response()->json(['status' => 'fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
        }
    }

    public function assignDriver(Request $request)
    {
        try {
            $rules = [
                'case_id' => 'required|exists:patient_registrations,id',
                'district_id' => 'required',
                'service_area_id' => 'required',
                'ambulance_id' => 'required',
            ];

            // Custom validation messages
            $messages = [
                'case_id.required' => 'Case ID is required.',
                'case_id.exists' => 'This Case Not Exist.',
                'ambulance_id.required' => 'Please Select Ambulance.',
                'service_area_id.required' => 'Please Select Service Area.',
                'district_id.required' => 'Please Select District.',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            // Check if validation fails
            if ($validator->fails()) {
                return response()->json(['status' => 'fail', 'message' => $validator->errors()->first()]);
            }

            $date = $request->case_date ? \Carbon\Carbon::createFromFormat('d/m/Y', $request->case_date)->format('Y-m-d') : null;
            $checkAmbulanceAvailabilty = ModelsPatientRegistration::where('ambulance_id', $request->ambulance_id)->whereDate('created_at', $date)->where('case_status', '!=', 'completed')->first();

            if ($checkAmbulanceAvailabilty) {
                return response()->json(['status' => 'Fail', 'message' => trans('messages.AMBULANCE_NOT_AVAILABLE')]);
            }

            DB::beginTransaction();
            $caseId = $request->case_id;
            $caseDetails = ModelsPatientRegistration::where('patient_registrations.id', $caseId)->first();
            $data = [];
            $data['request_status'] = 'accepted';
            $data['case_status'] = 'ongoing';
            $data['district_id'] = $request->district_id ?? '';
            $data['service_area_id'] = $request->service_area_id ?? '';
            $data['ambulance_id'] = $request->ambulance_id ?? '';
            $caseUpdate = $caseDetails->update($data);

            $createdAt = $caseDetails->created_at->format('H:i:s'); // Extracts the time part

            // Define the time range for the morning shift (from 10:00 AM to 10:00 PM)
            $morningShiftStart = '10:00:00';
            $morningShiftEnd = '22:00:00';

            if ($createdAt >= $morningShiftStart && $createdAt <= $morningShiftEnd) {
                $shift = 'Morning Shift';
            } else {
                $shift = 'Night Shift';
            }

            $driverDetails = ModelsAmbulanceShift::leftJoin('ambulance_user_mappings', 'ambulance_user_mappings.id','=', 'ambulance_shifts.ambulance_mapping_id')
            ->leftJoin('shift_types', 'ambulance_user_mappings.shift_type_id', '=', 'shift_types.id')
            ->whereDate('ambulance_shifts.date', $date)
            ->where('ambulance_shifts.service_area_id', $request->service_area_id)
            ->where('ambulance_user_mappings.ambulance_id', $request->ambulance_id)
            ->where('shift_types.shift_name', $shift)
            ->get();

            if ($caseUpdate) {
                if($driverDetails){
                    foreach($driverDetails as $driverDetail){
                        $notificationData = [];
                        $notificationData['related_resource_id'] = $caseId ?? null;
                        $notificationData['related_resource_user_id'] = $driverDetail->user_id ?? null;
                        $notificationData['related_resource_type'] = 'call-center/get-case-details/'. $caseId ?? null;
                        $notificationData['notification_title'] = 'New Case Assignment';
                        $notificationData['notification_description'] = 'New case has been assigned to you.';
                        $notificationData['created_by'] = Auth::user()->id ?? null;
                        $notification = Notification::create($notificationData);
                    }
                }
                
                DB::Commit();
                return response()->json(['status' => 'success', 'message' => trans('messages.AMBULANCE_ASSIGNED_SUCCESS')]);
            }
            DB::rollback();
            return response()->json(['status' => 'Fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
        } catch (Exception $e) {
            return response()->json(['status' => 'fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
        }
    }

    public function getAvailableDrivers(Request $request)
    {
        try {
            // $date = Carbon::now()->format('Y-m-d');
            $serviceAreaId = $request->service_area_id;
            $districtId = $request->district_id;
            $date = $request->case_date ? \Carbon\Carbon::createFromFormat('d/m/Y', $request->case_date)->format('Y-m-d') : null;
            $caseId = $request->case_id;

            $caseDetails = ModelsPatientRegistration::find($caseId);

            if ($caseDetails) {
                $createdAt = $caseDetails->created_at->format('H:i:s'); // Extracts the time part

                // Define the time range for the morning shift (from 10:00 AM to 10:00 PM)
                $morningShiftStart = '10:00:00';
                $morningShiftEnd = '22:00:00';

                if ($createdAt >= $morningShiftStart && $createdAt <= $morningShiftEnd) {
                    $shift = 'Morning Shift';
                } else {
                    $shift = 'Night Shift';
                }

                // Now, $shift contains the determined shift based on the created_at time
                // You can use $shift as needed in your further logic.
            } else {
                // Handle the case where the record with $caseId is not found
                $shift = 'Unknown Shift';
            }


            $ambulanceData = ModelsAmbulanceShift::select('ambulances.ambulance_no', 'ambulances.chassis_no', DB::raw('CONCAT(users.first_name, " ", users.last_name) AS user_name'), 'users.phone_no', 'attendances.login_latitude', 'attendances.login_longitude', 'shift_types.shift_name', 'ambulance_shifts.user_type')
                ->leftJoin('ambulance_user_mappings', 'ambulance_shifts.ambulance_mapping_id', '=', 'ambulance_user_mappings.id')
                ->leftJoin('ambulances', 'ambulance_user_mappings.ambulance_id', '=', 'ambulances.id')
                ->leftJoin('attendances', 'ambulance_user_mappings.user_id', '=', 'attendances.user_id')
                ->leftJoin('users', 'ambulance_user_mappings.user_id', '=', 'users.id')
                ->leftJoin('shift_types', 'ambulance_user_mappings.shift_type_id', '=', 'shift_types.id')
                //->where('ambulance_shifts.service_area_id',$serviceAreaId)
                ->where('users.district_id', $districtId)
                ->where('ambulance_shifts.user_type', 'Driver')
                ->where('shift_types.shift_name', $shift)
                ->whereDate('ambulance_shifts.date', $date)
                ->whereDate('attendances.attendance_date', $date)
                ->get();

            return response()->json(['status' => 'success', 'availableDrivers' => $ambulanceData]);
        } catch (Exception $e) {
            return response()->json(['status' => 'fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
        }
    }
}
