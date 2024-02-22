<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Yajra\Datatables\Datatables;
use App\Models\District as ModelsDistrict;
use App\Models\Notification;
use App\Models\Resignation as ModelsResignation;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ResignationController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $districts = ModelsDistrict::getAllDistricts();
        return view('user::resignations/index', ['districts' => $districts]);
    }

    public function getResignationList(Request $request){
        $resignationData = ModelsResignation::select(
            'resignations.id as resignation_id',
            'resignations.user_id',
            DB::raw('CONCAT(users.first_name, " ", users.last_name) as user_name'),
            'roles.role_name',
            'districts.district_name',
            'resignation_reasons.reason',
            DB::raw('DATE_FORMAT(resignations.created_at, "%d/%m/%Y") as resignation_date'),
            DB::raw('CONCAT(u2.first_name, " ", u2.last_name) as applied_to'),
            'resignations.status'
        )
            ->leftJoin('resignation_reasons', 'resignations.resignation_reasons_id', '=', 'resignation_reasons.id')
            ->leftJoin('users', 'resignations.user_id', '=', 'users.id')
            ->leftJoin('users as u2', 'resignations.applying_to', '=', 'u2.id')
            ->leftJoin('roles', 'users.role_id', '=', 'roles.id')
            ->leftJoin('districts', 'users.district_id', '=', 'districts.id');

        $resignationData = $resignationData->where(function ($query) use ($request) {
            if (!empty($request->toArray())) {

                if (isset($request->district_id) && !empty($request->district_id)) {
                    $query->where('users.district_id', $request->district_id);
                }
                if (isset($request->role_id) && !empty($request->role_id)) {
                    $query->where('users.role_id', $request->role_id);
                }
                if (isset($request->date_range) && !empty($request->date_range)) {
                    $this->filterByCreationDate($query, $request->date_range);
                }
                //  Add more conditions as needed
            }
        });

        $resignationData = $resignationData->orderBy('resignation_id','desc')->get();
        if ($request->ajax()) {
            return Datatables::of($resignationData)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '';
                    $btn .= "<ul class='nk-tb-actions gx-1'><li><div class='drodown mr-n1'><a href='#' class='dropdown-toggle btn btn-icon btn-trigger' data-toggle='dropdown'><em class='icon ni ni-more-h'></em></a><div class='dropdown-menu dropdown-menu-right'><ul class='link-list-opt no-bdr'>";
                    if (true) {
                        $btn .= "<li>
                                    <a href='" . url('human-resource/resignations/view-resignation/' . $row->resignation_id) . "' class='view-user toggle'>
                                        <em class='icon ni ni-eye'></em> <span>View</span>
                                    </a>
                                </li>";
                    }
                    $btn .= "</ul></div></div></li></ul>";
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function viewResignation(Request $request)
    {
        $id = $request->id;
        $resignationData =
            ModelsResignation::select(
                'resignations.id as resignation_id',
                'resignations.user_id',
                DB::raw('CONCAT(users.first_name, " ", users.last_name) as user_name'),
                DB::raw('CONCAT(u2.first_name, " ", u2.last_name) as applied_to_user_name'),
                'roles.role_name',
                'r2.role_name as applied_to_role',
                'districts.district_name',
                'resignation_reasons.reason',
                'resignations.rejection_reason',
                DB::raw('DATE_FORMAT(resignations.last_working_day, "%d/%m/%Y") as last_working_day'),
                'resignations.remark',
                DB::raw('DATE_FORMAT(resignations.created_at, "%d/%m/%Y") as resignation_date'),
                DB::raw('CONCAT(u2.first_name, " ", u2.last_name) as applied_to'),
                'resignations.status',
                DB::raw('DATEDIFF(resignations.last_working_day, resignations.created_at) AS notice_period')
            )
            ->leftJoin('resignation_reasons', 'resignations.resignation_reasons_id', '=', 'resignation_reasons.id')
            ->leftJoin('users', 'resignations.user_id', '=', 'users.id')
            ->leftJoin('users as u2', 'resignations.applying_to', '=', 'u2.id')
            ->leftJoin('roles', 'users.role_id', '=', 'roles.id')
            ->leftJoin('roles as r2', 'u2.role_id', '=', 'r2.id')
            ->leftJoin('districts', 'users.district_id', '=', 'districts.id')
            ->where('resignations.id', $id)
            ->first();

        return view('user::resignations/view_resignation', ['resignationData' => $resignationData]);
    }

    public function rejectResignation(Request $request){
        try{
            DB::beginTransaction();
            $data = [];
            $id = $request->resignation_id;
            $reason = $request->reason ?? '';
            $resignationData = ModelsResignation::where('id', $id)->first();
            if ($resignationData) {
                $data['status'] =  'rejected';
                $data['approved_by'] = auth()->id();
                $data['rejection_reason'] = $reason ?? '';
            }
            $resignationDataUpdate = $resignationData->update($data);

            if ($resignationDataUpdate) {
                $notificationData = [];
                $notificationData['related_resource_id'] =  $id ?? null;
                $notificationData['related_resource_user_id'] = $resignationData->user_id ?? null;
                $notificationData['related_resource_type'] = 'resignations/view-resignation/' .  $id;
                $notificationData['notification_title'] = 'Resignation Rejected';
                $notificationData['notification_description'] = 'Your resignation has been rejected.';
                $notificationData['created_by'] = auth()->id() ?? null;
                $notification = Notification::create($notificationData);
                DB::commit();
                return response()->json(['success' => true, 'message' => trans('messages.RESIGNATION_REJECTED')]);
            }
            DB::rollback();
            return response()->json(['status' => 'Fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
        } catch (Exception $e) {
            return response()->json(['status' => 'fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
        }
    }

    public function acceptResignation(Request $request){
        try {
            DB::beginTransaction();
            $data = [];
            $id = $request->resignation_id;
            $lastWorkingDate = $request->last_working_date;
            $resignationData = ModelsResignation::where('id', $id)->first();
            if ($resignationData) {
                $data['status'] =  'approved';
                $data['approved_by'] = auth()->id();
                $data['last_working_day'] = $lastWorkingDate ?? '';
            }
            $resignationDataUpdate = $resignationData->update($data);

            if ($resignationDataUpdate) {
                $notificationData = [];
                $notificationData['related_resource_id'] =  $id ?? null;
                $notificationData['related_resource_user_id'] = $resignationData->user_id ?? null;
                $notificationData['related_resource_type'] = 'resignations/view-resignation/' .  $id;
                $notificationData['notification_title'] = 'Resignation Accepted';
                $notificationData['notification_description'] = 'Your resignation has been accepted.';
                $notificationData['created_by'] = auth()->id() ?? null;
                $notification = Notification::create($notificationData);
                DB::commit();
                return response()->json(['success' => true, 'message' => trans('messages.RESIGNATION_ACCEPTED')]);
            }
            DB::rollback();
            return response()->json(['status' => 'Fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
        } catch (Exception $e) {
            return response()->json(['status' => 'fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
        }
    }

    protected function filterByCreationDate($query, $creationDate)
    {
        if ($creationDate == 'LastThreeMonth') {
            $query->where('resignations.created_at', '>=', Carbon::now()->subMonths(3));
        } elseif ($creationDate == 'LastSixMonth') {
            $query->where('resignations.created_at', '>=', Carbon::now()->subMonths(6));
        } elseif ($creationDate == 'CurrentYear') {
            $query->whereYear('resignations.created_at', Carbon::now()->year);
        } elseif ($creationDate == 'LastYear') {
            $query->whereYear('resignations.created_at', Carbon::now()->subYear()->year);
        } elseif ($creationDate == 'LastThreeYear') {
            $query->where('resignations.created_at', '<', Carbon::now()->subYears(3));
        }
    }
}
