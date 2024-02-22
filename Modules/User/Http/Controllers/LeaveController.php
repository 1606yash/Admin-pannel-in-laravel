<?php

namespace Modules\User\Http\Controllers;

use App\Models\Leave as ModelsLeave;
use App\Models\LeaveType as ModelsLeaveType;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB as FacadesDB;

class LeaveController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $leaveType = ModelsLeaveType::allLeaveTypes();
        return view('user::leaves/index', ['leaveTypes' => $leaveType]);
    }

    public function getLeavesList(Request $request)
    {
        $leaveData = ModelsLeave::select(
            'leaves.id as leave_id',
            'leave_types.id',
            'users.role_id',
            'users.id as user_id',
            FacadesDB::raw('CONCAT(users.first_name, " ", users.last_name) as user_name'),
            'roles.role_name',
            'districts.district_name',
            'leave_types.name as leave_name',
            FacadesDB::raw('DATE_FORMAT(leaves.from_date, "%d/%m/%Y") as from_date'),
            FacadesDB::raw('DATE_FORMAT(leaves.to_date, "%d/%m/%Y") as to_date'),
            FacadesDB::raw('DATE_FORMAT(leaves.created_at, "%d/%m/%Y") as leave_applied_date'),
            'leaves.status',
            FacadesDB::raw('DATEDIFF(leaves.to_date, leaves.from_date) + 1 AS amount')
        )
            ->leftJoin('leave_types', 'leaves.leave_type_id', '=', 'leave_types.id')
            ->leftJoin('users', 'leaves.user_id', '=', 'users.id')
            ->leftJoin('roles', 'users.role_id', '=', 'roles.id')
            ->leftJoin('districts', 'users.district_id', '=', 'districts.id');

        $leaveData = $leaveData->where(function ($query) use ($request) {
            if (!empty($request->toArray())) {

                if (isset($request->role_id) && !empty($request->role_id)) {
                    $query->where('users.role_id', $request->role_id);
                }
                if (isset($request->leave_type) && !empty($request->leave_type)) {
                    $query->where('leave_types.id', $request->leave_type);
                }
                if (isset($request->leave_status) && !empty($request->leave_status)) {
                    $query->where('leaves.status', $request->leave_status);
                }
                if (isset($request->leave_date_range) && !empty($request->leave_date_range)) {
                    $this->filterByCreationDate($query, $request->leave_date_range);
                }
            }
        });

        $leaveData = $leaveData->orderBy('leave_id', 'desc')->get();
        if ($request->ajax()) {
            return Datatables::of($leaveData)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '';
                    $btn .= "<ul class='nk-tb-actions gx-1'><li><div class='drodown mr-n1'><a href='#' class='dropdown-toggle btn btn-icon btn-trigger' data-toggle='dropdown'><em class='icon ni ni-more-h'></em></a><div class='dropdown-menu dropdown-menu-right'><ul class='link-list-opt no-bdr'>";
                    $btn .= "<li>
                        <a href='" . url('human-resource/leaves/view-leave-info/' . $row->leave_id) . "' class='nav-link'>
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

    public function viewLeaveInfo(Request $request)
    {
        $id = $request->id;
        $leaveData = ModelsLeave::select(
            'leaves.id',
            'users.role_id',
            'users.id as user_id',
            FacadesDB::raw('CONCAT(users.first_name, " ", users.last_name) as user_name'),
            FacadesDB::raw('CONCAT(u2.first_name, " ", u2.last_name) as applied_to_user_name'),
            'roles.role_name',
            'r2.role_name as applied_to_role',
            'leave_types.name as leave_name',
            FacadesDB::raw('DATE_FORMAT(leaves.from_date, "%d/%m/%Y") as from_date'),
            FacadesDB::raw('DATE_FORMAT(leaves.to_date, "%d/%m/%Y") as to_date'),
            FacadesDB::raw('DATE_FORMAT(leaves.created_at, "%d/%m/%Y") as leave_applied_date'),
            FacadesDB::raw('DATE_FORMAT(leaves.approved_on, "%d/%m/%Y") as leave_approved_date'),
            'leaves.leave_reason',
            'leaves.reject_reason',
            'leaves.attachment',
            'leaves.status',
            FacadesDB::raw('DATEDIFF(leaves.to_date, leaves.from_date) + 1 AS amount')
        )
            ->leftJoin('leave_types', 'leaves.leave_type_id', '=', 'leave_types.id')
            ->leftJoin('users', 'leaves.user_id', '=', 'users.id')
            ->leftJoin('roles', 'users.role_id', '=', 'roles.id')
            ->leftJoin('users as u2', 'leaves.applying_to', '=', 'u2.id')
            ->leftJoin('roles as r2', 'u2.role_id', '=', 'r2.id')
            ->where('leaves.id', $id)
            ->first();
        return view('user::leaves/view_request', ['leaveData' => $leaveData]);
    }

    protected function filterByCreationDate($query, $creationDate)
    {
        if ($creationDate == 'LastThreeMonth') {
            $query->where('leaves.created_at', '>=', Carbon::now()->subMonths(3));
        } elseif ($creationDate == 'LastSixMonth') {
            $query->where('leaves.created_at', '>=', Carbon::now()->subMonths(6));
        } elseif ($creationDate == 'CurrentYear') {
            $query->whereYear('leaves.created_at', Carbon::now()->year);
        } elseif ($creationDate == 'LastYear') {
            $query->whereYear('leaves.created_at', Carbon::now()->subYear()->year);
        } elseif ($creationDate == 'LastThreeYear') {
            $query->where('leaves.created_at', '<', Carbon::now()->subYears(3));
        }
        // Add more conditions as needed...
    }
}
