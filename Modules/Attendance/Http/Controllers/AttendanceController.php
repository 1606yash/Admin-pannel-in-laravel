<?php

namespace Modules\Attendance\Http\Controllers;

use App\Exports\AttendanceReport;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use App\Models\District as ModelsDistrict;
use App\Models\LeaveType as ModelsLeaveType;
use Yajra\Datatables\Datatables;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB as FacadesDB;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $date = now()->format('Y-m-d');
            $fromDate = $request->fromDate ? \Carbon\Carbon::createFromFormat('d/m/Y', $request->fromDate)->format('Y-m-d') : $date;
            $toDate = $request->toDate ? \Carbon\Carbon::createFromFormat('d/m/Y', $request->toDate)->format('Y-m-d') : $date;

            $result = FacadesDB::select('CALL GenerateAttendanceReport(?, ?)', [$fromDate, $toDate]);

            // Apply additional conditions to filter the results
            $result = collect($result);

            $result = $result->filter(function ($row) use ($request) {
                if (!empty($request->toArray())) {
                    if (isset($request->role_id) && !empty($request->role_id) && $row->role_id != $request->role_id) {
                        return false;
                    }
                    if (isset($request->district_id) && !empty($request->district_id) && $row->district_id != $request->district_id) {
                        return false;
                    }
                    if (isset($request->user_id) && !empty($request->user_id) && $row->user_id != $request->user_id) {
                        return false;
                    }
                    if (isset($request->attendance_filter_status) && !empty($request->attendance_filter_status) && $row->attendance_status != $request->attendance_filter_status) {
                        return false;
                    }
                }
                return true;
            });

            return DataTables::of($result)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '';

                    if ($row->attendance_date >= now()->toDateString()) {
                        $btn .= "<ul class='nk-tb-actions gx-1'>
                                    <li>
                                        <div class='drodown mr-n1'><a href='#' class='dropdown-toggle btn btn-icon btn-trigger' data-toggle='dropdown'><em class='icon ni ni-more-h'></em></a>
                                            <div class='dropdown-menu dropdown-menu-right'>
                                                <ul class='link-list-opt no-bdr'>";

                        // Replace the placeholder condition with your actual condition
                        if (true) {
                            $btn .= "<li>
                                                        <a href='#' data-toggle='modal' data-target='#viewUserAttendanceInfo' class='view-user-attendance toggle' data-id='" . $row->user_id . "' data-date='" . $row->attendance_date . "' data-shift-id='" . $row->shift_type_id . "' data-role-name='" . $row->role_slug . "'>
                                                            <em class='icon ni ni-eye'></em> <span>View</span>
                                                        </a>
                                                    </li>";
                        }

                        $btn .= "</ul>
                                            </div>
                                        </div>
                                    </li>
                                </ul>";
                    }

                    return $btn;
                })
                ->editColumn('attendance_status', function ($row) {
                    if ($row->attendance_status == 'approved') {
                        return 'Leave';
                    } elseif ($row->attendance_status == 'pending' || $row->attendance_status == 'rejected') {
                        return 'Absent';
                    } else {
                        return $row->attendance_status;
                    }
                })
                // ->editColumn('attendance_date', function ($row) {
                //     $row->attendance_date = \Carbon\Carbon::createFromFormat('Y-m-d', $row->attendance_date)->format('d/m/Y');
                //     return $row->attendance_date;
                // })
                ->rawColumns(['action', 'attendance_status', 'attendance_date'])
                ->make(true);
        }

        $districts = ModelsDistrict::getAllDistricts();
        $leaveTypes = ModelsLeaveType::allLeaveTypes();
        return view('attendance::index', ['districts' => $districts, 'leaveTypes' => $leaveTypes]);
    }

    public function exportGrid(Request $request)
    {
        $options = ['filename' => 'Attendance Records' . time()];
        return \Excel::download(new AttendanceReport($request), $options['filename'] . '.xlsx');
    }
}
