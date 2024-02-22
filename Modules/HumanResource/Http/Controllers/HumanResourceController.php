<?php

namespace Modules\HumanResource\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Yajra\Datatables\Datatables;
use App\Models\Role as ModelsRole;
use App\Models\HighestQualification as ModelsHighestQualification;
use App\Models\FieldOfStudy as ModelsFieldOfStudy;
use App\Models\District as ModelsDistrict;
use App\Models\Ambulance as ModelsAmbulance;
use App\Models\DrivingLicenceType as ModelsDrivingLicenceType;
use App\Models\ShiftType as ModelsShiftType;
use App\Models\Bank as ModelsBank;
use App\Models\User as ModelsUser;
use App\Models\Leave as ModelsLeave;
use App\Models\UserAcademicDetail as ModelsUserAcademicDetail;
use App\Models\UserBankDetail as ModelsUserBankDetail;
use App\Models\UserWorkExperience as ModelsUserWorkExperience;
use App\Models\UserLicenseDetail as ModelsUserLicenseDetail;
use App\Models\AmbulanceShift as ModelsAmbulanceShift;
use App\Models\AmbulanceUserMapping as ModelsAmbulanceUserMapping;
use App\Models\SalarySlip as ModelsSalarySlip;
use App\Models\Attendance as ModelsAttendance;
use App\Models\Notification;
use App\Models\Task as ModelsTask;
use App\Models\ServiceArea as ModelsServiceArea;
use App\Models\LeaveType as ModelsLeaveType;
use Exception;
use Helpers;
use PDF;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\CarbonPeriod;

use Illuminate\Support\Facades\File as FacadesFile;


class HumanResourceController extends Controller
{
    /**
     * Display a listing super-admin of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {

        // Query to fetch user data with specific columns
        $userData = ModelsUser::select(
            'roles.id',
            DB::raw('CONCAT(users.first_name, " ", users.last_name) AS user_name'),
            'users.email',
            'roles.role_name',
            'users.created_at AS date_creation',
            'users.is_active',
            'users.id as user_id'
        )
            ->leftJoin('roles', 'users.role_id', '=', 'roles.id')
            ->where('roles.role_slug', 'super-admin');

        // Get the user data
        $userData = $userData->get();

        // Check if the request is AJAX
        if ($request->ajax()) {
            // Return DataTables response for AJAX requests
            return Datatables::of($userData)
                ->addIndexColumn()
                ->addColumn(
                    'action',
                    function ($row) {
                        // Build the action buttons
                        $btn = '';
                        $btn .= "<ul class='nk-tb-actions gx-1'><li><div class='drodown mr-n1'><a href='#' class='dropdown-toggle btn btn-icon btn-trigger' data-toggle='dropdown'><em class='icon ni ni-more-h'></em></a><div class='dropdown-menu dropdown-menu-right'><ul class='link-list-opt no-bdr'>";
                        if (true) {
                            // Add view user action button
                            $btn .= "<li>
                                    <a href='#' data-id='" . $row->user_id . "' class='view-user toggle'>
                                        <em class='icon ni ni-eye'></em> <span>View</span>
                                    </a>
                                </li>";
                        }
                        $btn .= "</ul></div></div></li></ul>";
                        return $btn;
                    }
                )
                ->rawColumns(['action'])
                ->make(true);
        }

        // Return the view with user data for non-AJAX requests
        return view('humanresource::index', ['data' => $userData]);
    }

    public function getSubAdminListView()
    {
        $subRoles = Helpers::getSubAdminChildRoles();
        return view('humanresource::subAdmin.index', ['subRoles' => $subRoles]);
    }

    /**
     * Get sub-admin users and display in DataTable.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function getSubAdmin(Request $request)
    {

        // Query to fetch sub-admin user data with necessary details
        $userData = ModelsUser::select(
            'roles.id',
            DB::raw('CONCAT(users.first_name, " ", users.last_name) AS user_name'),
            'users.email',
            DB::raw('CONCAT(u2.first_name, " ", u2.last_name) AS created_by_name'),
            'roles.role_name',
            'sub_role.role_name AS sub_role',
            'users.created_at AS date_creation',
            'users.is_active',
            'users.id as user_id'
        )
            ->leftJoin('users AS u2', 'users.created_by', '=', 'u2.id')
            ->leftJoin('roles', 'users.role_id', '=', 'roles.id')
            ->leftJoin('roles AS sub_role', 'roles.parent_id', '=', 'sub_role.id')
            ->where('sub_role.role_slug', 'sub-admin');

        // Apply additional conditions based on the request
        $userData = $userData->where(function ($query) use ($request) {
            if (!empty($request->toArray())) {
                if (isset($request->sub_role_id) && !empty($request->sub_role_id)) {
                    $query->where('roles.id', $request->sub_role_id);
                }
            }
        });

        // Retrieve the user data
        $userData = $userData->orderBy('users.id', 'desc')->get();

        // If the request is AJAX, return the data as DataTable response
        if ($request->ajax()) {
            return Datatables::of($userData)
                ->addIndexColumn()
                ->editColumn('date_creation', function ($row) {
                    return date('d/m/Y', strtotime($row->date_creation));
                })
                ->addColumn('action', function ($row) {
                    $btn = '';
                    $btn .= "<ul class='nk-tb-actions gx-1'><li><div class='drodown mr-n1'><a href='#' class='dropdown-toggle btn btn-icon btn-trigger' data-toggle='dropdown'><em class='icon ni ni-more-h'></em></a><div class='dropdown-menu dropdown-menu-right'><ul class='link-list-opt no-bdr'>";
                    if (true) {
                        $btn .= "<li>
                                    <a href='#' data-id='" . $row->user_id . "' class='view-user toggle'>
                                        <em class='icon ni ni-eye'></em> <span>View</span>
                                    </a>
                                </li>";
                    }
                    $confirmMsg = 'Are you sure, you want to delete it?';
                    if (true) {
                        $btn .= "<li>
                                    <a href='#' data-id='" . $row . "' class='eg-swal-av3'>
                                        <em class='icon ni ni-star'></em> <span>Update Status</span>
                                    </a>
                                </li>";
                    }
                    $btn .= "</ul></div></div></li></ul>";
                    return $btn;
                })
                ->rawColumns(['action', 'date_creation'])
                ->make(true);
        }
    }

    public function getDistrictAnchorListView()
    {
        $districts = ModelsDistrict::getAllDistricts();
        $upperUsers = ModelsUser::upperUser('1');

        return view('humanresource::districtAnchor.index', ['districts' => $districts, 'upperUsers' => $upperUsers]);
    }
    /**
     * Get district anchor users and display in DataTable.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function getDistrictAnchor(Request $request)
    {
        // Query to fetch district anchor user data with necessary details
        $userData = ModelsUser::select(
            DB::raw('CONCAT(users.first_name, " ", users.last_name) as user_name'),
            'users.phone_no',
            'districts.district_name',
            DB::raw('CONCAT(u2.first_name, " ", u2.last_name) as created_by_name'),
            'users.created_at as date_creation',
            'users.is_active',
            'users.id as user_id'
        )
            ->leftJoin('districts', 'users.district_id', '=', 'districts.id')
            ->leftJoin('users as u2', 'users.created_by', '=', 'u2.id')
            ->leftJoin('roles', 'users.role_id', '=', 'roles.id')
            ->where('roles.role_slug', 'district-anchor');

        // Apply additional conditions based on the request
        $userData = $userData->where(function ($query) use ($request) {
            if (!empty($request->toArray())) {
                if (isset($request->district_id) && !empty($request->district_id)) {
                    $query->where('users.district_id', $request->district_id);
                }
                if (isset($request->created_by) && !empty($request->created_by)) {
                    $query->where('users.created_by', $request->created_by);
                }
                if (isset($request->status) && $request->status !== '') {
                    $query->where('users.is_active', $request->status);
                }
                if (isset($request->creation_date) && !empty($request->creation_date)) {
                    $this->filterByCreationDate($query, $request->creation_date);
                }
                // Add more conditions as needed
            }
        });

        // Retrieve the user data
        $userData = $userData->orderBy('users.id', 'desc')->get();

        // If the request is AJAX, return the data as DataTable response
        if ($request->ajax()) {
            return Datatables::of($userData)
                ->addIndexColumn()
                ->editColumn('date_creation', function ($row) {
                    return date('d/m/Y', strtotime($row->date_creation));
                })
                ->addColumn('action', function ($row) {
                    $btn = '';
                    $btn .= "<ul class='nk-tb-actions gx-1'><li><div class='drodown mr-n1'><a href='#' class='dropdown-toggle btn btn-icon btn-trigger' data-toggle='dropdown'><em class='icon ni ni-more-h'></em></a><div class='dropdown-menu dropdown-menu-right'><ul class='link-list-opt no-bdr'>";
                    if (true) {
                        $btn .= "<li>
                                    <a href='#' data-id='" . $row->user_id . "' class='view-user toggle'>
                                        <em class='icon ni ni-eye'></em> <span>View</span>
                                    </a>
                                </li>";
                    }
                    if (true) {
                        $btn .= "<li>
                                    <a href='#' data-id='" . $row . "' class='eg-swal-av3'>
                                        <em class='icon ni ni-star'></em> <span>Update Status</span>
                                    </a>
                                </li>";
                    }
                    $btn .= "</ul></div></div></li></ul>";
                    return $btn;
                })
                ->rawColumns(['action', 'date_creation'])
                ->make(true);
        }
    }

    public function getAttendantListView()
    {
        $districts = ModelsDistrict::getAllDistricts();
        $upperUsers = ModelsUser::upperUser('6');
        $pendingUsers = ModelsUser::leftJoin('roles', 'users.role_id', '=', 'roles.id')->whereNull('users.is_verified')->where('roles.role_slug', 'attendant')->count();
        $users = ModelsUser::leftJoin('roles', 'users.role_id', '=', 'roles.id')
            ->whereNotNull('users.is_verified')
            ->where('roles.role_slug', 'attendant')->count();

        return view('humanresource::attendant.index', ['data' => $users, 'districts' => $districts, 'upperUsers' => $upperUsers, 'pendingUsers' => $pendingUsers]);
    }
    /**
     * Get attendant users and display in DataTable.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function getAttendant(Request $request)
    {
        // Get all filter data from the request
        $filterData = $request->all();

        // Query to fetch attendant user data with necessary details
        $userData = ModelsUser::select(
            DB::raw('CONCAT(users.first_name, " ", users.last_name) as user_name'),
            'users.phone_no',
            'districts.district_name',
            DB::raw('CONCAT(u2.first_name, " ", u2.last_name) as created_by_name'),
            'r2.role_name as created_by_role',
            'users.created_at as date_creation',
            'users.is_active',
            'users.is_active',
            'users.id as user_id'
        )
            ->leftJoin('districts', 'users.district_id', '=', 'districts.id')
            ->leftJoin('users as u2', 'users.created_by', '=', 'u2.id')
            ->leftJoin('roles', 'users.role_id', '=', 'roles.id')
            ->leftJoin('roles as r2', 'u2.role_id', '=', 'r2.id')
            ->whereNotNull('users.is_verified')
            ->where('roles.role_slug', 'attendant');

        // Apply additional conditions based on the request
        $userData = $userData->where(function ($query) use ($request) {
            if (!empty($request->toArray())) {
                if (isset($request->district_id) && !empty($request->district_id)) {
                    $query->where('users.district_id', $request->district_id);
                }
                if (isset($request->created_by) && !empty($request->created_by)) {
                    $query->where('users.created_by', $request->created_by);
                }
                if (isset($request->status) && $request->status !== '') {
                    $query->where('users.is_active', $request->status);
                }
                if (isset($request->creation_date) && !empty($request->creation_date)) {
                    $this->filterByCreationDate($query, $request->creation_date);
                }
            }
        });

        // Retrieve the user data
        $userData = $userData->orderBy('users.id', 'desc')->get();

        // If the request is AJAX, return the data as DataTable response
        if ($request->ajax()) {
            return Datatables::of($userData)
                ->addIndexColumn()
                ->editColumn('date_creation', function ($row) {
                    return date('d/m/Y', strtotime($row->date_creation));
                })
                ->addColumn('action', function ($row) {
                    $btn = '';
                    $btn .= "<ul class='nk-tb-actions gx-1'><li><div class='drodown mr-n1'><a href='#' class='dropdown-toggle btn btn-icon btn-trigger' data-toggle='dropdown'><em class='icon ni ni-more-h'></em></a><div class='dropdown-menu dropdown-menu-right'><ul class='link-list-opt no-bdr'>";
                    if (true) {
                        $btn .= "<li>
                                    <a href='#' data-id='" . $row->user_id . "' class='view-user toggle'>
                                        <em class='icon ni ni-eye'></em> <span>View</span>
                                    </a>
                                </li>";
                    }
                    if (true) {
                        $btn .= "<li>
                                    <a href='#' data-id='" . $row . "' class='eg-swal-av3'>
                                        <em class='icon ni ni-star'></em> <span>Update Status</span>
                                    </a>
                                </li>";
                    }
                    $btn .= "</ul></div></div></li></ul>";
                    return $btn;
                })
                ->editColumn('created_by_name', function ($row) {
                    if (!empty($row->created_by_name)) {
                        return !empty($row->created_by_role) ? "{$row->created_by_name} ({$row->created_by_role})" : $row->created_by_name;
                    } else {
                        return !empty($row->created_by_role) ? $row->created_by_role : 'NA';
                    }
                })
                ->rawColumns(['action', 'ambulance_number', 'created_by_name', 'date_creation'])
                ->make(true);
        }

        // If not an AJAX request, get additional data and return the view

    }

    public function getDriverListView()
    {

        // If not an AJAX request, get additional data and return the view
        $districts = ModelsDistrict::getAllDistricts();
        $upperUsers = ModelsUser::upperUser('2');
        $pendingUsers = ModelsUser::leftJoin('roles', 'users.role_id', '=', 'roles.id')->whereNull('users.is_verified')->where('roles.role_slug', 'driver')->count();
        $drivers = ModelsUser::leftJoin('roles', 'users.role_id', '=', 'roles.id')
            ->whereNotNull('users.is_verified')
            ->where('roles.role_slug', 'driver')->count();

        return view('humanresource::driver.index', ['drivers' => $drivers, 'districts' => $districts, 'upperUsers' => $upperUsers, 'pendingUsers' => $pendingUsers]);
    }
    /**
     * Get driver users and display in DataTable.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function getDriver(Request $request)
    {
        // Query to fetch driver user data with necessary details
        $userData = ModelsUser::select(
            DB::raw('CONCAT(users.first_name, " ", users.last_name) as user_name'),
            'users.phone_no',
            'districts.district_name',
            DB::raw('CONCAT(u2.first_name, " ", u2.last_name) as created_by_name'),
            'r2.role_name as created_by_role',
            'users.created_at as date_creation',
            'users.is_active',
            'users.id as user_id'
        )
            ->leftJoin('districts', 'users.district_id', '=', 'districts.id')
            ->leftJoin('users as u2', 'users.created_by', '=', 'u2.id')
            ->leftJoin('roles', 'users.role_id', '=', 'roles.id')
            ->leftJoin('roles as r2', 'u2.role_id', '=', 'r2.id')
            ->whereNotNull('users.is_verified')
            ->where('roles.role_slug', 'driver');

        // Apply additional conditions based on the request
        $userData = $userData->where(function ($query) use ($request) {
            if (!empty($request->toArray())) {
                if (isset($request->district_id) && !empty($request->district_id)) {
                    $query->where('users.district_id', $request->district_id);
                }
                if (isset($request->created_by) && !empty($request->created_by)) {
                    $query->where('users.created_by', $request->created_by);
                }
                if (isset($request->status) && $request->status !== '') {
                    $query->where('users.is_active', $request->status);
                }
                if (isset($request->creation_date) && !empty($request->creation_date)) {
                    $this->filterByCreationDate($query, $request->creation_date);
                }
            }
        });

        // Retrieve the user data
        $userData = $userData->orderBy('users.id', 'desc')->get();

        // If the request is AJAX, return the data as DataTable response
        if ($request->ajax()) {
            return Datatables::of($userData)
                ->addIndexColumn()
                ->editColumn('date_creation', function ($row) {
                    return date('d/m/Y', strtotime($row->date_creation));
                })
                ->addColumn('action', function ($row) {
                    $btn = '';
                    $btn .= "<ul class='nk-tb-actions gx-1'><li><div class='drodown mr-n1'><a href='#' class='dropdown-toggle btn btn-icon btn-trigger' data-toggle='dropdown'><em class='icon ni ni-more-h'></em></a><div class='dropdown-menu dropdown-menu-right'><ul class='link-list-opt no-bdr'>";
                    if (true) {
                        $btn .= "<li>
                                    <a href='#' data-id='" . $row->user_id . "' class='view-user toggle'>
                                        <em class='icon ni ni-eye'></em> <span>View</span>
                                    </a>
                                </li>";
                    }
                    if (true) {
                        $btn .= "<li>
                                    <a href='#' data-id='" . $row . "' class='eg-swal-av3'>
                                        <em class='icon ni ni-star'></em> <span>Update Status</span>
                                    </a>
                                </li>";
                    }
                    $btn .= "</ul></div></div></li></ul>";
                    return $btn;
                })
                ->editColumn('created_by_name', function ($row) {
                    if (!empty($row->created_by_name)) {
                        return !empty($row->created_by_role) ? "{$row->created_by_name} ({$row->created_by_role})" : $row->created_by_name;
                    } else {
                        return !empty($row->created_by_role) ? $row->created_by_role : 'NA';
                    }
                })
                ->rawColumns(['created_by_name', 'action', 'date_creation'])
                ->make(true);
        }
    }

    /**
     * Get pending driver requests and display in DataTable.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function getDriverPendingRequest(Request $request)
    {
        // Query to fetch pending driver requests with necessary details
        $userData = ModelsUser::select(
            DB::raw('CONCAT(users.first_name, " ", users.last_name) as user_name'),
            'users.phone_no',
            'districts.district_name',
            DB::raw('CONCAT(u2.first_name, " ", u2.last_name) as created_by_name'),
            'r2.role_name as created_by_role',
            'users.created_at as date_creation',
            'users.is_active',
            'users.id as user_id'
        )
            ->leftJoin('districts', 'users.district_id', '=', 'districts.id')
            ->leftJoin('users as u2', 'users.created_by', '=', 'u2.id')
            ->leftJoin('roles', 'users.role_id', '=', 'roles.id')
            ->leftJoin('roles as r2', 'u2.role_id', '=', 'r2.id')
            ->leftJoin('ambulance_user_mappings', 'ambulance_user_mappings.user_id', '=', 'users.id')
            ->leftJoin('ambulances', 'ambulance_user_mappings.ambulance_id', '=', 'ambulances.id')
            ->where('users.is_verified', null)
            ->where('roles.role_slug', 'driver');

        // Apply additional conditions based on the request
        $userData = $userData->where(function ($query) use ($request) {
            if (!empty($request->toArray())) {
                if (isset($request->district_id) && !empty($request->district_id)) {
                    $query->where('users.district_id', $request->district_id);
                }
                if (isset($request->created_by) && !empty($request->created_by)) {
                    $query->where('users.created_by', $request->created_by);
                }
                if (isset($request->status) && !empty($request->status)) {
                    $query->where('users.is_active', $request->status);
                }
                if (isset($request->creation_date) && !empty($request->creation_date)) {
                    $this->filterByCreationDate($query, $request->creation_date);
                }
            }
        });

        // Retrieve the user data
        $userData = $userData->get();

        // If the request is AJAX, return the data as DataTable response
        if ($request->ajax()) {
            return Datatables::of($userData)
                ->addIndexColumn()
                ->editColumn('date_creation', function ($row) {
                    return date('d/m/Y', strtotime($row->date_creation));
                })
                ->addColumn('action', function ($row) {
                    $btn = '';
                    $btn .= "<ul class='nk-tb-actions gx-1'><li><div class='drodown mr-n1'><a href='#' class='dropdown-toggle btn btn-icon btn-trigger' data-toggle='dropdown'><em class='icon ni ni-more-h'></em></a><div class='dropdown-menu dropdown-menu-right'><ul class='link-list-opt no-bdr'>";
                    if (true) {
                        $btn .= "<li>
                                    <a href='#' data-id='" . $row->user_id . "' class='view-user toggle'>
                                        <em class='icon ni ni-eye'></em> <span>View</span>
                                    </a>
                                </li>";
                    }
                    $btn .= "</ul></div></div></li></ul>";
                    return $btn;
                })
                ->editColumn('created_by_name', function ($row) {
                    if (!empty($row->created_by_name)) {
                        return !empty($row->created_by_role) ? "{$row->created_by_name} ({$row->created_by_role})" : $row->created_by_name;
                    } else {
                        return !empty($row->created_by_role) ? $row->created_by_role : 'NA';
                    }
                })
                ->rawColumns(['created_by_name', 'action', 'date_creation'])
                ->make(true);
        }
    }


    /**
     * Get pending attendant requests and display in DataTable.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function getAttendantPendingRequest(Request $request)
    {
        // Query to fetch pending attendant requests with necessary details
        $userData = ModelsUser::select(
            DB::raw('CONCAT(users.first_name, " ", users.last_name) as user_name'),
            'users.phone_no',
            'districts.district_name',
            DB::raw('CONCAT(u2.first_name, " ", u2.last_name) as created_by_name'),
            'r2.role_name as created_by_role',
            'users.created_at as date_creation',
            'users.is_active',
            'users.id as user_id'
        )
            ->leftJoin('districts', 'users.district_id', '=', 'districts.id')
            ->leftJoin('users as u2', 'users.created_by', '=', 'u2.id')
            ->leftJoin('roles', 'users.role_id', '=', 'roles.id')
            ->leftJoin('roles as r2', 'u2.role_id', '=', 'r2.id')
            ->where('users.is_verified', null)
            ->where('roles.role_slug', 'attendant');

        // Apply additional conditions based on the request
        $userData = $userData->where(function ($query) use ($request) {
            if (!empty($request->toArray())) {
                if (isset($request->district_id) && !empty($request->district_id)) {
                    $query->where('users.district_id', $request->district_id);
                }
                if (isset($request->created_by) && !empty($request->created_by)) {
                    $query->where('users.created_by', $request->created_by);
                }
                if (isset($request->status) && !empty($request->status)) {
                    $query->where('users.is_active', $request->status);
                }
                if (isset($request->creation_date) && !empty($request->creation_date)) {
                    $this->filterByCreationDate($query, $request->creation_date);
                }
            }
        });

        // Retrieve the user data
        $userData = $userData->get();

        // If the request is AJAX, return the data as DataTable response
        if ($request->ajax()) {
            return Datatables::of($userData)
                ->addIndexColumn()
                ->editColumn('date_creation', function ($row) {
                    return date('d/m/Y', strtotime($row->date_creation));
                })
                ->addColumn('action', function ($row) {
                    $btn = '';
                    $btn .= "<ul class='nk-tb-actions gx-1'><li><div class='drodown mr-n1'><a href='#' class='dropdown-toggle btn btn-icon btn-trigger' data-toggle='dropdown'><em class='icon ni ni-more-h'></em></a><div class='dropdown-menu dropdown-menu-right'><ul class='link-list-opt no-bdr'>";
                    if (true) {
                        $btn .= "<li>
                                    <a href='#' data-id='" . $row->user_id . "' class='view-user toggle'>
                                        <em class='icon ni ni-eye'></em> <span>View</span>
                                    </a>
                                </li>";
                    }
                    $btn .= "</ul></div></div></li></ul>";
                    return $btn;
                })
                ->editColumn('created_by_name', function ($row) {
                    if (!empty($row->created_by_name)) {
                        return !empty($row->created_by_role) ? "{$row->created_by_name} ({$row->created_by_role})" : $row->created_by_name;
                    } else {
                        return !empty($row->created_by_role) ? $row->created_by_role : 'NA';
                    }
                })
                ->rawColumns(['created_by_name', 'action', 'date_creation'])
                ->make(true);
        }

        // If not an AJAX request, get additional data and return the view
        $pendingUsers = ModelsUser::leftJoin('roles', 'users.role_id', '=', 'roles.id')->whereNull('users.is_verified')->where('roles.role_name', 'LIKE', '%attendant%')->count();
        return view('humanresource::attendant.index', ['pendingRequestAttendant' => $userData, 'pendingUsers' => $pendingUsers]);
    }


    /**
     * Display the form to add a new user.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function addUser(Request $request)
    {
        // Retrieve all roles
        $roles = ModelsRole::getAllRoles();

        // Retrieve all districts
        $districts = ModelsDistrict::getAllDistricts();

        // Retrieve all highest qualifications
        $highestQualification = ModelsHighestQualification::getAllHighestQualification();

        // Retrieve all fields of study
        $fieldOfStudy = ModelsFieldOfStudy::getAllFieldOfStudy();

        // Retrieve all driving license types
        $drivingLicenceTypes = ModelsDrivingLicenceType::getAllDrivingLicenceTypes();

        // Retrieve all shift types
        $shiftTypes = ModelsShiftType::getAllShifts();

        // Retrieve all banks
        $banks = ModelsBank::getAllBanks();

        // Retrieve the role slug from the request
        $role_slug = $request->role; // role slug

        // Return the view with relevant data
        return view('humanresource::addUser', [
            'roles' => $roles,
            'districts' => $districts,
            'highestQualifications' => $highestQualification,
            'fieldOfStudys' => $fieldOfStudy,
            'drivingLicenceTypes' => $drivingLicenceTypes,
            'shiftTypes' => $shiftTypes,
            'banks' => $banks,
            'roleSlug' => $role_slug
        ]);
    }

    /**
     * Display the details of a specific user.
     *
     * @param string $role
     * @param int $user_id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function viewUser($role, $user_id)
    {
        // Retrieve user details
        $userdata = ModelsUser::getUserDetailsById($user_id ?? null);;

        // Retrieve all districts
        $districts = ModelsDistrict::getAllDistricts();

        // Retrieve all highest qualifications
        $highestQualification = ModelsHighestQualification::getAllHighestQualification();

        // Retrieve all fields of study
        $fieldOfStudy = ModelsFieldOfStudy::getAllFieldOfStudy();

        // Retrieve all banks
        $banks = ModelsBank::getAllBanks();

        // Retrieve all shift types
        $shiftTypes = ModelsShiftType::getAllShifts();

        // Retrieve all driving license types
        $drivingLicenceTypes = ModelsDrivingLicenceType::getAllDrivingLicenceTypes();

        $ambulances = ModelsAmbulance::getAllAmbulances();

        // Return the view with relevant data
        return view('humanresource::viewUser', [
            'data' => $userdata,
            'districts' => $districts,
            'highestQualifications' => $highestQualification,
            'fieldOfStudys' => $fieldOfStudy,
            'banks' => $banks,
            'roleSlug' => $role,
            'shiftTypes' => $shiftTypes,
            'drivingLicenceTypes' => $drivingLicenceTypes,
            'ambulances' => $ambulances
        ]);
    }

    /**
     * Store a new user with specific role and additional details.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeUser(Request $request)
    {
        try {
            // Retrieve request parameters
            $roleName = $request->role_name;
            $roleSlug = $request->role_slug;
            $roleId = $request->role_id;

            $checkPhoneNumber = ModelsUser::where('phone_no', $request->phone_no)->first();

            if ($checkPhoneNumber) {
                return response()->json(['status' => 'fail', 'message' => trans('messages.PHONE_NO_ALREADY_EXIST')]);
            }

            // Start a database transaction
            DB::beginTransaction();

            // Common data for user creation
            $data = [];
            $data['role_id'] = $roleId ?? null;
            $data['email'] = $request->email ?? '';
            $data['first_name'] = $request->first_name ?? '';
            $data['last_name'] = $request->last_name ?? '';
            $data['middle_name'] = $request->middle_name ?? '';
            $data['gender'] = $request->gender ?? '';
            $data['dob'] = $request->dob ? \Carbon\Carbon::createFromFormat('d/m/Y', $request->dob)->format('Y-m-d') : null;
            $data['phone_no'] = $request->phone_no ?? '';
            $data['created_by'] = Auth::user()->id ?? null;
            $data['is_verified'] = 1;

            // for super admin this fields are not in form but this is not null in db that's why we are assigning 1
            $data['district_id'] = $request->district_id ?? 1;
            $data['state_id'] = $request->state_id ?? 1;

            // Upload profile picture to AWS 
            if ($request->hasFile('profile')) {
                $folderName = 'profile_pic';
                $attachmentUrl = Helpers::uploadAttachment($request->file('profile'), $folderName, time() . rand(100, 100000));
                $data['profile_path'] = $attachmentUrl;
            }

            // Check role and handle role-specific data
            if ($roleSlug == 'district-anchor' || $roleSlug == 'driver' || $roleSlug == 'attendant') {
                // Code for 'district-anchor', 'driver' or 'attendant' role 

                $data['reporting_manager_id'] = $request->reporting_manager_id ?? null;
                $data['joining_date'] = $request->joining_date ? \Carbon\Carbon::createFromFormat('d/m/Y', $request->joining_date)->format('Y-m-d') : null;
                $data['address'] = $request->address ?? '';
                $data['adhar_number'] = $request->aadhar_no ?? '';
                $data['pan_card_number'] = $request->pan_no ?? '';

                // uploading aadhar card document in AWS 
                if ($request->hasFile('aadhar_doc')) {
                    $folderName = 'aadhar_doc';
                    $aadharAttachmentUrl = Helpers::uploadAttachment($request->file('aadhar_doc'), $folderName, time() . rand(100, 100000));
                    $data['aadhar_image_path'] = $aadharAttachmentUrl;
                }

                // uploading pan card document in AWS S3 Bucket
                if ($request->hasFile('pan_doc')) {
                    $folderName = 'pan_doc';
                    $panAttachmentUrl = Helpers::uploadAttachment($request->file('pan_doc'), $folderName, time() . rand(100, 100000));
                    $data['pan_image_path'] = $panAttachmentUrl;
                }

                // create user information entry for the district anchor, attendant and driver
                $createUser = ModelsUser::create($data);

                if ($createUser) {
                    // after user is successfully created 

                    $userId = $createUser->id ?? null;

                    //update employee id to user by userId
                    $data['employee_id'] = Helpers::createEmployeeId($userId);
                    $userUpdate = ModelsUser::where('id', $userId)->first();
                    $userUpdate->employee_id = $data['employee_id'];
                    $userUpdate = $userUpdate->update();

                    // create user academic details entry by inserting in $academicDetails
                    $academicDetails = [];
                    $academicDetails['user_id'] = $userId;
                    $academicDetails['highest_qualification_id'] = $request->highest_qualification_id ?? null;
                    $academicDetails['year_of_completion'] = $request->year_of_completion ?? null;
                    $academicDetails['field_of_study_id'] = $request->field_of_study_id ?? null;

                    // upload marksheet in AWS S3 bucket
                    if ($request->hasFile('mark_doc')) {
                        $folderName = 'marksheets';
                        $marksheetAttachmentUrl = Helpers::uploadAttachment($request->file('mark_doc'), $folderName, time() . rand(100, 100000));
                        $academicDetails['marksheet_file_path'] = $marksheetAttachmentUrl;
                    }

                    // create entry for user academic details
                    $createUserAcademicDetails = ModelsUserAcademicDetail::create($academicDetails);

                    // create user bank details entry by inserting in $bankDetails
                    $bankDetails = [];
                    $bankDetails['user_id'] = $userId;
                    $bankDetails['bank_id'] = $request->bank_id;
                    $bankDetails['account_number'] = $request->account_number;
                    $bankDetails['ifsc_code'] = $request->ifsc_code;

                    // upload bank document in AWS S3 bucket
                    if ($request->hasFile('bank_doc')) {
                        $folderName = 'bank_doc';
                        $bankAttachmentUrl = Helpers::uploadAttachment($request->file('bank_doc'), $folderName, time() . rand(100, 100000));
                        $bankDetails['bank_proof_image_path'] = $bankAttachmentUrl;
                    }

                    // create entry for user bank details
                    $createUserBankDetails = ModelsUserBankDetail::create($bankDetails);

                    // create past work experience entry by inserting in $workExperienceDetails bu check if last_company_name is not empty
                    if (!empty($request->last_company_name)) {
                        $workExperienceDetails = [];
                        $workExperienceDetails['user_id'] = $userId;
                        $workExperienceDetails['company_name'] = $request->last_company_name;
                        $workExperienceDetails['designation'] = $request->designation;
                        $workExperienceDetails['location'] = $request->past_experience_location;
                        $workExperienceDetails['start_date'] = $request->start_date ? \Carbon\Carbon::createFromFormat('d/m/Y', $request->start_date)->format('Y-m-d') : null;
                        $workExperienceDetails['end_date'] = $request->end_date ? \Carbon\Carbon::createFromFormat('d/m/Y', $request->end_date)->format('Y-m-d') : null;

                        // upload past experience document in AWS S3 bucket
                        if ($request->hasFile('past_experience_document')) {

                            $folderName = 'past_experience_document';
                            $workExperienceAttachmentUrl = Helpers::uploadAttachment($request->file('past_experience_document'), $folderName, time() . rand(100, 100000));
                            $workExperienceDetails['document_image_path'] = $workExperienceAttachmentUrl;
                        }

                        // create entry for past work experience 
                        $createWorkExperienceDetails = ModelsUserWorkExperience::create($workExperienceDetails);
                    }

                    if ($roleSlug == 'district-anchor') {
                        // for district anchor user added successfully
                        DB::Commit();
                        return response()->json(['status' => 'success', 'message' => trans('messages.DISTRICT_ANCHOR_ADDED'), 'role' => 'district-anchor']);
                    } else {
                        // for driver and attendant driver license details stored in $driverLicenseDetails
                        $driverLicenseDetails = [];
                        $driverLicenseDetails['user_id'] = $userId;
                        $driverLicenseDetails['license_number'] = $request->license_number;
                        $driverLicenseDetails['dl_type_id'] = $request->license_type_id;
                        $driverLicenseDetails['expiry_date'] = $request->license_expiry_date ? \Carbon\Carbon::createFromFormat('d/m/Y', $request->license_expiry_date)->format('Y-m-d') : null;

                        // upload driving license in AWS S3 bucket
                        if ($request->hasFile('dl_doc')) {
                            $folderName = 'dl_doc';
                            $driverLicenseAttachnmentURL = Helpers::uploadAttachment($request->file('dl_doc'), $folderName, time() . rand(100, 100000));
                            $driverLicenseDetails['license_image_path'] = $driverLicenseAttachnmentURL;
                        }

                        // create entry for driver license details
                        $createDriverLicenseDetails = ModelsUserLicenseDetail::create($driverLicenseDetails);
                        $userType = ($roleSlug == 'driver') ? 'Driver' : 'Attendant';
                        if (isset($request['shift_start_date']) && !empty($request['shift_start_date'])) {
                            $shiftStartDates = array_filter($request['shift_start_date']);

                            // Check if any non-empty dates exist
                            if (!empty($shiftStartDates)) {
                                foreach ($request['ambulance_id'] as $index => $ambulanceId) {
                                    $shiftId = $request['shift_id'][$index];
                                    $serviceArea = $request['service_area_id'][$index];
                                    $stationArea = $request['station_area'][$index];
                                    $shiftStartDate = $request['shift_start_date'][$index];
                                    $shiftEndDate = $request['shift_end_date'][$index];

                                    $checkAmbulanceMapping = ModelsAmbulanceUserMapping::where('ambulance_id', $ambulanceId)->where('shift_type_id', $shiftId)->where('user_id', $userId)->first();

                                    if ($checkAmbulanceMapping) {
                                        $mappingId = $checkAmbulanceMapping->id;
                                    } else {
                                        $userMapping = ModelsAmbulanceUserMapping::create([
                                            'ambulance_id' => $ambulanceId,
                                            'user_id' => $userId,
                                            'shift_type_id' => $shiftId,
                                        ]);
                                        if ($userMapping) {
                                            $mappingId = $userMapping->id;
                                        }
                                    }


                                    $start_date = $this->parseDate($shiftStartDate);
                                    $end_date = $this->parseDate($shiftEndDate);

                                    if ($start_date && $end_date) {
                                        $period = CarbonPeriod::create($start_date, $end_date);

                                        foreach ($period as $date) {


                                            $checkAmbulanceShift = ModelsAmbulanceShift::leftJoin('ambulance_user_mappings', 'ambulance_user_mappings.id', '=', 'ambulance_shifts.ambulance_mapping_id')
                                                ->leftJoin('shift_types', 'shift_types.id', '=', 'ambulance_user_mappings.shift_type_id')
                                                ->where('ambulance_user_mappings.ambulance_id', $ambulanceId)
                                                ->where('ambulance_user_mappings.shift_type_id', $shiftId)
                                                ->where('ambulance_shifts.user_type', $userType)
                                                ->whereDate('ambulance_shifts.date', $date)
                                                ->first();

                                            if ($checkAmbulanceShift) {
                                                $shift = ModelsShiftType::where('id', $shiftId)->first();
                                                $shiftName = $shift->shift_name;

                                                DB::rollback();
                                                return response()->json([
                                                    'status' => 'Fail',
                                                    'message' => "This shift for this date: " . $date->format('d/m/Y') . " and for the " . $shiftName . " already assigned. Please unassign this shift first."
                                                ]);
                                            } else {

                                                $ambulanceShiftDetails = [
                                                    'ambulance_mapping_id' => $mappingId,
                                                    'user_type' => $userType,
                                                    'type' => 'Permanent',
                                                    'service_area_id' => $serviceArea,
                                                    'station_area' => $stationArea,
                                                    'date' => $date->format('Y-m-d'),
                                                    'created_by' => Auth::user()->id ?? null,
                                                ];

                                                $ambulanceShiftAssignment = ModelsAmbulanceShift::create($ambulanceShiftDetails);
                                            }
                                        }
                                    }
                                }
                            }
                        }


                        $notificationData = [];
                        $notificationData['related_resource_id'] = $userId ?? null;
                        $notificationData['related_resource_user_id'] = $request->reporting_manager_id ?? null;
                        $notificationData['related_resource_type'] = $roleSlug . '/view-user/' . $userId ?? null;
                        $notificationData['notification_title'] = 'New ' . $userType . ' Assignment';
                        $notificationData['notification_description'] = 'New ' . $userType . ' has been assigned to your district.';
                        $notificationData['created_by'] = Auth::user()->id ?? null;
                        //dd($notificationData);

                        $notification = Notification::create($notificationData);

                        DB::commit();

                        if ($roleSlug == 'driver') {
                            return response()->json(['status' => 'success', 'message' => trans('messages.DRIVER_ADDED'), 'role' => 'driver']);
                        } elseif ($roleSlug == 'attendant') {
                            return response()->json(['status' => 'success', 'message' => trans('messages.ATTENDANT_ADDED'), 'role' => 'attendant']);
                        }
                    }
                }
            } elseif ($roleSlug == 'sub-admin') {

                // code for sub-admin
                $subRoleId = $request->sub_role_id;
                $data['role_id'] = $subRoleId;

                // modified required fields in $data array
                $createUser = ModelsUser::create($data);

                if ($createUser) {
                    // sub-admin added successfully
                    $data['employee_id'] = Helpers::createEmployeeId($createUser->id ?? null);
                    $userUpdate = ModelsUser::where('id', $createUser->id)->first();
                    $userUpdate->employee_id = $data['employee_id'];
                    $userUpdate = $userUpdate->update();

                    DB::Commit();
                    return response()->json(['status' => 'success', 'message' => trans('messages.SUB_ADMIN_ADDED'), 'role' => 'sub-admin']);
                } else {
                    // if some issue is faced user is not created
                    DB::rollback();
                    return response()->json(['status' => 'Fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
                }
            } else {
                // it is going for some other role
                DB::rollback();
                return response()->json(['status' => 'Fail', 'message' => trans('messages.USER_NOT_ALLOWED_TO_REGISTER')]);
            }

            DB::rollback();
            return response()->json(['status' => 'Fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
        } catch (Exception $e) {
            return response()->json(['status' => 'fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
        }
    }

    private function parseDate($date)
    {
        return $date ? Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d') : null;
    }

    /**
     * Update user details based on the role.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateUserDetails(Request $request)
    {
        try {

            $checkPhoneNumber = ModelsUser::where('phone_no', $request->phone_no)->first();
            
            if ($checkPhoneNumber) {
                if ($checkPhoneNumber->id != $request->user_id) {
                    return response()->json(['status' => 'fail', 'message' => trans('messages.PHONE_NO_ALREADY_IN_USED')]);
                }
            }

            // Start a database transaction
            DB::beginTransaction();

            // Retrieve request parameters
            $userId = $request->user_id ?? null;
            $role = $request->role ?? '';
            $data = [];
            $data = [
                'role_id' => $request->role_id ?? null,
                'first_name' => $request->first_name ?? '',
                'last_name' => $request->last_name ?? '',
                'middle_name' => $request->middle_name ?? '',
                'gender' => $request->gender ?? '',
                'dob' => $request->dob ? \Carbon\Carbon::createFromFormat('d/m/Y', $request->dob)->format('Y-m-d') : null,
                'phone_no' => $request->phone_no ?? '',
                'email' => $request->email ?? '',
                'updated_by' => Auth::user()->id ?? null,
            ];

            // Handle role-specific data for 'driver', 'attendant', 'district-anchor'
            if ($role == 'driver' || $role == 'attendant' || $role == 'district-anchor') {

                $data['pan_card_number'] = $request->pan_no ?? '';
                $data['adhar_number'] = $request->aadhar_no ?? '';
                $data['address'] = $request->address ?? '';
                $data['district_id'] = $request->district_id ?? 0;
                $data['state_id'] = $request->state_id ?? 0;
                $data['reporting_manager_id'] = $request->reporting_manager_id ?? null;
                $data['joining_date'] = $request->joining_date ? \Carbon\Carbon::createFromFormat('d/m/Y', $request->joining_date)->format('Y-m-d') : null;


                // Upload Aadhar card document to AWS
                if ($request->hasFile('aadhar_doc')) {
                    $folderName = 'aadhar_doc';
                    $aadharAttachmentUrl = Helpers::uploadAttachment($request->file('aadhar_doc'), $folderName, time() . rand(100, 100000));
                    $data['aadhar_image_path'] = $aadharAttachmentUrl;
                }

                // Upload PAN card document to AWS
                if ($request->hasFile('pan_doc')) {
                    $folderName = 'pan_doc';
                    $panAttachmentUrl = Helpers::uploadAttachment($request->file('pan_doc'), $folderName, time() . rand(100, 100000));
                    $data['pan_image_path'] = $panAttachmentUrl;
                }
            }

            // Update user details
            $updateUser = ModelsUser::where('id', $userId)->first();
            if (!empty($updateUser)) {
                $updateUser->update($data);
            } else {
                return response()->json(['status' => 'Fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
            }

            // Handle additional details for 'driver', 'attendant', 'district-anchor'
            if ($role == 'driver' || $role == 'attendant' || $role == 'district-anchor') {
                if ($updateUser) {
                    // Update user academic details
                    $academicDetails = [
                        'highest_qualification_id' => $request->highest_qualification_id ?? null,
                        'year_of_completion' => $request->year_of_completion ?? null,
                        'field_of_study_id' => $request->field_of_study_id ?? null,
                    ];

                    // Upload mark sheet document to AWS
                    if ($request->hasFile('mark_doc')) {
                        $folderName = 'marksheets';
                        $marksheetAttachmentUrl = Helpers::uploadAttachment($request->file('mark_doc'), $folderName, time() . rand(100, 100000));
                        $academicDetails['marksheet_file_path'] = $marksheetAttachmentUrl;
                    }
                    $updateAcademicDetails = ModelsUserAcademicDetail::where('user_id', $userId)->first();

                    if (!empty($updateAcademicDetails)) {
                        $updateAcademicDetails->update($academicDetails);
                    }

                    // Update user bank details
                    $bankDetails = [
                        'bank_id' => $request->bank_id ?? null,
                        'account_number' => $request->account_number ?? null,
                        'ifsc_code' => $request->ifsc_code ?? null,
                    ];

                    // Upload bank document to AWS
                    if ($request->hasFile('bank_doc')) {
                        $folderName = 'bank_doc';
                        $bankAttachmentUrl = Helpers::uploadAttachment($request->file('bank_doc'), $folderName, time() . rand(100, 100000));
                        $bankDetails['bank_proof_image_path'] = $bankAttachmentUrl;
                    }

                    $updateBankDetails = ModelsUserBankDetail::where('user_id', $userId)->first();

                    if (!empty($updateBankDetails)) {
                        $updateBankDetails->update($bankDetails);
                    }

                    // Update user work experience details
                    $workExperienceDetails = [
                        'company_name' => $request->last_company_name ?? null,
                        'designation' => $request->designation ?? null,
                        'location' => $request->past_experience_location ?? null,
                        'start_date' => $request->start_date ? \Carbon\Carbon::createFromFormat('d/m/Y', $request->start_date)->format('Y-m-d') : null,
                        'end_date' => $request->end_date ? \Carbon\Carbon::createFromFormat('d/m/Y', $request->end_date)->format('Y-m-d') : null,
                    ];

                    // Upload past experience document to AWS
                    if ($request->hasFile('past_experience_document')) {

                        $folderName = 'past_experience_document';
                        $workExperienceAttachmentUrl = Helpers::uploadAttachment($request->file('past_experience_document'), $folderName, time() . rand(100, 100000));
                        $workExperienceDetails['document_image_path'] = $workExperienceAttachmentUrl;
                    }
                    $updateExperienceDetails = ModelsUserWorkExperience::where('user_id', $userId)->first();

                    if (!empty($updateExperienceDetails)) {
                        $updateExperienceDetails->update($workExperienceDetails);
                    }

                    // Additional details for 'driver', 'attendant'
                    if ($role == 'driver' || $role == 'attendant') {
                        $driverLicenseDetails = [
                            'license_number' => $request->license_number,
                            'dl_type_id' => $request->license_type_id,
                            'expiry_date' => $request->license_expiry_date ? \Carbon\Carbon::createFromFormat('d/m/Y', $request->license_expiry_date)->format('Y-m-d') : null,
                        ];

                        // Upload driving license to AWS
                        if ($request->hasFile('dl_doc')) {
                            $folderName = 'dl_doc';
                            $driverLicenseAttachnmentURL = Helpers::uploadAttachment($request->file('dl_doc'), $folderName, time() . rand(100, 100000));
                            $driverLicenseDetails['license_image_path'] = $driverLicenseAttachnmentURL;
                        }

                        $updatedriverLicenseDetails = ModelsUserLicenseDetail::where('user_id', $userId)->first();

                        if (!empty($updatedriverLicenseDetails)) {
                            $updatedriverLicenseDetails->update($driverLicenseDetails);
                        }
                    }
                }
            }
            // Commit the transaction and respond
            DB::Commit();
            return response()->json(['status' => 'success', 'message' => trans('messages.PROFILE_UPDATED_SUCCESS')]);

            DB::rollback();
            return response()->json(['status' => 'Fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
        } catch (Exception $e) {
            // Catch any exceptions during the process
            return response()->json(['status' => 'fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
        }
    }

    /**
     * Approve a user account based on the provided user ID.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function approveUserAccount(Request $request)
    {
        // Extract the user ID from the request
        $id = $request->input("id");

        // Call the approveUser method on ModelsUser to handle the approval
        $item = ModelsUser::approveUser($id ?? null);
        $notificationData = [];
        $notificationData['related_resource_id'] = $id ?? null;
        $notificationData['related_resource_user_id'] = $id ?? null;
        $notificationData['related_resource_type'] = '';
        $notificationData['notification_title'] = 'Profile Verified';
        $notificationData['notification_description'] = 'Your profile has been verified.';
        $notificationData['created_by'] = auth()->id() ?? null;
        $notification = Notification::create($notificationData);
        // Respond with a JSON indicating success and a message
        return response()->json(['success' => true, 'message' => trans('messages.USER_APPROVED')]);
    }

    /**
     * Reject a user request based on the provided user ID and rejection reason.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function rejectUserRequest(Request $request)
    {
        // Extract the user ID and rejection reason from the request
        $id = $request->input("id");
        $reason = $request->reason ?? null;

        // Call the addRejectReason method on ModelsUser to handle the rejection
        $item = ModelsUser::addRejectReason($id ?? null, $reason);
        $notificationData = [];
        $notificationData['related_resource_id'] = $id ?? null;
        $notificationData['related_resource_user_id'] = $id ?? null;
        $notificationData['related_resource_type'] = '';
        $notificationData['notification_title'] = 'Profile Rejected';
        $notificationData['notification_description'] = 'Your profile has been rejected.';
        $notificationData['created_by'] = auth()->id() ?? null;
        $notification = Notification::create($notificationData);
        // Respond with a JSON indicating success and a rejection message
        return response()->json(['success' => true, 'message' => trans('messages.USER_REJECTED')]);
    }


    /**
     * Update the status of a user based on the provided user ID and active status.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus(Request $request)
    {
        // Extract the user ID and active status from the request
        $userId = $request->id ?? null;
        $activeStatus = $request->active_status ?? 0;

        $alreadyShiftAssigned = ModelsAmbulanceUserMapping::leftJoin('ambulance_shifts', 'ambulance_shifts.ambulance_mapping_id', '=', 'ambulance_user_mappings.id')
            ->where('ambulance_user_mappings.user_id', $userId)
            ->whereDate('ambulance_shifts.date', '>=', now()->toDateString())
            ->first();

        if (empty($activeStatus) || $activeStatus == '') {
            if ($alreadyShiftAssigned) {
                return response()->json(['success' => false, 'data' => array(), 'msg' => trans('messages.USER_STATUS_NOT_CHANGED')]);
            } else {
                $userData = ModelsUser::where('id', $userId)->update(['is_active' => $activeStatus]);
            }
        } else {
            $userData = ModelsUser::where('id', $userId)->update(['is_active' => $activeStatus]);
        }

        if ($userData) {
            return response()->json(['data' => array(), 'success' => true, 'msg' => trans('messages.USER_STATUS_UPDATED')]);
        } else {
            return response()->json(['success' => false, 'data' => array(), 'msg' => trans('messages.SOMETHING_WENT_WRONG')]);
        }
    }


    /**
     * Filter the query based on the provided creation date range.
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @param string $creationDate
     */
    protected function filterByCreationDate($query, $creationDate)
    {
        // Check the provided creationDate and apply the corresponding filter to the query
        if ($creationDate == 'LastThreeMonth') {
            $query->where('users.created_at', '>=', Carbon::now()->subMonths(3));
        } elseif ($creationDate == 'LastSixMonth') {
            $query->where('users.created_at', '>=', Carbon::now()->subMonths(6));
        } elseif ($creationDate == 'CurrentYear') {
            $query->whereYear('users.created_at', Carbon::now()->year);
        } elseif ($creationDate == 'LastYear') {
            $query->whereYear('users.created_at', Carbon::now()->subYear()->year);
        } elseif ($creationDate == 'LastThreeYear') {
            $query->where('users.created_at', '<', Carbon::now()->subYears(3));
        }
    }


    /**
     * Get the list of ambulances based on the provided district ID.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAmbulanceByDistrictId(Request $request)
    {
        // Retrieve ambulances from the database based on the provided district ID
        $ambulance = ModelsAmbulance::where('district_id', $request->district_id)->where('status', 'Running')->get();

        // Return the ambulances as a JSON response
        return response()->json(['ambulance' => $ambulance]);
    }

    /**
     * Get payslip details for a specific user based on the provided year and month.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPayslipByMonthYearId(Request $request)
    {
        // Extract parameters from the request
        $userId = $request->user_id;
        $yearId = $request->year_id;
        $monthId = $request->month_id;

        // Retrieve payslip details from the database based on user, year, and month
        $payslipDetails = ModelsSalarySlip::where('user_id', $userId)->where('year', $yearId)->where('month', $monthId)->first();

        // Format the date for display
        $formattedDate = Carbon::createFromDate($yearId, $monthId, 1)->format('M, Y');

        // Extract specific details from the payslip and provide default values if not present
        $data['basic_salary'] = $payslipDetails->basic_salary ?? 0;
        $data['house_rent_allowance'] = $payslipDetails->house_rent_allowance ?? 0;
        $data['conveyance_allowance'] = $payslipDetails->conveyance_allowance ?? 0;
        $data['special_allowances'] = $payslipDetails->special_allowances ?? 0;
        $data['pf_contribution'] = $payslipDetails->pf_contribution ?? 0;
        $data['professional_tax'] = $payslipDetails->professional_tax ?? 0;

        // Calculate total deductions and total earnings
        $totalDeductions = ($payslipDetails->pf_contribution ?? 0) + ($payslipDetails->professional_tax ?? 0);
        $totalEarnings = ($payslipDetails->basic_salary ?? 0) + ($payslipDetails->house_rent_allowance ?? 0) + ($payslipDetails->conveyance_allowance ?? 0) + ($payslipDetails->special_allowances ?? 0);

        // Calculate net payable amount
        $netPayable = $totalEarnings - $totalDeductions;

        // Convert net payable amount to words using a helper function
        $netPayableInWords = Helpers::convertFiguresIntoWords($netPayable);

        // Return the payslip details as a JSON response
        return response()->json(['paySlipDetails' => $data, 'totalEarning' => $totalEarnings, 'totalDeduction' => $totalDeductions, 'netPayable' => $netPayable, 'netPayableInWords' => $netPayableInWords, 'salaryMonthYear' => $formattedDate]);
    }


    /**
     * Get attendance details for a specific user based on the provided year and month.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\View\Factory|\Illuminate\View\View|\Yajra\DataTables\DataTables
     */

    public function getAttendanceByMonthYearId(Request $request)
    {
        $userId = $request->user_id;
        $yearId = $request->year_id;
        $monthId = $request->month_id;
        $fromDate = $yearId . "-" . $monthId . "-01";
        $toDate = date("Y-m-t", strtotime($fromDate));

        $result = DB::select('CALL GenerateAttendanceReport(?, ?)', [$fromDate, $toDate]);

        // Apply additional conditions to filter the results
        $result = collect($result);

        $result = $result->filter(function ($row) use ($request) {
            if (!empty($request->toArray())) {
                if (isset($request->user_id) && !empty($request->user_id) && $row->user_id != $request->user_id) {
                    return false;
                }
            }
            return true;
        });

        return DataTables::of($result)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $btn = '';

                if ($row->attendance_date <= now()->toDateString()) {
                    $btn .= "<ul class='nk-tb-actions gx-1'>
                                    <li>
                                        <div class='drodown mr-n1'><a href='#' class='dropdown-toggle btn btn-icon btn-trigger' data-toggle='dropdown'><em class='icon ni ni-more-h'></em></a>
                                            <div class='dropdown-menu dropdown-menu-right'>
                                                <ul class='link-list-opt no-bdr'>";

                    // Replace the placeholder condition with your actual condition
                    if (true) {
                        $btn .= "<li>
                                                                    <a href='#' data-toggle='modal' data-target='#viewUserAttendanceInfo' class='view-user-attendance toggle' data-id='" . $row->user_id . "' data-date='" . $row->attendance_date . "' data-shift-id='" . $row->shift_type_id . "'>
                                                                        <em class='icon ni ni-eye'></em> <span>View</span>
                                                                    </a>
                                                                </li>";
                    }
                    $btn .=                     "</ul>
                                            </div>
                                        </div>
                                    </li>
                                </ul>";
                }

                return $btn;
            })
            ->editColumn('attendance_status', function ($row) {
                $status = '';
                $currentDate = \carbon\carbon::now()->format('Y-m-d');
                if ($row->attendance_date < $currentDate) {
                    if ($row->attendance_status == 'approved') {
                        return 'Leave';
                    } elseif ($row->attendance_status == 'pending' || $row->attendance_status == 'rejected') {
                        return 'Absent';
                    } else {
                        return $row->attendance_status;
                    }
                } else {
                    return $status;
                }
            })
            ->editColumn('attendance_date', function ($row) {
                $row->attendance_date = \Carbon\Carbon::createFromFormat('Y-m-d', $row->attendance_date)->format('d/m/Y');
                return $row->attendance_date;
            })
            ->rawColumns(['action', 'attendance_status', 'attendance_date'])
            ->make(true);
    }

    /**
     * Get attendance information for a specific user on a given date and shift.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAttendanceInfoByDate(Request $request)
    {
        // Extract parameters from the request
        $userId = $request->user_id;
        $date = $request->attendanceDate;
        $shiftId = $request->shiftId;
        $result = DB::select('CALL GenerateAttendanceReport(?, ?)', [$date, $date]);

        // Apply additional conditions to filter the results
        $result = collect($result);

        $result = $result->filter(function ($row) use ($request) {
            if (!empty($request->toArray())) {
                if (isset($request->shiftId) && !empty($request->shiftId) && $row->shift_type_id != $request->shiftId) {
                    return false;
                }
                if (isset($request->user_id) && !empty($request->user_id) && $row->user_id != $request->user_id) {
                    return false;
                }
            }
            return true;
        });

        $response = [];

        if (!empty($result)) {
            $logsArray = [];
            foreach ($result as $key =>  $log) {
                // Create an array for each log with relevant properties
                $logArray = [
                    //'log_id' => $log['id'],
                    'user_id' => $log->user_id,
                    'attendance_date' => $log->attendance_date,
                    'login_time' => $log->login_time ? Carbon::createFromFormat(
                        'H:i:s',
                        $log->login_time
                    )->format('H:i') : '',
                    'logout_time' => $log->logout_time,
                    'shift_id' => $log->shift_type_id,
                    'login_meter_reading' => $log->login_meter_reading,
                    'login_location' => $log->login_location,
                    'logout_time' => $log->logout_time ? Carbon::createFromFormat(
                        'H:i:s',
                        $log->logout_time
                    )->format('H:i') : '',
                    'logout_meter_reading' => $log->logout_meter_reading,
                    'logout_location' => $log->logout_location,
                    'duration' => $log->duration,
                    'km_run' => $log->km_run,
                    'attendanceStatus' => ($log->attendance_status == 'approved') ? 'Leave' : (($log->attendance_status == 'rejected' || $log->attendance_status == 'pending') ? 'Absent' : $log->attendance_status),
                    'leave_type_id' => $log->leave_type_id,
                    'leave_reason' => $log->leave_reason

                    // Add other log properties as needed
                ];
                $logsArray[] = $logArray;
            }

            // Check if the shift ID matches the requested shift
            if ($logsArray[0]['shift_id'] == $shiftId) {
                $response['attendanceLogs'] = $logsArray;
            } else {
                // If the shift ID doesn't match, user is considered absent for the requested shift
                $response['attendanceLogs'] = [];
            }
        } else {
            // If no attendance logs are found, user is considered absent
            $response['attendanceLogs'] = [];
        }

        // Format date for response
        $response['date'] = $date ? Carbon::createFromFormat('Y-m-d', $date)->format('l, d M Y') : '';
        $response['attendance_date'] = $date;
        $response['attendance_user_id'] = $userId;
        $response['attendance_shift_id'] = $shiftId;
        $leaveTypes = ModelsLeaveType::allLeaveTypes();
        // Return the response as a JSON
        return response()->json(['attendanceInfo' => $response, 'leaveType' => $leaveTypes]);
    }


    /**
     * Update attendance information for a specific user on a given date and shift.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateAttendanceInfoByDate(Request $request)
    {
        try {
            // Start a database transaction
            DB::beginTransaction();

            // Extract parameters from the request
            $userId = $request->attendance_user_id;
            $attendanceStatus = $request->attendance_status;
            $shiftId = $request->attendance_shift_id;
            $date = $request->attendance_details_date;
            $data = [];
            //print_r($request->all());die;
            // Check if the attendance status is 'Present'
            if ($attendanceStatus == 'Present') {
                // Prepare data for attendance update or creation
                $data['user_id'] = $userId ?? null;
                $data['shift_type_id'] = $shiftId ?? null;
                $data['attendance_date'] = $date ?? null;
                $data['login_time'] = Carbon::createFromFormat('H:i', $request->checkIn_time)->toTimeString() ?? null;
                $data['login_meter_reading'] = $request->checkIn_meter_reading ?? null;
                $data['logout_time'] = Carbon::createFromFormat('H:i', $request->checkOut_time)->toTimeString() ?? null;
                $data['logout_meter_reading'] = $request->checkOut_meter_reading ?? null;
                $data['duration'] = $request->duration ?? null;
                $data['km_run'] = $request->km_run ?? null;
                $data['login_location'] = $request->checkIn_location ?? null;
                $data['logout_location'] = $request->checkOut_location ?? null;


                // Check if attendance record already exists
                $attendanceStatusCheck = ModelsAttendance::where('user_id', $userId)
                    ->where('shift_type_id', $shiftId)
                    ->whereDate('attendance_date', $date)
                    ->first();

                // Update or create the attendance record based on its existence
                if (!empty($attendanceStatusCheck)) {
                    $updateAttendance = $attendanceStatusCheck->update($data);
                } else {
                    $updateAttendance = ModelsAttendance::create($data);
                }
            } else if ($attendanceStatus == 'Leave') {
                // If the attendance status is not 'Present', delete the attendance record
                $deleteAttendance = ModelsAttendance::where('user_id', $userId)
                    ->where('shift_type_id', $shiftId)
                    ->whereDate('attendance_date', $date)->first();

                if ($deleteAttendance) {
                    $deleteAttendance =  $deleteAttendance->delete();
                }

                $reportingManagerData = ModelsUser::where('id', $userId)->first();
                $leaveData = [];
                $leaveData['user_id'] = $userId ?? null;
                $leaveData['leave_type_id'] = $request->attendance_leave_type ?? null;
                $leaveData['applying_to'] = $reportingManagerData->reporting_manager_id ?? null;
                $leaveData['leave_reason'] = $request->attendance_leave_reason ?? null;
                $leaveData['status'] = 'approved';
                $leaveData['approved_by'] = Auth::user()->id ?? null;
                $leaveData['approved_on'] = $date ?? null;


                $leaveStatusCheck = ModelsLeave::where('user_id', $userId)
                    ->whereDate('from_date', '<=', $date) // Check if today is after or equal to from_date
                    ->whereDate('to_date', '>=', $date) // Check if today is before or equal to to_date
                    ->first();

                if ($leaveStatusCheck) {
                    $leaveData['updated_by'] = Auth::user()->id ?? null;
                    $updateAttendance = $leaveStatusCheck->update($leaveData);
                } else {
                    $leaveData['from_date'] = $date ?? null;
                    $leaveData['to_date'] = $date ?? null;
                    $leaveData['created_by'] = Auth::user()->id ?? null;
                    $updateAttendance = ModelsLeave::create($leaveData);
                }
            } else {
                // If the attendance status is not 'Present', delete the attendance record
                $updateAttendance = ModelsAttendance::where('user_id', $userId)
                    ->where('shift_type_id', $shiftId)
                    ->whereDate('attendance_date', $date)
                    ->delete();
            }
            // Check if the update or deletion was successful
            if ($updateAttendance) {
                // Commit the database transaction
                DB::commit();
                return response()->json(['status' => 'success', 'message' => trans('messages.ATTENDANCE_UPDATED')]);
            }

            // Rollback the database transaction if something went wrong
            DB::rollback();
            return response()->json(['status' => 'Fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
        } catch (Exception $e) {
            // Handle exceptions and return a failure response
            return response()->json(['status' => 'fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
        }
    }



    /**
     * Get leave details for a specific user based on the given year and month.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLeaveDetailsByDate(Request $request)
    {
        // Extract parameters from the request
        $userId = $request->user_id;
        $yearId = $request->year_id;
        $monthId = $request->month_id;

        // Retrieve leave details from the database
        $leaveDetails = ModelsLeave::select(
            'leaves.id',
            'leaves.user_id',
            'leaves.status',
            'leave_types.name as leave_type',
            DB::raw("DATE_FORMAT(leaves.from_date, '%d/%m/%y') AS leave_from_date"),
            DB::raw("DATE_FORMAT(leaves.to_date, '%d/%m/%y') AS leave_to_date"),
            DB::raw("CONCAT(applying_to.first_name, ' ', applying_to.last_name) AS applying_to_name"),
            'applying_to_role.role_name',
            DB::raw("DATE_FORMAT(leaves.created_at, '%d/%m/%y') AS leave_created_at"),
            DB::raw("DATE_FORMAT(leaves.approved_on, '%d/%m/%y') AS leave_approved_on"),
            'leaves.leave_reason',
            'leaves.reject_reason',
            'leaves.attachment',
            DB::raw("DATEDIFF(leaves.to_date, leaves.from_date) + 1 AS duration_in_days")
        )
            ->leftJoin('leave_types', 'leave_types.id', '=', 'leaves.leave_type_id')
            ->leftJoin('users AS approved_by', 'approved_by.id', '=', 'leaves.approved_by')
            ->leftJoin('users AS applying_to', 'applying_to.id', '=', 'leaves.applying_to')
            ->leftJoin('roles AS applying_to_role', 'applying_to_role.id', '=', 'applying_to.role_id')
            ->where('leaves.user_id', $userId)
            ->whereMonth('leaves.created_at', $monthId)
            ->whereYear('leaves.created_at', $yearId)
            ->get();

        // Return the leave details as a JSON response
        return response()->json(['leaveDetails' => $leaveDetails]);
    }


    /**
     * Get task details for a specific user based on the given year and month.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTaskInfoByDate(Request $request)
    {
        // Extract parameters from the request
        $userId = $request->user_id;
        $yearId = $request->year_id;
        $monthId = $request->month_id;

        // Retrieve task details from the database
        $taskDetails = ModelsTask::select(
            'tasks.id',
            'tasks.title',
            'u2.role_id',
            'tasks.description',
            'tasks.attached_file',
            'tasks.remark',
            'u2.id as user_id',
            \DB::raw('COALESCE(CONCAT(users.first_name, " ", users.last_name)) as task_created_by'),
            \DB::raw('COALESCE(roles.role_name) as task_created_user_role'),
            \DB::raw('COALESCE(districts.district_name) as task_created_user_district'),
            \DB::raw('COALESCE(DATE_FORMAT(tasks.created_at, "%d-%m-%y")) as task_created_date'),
            \DB::raw('COALESCE(DATE_FORMAT(tasks.updated_at, "%d-%m-%y")) as task_updated_date'),
            \DB::raw('COALESCE(CONCAT(u2.first_name, " ", u2.last_name)) as task_assigned_to'),
            \DB::raw('COALESCE(r2.role_name) as task_assigned_user_role'),
            \DB::raw('COALESCE(tasks.priority) as priority'),
            \DB::raw('COALESCE(tasks.status) as status')
        )
            ->leftJoin('users', 'tasks.created_by', '=', 'users.id')
            ->leftJoin('users as u2', 'tasks.assigned_to', '=', 'u2.id')
            ->leftJoin('districts', 'users.district_id', '=', 'districts.id')
            ->leftJoin('roles', 'users.role_id', '=', 'roles.id')
            ->leftJoin('roles as r2', 'u2.role_id', '=', 'r2.id')
            ->where('tasks.assigned_to', $userId)
            ->whereYear('tasks.created_at', $yearId)
            ->whereMonth('tasks.created_at', $monthId)
            ->get();

        // Return the task details as a JSON response
        return response()->json(['taskDetails' => $taskDetails]);
    }


    /**
     * Approve leave for a specific user.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function approveLeave(Request $request)
    {
        try {
            // Begin a database transaction
            DB::beginTransaction();

            // Extract parameters from the request
            $leaveId = $request->leave_id;
            $userId = $request->user_id;

            // Retrieve leave details from the database
            $leaveDetails = ModelsLeave::where('id', $leaveId)->where('user_id', $userId)->first();

            // Check if leave details exist
            if ($leaveDetails) {
                // Update leave details with approval information
                $leaveDetails->approved_by = auth()->id();
                $leaveDetails->updated_by = auth()->id();
                $leaveDetails->approved_on = now(); // Use Carbon for consistent date/time handling
                $leaveDetails->status = 'approved';
            }

            // Update leave data in the database
            $leaveDataUpdate = $leaveDetails->update();

            // Check if leave data update was successful
            if ($leaveDataUpdate) {
                $notificationData = [];
                $notificationData['related_resource_id'] = $leaveId ?? null;
                $notificationData['related_resource_user_id'] = $request->user_id ?? null;
                $notificationData['related_resource_type'] = 'leaves/view-leave-info/' . $leaveId;
                $notificationData['notification_title'] = 'Leave Accepted';
                $notificationData['notification_description'] = 'Your leave request has been accepted.';
                $notificationData['created_by'] = auth()->id() ?? null;
                $notification = Notification::create($notificationData);
                // Commit the database transaction
                DB::commit();
                return response()->json(['success' => true, 'message' => trans('messages.LEAVE_APPROVED')]);
            }

            // If the update was not successful, rollback the transaction
            DB::rollback();
            return response()->json(['status' => 'Fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
        } catch (Exception $e) {
            // Handle exceptions and return an appropriate response
            return response()->json(['status' => 'fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
        }
    }


    /**
     * Reject leave for a specific user.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function rejectLeave(Request $request)
    {
        try {
            // Begin a database transaction
            DB::beginTransaction();

            // Extract parameters from the request
            $leaveId = $request->leave_id;
            $userId = $request->user_id;

            // Retrieve leave details from the database
            $leaveDetails = ModelsLeave::where('id', $leaveId)->where('user_id', $userId)->first();

            // Check if leave details exist
            if ($leaveDetails) {
                // Update leave details with rejection information
                $leaveDetails->status = 'rejected';
                $leaveDetails->reject_reason = $request->reason;
                $leaveDetails->updated_by = auth()->id();
            }

            // Update leave data in the database
            $leaveDataUpdate = $leaveDetails->update();

            // Check if leave data update was successful
            if ($leaveDataUpdate) {
                $notificationData = [];
                $notificationData['related_resource_id'] = $leaveId ?? null;
                $notificationData['related_resource_user_id'] = $request->user_id ?? null;
                $notificationData['related_resource_type'] = 'leaves/view-leave-info/' . $leaveId;
                $notificationData['notification_title'] = 'Leave Rejected';
                $notificationData['notification_description'] = 'Your leave request has been rejected.';
                $notificationData['created_by'] = auth()->id() ?? null;
                $notification = Notification::create($notificationData);
                // Commit the database transaction
                DB::commit();
                return response()->json(['success' => true, 'message' => trans('messages.LEAVE_CANCELLED')]);
            }

            // If the update was not successful, rollback the transaction
            DB::rollback();
            return response()->json(['status' => 'Fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
        } catch (Exception $e) {
            // Handle exceptions and return an appropriate response
            return response()->json(['status' => 'fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
        }
    }


    /**
     * Add a salary slip for a specific user based on month and year.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addSalarySlipByMonthYearId(Request $request)
    {
        try {
            // Begin a database transaction
            DB::beginTransaction();

            // Extract parameters from the request
            $monthId = $request->payslip_month_id;
            $yearId = $request->payslip_year_id;
            $userId = $request->salary_user_id;

            // Check if a salary slip already exists for the specified month and year
            $checkMonthSalarySlipCount = ModelsSalarySlip::where('user_id', $userId)->where('year', $yearId)->where('month', $monthId)->count();
            if ($checkMonthSalarySlipCount > 0) {
                // Return a response indicating that a duplicate salary slip is not allowed
                return response()->json(['status' => 'Fail', 'message' => trans('messages.SALARY_SLIP_DUPLICATE')]);
            }

            // Extract salary components from the request
            $basicSalary = $request->add_basic_salary ?? 0;
            $hra = $request->add_hra ?? 0;
            $professionalTax = $request->add_professional_tax ?? 0;
            $conveyance = $request->add_conveyance ?? 0;
            $specialAllowance = $request->add_special_allowance ?? 0;

            // Calculate salary information based on the provided components
            $salaryInfo = Helpers::calculateSalary($basicSalary, $hra, $conveyance, $specialAllowance, $professionalTax);

            // Prepare data for the new salary slip
            $data = [
                'user_id' => $userId,
                'month' => $monthId,
                'year' => $yearId,
                'salary_date' => $request->salary_date,
                'basic_salary' => $basicSalary,
                'house_rent_allowance' => $hra,
                'conveyance_allowance' => $conveyance,
                'special_allowances' => $specialAllowance,
                'professional_tax' => $professionalTax,
                'gross_salary' => $salaryInfo['grossSalary'],
                'net_payable_amount' => $salaryInfo['netPayableSalary'],
                'pf_contribution' => $salaryInfo['pf'],
            ];

            // Create a new salary slip in the database
            $createPayslip = ModelsSalarySlip::create($data);

            // Check if the salary slip creation was successful
            if ($createPayslip) {
                // Commit the database transaction
                DB::commit();
                return response()->json(['status' => 'success', 'message' => trans('messages.SALARY_SLIP_ADDED')]);
            }

            // If the creation was not successful, rollback the transaction
            DB::rollback();
            return response()->json(['status' => 'Fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
        } catch (Exception $e) {
            // Handle exceptions and return an appropriate response
            return response()->json(['status' => 'fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
        }
    }


    /**
     * Delete a salary slip for a specific user based on month and year.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteSalarySlipByMonthYear(Request $request)
    {
        try {
            // Begin a database transaction
            DB::beginTransaction();

            // Extract parameters from the request
            $monthId = $request->month_id;
            $yearId = $request->year_id;
            $userId = $request->user_id;

            // Check if a salary slip exists for the specified month and year
            $checkMonthSalarySlipCount = ModelsSalarySlip::where('user_id', $userId)->where('year', $yearId)->where('month', $monthId)->count();
            if ($checkMonthSalarySlipCount < 0) {
                // Return a response indicating that the salary slip does not exist
                return response()->json(['status' => 'Fail', 'message' => trans('messages.SALARY_SLIP_NOT_EXIST')]);
            }

            // Delete the salary slip for the specified month and year
            $deleteSalarySlip = ModelsSalarySlip::where('user_id', $userId)->where('year', $yearId)->where('month', $monthId)->delete();

            // Check if the deletion was successful
            if ($deleteSalarySlip) {
                // Commit the database transaction
                DB::commit();
                return response()->json(['status' => 'success', 'message' => trans('messages.SALARY_SLIP_DELETED')]);
            }

            // If the deletion was not successful, rollback the transaction
            DB::rollback();
            return response()->json(['status' => 'Fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
        } catch (Exception $e) {
            // Handle exceptions and return an appropriate response
            return response()->json(['status' => 'fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
        }
    }


    /**
     * Update salary slip details for a specific user based on month and year.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateSalarySlipDetails(Request $request)
    {
        try {
            // Begin a database transaction
            DB::beginTransaction();

            // Extract parameters from the request
            $monthId = $request->month_id;
            $yearId = $request->year_id;
            $userId = $request->user_id;

            // Find the existing salary slip for the specified month and year
            $salarySlip = ModelsSalarySlip::where('user_id', $userId)->where('year', $yearId)->where('month', $monthId)->first();

            // Check if the salary slip exists
            if ($salarySlip) {
                // Extract salary details from the request
                $basicSalary = $request->basic_salary ?? 0;
                $hra = $request->hra ?? 0;
                $professionalTax = $request->professional_tax ?? 0;
                $conveyance = $request->conveyance ?? 0;
                $specialAllowance = $request->special_allowance ?? 0;

                // Calculate salary information
                $salaryInfo = Helpers::calculateSalary($basicSalary, $hra, $conveyance, $specialAllowance, $professionalTax);

                // Prepare data for updating the salary slip
                $data['basic_salary'] = $basicSalary;
                $data['house_rent_allowance'] = $hra;
                $data['conveyance_allowance'] = $conveyance;
                $data['special_allowances'] = $specialAllowance;
                $data['professional_tax'] = $professionalTax;
                $data['gross_salary'] = $salaryInfo['grossSalary'];
                $data['net_payable_amount'] = $salaryInfo['netPayableSalary'];
                $data['pf_contribution'] = $salaryInfo['pf'];

                // Update the salary slip details
                $updateSalarySlipDetails = $salarySlip->update($data);

                // Check if the update was successful
                if ($updateSalarySlipDetails) {
                    // Commit the database transaction
                    DB::commit();
                    return response()->json(['status' => 'success', 'message' => trans('messages.SALARY_SLIP_UPDATED')]);
                }

                // If the update was not successful, rollback the transaction
                DB::rollback();
                return response()->json(['status' => 'Fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
            }

            // If the salary slip does not exist, rollback the transaction
            DB::rollback();
            return response()->json(['status' => 'Fail', 'message' => trans('messages.SALARY_SLIP_NOT_EXIST')]);
        } catch (Exception $e) {
            // Handle exceptions and return an appropriate response
            return response()->json(['status' => 'fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
        }
    }


    /**
     * Download salary slip in PDF format.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function downloadSalarySlip(Request $request)
    {
        try {
            // Extract parameters from the request
            $monthId = $request->input('month_id', '');
            $yearId = $request->input('year_id', '');
            $userId = $request->input('user_id', '');

            // Retrieve salary slip data from the database
            $data = ModelsSalarySlip::where('user_id', $userId)
                ->where('year', $yearId)
                ->where('month', $monthId)
                ->first();

            if ($data) {
                // Generate PDF using the salary slip data
                $pdf = PDF::loadView('pdf', compact('data'))->output();

                // Set the response headers for PDF download
                $headers = [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="Payslip_' . $monthId . $yearId . '.pdf"',
                ];

                // Send the PDF content as the response
                return response($pdf, 200, $headers);
            }

            return response()->json(['status' => 'fail', 'message' => trans('messages.SALARY_SLIP_NOT_EXIST')]);
        } catch (\Exception $e) {
            // Handle exceptions and return an appropriate response
            return response()->json(['status' => 'fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
        }
    }

    public function getUserShiftDetails(Request $request)
    {
        $userId = $request->user_id;
        $month = $request->month_id;
        $year = $request->year_id;
        $assignShift = ModelsAmbulanceUserMapping::select('ambulances.ambulance_no', 'ambulances.chassis_no', 'shift_types.shift_name', 'ambulance_shifts.date', 'ambulance_shifts.id as ambulance_shift_id')
            ->leftJoin('ambulance_shifts', 'ambulance_user_mappings.id', '=', 'ambulance_shifts.ambulance_mapping_id')
            ->leftJoin('shift_types', 'ambulance_user_mappings.shift_type_id', '=', 'shift_types.id')
            ->leftJoin('ambulances', 'ambulances.id', '=', 'ambulance_user_mappings.ambulance_id')
            ->where('ambulance_user_mappings.user_id', $userId)
            ->whereMonth('ambulance_shifts.date', $month)
            ->whereYear('ambulance_shifts.date', $year);

        $assignShift = $assignShift->where(function ($query) use ($request) {
            if (!empty($request->toArray())) {
                if (isset($request->ambulance_id) && !empty($request->ambulance_id)) {
                    $query->where('ambulance_user_mappings.ambulance_id', $request->ambulance_id);
                }
                if (isset($request->shift_type) && !empty($request->shift_type)) {
                    $query->where('ambulance_user_mappings.shift_type_id', $request->shift_type);
                }
            }
        })
            ->orderBy('date', 'asc');

        $assignShift = $assignShift->get();

        return DataTables::of($assignShift)
            ->addIndexColumn()
            ->addColumn('checkbox', function ($row) {
                if (strtotime($row->date) >= strtotime(date('Y-m-d'))) {
                    return '<input type="checkbox" class="shift-checkbox" value="' . $row->ambulance_shift_id . '">';
                } else {
                    return '';
                }
            })

            ->editColumn('date', function ($row) {
                $row->date = \Carbon\Carbon::createFromFormat('Y-m-d', $row->date)->format('d/m/Y');
                return $row->date;
            })
            ->addColumn('vehicle_no', function ($row) {
                return $row->ambulance_no ? $row->ambulance_no : $row->chassis_no;
            })
            ->rawColumns(['date', 'vehicle_no', 'checkbox'])
            ->make(true);
    }

    public function addShift(Request $request)
    {
        try {
            //dd($request->all());
            $userType = $request->role_name;
            $userId = $request->user_id;
            $ambulanceId = $request->shift_ambulance_id;
            $shiftIds = $request->shift_id;
            $serviceArea = $request->service_area_id;
            $stationArea = $request->station_area;
            $shiftStartDate =
                $request->shift_start_date ? \Carbon\Carbon::createFromFormat('d/m/Y', $request->shift_start_date)->format('Y-m-d') : null;
            $shiftEndDate =
                $request->shift_end_date ? \Carbon\Carbon::createFromFormat('d/m/Y', $request->shift_end_date)->format('Y-m-d') : null;
            DB::beginTransaction();
            //dd($shiftIds);
            foreach ($shiftIds as $shiftId) {
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
                if ($shiftStartDate && $shiftEndDate) {
                    $period = CarbonPeriod::create($shiftStartDate, $shiftEndDate);

                    foreach ($period as $date) {
                        //dd($shiftId);
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

                        $checkUserProfileStatus = ModelsUser::where('id', $userId)->first();
                        if ($checkUserProfileStatus) {
                            if($checkUserProfileStatus->is_active != '1'){
                                DB::rollback();
                                return response()->json([
                                    'status' => 'Fail',
                                    'message' => trans('messages.PROFILE_NOT_ACTIVE'),
                                ]);
                            }

                            if($checkUserProfileStatus->is_verified != '1'){
                                DB::rollback();
                                return response()->json([
                                    'status' => 'Fail',
                                    'message' => trans('messages.PROFILE_NOT_VERIFIED'),
                                ]);
                            }
                            
                        }
                        
                        //dd($checkAmbulanceShift);
                        if ($checkAmbulanceShift) {
                            $shift = ModelsShiftType::where('id', $shiftId)->first();
                            $shiftName = $shift->shift_name;

                            DB::rollback();
                            return response()->json([
                                'status' => 'Fail',
                                'message' => "This shift for this date: " . $date->format('d/m/Y') . " and for the " . $shiftName . " already assigned. Please unassign this shift first."
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
            }
            DB::rollback();
            return response()->json(['status' => 'Fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
        } catch (Exception $e) {
            return response()->json(['status' => 'fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
        }
    }

    public function unassignShiftByShiftId(Request $request)
    {
        // Retrieve the array of IDs from the request
        $shiftIds = $request->input('id', []);

        // Check if there are IDs to process
        if (!empty($shiftIds)) {
            // Use the whereIn method to delete records based on the array of IDs
            $deletedRows = ModelsAmbulanceShift::whereIn('id', $shiftIds)->delete();

            if ($deletedRows > 0) {
                return response()->json(['status' => 'success', 'message' => trans('messages.SHIFT_UNASSIGNED_SUCCESS')]);
            }
        }

        return response()->json(['status' => 'fail', 'message' => trans('messages.SHIFT_NOT_FOUND')]);
    }


    public function getServiceAreaByDistrictId(Request $request)
    {
        $districtId = $request->district_id;
        $serviceArea = ModelsServiceArea::where('district_id', $districtId)->get();
        return response()->json(['serviceArea' => $serviceArea]);
    }
}
