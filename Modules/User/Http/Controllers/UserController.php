<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\User\Entities\Role;
use Modules\User\Entities\Address;
use App\Models\User;
use Modules\User\Entities\SpBrands;
use Modules\User\Entities\RetailerCategories;
use Modules\User\Entities\State;
use Modules\User\Entities\City;
use Modules\User\Entities\District;
use Modules\User\Entities\OrganizationBuyer;
use Modules\User\Entities\OrganizationStaff;
use Modules\User\Entities\RetailerMapping;
use Modules\User\Http\Requests\UserRequest;
use DB;
use Image;
use Auth;
use DataTables;
use Modules\User\Entities\ModelRole;
use Modules\Saas\Entities\Organization;
use Maatwebsite\Excel\HeadingRowImport;
use Modules\User\Imports\BuyerImport;
use Modules\User\Exports\BuyerErrorExport;
use App\Models\Audit;
use Modules\Administration\Entities\NotificationTemplate;
use Helpers;
use App\Jobs\SendNotificationJob;
use App\Models\District as ModelsDistrict;
use App\Models\Notification as ModelsNotification;
use App\Models\State as ModelsState;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Carbon\Carbon;

class UserController extends Controller
{

    public function __construct()
    {

        /* Execute authentication filter before processing any request */
        $this->middleware('auth');

        if (\Auth::check()) {
            return redirect('/');
        }
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        $userPermission = \Session::get('userPermission');

        if (!isset($userPermission[\Config::get('constants.FEATURES.BUYER')]))
            return view('error/403');

        $authUser = \Auth::user();

        $role = $authUser->getRoleNames()->toArray();
        $organizations = array();

        if (!empty($role) && $role[0] != \Config::get('constants.ROLES.OWNER')) {
            $organizations = Helpers::getUserOrganizations($authUser->id);
        }


        $data = User::from('users as u')
            ->select('u.id', 'u.name', 'u.last_name', 'u.shop_name', 'u.email', 'u.file', 'ob.status', 'u.phone_number', 'u.created_at', 'u.updated_at', 'r.name as role', 'r.label as roleName', 'ob.organization_id as obid', 'rc.retailer_catagory as retailer_category', 'ob.credit_limit', 'c.name as city')
            ->leftJoin('model_has_roles as mr', 'mr.model_id', '=', 'u.id')
            ->leftJoin('roles as r', 'mr.role_id', '=', 'r.id')
            ->leftJoin('organization_buyer as ob', 'u.id', '=', 'ob.buyer_id')
            ->leftJoin('cities as c', 'c.id', '=', 'u.city')
            ->leftJoin('retailer_catagory as rc', 'rc.id', '=', 'ob.buyer_category')
            ->where('r.name', \Config::get('constants.ROLES.BUYER'))
            ->when(!empty($organizations), function ($query) use ($organizations) {
                $query->whereIn('ob.organization_id', $organizations);
            }, function ($query) {
                $query->where('ob.organization_id', '!=', 0);
            })
            ->where('u.is_approved', 1)
            ->where(function ($query) use ($request) {
                if (!empty($request->toArray())) {
                    if ($request->get('firstname') != '') {
                        $query->where('u.name', $request->get('firstname'));
                    }
                    if ($request->get('lastname') != '') {
                        $query->where('u.last_name', $request->get('lastname'));
                    }
                    if ($request->get('contact_number') != '') {
                        $query->where('u.phone_number', $request->get('contact_number'));
                    }
                    if ((isset($request->fromDate) && isset($request->toDate))) {
                        $dateFrom =  date('Y-m-d', strtotime($request->fromDate));
                        $dateTo =  date('Y-m-d', strtotime($request->toDate . ' +1 day'));
                        $query->whereBetween('u.created_at', array($dateFrom, $dateTo));
                    } elseif (isset($request->fromDate)) {

                        $dateFrom =  date('Y-m-d', strtotime($request->fromDate));
                        $query->where('u.created_at', '>=', $dateFrom);
                    } elseif (isset($request->toDate)) {
                        $dateTo =  date('Y-m-d', strtotime($request->toDate));
                        $query->where('u.created_at', '<=', $dateTo);
                    }
                }
            })
            ->orderby('u.id', 'desc')
            ->groupBy('buyer_id')
            ->get();
        $buyersCount = 0;
        if (!empty($data->toArray())) {
            $buyersCount = count($data);
        }

        if ($request->ajax()) {
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('name', function ($row) use ($userPermission) {

                    if (isset($userPermission['buyer']) && ($userPermission['buyer']['read_all'])) {
                        $detailLink = url('user/detail/' . $row->id);
                    } else {
                        $detailLink = '#';
                    }

                    $username = $row->name . ' ' . $row->last_name;

                    if (!is_null($row->file)) {
                        $file = public_path('uploads/users/') . $row->file;
                    }

                    if (!is_null($row->file) && file_exists($file))
                        $avatar = "<img src=" . url('uploads/users/' . $row->file) . ">";
                    else
                        $avatar = "<span>" . \Helpers::getAcronym($username) . "</span>";


                    $name = '
                                        <a href="' . $detailLink . '">
                                            <div class="user-card">
                                                <div class="user-avatar bg-primary">
                                                    ' . $avatar . '
                                                </div>
                                                <div class="user-info">
                                                    <span class="tb-lead">' . $row->shop_name . ' <span class="dot dot-success d-md-none ml-1"></span></span>
                                                    <span>' . $username . ' </span>
                                                </div>
                                            </div>
                                        </a>
                                    ';
                    return $name;
                })
                ->addColumn('status', function ($row) {
                    if ($row->status == 1) {
                        $statusValue = 'Active';
                    } else {
                        $statusValue = 'Inactive';
                    }

                    $value = ($row->status == '1') ? 'badge badge-success' : 'badge badge-danger';
                    $status = '
                            <span class="tb-sub">
                                <span class="' . $value . '">
                                    ' . $statusValue . '
                                </span>
                            </span>
                        ';
                    return $status;
                })
                ->addColumn('credit_limit', function ($row) {
                    return '₹' . number_format($row->credit_limit, 2);
                })
                ->addColumn('action', function ($row) use ($userPermission) {
                    $edit = url('/') . '/user/edit/' . $row->id;
                    $delete = url('/') . '/user/delete/' . $row->id;
                    $confirm = '"Are you sure, you want to delete it?"';

                    if (isset($userPermission['buyer']) && ($userPermission['buyer']['edit_all'] || $userPermission['buyer']['edit_own'])) {
                        $editBtn = "<li>
                                            <a href='" . $edit . "'>
                                                <em class='icon ni ni-edit'></em> <span>Edit</span>
                                            </a>
                                        </li>";
                    } else {
                        $editBtn = '';
                    }

                    if (isset($userPermission['buyer']) && ($userPermission['buyer']['delete_all'] || $userPermission['buyer']['delete_own'])) {
                        $deleteBtn = "<li>
                                            <a href='" . $delete . "' onclick='return confirm(" . $confirm . ")'  class='delete'>
                                                <em class='icon ni ni-trash'></em> <span>Delete</span>
                                            </a>
                                        </li>";
                    } else {
                        $deleteBtn = '';
                    }

                    $logbtn = '<li><a href="#" data-resourceId="' . $row->id . '" class="audit_logs"><em class="icon ni ni-list"></em> <span>Audit Logs</span></a></li>';

                    $changePassword = '<li><a href="#" data-resourceId="' . $row->id . '" class="changePassword"><em class="icon ni ni-lock-alt"></em> <span>Update Password</span></a></li>';

                    $btn = '';
                    $btn .= '<ul class="nk-tb-actio ns gx-1">
                                        <li>
                                            <div class="drodown mr-n1">
                                                <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <ul class="link-list-opt no-bdr">
                                        ';

                    $btn .=       $editBtn . "
                                        " . $deleteBtn . "
                                        " . $logbtn . "
                                        " . $changePassword;

                    $btn .= "</ul>
                                            </div>
                                        </div>
                                    </li>
                                    </ul>";
                    return $btn;
                })
                ->addColumn('created_at', function ($row) {
                    return date(\Config::get('constants.DATE.DATE_FORMAT'), strtotime($row->created_at));
                })
                ->addColumn('updated_at', function ($row) {
                    return date(\Config::get('constants.DATE.DATE_FORMAT'), strtotime($row->updated_at));
                })
                ->rawColumns(['action', 'created_at', 'name', 'updated_at', 'status', 'credit_limit'])
                ->make(true);
        }


        $newBuyers = User::from('users as u')
            ->select('u.id')
            ->leftJoin('model_has_roles as mr', 'mr.model_id', '=', 'u.id')
            ->leftJoin('roles as r', 'mr.role_id', '=', 'r.id')
            ->leftJoin('organization_buyer as ob', 'u.id', '=', 'ob.buyer_id')
            ->where('r.name', \Config::get('constants.ROLES.BUYER'))
            ->where('u.is_approved', 0)
            // ->where('ob.organization_id',$authUser->organization_id)
            ->groupBy('u.id')
            ->get();

        $newBuyers = count($newBuyers);

        return view('user::index')->with(compact('buyersCount', 'newBuyers'));
    }


    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function newUsers(Request $request)
    {
        $userPermission = \Session::get('userPermission');

        if (!isset($userPermission[\Config::get('constants.FEATURES.BUYER')]))
            return view('error/403');

        $authUser = \Auth::user();


        $data = User::from('users as u')
            ->select('u.id', 'u.name', 'u.last_name', 'u.shop_name', 'u.email', 'u.file', 'ob.status', 'u.phone_number', 'u.created_at', 'u.updated_at', 'r.name as role', 'r.label as roleName', 'ob.organization_id as obid', 'u.created_by')
            ->leftJoin('model_has_roles as mr', 'mr.model_id', '=', 'u.id')
            ->leftJoin('roles as r', 'mr.role_id', '=', 'r.id')
            ->leftJoin('organization_buyer as ob', 'u.id', '=', 'ob.buyer_id')
            ->where('r.name', \Config::get('constants.ROLES.BUYER'))
            ->where('u.is_approved', 0)
            // ->where('ob.organization_id',$authUser->organization_id)
            ->where(function ($query) use ($request) {
                if (!empty($request->toArray())) {
                    if ($request->get('firstname') != '') {
                        $query->where('u.name', $request->get('firstname'));
                    }
                    if ($request->get('lastname') != '') {
                        $query->where('u.last_name', $request->get('lastname'));
                    }
                    if ($request->get('phone_number') != '') {
                        $query->where('u.phone_number', $request->get('phone_number'));
                    }
                    if ((isset($request->fromDate) && isset($request->toDate))) {
                        $dateFrom =  date('Y-m-d', strtotime($request->fromDate));
                        $dateTo =  date('Y-m-d', strtotime($request->toDate . ' +1 day'));
                        $query->whereBetween('u.created_at', array($dateFrom, $dateTo));
                    } elseif (isset($request->fromDate)) {

                        $dateFrom =  date('Y-m-d', strtotime($request->fromDate));
                        $query->where('u.created_at', '>=', $dateFrom);
                    } elseif (isset($request->toDate)) {
                        $dateTo =  date('Y-m-d', strtotime($request->toDate));
                        $query->where('u.created_at', '<=', $dateTo);
                    }
                }
            })
            ->orderby('u.id', 'desc')
            ->groupBy('u.id')
            ->get();
        $buyersCount = 0;
        if (!empty($data->toArray())) {
            $buyersCount = count($data);
        }

        if ($request->ajax()) {
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('name', function ($row) use ($userPermission) {

                    if (isset($userPermission['buyer']) && ($userPermission['buyer']['read_all'])) {
                        $detailLink = url('user/detail/' . $row->id);
                    } else {
                        $detailLink = '#';
                    }

                    $username = $row->name . ' ' . $row->last_name;

                    if (!is_null($row->file)) {
                        $file = public_path('uploads/users/') . $row->file;
                    }

                    if (!is_null($row->file) && file_exists($file))
                        $avatar = "<img src=" . url('uploads/users/' . $row->file) . ">";
                    else
                        $avatar = "<span>" . \Helpers::getAcronym($username) . "</span>";


                    $name = '
                                        <a href="' . $detailLink . '">
                                            <div class="user-card">
                                                <div class="user-avatar bg-primary">
                                                    ' . $avatar . '
                                                </div>
                                                <div class="user-info">
                                                    <span class="tb-lead">' . $row->shop_name . ' <span class="dot dot-success d-md-none ml-1"></span></span>
                                                    <span>' . $username . ' </span>
                                                </div>
                                            </div>
                                        </a>
                                    ';
                    return $name;
                })
                ->addColumn('status', function ($row) {
                    if ($row->is_approved == 1) {
                        $statusValue = 'Approved';
                    } else {
                        $statusValue = 'Not Approved';
                    }

                    $value = ($row->is_approved == '1') ? 'badge badge-success' : 'badge badge-danger';
                    $status = '
                            <span class="tb-sub">
                                <span class="' . $value . '">
                                    ' . $statusValue . '
                                </span>
                            </span>
                        ';
                    return $status;
                })
                ->addColumn('action', function ($row) use ($userPermission) {
                    $edit = url('/') . '/user/edit/' . $row->id;
                    $delete = url('/') . '/user/delete/' . $row->id;
                    $confirm = '"Are you sure, you want to delete it?"';

                    /*if(isset($userPermission['buyer']) && ($userPermission['buyer']['edit_all'] || $userPermission['buyer']['edit_own'])){
                                $editBtn = "<li class='nk-tb-action-hidden'>
                                            <a href='".$edit."' class='btn btn-trigger btn-icon' data-toggle='tooltip' data-placement='top' title='Edit'>
                                                <em class='icon ni ni-edit'></em>
                                            </a>
                                        </li>";
                            }else{
                                $editBtn = '';
                            }*/
                    $editBtn = '';

                    if (isset($userPermission['buyer']) && ($userPermission['buyer']['delete_all'] || $userPermission['buyer']['delete_own'])) {
                        $deleteBtn = "<li class='nk-tb-action-hidden'>
                                            <a href='" . $delete . "' onclick='return confirm(" . $confirm . ")'  class='btn btn-trigger btn-icon delete' data-toggle='tooltip' data-placement='top' title='Delete'>
                                                <em class='icon ni ni-trash'></em>
                                            </a>
                                        </li>";
                    } else {
                        $deleteBtn = '';
                    }

                    $btn = "<ul class='nk-tb-actions gx-1'>
                                        " . $editBtn . "
                                        " . $deleteBtn . "
                                        <li>
                                            &nbsp;
                                        </li>
                                    </ul>
                                ";
                    return $btn;
                })
                ->addColumn('created_at', function ($row) {
                    return date(\Config::get('constants.DATE.DATE_FORMAT'), strtotime($row->created_at));
                })
                ->addColumn('updated_at', function ($row) {
                    return date(\Config::get('constants.DATE.DATE_FORMAT'), strtotime($row->updated_at));
                })
                ->addColumn('created_by', function ($row) {

                    if ($row->id == $row->created_by) {
                        $created_by = 'Self';
                        $created_by_name = '';
                    } else {
                        $created = User::from('users as u')
                            ->select('u.id', 'u.name', 'u.last_name', 'r.name as role')
                            ->leftJoin('model_has_roles as mr', 'mr.model_id', '=', 'u.id')
                            ->leftJoin('roles as r', 'mr.role_id', '=', 'r.id')
                            ->where('u.id', $row->created_by)
                            ->first();

                        $created_by = $created->role;
                        $created_by_name = $created->name . ' ' . $created->last_name;
                    }


                    $created_by = '
                                        <a href="#">
                                            <div class="user-card">
                                                <div class="user-info">
                                                    <span class="tb-lead">' . $created_by . ' <span class="dot dot-success d-md-none ml-1"></span></span>
                                                    <span>' . $created_by_name . ' </span>
                                                </div>
                                            </div>
                                        </a>
                                    ';
                    return $created_by;
                })
                ->rawColumns(['action', 'created_at', 'name', 'updated_at', 'status', 'created_by'])
                ->make(true);
        }

        return view('user::new_buyer')->with(compact('buyersCount'));
    }
    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function profile()
    {
        $authUser = Auth::user();

        $user =     User::where('users.id', $authUser->id)->first();

        return view('user::profile/index')->with(compact('user'));
    }

    public function profileAddress(Request $request)
    {
        $authUser = Auth::user();
        $user =     User::userDetails($authUser->id ?? null);
        $states             =   ModelsState::all();
        $districts          =   ModelsDistrict::all();
        $cities             =   ModelsDistrict::all();

        return view('user::profile/address')->with(compact('user', 'states', 'districts', 'cities'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function updateProfileAddress(Request $request)
    {

        $user = Auth::user();
        $data = $request->all();
        unset($data['_token']);
        $profileInfo = User::updateUserDetails($user->id, $data);
        if ($profileInfo) {
            return response()->json(['status' => 'success', 'message' => trans('messages.ADDRESS_UPDATED_SUCCESS')]);
        } else {
            return response()->json(['status' => 'Fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function updateProfile(Request $request)
    {
        $data = $request->all();
        unset($data['_token']);
        $user = FacadesAuth::user();
        if ($request->hasFile('profile_path')) {
            $folderName = 'profile_pic';
            $attachmentUrl = Helpers::uploadAttachment($request->file('profile_path'), $folderName, $user->id);
            $data['profile_path'] = $attachmentUrl;
        }
        //print_r($data);die;
        $updateUser = User::updateUserDetails($user->id, $data);
        if ($updateUser) {
            return response()->json(['status' => 'success', 'message' => trans('messages.PROFILE_UPDATED_SUCCESS')]);
        } else {
            return response()->json(['status' => 'Fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
        }
    }


    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function notification()
    {
        return view('user::profile/notification');
    }
    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function notifications()
    {
        $notificationCount = ModelsNotification::where('related_resource_user_id', auth()->id())->count();
        return view('user::notification/index', compact('notificationCount'));
    }

    public function getNotificationList(Request $request)
    {
        $notifications = ModelsNotification::where('related_resource_user_id', auth()->id())->orderBy('created_at', 'desc')->get();
        return DataTables::of($notifications)
            ->addIndexColumn()
            ->editColumn('created_at', function ($row) {
                return date('d/m/Y', strtotime($row->created_at));
            })
            ->addColumn('action', function ($row) {
            })
            ->rawColumns(['created_at', 'action'])
            ->make(true);
    }

    public function notificationMarkedAsRead(Request $request)
    {
        try {
            $userId = auth()->id();
            $currentTimeStamp = Carbon::now();

            // Fetch unread notifications for the authenticated user
            $notifications = ModelsNotification::where('related_resource_user_id', $userId)
                ->whereNull('read_at')
                ->orderByDesc('created_at')
                ->get();

            // Check if there are unread notifications before attempting to update
            if ($notifications->isNotEmpty()) {
                DB::beginTransaction();

                // Update all unread notifications to mark them as read
                $readNotification = $notifications->each(function ($notification) use ($currentTimeStamp) {
                    $notification->update(['read_at' => $currentTimeStamp]);
                });

                DB::commit();

                return response()->json(['status' => 'success', 'message' => trans('messages.NOTIFICATION_MARK_AS_READ_SUCCESS')]);
            }

            // If no unread notifications found, consider it a success
            return response()->json(['status' => 'success', 'message' => trans('messages.NO_UNREAD_NOTIFICATIONS')]);
        } catch (Exception $e) {
            // Handle exceptions and return a failure response
            DB::rollback();
            return response()->json(['status' => 'fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
        }
    }
    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function activity()
    {
        return view('user::profile/activity');
    }
    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function setting()
    {
        $user = Auth::user();
        return view('user::profile/setting')->with(compact('user'));
    }


    public function updateUserPassword(Request $request)
    {

        $user = User::find($request->password_user_id);
        $user->password = \Hash::make($request->newPassword);
        if ($user->save()) {
            return redirect()->back()->with('message', trans('messages.PASSWORD_UPDATED'));
        } else {
            return redirect()->back()->with('error', trans('messages.SOMETHING_WENT_WRONG'));
        }
    }
    public function updatePassword(Request $request)
    {

        $validator = \Validator::make($request->all(), [
            'oldPassword' => [
                'required', function ($attribute, $value, $fail) {
                    if (!\Hash::check($value, Auth::user()->password)) {
                        $fail('Current Password didn\'t match');
                    }
                },
            ],
            'newPassword' => [
                'required', function ($attribute, $value, $fail) use ($request) {
                    if (\Hash::check($value, Auth::user()->password)) {
                        $fail('New password can not be the current password!');
                    }
                },
            ],
            'confirmPassword' => [
                'required', function ($attribute, $value, $fail) use ($request) {
                    if ($value != $request->newPassword) {
                        $fail('New password and Confirm password didn\'t match');
                    }
                },
            ]
        ]);


        if ($validator->fails()) {
            //return redirect()->back()->withInput()->withErrors($validator);
            return response()->json(['status' => 'Fail', 'message' => $validator->errors()->first()]);
        }

        $user = User::find(Auth::user()->id);
        $user->password = \Hash::make($request->newPassword);
        if ($user->save()) {
            // return redirect('profile/setting')->with('message', trans('messages.PASSWORD_UPDATED'));
            return response()->json(['status' => 'success', 'message' => trans('messages.PASSWORD_UPDATED')]);
        } else {
            // return redirect('profile/setting')->with('error', trans('messages.SOMETHING_WENT_WRONG'));
            return response()->json(['status' => 'Fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
        }
    }


    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function staffList(Request $request)
    {
        $userPermission = \Session::get('userPermission');

        if (!isset($userPermission[\Config::get('constants.FEATURES.STAFF')]))
            return view('error/403');

        $authUser = \Auth::user();
        $role = $authUser->getRoleNames()->toArray();
        $organizations = array();

        if (!empty($role) && $role[0] != \Config::get('constants.ROLES.OWNER')) {
            $organizations = Helpers::getUserOrganizations($authUser->id);
        }

        $users = User::from('users as u')
            ->select('u.id', 'u.name', 'u.last_name', 'u.email', 'u.file', 'u.status', 'u.phone_number', 'u.created_at', 'u.updated_at', 'r.name as role', 'r.label as roleName', 'u.created_by')
            ->leftJoin('model_has_roles as mr', 'mr.model_id', '=', 'u.id')
            ->leftJoin('roles as r', 'mr.role_id', '=', 'r.id')
            ->leftJoin('organization_staff as os', 'u.id', '=', 'os.user_id')
            ->whereNotIn('r.name', [\Config::get('constants.ROLES.BUYER'), \Config::get('constants.ROLES.OWNER'), \Config::get('constants.ROLES.SUPERUSER')])
            ->where('u.organization_id', $authUser->organization_id)
            ->when(!empty($organizations), function ($query) use ($organizations) {
                $query->whereIn('os.organization_id', $organizations);
            }, function ($query) {
                $query->where('os.organization_id', '!=', 0);
            })
            ->where('u.id', '!=', $authUser->id)
            ->where(function ($query) use ($request) {
                if (!empty($request->toArray())) {
                    if ($request->get('firstname') != '') {
                        $query->where('u.name', $request->get('firstname'));
                    }
                    if ($request->get('lastname') != '') {
                        $query->where('u.last_name', $request->get('lastname'));
                    }
                    if ($request->get('mobileNumber') != '') {
                        $query->where('u.phone_number', $request->get('mobileNumber'));
                    }
                    if ($request->get('role') != '') {
                        $query->where('r.name', $request->get('role'));
                    }
                    if ((isset($request->fromDate) && isset($request->toDate))) {
                        $dateFrom =  date('Y-m-d', strtotime($request->fromDate));
                        $dateTo =  date('Y-m-d', strtotime($request->toDate . ' +1 day'));
                        $query->whereBetween('u.created_at', array($dateFrom, $dateTo));
                    } elseif (isset($request->fromDate)) {

                        $dateFrom =  date('Y-m-d', strtotime($request->fromDate));
                        $query->where('u.created_at', '>=', $dateFrom);
                    } elseif (isset($request->toDate)) {
                        $dateTo =  date('Y-m-d', strtotime($request->toDate));
                        $query->where('u.created_at', '<=', $dateTo);
                    }
                }
            })
            ->orderby('u.id', 'desc')
            ->get();

        $usersCount = 0;
        if (!empty($users->toArray())) {
            $usersCount = count($users);
        }

        $filterRequests = $request->all();

        $authUser = \Auth::user();
        $roles              =   Role::where('organization_id', $authUser->organization_id)
            ->whereNotIn('name', [\Config::get('constants.ROLES.BUYER'), \Config::get('constants.ROLES.SELLER'), \Config::get('constants.ROLES.SUPERUSER')])
            ->get();
        return view('user::staff/index')->with(compact('usersCount', 'roles', 'users', 'filterRequests'));
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function staffListOld(Request $request)
    {
        $userPermission = \Session::get('userPermission');

        if (!isset($userPermission[\Config::get('constants.FEATURES.STAFF')]))
            return view('error/403');

        $authUser = \Auth::user();
        $role = $authUser->getRoleNames()->toArray();
        $organizations = array();

        if (!empty($role) && $role[0] != \Config::get('constants.ROLES.OWNER')) {
            $organizations = Helpers::getUserOrganizations($authUser->id);
        }

        $data = User::from('users as u')
            ->select('u.id', 'u.name', 'u.last_name', 'u.email', 'u.file', 'u.status', 'u.phone_number', 'u.created_at', 'u.updated_at', 'r.name as role', 'r.label as roleName', 'u.created_by')
            ->leftJoin('model_has_roles as mr', 'mr.model_id', '=', 'u.id')
            ->leftJoin('roles as r', 'mr.role_id', '=', 'r.id')
            ->leftJoin('organization_staff as os', 'u.id', '=', 'os.user_id')
            ->whereNotIn('r.name', [\Config::get('constants.ROLES.BUYER'), \Config::get('constants.ROLES.OWNER'), \Config::get('constants.ROLES.SUPERUSER')])
            ->where('u.organization_id', $authUser->organization_id)
            ->when(!empty($organizations), function ($query) use ($organizations) {
                $query->whereIn('os.organization_id', $organizations);
            }, function ($query) {
                $query->where('os.organization_id', '!=', 0);
            })
            ->where('u.id', '!=', $authUser->id)
            ->where(function ($query) use ($request) {
                if (!empty($request->toArray())) {
                    if ($request->get('firstname') != '') {
                        $query->where('u.name', $request->get('firstname'));
                    }
                    if ($request->get('lastname') != '') {
                        $query->where('u.last_name', $request->get('lastname'));
                    }
                    if ($request->get('phone_number') != '') {
                        $query->where('u.phone_number', $request->get('phone_number'));
                    }
                    if ($request->get('role') != '') {
                        $query->where('r.name', $request->get('role'));
                    }
                    if ((isset($request->fromDate) && isset($request->toDate))) {
                        $dateFrom =  date('Y-m-d', strtotime($request->fromDate));
                        $dateTo =  date('Y-m-d', strtotime($request->toDate . ' +1 day'));
                        $query->whereBetween('u.created_at', array($dateFrom, $dateTo));
                    } elseif (isset($request->fromDate)) {

                        $dateFrom =  date('Y-m-d', strtotime($request->fromDate));
                        $query->where('u.created_at', '>=', $dateFrom);
                    } elseif (isset($request->toDate)) {
                        $dateTo =  date('Y-m-d', strtotime($request->toDate));
                        $query->where('u.created_at', '<=', $dateTo);
                    }
                }
            })
            ->orderby('u.id', 'desc')
            ->get();

        $usersCount = 0;
        if (!empty($data->toArray())) {
            $usersCount = count($data);
        }

        if ($request->ajax()) {
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('name', function ($row) use ($userPermission) {
                    if (isset($userPermission['buyer']) && ($userPermission['buyer']['read_all'])) {
                        $detailLink = url('user/staff/staff-detail/' . $row->id);
                    } else {
                        $detailLink = '#';
                    }
                    $username = $row->name . ' ' . $row->last_name;
                    if (!is_null($row->file)) {
                        $file = public_path('uploads/users/') . $row->file;
                    }

                    if (!is_null($row->file) && file_exists($file)) {
                        $avatar = "<img src=" . url('uploads/users/' . $row->file) . ">";
                    } else {
                        $avatar = "<span>" . \Helpers::getAcronym($username) . "</span>";
                    }


                    $name = '<a href="' . $detailLink . '">
                                            <div class="user-card">
                                                <div class="user-avatar bg-primary">
                                                    ' . $avatar . '
                                                </div>
                                                <div class="user-info">
                                                    <span class="tb-lead">' . $username . ' <span class="dot dot-success d-md-none ml-1"></span></span>
                                                    <span>' . $row->email . ' </span>
                                                </div>
                                            </div>
                                        </a>';
                    return $name;
                })
                ->addColumn('status', function ($row) {
                    if ($row->status == 1) {
                        $statusValue = 'Approved';
                    } else {
                        $statusValue = 'Not Approved';
                    }

                    $value = ($row->status == '1') ? 'badge badge-success' : 'badge badge-danger';
                    $status = '
                            <span class="tb-sub">
                                <span class="' . $value . '">
                                    ' . $statusValue . '
                                </span>
                            </span>
                        ';
                    return $status;
                })
                ->addColumn('action', function ($row) use ($userPermission, $authUser) {
                    $edit = url('/') . '/user/staff/edit-staff/' . $row->id;
                    $delete = url('/') . '/user/staff/delete-staff/' . $row->id;
                    $confirm = '"Are you sure, you want to delete it?"';

                    if (isset($userPermission['staff']) && ($userPermission['staff']['edit_all'] || ($userPermission['staff']['edit_own'] && $row->created_by == $authUser->id))) {
                        $editBtn = "<li>
                                            <a href='" . $edit . "'>
                                                <em class='icon ni ni-edit'></em><span>Edit</span>
                                            </a>
                                        </li>";
                    } else {
                        $editBtn = '';
                    }

                    if (isset($userPermission['staff']) && ($userPermission['staff']['delete_all'] || ($userPermission['staff']['delete_own'] && $row->created_by == $authUser->id))) {
                        $deleteBtn = "<li>
                                            <a href='" . $delete . "' onclick='return confirm(" . $confirm . ")'  class='delete'>
                                                <em class='icon ni ni-trash'></em><span>Delete</span>
                                            </a>
                                        </li>";
                    } else {
                        $deleteBtn = '';
                    }

                    $logbtn = '<li><a href="#" data-resourceId="' . $row->id . '" class="audit_logs"><em class="icon ni ni-list"></em><span>Audit Logs</span></a></li>';

                    $changePassword = '<li><a href="#" data-resourceId="' . $row->id . '" class="changePassword"><em class="icon ni ni-lock-alt"></em><span>Update Password</span></a></li>';


                    $btn = '';
                    $btn .= '<ul class="nk-tb-actio ns gx-1">
                                        <li>
                                            <div class="drodown mr-n1">
                                                <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <ul class="link-list-opt no-bdr">';

                    $btn .=    $editBtn . "
                                        " . $deleteBtn . "
                                        " . $logbtn . "
                                        " . $changePassword;

                    $btn .= "</ul>
                                            </div>
                                        </div>
                                    </li>
                                    </ul>";
                    return $btn;
                })
                ->addColumn('created_at', function ($row) {
                    return date(\Config::get('constants.DATE.DATE_FORMAT'), strtotime($row->created_at));
                })
                ->addColumn('updated_at', function ($row) {
                    return date(\Config::get('constants.DATE.DATE_FORMAT'), strtotime($row->updated_at));
                })
                ->rawColumns(['action', 'created_at', 'name', 'updated_at', 'status'])
                ->make(true);
        }
        $user = \Auth::user();
        $roles              =   Role::where('organization_id', $user->organization_id)
            ->whereNotIn('name', [\Config::get('constants.ROLES.BUYER'), \Config::get('constants.ROLES.SELLER'), \Config::get('constants.ROLES.SUPERUSER')])
            ->get();
        return view('user::staff/old_list')->with(compact('usersCount', 'roles'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {

        $user = \Auth::user();
        $roles              =   Role::where('organization_id', $user->organization_id)->where('name', \Config::get('constants.ROLES.BUYER'))->first();

        $retailerCategories = RetailerCategories::all();
        $states             = State::all();

        $organization_type = \Session::get('organization_type');
        if (isset($organization_type) && $organization_type == 'MULTIPLE') {
            $organizations = Organization::select('id', 'name')->where('parent_id', '!=', 0)->get();
        } else {
            $organizations = array();
        }

        return view('user::create', ['roles' => $roles, 'retailerCategories' => $retailerCategories, 'states' => $states, 'organizations' => $organizations]);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function createStaff()
    {
        $user = \Auth::user();

        $role = $user->getRoleNames()->toArray();

        if (!empty($role) && $role[0] == \Config::get('constants.ROLES.SELLER')) {
            $getRoles = [\Config::get('constants.ROLES.BUYER'), \Config::get('constants.ROLES.OWNER'), \Config::get('constants.ROLES.SUPERUSER'), \Config::get('constants.ROLES.SELLER')];
        } else {
            $getRoles = [\Config::get('constants.ROLES.BUYER'), \Config::get('constants.ROLES.OWNER'), \Config::get('constants.ROLES.SUPERUSER')];
        }

        $roles              =   Role::where('organization_id', $user->organization_id)
            ->whereNotIn('name', $getRoles)
            ->orderby('name', 'ASC')
            ->get();
        $retailerCategories =    RetailerCategories::all();
        $states             =    State::all();
        $brands             =   array();

        $organizations = array();
        $organization_type = \Session::get('organization_type');
        if (isset($organization_type) && $organization_type == 'MULTIPLE') {
            $organizations = Organization::select('id', 'name')->where('parent_id', '!=', 0)->get();
        }

        return view('user::staff/create', ['roles' => $roles, 'retailerCategories' => $retailerCategories, 'states' => $states, 'brands' => $brands, 'organizations' => $organizations]);
    }

    /**
     * @param UserRequest $request
     * @return $this
     */
    public function districts($state_id)
    {
        $districts   =   District::where('state_id', $state_id)->orderby('district_name', 'asc')->get();
        if (!empty($districts->toArray())) {
            return $arrayName = array('districts' => $districts);
        } else {
            return false;
        }
    }

    /**
     * @param UserRequest $request
     * @return $this
     */
    public function cities($district_id)
    {
        $cities   =   City::where('district_id', $district_id)->orderby('name', 'asc')->get();
        if (!empty($cities->toArray())) {
            return $arrayName = array('cities' => $cities);
        } else {
            return false;
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function address($user_id)
    {

        $addresses =    Address::from('addresses as a')
            ->select('a.*', 's.name as state', 'c.name as city', 'd.name as district', 's.id as stateId', 'c.id as cityId', 'd.id as districtId')
            ->leftJoin('states as s', 's.id', '=', 'a.state')
            ->leftJoin('cities as c', 'c.id', '=', 'a.city')
            ->leftJoin('districts as d', 'd.id', '=', 'a.district')
            ->where('user_id', $user_id)
            ->orderby('a.id', 'desc')
            ->get();
        $states =    State::all();
        $user   =    User::findorfail($user_id);
        return view('user::address', ['user_id' => $user_id, 'user' => $user, 'addresses' => $addresses, 'states' => $states]);
    }


    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function addressDetails($address_id)
    {

        $address =    Address::from('addresses as a')
            ->select('a.*', 's.name as state', 'c.name as city', 'd.name as district', 's.id as stateId', 'c.id as cityId', 'd.id as districtId')
            ->leftJoin('states as s', 's.id', '=', 'a.state')
            ->leftJoin('cities as c', 'c.id', '=', 'a.city')
            ->leftJoin('districts as d', 'd.id', '=', 'a.district')
            ->where('a.id', $address_id)
            ->first();

        if (!empty($address->toArray())) {
            return $arrayName = array('address' => $address);
        } else {
            return false;
        }
    }

    public function addressUpdate(Request $request, $user)
    {
        if (isset($request->address_id) && $request->address_id != 0) {
            $address = Address::findorfail($request->address_id);
            $msg = trans('messages.ADDRESS_UPDATED_SUCCESS');
        } else {
            $address = new Address();
            $msg = trans('messages.ADDRESS_ADDED_SUCCESS');
        }

        $address->user_id = $user;
        $address->address_type = $request->type;
        $address->name = $request->addressName;
        $address->address1 = $request->address1;
        $address->address2 = $request->address2;
        $address->country = $request->country;
        $address->state = $request->state;
        $address->district = $request->district;
        $address->city = $request->city;
        $address->pincode = $request->pincode;

        if ($address->save()) {
            return redirect('user/address/' . $user)->with('message', $msg);
        } else {
            return redirect('user/address/' . $user)->with('error', trans('messages.SOMETHING_WENT_WRONG'));
        }
    }

    public function removeAddress(Request $request, $address)
    {
        $address = Address::findorfail($request->address_id);
        $user = $address->user_id;
        if ($address->forceDelete()) {
            return redirect('user/address/' . $user)->with('message', trans('messages.ADDRESS_DELETED_SUCCESS'));
        } else {
            return redirect('user/address/' . $user)->with('error', trans('messages.SOMETHING_WENT_WRONG'));
        }
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(UserRequest $request)
    {
        $authUser = \Auth::user();


        //Check buyer registration limit
        /*$buyer_limit = \Session::get('buyer_limit');
        if($buyer_limit > 0){
            $countBuyers = User::from('users as u')
                ->leftJoin('model_has_roles as mr','mr.model_id','=','u.id')
                ->leftJoin('roles as r','mr.role_id','=','r.id')
                ->leftJoin('organization_buyer as ob','u.id','=','ob.buyer_id')
                ->where('r.name',\Config::get('constants.ROLES.BUYER'))
                ->where('ob.organization_id',$authUser->organization_id)
                ->count();

            if($countBuyers >= $buyer_limit){
                return redirect('user')->with('error', 'Buyer limit exceeds!');
            }
        }*/

        if ($request->exists("userFound") && $request->userFound == 0) {

            DB::beginTransaction();
            $user = new User();

            if ($request->hasFile('file')) {

                $image1 = $request->file('file');
                $image1NameWithExt = $image1->getClientOriginalName();
                list($image1_width, $image1_height) = getimagesize($image1);
                // Get file path
                $originalName = pathinfo($image1NameWithExt, PATHINFO_FILENAME);
                $image1Name = pathinfo($image1NameWithExt, PATHINFO_FILENAME);
                // Remove unwanted characters
                $image1Name = preg_replace("/[^A-Za-z0-9 ]/", '', $image1Name);
                $image1Name = preg_replace("/\s+/", '-', $image1Name);

                // Get the original image extension
                $extension  = $image1->getClientOriginalExtension();
                if ($extension != 'jpg' && $extension != 'jpeg' && $extension != 'png') {
                    return redirect('user')->with('error', trans('messages.INVALID_IMAGE'));
                }
                $image1Name = $image1Name . '_' . time() . '.' . $extension;

                $destinationPath = public_path('uploads/users');
                if ($image1_width > 800) {
                    $image1_canvas = Image::canvas(800, 800);
                    $image1_image = Image::make($image1->getRealPath())->resize(800, 800, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                    $image1_canvas->insert($image1_image, 'center');
                    $image1_canvas->save($destinationPath . '/' . $image1Name, 80);
                } else {
                    $image1->move($destinationPath, $image1Name);
                }
                $image1_file = public_path('uploads/users/' . $image1Name);

                $user->file = $image1Name;
                $user->original_name = $image1NameWithExt;
            }

            /*if($request->input("role") != \Config::get('constants.ROLES.BUYER')){
                $user->organization_id      = $authUser->organization_id;
            }*/

            $user->organization_id      = $authUser->organization_id;
            $user->name                 = $request->exists("firstname") ? $request->input("firstname") : "";
            $user->last_name            = $request->exists("lastname") ? $request->input("lastname") : "";
            $user->email                = $request->exists("email") ? $request->input("email") : "";
            $user->password             = \Hash::make($request->input("password"));
            $user->phone_number         = $request->exists("mobileNumber") ? $request->input("mobileNumber") : "";
            $user->created_by           = $authUser->id;
            $user->shop_name            = $request->exists("shopname") ? $request->input("shopname") : "";
            $user->gst                  = $request->exists("gst") ? $request->input("gst") : "";
            // $user->retailer_category    = $request->exists("category") ? $request->input("category") : "";
            $user->address1             = $request->exists("address1") ? $request->input("address1") : "";
            $user->address2             = $request->exists("address2") ? $request->input("address2") : "";
            $user->country              = $request->exists("country") ? $request->input("country") : "";
            $user->state                = $request->exists("state") ? $request->input("state") : "";
            $user->pincode              = $request->exists("pincode") ? $request->input("pincode") : "";
            $user->district             = $request->exists("district") ? $request->input("district") : "";
            $user->city                 = $request->exists("city") ? $request->input("city") : "";
            $user->is_approved          = 1;
            // $user->credit_limit      = $request->exists("creditLimit") ? $request->input("creditLimit") : "";

            if ($request->exists("approved") && $request->input("approved") == 1) {
                $user->status           = 1;
            } else {
                $user->status           = 0;
            }

            if ($user->save()) {
                $roleLable = ucfirst(str_replace('_', ' ', $request->input("role")));
                $msg = trans('messages.ADDED_SUCCESSFULLY');
                $organization_type = \Session::get('organization_type');
                if (isset($organization_type) && $organization_type == 'MULTIPLE') {
                    if (isset($request->organization) && !empty($request->organization)) {
                        $organizations = $request->organization;
                        $orgRemove = OrganizationBuyer::where('buyer_id', $user->id)->forceDelete();
                        foreach ($organizations as $key => $org) {
                            $assignOrganization = new OrganizationBuyer();
                            $assignOrganization->organization_id = $org;
                            $assignOrganization->buyer_id = $user->id;
                            $assignOrganization->buyer_category = $request->exists("category") ? $request->input("category") : "";
                            $assignOrganization->credit_limit = $request->exists("creditLimit") ? $request->input("creditLimit") : 0;
                            if ($request->exists("approved") && $request->input("approved") == 1) {
                                $assignOrganization->status = 1;
                            }

                            $assignOrganization->save();
                        }
                    }
                } else {
                    $assignOrganization = new OrganizationBuyer();
                    $assignOrganization->organization_id = $authUser->organization_id;
                    $assignOrganization->buyer_category = $request->exists("category") ? $request->input("category") : "";
                    $assignOrganization->credit_limit = $request->exists("creditLimit") ? $request->input("creditLimit") : 0;
                    $assignOrganization->buyer_id = $user->id;
                    if ($request->exists("approved") && $request->input("approved") == 1) {
                        $assignOrganization->status = 1;
                    }
                    $assignOrganization->save();
                }


                //Assign role to the user
                // $user->assignRole($request->input("role"));

                $modelRole = new ModelRole();
                $modelRole->role_id = $request->input("role");
                $modelRole->model_type = 'Modules\User\Entities\User';
                $modelRole->model_id = $user->id;
                $modelRole->save();

                if ($request->exists("billing") && $request->input("billing") == 1) {
                    $billing = new Address();
                    $billing->user_id = $user->id;
                    $billing->address_type = 1;
                    $billing->name = 'Billing Address';
                    $billing->address1 = $request->address1;
                    $billing->address2 = $request->address2;
                    $billing->country = $request->country;
                    $billing->state = $request->state;
                    $billing->district = $request->district;
                    $billing->city = $request->city;
                    $billing->pincode = $request->pincode;
                    $billing->is_active = 1;
                    $billing->created_by = $authUser->id;
                    $billing->save();

                    if (!$billing->id) {
                        DB::rollback();
                        return redirect('user')->with('error', trans('messages.SOMETHING_WENT_WRONG'));
                    }
                }

                if ($request->exists("shipping") && $request->input("shipping") == 1) {
                    $shipping = new Address();
                    $shipping->user_id = $user->id;
                    $shipping->address_type = 2;
                    $shipping->name = 'Shipping Address';
                    $shipping->address1 = $request->address1;
                    $shipping->address2 = $request->address2;
                    $shipping->country = $request->country;
                    $shipping->state = $request->state;
                    $shipping->district = $request->district;
                    $shipping->city = $request->city;
                    $shipping->pincode = $request->pincode;
                    $shipping->is_active = 1;
                    $shipping->created_by = $authUser->id;

                    $shipping->save();

                    if (!$shipping->id) {
                        DB::rollback();
                        return redirect('user')->with('error', trans('messages.SOMETHING_WENT_WRONG'));
                    }
                }

                if ($user->id) {
                    DB::commit();


                    /*----------  Notifications  ----------*/
                    $user   = User::findorfail($user->id);
                    $organization   = Organization::findorfail($authUser->organization_id);

                    $notificationData = NotificationTemplate::where('organization_id', $authUser->organization_id)
                        ->where('event_name', 'notifications.buyer.new_password')
                        ->first();
                    $chanels = $notificationData->via;
                    $shortCodes = json_decode($notificationData->shortcodes, true);

                    $bodies = array();
                    $pushNotificationDetails = array();
                    $codeData = array(
                        'buyer_name' => $request->input("firstname") . ' ' . $request->input("lastname"),
                        'mobile_no' => $request->input("mobileNumber"),
                        'password' => $request->input("password"),
                        'shop_name' => $request->input("shopname"),
                        'organization_name' => $organization->name,
                    );

                    if (isset($notificationData->via)) {
                        if (!empty($notificationData->body)) {
                            foreach ($notificationData->body as $key => $body) {
                                if (in_array($key, $chanels)) {
                                    foreach ($shortCodes as $code => $shortcode) {
                                        $searchKey = '{' . $code . '}';
                                        if (isset($codeData[$code])) {
                                            $replaceWith = $codeData[$code];
                                        } else {
                                            $replaceWith = "";
                                        }
                                        $body = str_replace($searchKey, $replaceWith, $body);
                                        $bodies[$key] = $body;
                                    }
                                }
                            }
                        }
                    }

                    if (in_array('database', $chanels)) {
                        $pushNotificationDetails = [
                            'title' => 'Profitley - Login Details',
                            'body' => $bodies['database'],
                            'user_id' => $user->id,
                            'fcm_token' => $user->fcm_token,
                            'organization_id' => $authUser->organization_id
                        ];
                    }
                    $mailSubject = $notificationData->email_subject;

                    $jobData =  array(
                        'receiver' => $user,
                        'bodies' => $bodies,
                        'channels' => $chanels,
                        'mailSubject' => $mailSubject,
                        'details' => $pushNotificationDetails,
                    );

                    $emailJob = (new SendNotificationJob($jobData))->delay(\Carbon\Carbon::now()->addSeconds(3));
                    dispatch($emailJob);

                    //Helpers::sendNotifications($user,$bodies,$chanels,$mailSubject,$pushNotificationDetails);
                    /*----------  Notifications  ----------*/

                    return redirect('user')->with('message', $msg);
                } else {
                    return redirect('user')->with('error', trans('messages.SOMETHING_WENT_WRONG'));
                }
            } else {
                DB::rollback();
                return redirect('user')->with('error', trans('messages.SOMETHING_WENT_WRONG'));
            }
        } else {

            $user = User::findorfail($request->userFound);

            $assignOrganization = new OrganizationBuyer();
            $assignOrganization->organization_id = $authUser->organization_id;
            $assignOrganization->buyer_id = $user->id;
            $assignOrganization->buyer_category = $request->exists("category") ? $request->input("category") : "";
            $assignOrganization->credit_limit = $request->exists("creditLimit") ? $request->input("creditLimit") : 0;
            if ($request->exists("approved") && $request->input("approved") == 1) {
                $assignOrganization->status = 1;
            }

            if ($assignOrganization->save()) {
                return redirect('user')->with('message', trans('messages.USER_ADDED_SUCCESS'));
            } else {
                return redirect('user')->with('error', trans('messages.SOMETHING_WENT_WRONG'));
            }
        }
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function storeStaff(UserRequest $request)
    {
        $authUser = \Auth::user();


        $staff_limit = \Session::get('staff_limit');
        $seller_limit = \Session::get('seller_limit');

        $roleData = Role::where('id', $request->input("role"))->first();
        if ($roleData->name == \Config::get('constants.ROLES.SELLER')) {
            $countSellers = User::from('users as u')
                ->leftJoin('model_has_roles as mr', 'mr.model_id', '=', 'u.id')
                ->leftJoin('roles as r', 'mr.role_id', '=', 'r.id')
                ->where('r.name', \Config::get('constants.ROLES.SELLER'))
                ->where('u.organization_id', $authUser->organization_id)
                ->count();

            if ($countSellers >= $seller_limit) {
                return redirect('user/staff')->with('error', trans('messages.SELLER_LIMIT_EXCEEDS'));
            }
        }

        if ($roleData->name == \Config::get('constants.ROLES.SP')) {
            $countStaff = User::from('users as u')
                ->leftJoin('model_has_roles as mr', 'mr.model_id', '=', 'u.id')
                ->leftJoin('roles as r', 'mr.role_id', '=', 'r.id')
                ->where('r.name', \Config::get('constants.ROLES.SP'))
                ->where('u.organization_id', $authUser->organization_id)
                ->count();

            if ($countStaff >= $staff_limit) {
                return redirect('user/staff')->with('error', trans('messages.STAFF_LIMIT_EXCEEDS'));
            }
        }



        DB::beginTransaction();
        $user = new User();

        if ($request->hasFile('file')) {

            $image1 = $request->file('file');
            $image1NameWithExt = $image1->getClientOriginalName();
            list($image1_width, $image1_height) = getimagesize($image1);
            // Get file path
            $originalName = pathinfo($image1NameWithExt, PATHINFO_FILENAME);
            $image1Name = pathinfo($image1NameWithExt, PATHINFO_FILENAME);
            // Remove unwanted characters
            $image1Name = preg_replace("/[^A-Za-z0-9 ]/", '', $image1Name);
            $image1Name = preg_replace("/\s+/", '-', $image1Name);

            // Get the original image extension
            $extension  = $image1->getClientOriginalExtension();
            if ($extension != 'jpg' && $extension != 'jpeg' && $extension != 'png') {
                return redirect('user/staff')->with('error', trans('messages.INVALID_IMAGE'));
            }
            $image1Name = $image1Name . '_' . time() . '.' . $extension;

            $destinationPath = public_path('uploads/users');
            if ($image1_width > 800) {
                $image1_canvas = Image::canvas(800, 800);
                $image1_image = Image::make($image1->getRealPath())->resize(800, 800, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
                $image1_canvas->insert($image1_image, 'center');
                $image1_canvas->save($destinationPath . '/' . $image1Name, 80);
            } else {
                $image1->move($destinationPath, $image1Name);
            }
            $image1_file = public_path('uploads/users/' . $image1Name);

            $user->file = $image1Name;
            $user->original_name = $image1NameWithExt;
        }

        /*if($request->input("role") != \Config::get('constants.ROLES.BUYER')){
            $user->organization_id      = $authUser->organization_id;
        }*/

        $user->organization_id      = $authUser->organization_id;
        $user->name                 = $request->exists("firstname") ? $request->input("firstname") : "";
        $user->last_name            = $request->exists("lastname") ? $request->input("lastname") : "";
        $user->email                = $request->exists("email") ? $request->input("email") : "";
        $user->password             = \Hash::make($request->input("password"));
        $user->phone_number         = $request->exists("mobileNumber") ? $request->input("mobileNumber") : "";
        $user->created_by           = $authUser->id;
        $user->address1             = $request->exists("address1") ? $request->input("address1") : "";
        $user->address2             = $request->exists("address2") ? $request->input("address2") : "";
        $user->country              = $request->exists("country") ? $request->input("country") : "";
        $user->state                = $request->exists("state") ? $request->input("state") : "";
        $user->pincode              = $request->exists("pincode") ? $request->input("pincode") : "";
        $user->district             = $request->exists("district") ? $request->input("district") : "";
        $user->city                 = $request->exists("city") ? $request->input("city") : "";
        $user->is_approved          = 1;

        if ($request->exists("approved") && $request->input("approved") == 1) {
            $user->status           = 1;
        } else {
            $user->status           = 0;
        }

        if ($user->save()) {

            $organization_type = \Session::get('organization_type');
            if (isset($organization_type) && $organization_type == 'MULTIPLE') {
                if (isset($request->organization) && !empty($request->organization)) {
                    $organizations = $request->organization;
                    $orgRemove = OrganizationStaff::where('user_id', $user->id)->forceDelete();
                    foreach ($organizations as $key => $org) {
                        $assignOrganization = new OrganizationStaff();
                        $assignOrganization->organization_id = $org;
                        $assignOrganization->user_id = $user->id;
                        $assignOrganization->save();
                    }
                }
            } else {
                $assignOrganization = new OrganizationStaff();
                $assignOrganization->organization_id = $authUser->organization_id;
                $assignOrganization->user_id = $user->id;
                $assignOrganization->save();
            }

            if (isset($request->brands) && !empty($request->brands)) {
                $brandData = array();
                foreach ($request->brands as $key => $brand) {
                    $brandData[] =    array(
                        'sales_person' => $user->id,
                        'brand' => $brand
                    );
                }
                $mapBrands = SpBrands::insert($brandData);
            }


            $roleLable = ucfirst(str_replace('_', ' ', $request->input("role")));
            $msg = trans('messages.ADDED_SUCCESSFULLY');

            //Assign role to the user
            // $user->assignRole($request->input("role"));
            $modelRole = new ModelRole();
            $modelRole->role_id = $request->input("role");
            $modelRole->model_type = 'Modules\User\Entities\User';
            $modelRole->model_id = $user->id;
            $modelRole->save();

            if ($user->id) {
                DB::commit();

                /*----------  Notifications  ----------*/
                $user   = User::findorfail($user->id);
                $organization   = Organization::findorfail($authUser->organization_id);

                $notificationData = NotificationTemplate::where('organization_id', $authUser->organization_id)
                    ->where('event_name', 'notifications.sp.password')
                    ->first();
                $chanels = $notificationData->via;
                $shortCodes = json_decode($notificationData->shortcodes, true);

                $bodies = array();
                $pushNotificationDetails = array();
                $codeData = array(
                    'sp_name' => $request->input("firstname") . ' ' . $request->input("lastname"),
                    'mobile_no' => $request->input("mobileNumber"),
                    'password' => $request->input("password"),
                    'organization_name' => $organization->name,
                );

                if (isset($notificationData->via)) {
                    if (!empty($notificationData->body)) {
                        foreach ($notificationData->body as $key => $body) {
                            if (in_array($key, $chanels)) {
                                foreach ($shortCodes as $code => $shortcode) {
                                    $searchKey = '{' . $code . '}';
                                    if (isset($codeData[$code])) {
                                        $replaceWith = $codeData[$code];
                                    } else {
                                        $replaceWith = "";
                                    }
                                    $body = str_replace($searchKey, $replaceWith, $body);
                                    $bodies[$key] = $body;
                                }
                            }
                        }
                    }
                }

                if (in_array('database', $chanels)) {
                    $pushNotificationDetails = [
                        'title' => 'Profitley - Login Details',
                        'body' => $bodies['database'],
                        'user_id' => $user->id,
                        'fcm_token' => $user->fcm_token,
                        'organization_id' => $authUser->organization_id
                    ];
                }
                $mailSubject = $notificationData->email_subject;

                $jobData =  array(
                    'receiver' => $user,
                    'bodies' => $bodies,
                    'channels' => $chanels,
                    'mailSubject' => $mailSubject,
                    'details' => $pushNotificationDetails,
                );

                $emailJob = (new SendNotificationJob($jobData))->delay(\Carbon\Carbon::now()->addSeconds(3));
                dispatch($emailJob);

                //Helpers::sendNotifications($user,$bodies,$chanels,$mailSubject,$pushNotificationDetails);
                /*----------  Notifications  ----------*/

                return redirect('user/staff')->with('message', $msg);
            } else {
                return redirect('user/staff')->with('error', trans('messages.SOMETHING_WENT_WRONG'));
            }
        } else {
            DB::rollback();
            return redirect('user/staff')->with('error', trans('messages.SOMETHING_WENT_WRONG'));
        }
    }

    public function removeImage($user_id)
    {
        $user = User::where('id', $user_id)->first();
        $user->file = Null;
        $user->original_name = Null;
        if ($user->save()) {
            return $arrayName = array('success' => true);
        } else {
            return $arrayName = array('success' => false);
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        $authUser = \Auth::user();

        $user =     User::from('users as u')
            ->select(
                'u.*',
                'r.name as role',
                'r.label as roleName',
                'ob.buyer_category',
                'ob.credit_limit',
                'ob.status',
                'rc.retailer_catagory as retailer_category',
                's.name as state',
                'c.name as city',
                'd.name as district',
                DB::Raw('CONCAT(u.name," ", u.last_name) AS FullName')
            )
            ->leftJoin('model_has_roles as mr', 'mr.model_id', '=', 'u.id')
            ->leftJoin('roles as r', 'mr.role_id', '=', 'r.id')
            ->leftJoin('organization_buyer as ob', 'ob.buyer_id', '=', 'u.id')
            ->leftJoin('retailer_catagory as rc', 'rc.id', '=', 'ob.buyer_category')
            ->leftJoin('states as s', 's.id', '=', 'u.state')
            ->leftJoin('cities as c', 'c.id', '=', 'u.city')
            ->leftJoin('districts as d', 'd.id', '=', 'u.district')
            ->where('u.id', $id)
            // ->where('ob.organization_id',$authUser->organization_id)
            ->first();

        if ($user) {
            return view('user::detail', ['user' => $user]);
        } else {
            return redirect('user/staff')->with('error', trans('messages.SOMETHING_WENT_WRONG'));
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function showStaff($id)
    {
        $authUser = \Auth::user();

        $user =     User::from('users as u')
            ->select(
                'u.*',
                'r.name as role',
                'r.label as roleName',
                's.name as state',
                'c.name as city',
                'd.name as district',
                DB::Raw('CONCAT(u.name," ", u.last_name) AS FullName')
            )
            ->leftJoin('model_has_roles as mr', 'mr.model_id', '=', 'u.id')
            ->leftJoin('roles as r', 'mr.role_id', '=', 'r.id')
            ->leftJoin('states as s', 's.id', '=', 'u.state')
            ->leftJoin('cities as c', 'c.id', '=', 'u.city')
            ->leftJoin('districts as d', 'd.id', '=', 'u.district')
            ->where('u.id', $id)
            // ->where('u.organization_id',$authUser->organization_id)
            ->first();

        if ($user) {
            return view('user::staff/detail', ['user' => $user]);
        } else {
            return redirect('user')->with('error', trans('messages.SOMETHING_WENT_WRONG'));
        }
    }

    public function checkUser(Request $request)
    {

        if ($request->searchKey == "") {
            return false;
        }

        $user =     User::from('users as u')
            ->select('u.*', 'r.name as role', 'r.label as roleName', 'ob.buyer_category', 'ob.credit_limit', 'ob.status', 'rc.retailer_catagory as retailer_category', 's.name as state', 'c.name as city', 'd.name as district', 's.id as stateId', 'd.id as districtId', 'c.id as cityId')
            ->leftJoin('model_has_roles as mr', 'mr.model_id', '=', 'u.id')
            ->leftJoin('roles as r', 'mr.role_id', '=', 'r.id')
            ->leftJoin('organization_buyer as ob', 'ob.buyer_id', '=', 'u.id')
            ->leftJoin('retailer_catagory as rc', 'rc.id', '=', 'ob.buyer_category')
            ->leftJoin('states as s', 's.id', '=', 'u.state')
            ->leftJoin('cities as c', 'c.id', '=', 'u.city')
            ->leftJoin('districts as d', 'd.id', '=', 'u.district')
            ->where(function ($query) use ($request) {
                if (isset($request->searchKey)) {
                    $query->where('u.email', $request->searchKey);
                    $query->orWhere('u.phone_number', $request->searchKey);
                    $query->orWhere('u.gst', $request->searchKey);
                }
            })
            ->first();

        if (!empty($user)) {
            return $arrayName = array('user' => $user);
        } else {
            return false;
        }
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        try {
            $authUser = \Auth::user();
            $user = User::from('users as u')
                ->select('u.*', 'r.name as role', 'ob.organization_id', 'ob.buyer_category', 'ob.buyer_category', 'ob.status', 'ob.credit_limit')
                ->leftJoin('model_has_roles as mr', 'mr.model_id', '=', 'u.id')
                ->leftJoin('roles as r', 'mr.role_id', '=', 'r.id')
                ->leftJoin('organization_buyer as ob', 'u.id', '=', 'ob.buyer_id')
                ->where('u.id', $id)
                ->first();

            $roles              =   Role::where('organization_id', $authUser->organization_id)->where('name', \Config::get('constants.ROLES.BUYER'))->first();
            $retailerCategories =   RetailerCategories::all();
            $states             =   State::all();
            $districts          =   District::where('state_id', $user->state)->orderby('name', 'asc')->get();
            $cities             =   City::where('district_id', $user->district)->orderby('name', 'asc')->get();

            $buyerOrgs = $organizations = array();
            $organization_type = \Session::get('organization_type');
            if (isset($organization_type) && $organization_type == 'MULTIPLE') {


                $buyerOrgs = OrganizationBuyer::select(\DB::Raw('group_concat(organization_id) as orgs'))->where('buyer_id', $id)->first();
                if ($buyerOrgs->orgs != "") {
                    $buyerOrgs = explode(',', $buyerOrgs->orgs);
                } else {
                    $buyerOrgs = array();
                }

                $organizations = Organization::select('id', 'name')->where('parent_id', '!=', 0)->get();
            }

            return view('user::create', ['roles' => $roles, 'retailerCategories' => $retailerCategories, 'states' => $states, 'user' => $user, 'districts' => $districts, 'cities' => $cities, 'organizations' => $organizations, 'buyerOrgs' => $buyerOrgs]);
        } catch (Exception $e) {
            return redirect('user')->with('error', $e->getMessage());
        }


        return view('user::edit');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function editStaff($id)
    {
        try {
            $authUser = \Auth::user();
            $user = User::from('users as u')
                ->select('u.*', 'r.id as role', 'r.name as roleName', 'r.label as roleLable')
                ->leftJoin('model_has_roles as mr', 'mr.model_id', '=', 'u.id')
                ->leftJoin('roles as r', 'mr.role_id', '=', 'r.id')
                ->where('u.id', $id)
                ->first();

            if ($user->organization_id != $authUser->organization_id) {
                return redirect('user')->with('error', trans('messages.USER_ORGANIZATION'));
            }

            $role = $authUser->getRoleNames()->toArray();

            if (!empty($role) && $role[0] == \Config::get('constants.ROLES.SELLER')) {
                $getRoles = [\Config::get('constants.ROLES.BUYER'), \Config::get('constants.ROLES.OWNER'), \Config::get('constants.ROLES.SUPERUSER'), \Config::get('constants.ROLES.SELLER')];
            } else {
                $getRoles = [\Config::get('constants.ROLES.BUYER'), \Config::get('constants.ROLES.OWNER'), \Config::get('constants.ROLES.SUPERUSER')];
            }

            $roles              =   Role::where('organization_id', $user->organization_id)
                ->whereNotIn('name', $getRoles)
                ->orderby('name', 'ASC')
                ->get();

            $retailerCategories =   RetailerCategories::all();
            $states             =   State::all();
            $districts          =   District::where('state_id', $user->state)->orderby('name', 'asc')->get();
            $cities             =   City::where('district_id', $user->district)->orderby('name', 'asc')->get();
            $brands             =   Brand::select('name', 'id')->where('status', 'active')->get();

            $buyerOrgs = $organizations = array();
            $organization_type = \Session::get('organization_type');
            if (isset($organization_type) && $organization_type == 'MULTIPLE') {


                $buyerOrgs = OrganizationStaff::select(\DB::Raw('group_concat(organization_id) as orgs'))->where('user_id', $id)->first();
                if ($buyerOrgs->orgs != "") {
                    $buyerOrgs = explode(',', $buyerOrgs->orgs);
                } else {
                    $buyerOrgs = array();
                }

                $organizations = Organization::select('id', 'name')->where('parent_id', '!=', 0)->get();
            }

            $mappedBrands = array();

            if ($user->roleName == \Config::get('constants.ROLES.SP')) {
                $brandData = SpBrands::select(\DB::Raw('group_concat(brand) as brands'))
                    ->where('sales_person', $user->id)
                    ->first();
                if ($brandData) {
                    $mappedBrands = $brandData->brands;
                    $mappedBrands = explode(',', $mappedBrands);
                }
            }
            return view('user::staff/create', ['roles' => $roles, 'retailerCategories' => $retailerCategories, 'states' => $states, 'user' => $user, 'districts' => $districts, 'cities' => $cities, 'brands' => $brands, 'mappedBrands' => $mappedBrands, 'organizations' => $organizations, 'buyerOrgs' => $buyerOrgs]);
        } catch (Exception $e) {
            return redirect('user')->with('error', $e->getMessage());
        }


        return view('user::staff/edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(UserRequest $request, $id)
    {

        try {
            $authUser = \Auth::user();
            $user = User::from('users as u')
                ->select('u.*', 'r.name as role')
                ->leftJoin('model_has_roles as mr', 'mr.model_id', '=', 'u.id')
                ->leftJoin('roles as r', 'mr.role_id', '=', 'r.id')
                ->where('u.id', $id)
                ->first();

            $oldUserRole = $user->role;

            // $user->organization_id       = $authUser->organization_id;

            if ($request->input("role") != \Config::get('constants.ROLES.BUYER')) {
                $user->organization_id      = $authUser->organization_id;
            }

            $user->name                 = $request->exists("firstname") ? $request->input("firstname") : "";
            $user->last_name            = $request->exists("lastname") ? $request->input("lastname") : "";
            $user->email                = $request->exists("email") ? $request->input("email") : "";
            $user->phone_number         = $request->exists("mobileNumber") ? $request->input("mobileNumber") : "";
            $user->created_by           = $authUser->id;
            $user->shop_name            = $request->exists("shopname") ? $request->input("shopname") : "";
            $user->gst                  = $request->exists("gst") ? $request->input("gst") : "";
            $user->retailer_category    = $request->exists("category") ? $request->input("category") : "";
            $user->address1             = $request->exists("address1") ? $request->input("address1") : "";
            $user->address2             = $request->exists("address2") ? $request->input("address2") : "";
            $user->country              = $request->exists("country") ? $request->input("country") : "";
            $user->state                = $request->exists("state") ? $request->input("state") : "";
            $user->pincode              = $request->exists("pincode") ? $request->input("pincode") : "";
            $user->district             = $request->exists("district") ? $request->input("district") : "";
            $user->city                 = $request->exists("city") ? $request->input("city") : "";
            $user->credit_limit         = $request->exists("creditLimit") ? !empty($request->input("creditLimit") ? $request->input("creditLimit") : 0) : 0;

            if ($request->hasFile('file')) {

                $image1 = $request->file('file');
                $image1NameWithExt = $image1->getClientOriginalName();
                list($image1_width, $image1_height) = getimagesize($image1);
                // Get file path
                $originalName = pathinfo($image1NameWithExt, PATHINFO_FILENAME);
                $image1Name = pathinfo($image1NameWithExt, PATHINFO_FILENAME);
                // Remove unwanted characters
                $image1Name = preg_replace("/[^A-Za-z0-9 ]/", '', $image1Name);
                $image1Name = preg_replace("/\s+/", '-', $image1Name);

                // Get the original image extension
                $extension  = $image1->getClientOriginalExtension();
                if ($extension != 'jpg' && $extension != 'jpeg' && $extension != 'png') {
                    return redirect('user')->with('error', trans('messages.INVALID_IMAGE'));
                }
                $image1Name = $image1Name . '_' . time() . '.' . $extension;

                $destinationPath = public_path('uploads/users');
                if ($image1_width > 800) {
                    $image1_canvas = Image::canvas(800, 800);
                    $image1_image = Image::make($image1->getRealPath())->resize(800, 800, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                    $image1_canvas->insert($image1_image, 'center');
                    $image1_canvas->save($destinationPath . '/' . $image1Name, 80);
                } else {
                    $image1->move($destinationPath, $image1Name);
                }
                $image1_file = public_path('uploads/users/' . $image1Name);

                $user->file = $image1Name;
                $user->original_name = $image1NameWithExt;
            }

            if ($user->save()) {

                $organization_type = \Session::get('organization_type');
                if (isset($organization_type) && $organization_type == 'MULTIPLE') {
                    if (isset($request->organization) && !empty($request->organization)) {
                        $organizations = $request->organization;
                        $orgRemove = OrganizationBuyer::where('buyer_id', $user->id)->forceDelete();
                        foreach ($organizations as $key => $org) {
                            $assignOrganization = new OrganizationBuyer();
                            $assignOrganization->organization_id = $org;
                            $assignOrganization->buyer_id = $user->id;
                            $assignOrganization->buyer_category = $request->exists("category") ? $request->input("category") : "";
                            $assignOrganization->credit_limit = $request->exists("creditLimit") ? $request->input("creditLimit") : 0;
                            if ($request->exists("approved") && $request->input("approved") == 1) {
                                $assignOrganization->status = 1;
                            } else {
                                $assignOrganization->status = 0;
                            }

                            $assignOrganization->save();
                        }
                    }
                } else {
                    $assignOrganization =   OrganizationBuyer::where('buyer_id', $id)
                        ->first();
                    $assignOrganization->organization_id = $authUser->organization_id;
                    $assignOrganization->buyer_category = $request->exists("category") ? $request->input("category") : "";
                    $assignOrganization->credit_limit = $request->exists("creditLimit") ? $request->input("creditLimit") : 0;

                    $assignOrganization->buyer_id = $user->id;
                    if ($request->exists("approved") && $request->input("approved") == 1) {
                        $assignOrganization->status = 1;
                    } else {
                        $assignOrganization->status = 0;
                    }
                    $assignOrganization->save();
                }
                //Remove role of the user
                if ($request->input("role") != $user->role) {

                    //Add user to the organization
                    /*$assignOrganization =   OrganizationBuyer::where('organization_id',$authUser->organization_id)
                                            ->where('buyer_id',$id)
                                            ->first();
                    $assignOrganization->buyer_category = $request->exists("category") ? $request->input("category") : "";
                    $assignOrganization->credit_limit = $request->exists("creditLimit") ? $request->input("creditLimit") : 0;
                    if($request->exists("approved") && $request->input("approved") == 1){
                        $assignOrganization->status = 1;
                    }else{
                        $assignOrganization->status = 0;
                    }

                    $assignOrganization->save();*/

                    $user->removeRole($user->role);
                    //Assign role to the user
                    $user->assignRole($request->input("role"));
                }
                $roleLable = ucfirst(str_replace('_', ' ', $request->input("role")));

                return redirect('user')->with('message', trans('messages.UPDATED_SUCCESSFULLY'));
            } else {
                return redirect('user')->with('error', trans('messages.SOMETHING_WENT_WRONG'));
            }
        } catch (Exception $e) {
            return redirect('user')->with('error', $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function updateStaff(UserRequest $request, $id)
    {

        try {

            $authUser = \Auth::user();
            $user = User::from('users as u')
                ->select('u.*', 'r.id as role')
                ->leftJoin('model_has_roles as mr', 'mr.model_id', '=', 'u.id')
                ->leftJoin('roles as r', 'mr.role_id', '=', 'r.id')
                ->where('u.id', $id)
                ->first();

            $oldUserRole = $user->role;
            $user->organization_id      = $authUser->organization_id;

            $user->name                 = $request->exists("firstname") ? $request->input("firstname") : "";
            $user->last_name            = $request->exists("lastname") ? $request->input("lastname") : "";
            $user->email                = $request->exists("email") ? $request->input("email") : "";
            $user->phone_number         = $request->exists("mobileNumber") ? $request->input("mobileNumber") : "";
            $user->created_by           = $authUser->id;
            $user->shop_name            = $request->exists("shopname") ? $request->input("shopname") : "";
            $user->gst                  = $request->exists("gst") ? $request->input("gst") : "";
            // $user->retailer_category    = $request->exists("category") ? $request->input("category") : "";
            $user->address1             = $request->exists("address1") ? $request->input("address1") : "";
            $user->address2             = $request->exists("address2") ? $request->input("address2") : "";
            $user->country              = $request->exists("country") ? $request->input("country") : "";
            $user->state                = $request->exists("state") ? $request->input("state") : "";
            $user->pincode              = $request->exists("pincode") ? $request->input("pincode") : "";
            $user->district             = $request->exists("district") ? $request->input("district") : "";
            $user->city                 = $request->exists("city") ? $request->input("city") : "";
            // $user->credit_limit         = $request->exists("creditLimit") ? $request->input("creditLimit") : "";

            if ($request->exists("approved") && $request->input("approved") == 1) {
                $user->status           = 1;
            } else {
                $user->status           = 0;
            }

            if ($request->hasFile('file')) {

                $image1 = $request->file('file');
                $image1NameWithExt = $image1->getClientOriginalName();
                list($image1_width, $image1_height) = getimagesize($image1);
                // Get file path
                $originalName = pathinfo($image1NameWithExt, PATHINFO_FILENAME);
                $image1Name = pathinfo($image1NameWithExt, PATHINFO_FILENAME);
                // Remove unwanted characters
                $image1Name = preg_replace("/[^A-Za-z0-9 ]/", '', $image1Name);
                $image1Name = preg_replace("/\s+/", '-', $image1Name);

                // Get the original image extension
                $extension  = $image1->getClientOriginalExtension();
                if ($extension != 'jpg' && $extension != 'jpeg' && $extension != 'png') {
                    return redirect('user/staff')->with('error', trans('messages.INVALID_IMAGE'));
                }
                $image1Name = $image1Name . '_' . time() . '.' . $extension;

                $destinationPath = public_path('uploads/users');
                if ($image1_width > 800) {
                    $image1_canvas = Image::canvas(800, 800);
                    $image1_image = Image::make($image1->getRealPath())->resize(800, 800, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                    $image1_canvas->insert($image1_image, 'center');
                    $image1_canvas->save($destinationPath . '/' . $image1Name, 80);
                } else {
                    $image1->move($destinationPath, $image1Name);
                }
                $image1_file = public_path('uploads/users/' . $image1Name);

                $user->file = $image1Name;
                $user->original_name = $image1NameWithExt;
            }

            if ($user->save()) {
                $organization_type = \Session::get('organization_type');
                if (isset($organization_type) && $organization_type == 'MULTIPLE') {
                    if (isset($request->organization) && !empty($request->organization)) {
                        $organizations = $request->organization;
                        $orgRemove = OrganizationStaff::where('user_id', $user->id)->forceDelete();
                        foreach ($organizations as $key => $org) {
                            $assignOrganization = new OrganizationStaff();
                            $assignOrganization->organization_id = $org;
                            $assignOrganization->user_id = $user->id;
                            $assignOrganization->save();
                        }
                    }
                }

                if (isset($request->brands) && !empty($request->brands)) {
                    $brandData = array();
                    foreach ($request->brands as $key => $brand) {
                        $brandData[] =    array(
                            'sales_person' => $user->id,
                            'brand' => $brand
                        );
                    }

                    $brandRemove = SpBrands::where('sales_person', $user->id)->forceDelete();
                    $mapBrands = SpBrands::insert($brandData);
                }

                //Remove role of the user
                if ($request->input("role") != $user->role) {
                    // $user->removeRole($user->role);

                    //Assign role to the user
                    // $user->assignRole($request->input("role"));


                    DB::statement("UPDATE model_has_roles SET role_id = " . $request->input("role") . " where model_id = " . $user->id);


                    /*$modelRole = ModelRole::where('model_id',$user->id)->first();
                    $modelRole->role_id = $request->input("role");
                    $modelRole->save();*/
                }
                $roleLable = ucfirst(str_replace('_', ' ', $request->input("role")));
                return redirect('user/staff')->with('message', trans('messages.UPDATED_SUCCESSFULLY'));
            } else {
                return redirect('user/staff')->with('error', trans('messages.SOMETHING_WENT_WRONG'));
            }
        } catch (Exception $e) {
            return redirect('user')->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {

        $orders_count = 0; // \DB::select("select count(*) as total_orders from ecommerce_orders where user_id = '".$id."'");

        if ($orders_count[0]->total_orders > 0) {
            return redirect('user')->with('message', trans('messages.BUYER_DELETE_ORDER_ERROR', ['order_count' => $orders_count[0]->total_orders]));
        } else {
            $visitsCount = \DB::select("select count(*) as total_visits from visits where buyer = '" . $id . "'");
            if ($visitsCount[0]->total_visits > 0) {
                return redirect('user')->with('message', trans('messages.BUYER_DELETE_VISIT_ERROR', ['visit_count' => $visitsCount[0]->total_visits]));
            }
        }

        $user = User::findorfail($id);
        if ($user->forceDelete()) {
            $mapping = RetailerMapping::where('retailer_id', $id)->forceDelete();
            $organizationBuyer = OrganizationBuyer::where('buyer_id', $id)->forceDelete();
            return redirect('user')->with('message', trans('messages.BUYER_DELETED_SUCCESS'));
        } else {
            return redirect('user')->with('error', trans('messages.SOMETHING_WENT_WRONG'));
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroyStaff($id)
    {

        $orders_count = \DB::select("select count(*) as total_orders from ecommerce_orders where created_by = '" . $id . "'");

        if ($orders_count[0]->total_orders > 0) {
            return redirect('user')->with('message', trans('messages.USER_DELETE_ORDER_ERROR', ['order_count' => $orders_count[0]->total_orders]));
        } else {
            $visitsCount = \DB::select("select count(*) as total_visits from visits where dsp = '" . $id . "'");
            if ($visitsCount[0]->total_visits > 0) {
                return redirect('user')->with('message', trans('messages.USER_DELETE_VISIT_ERROR', ['visit_count' => $visitsCount[0]->total_visits]));
            }
        }


        $user = User::findorfail($id);
        if ($user->forceDelete()) {
            $organizationStaff = OrganizationStaff::where('user_id', $id)->forceDelete();
            return redirect('user/staff')->with('message', trans('messages.USER_DELETED_SUCCESS'));
        } else {
            return redirect('user/staff')->with('error', trans('messages.SOMETHING_WENT_WRONG'));
        }
    }
    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function bulkUpdate(Request $request)
    {
        $authUser = \Auth::user();
        $update = OrganizationBuyer::where('organization_id', $authUser->organization_id)->whereIn('buyer_id', $request->ids)->update(['status' => $request->status]);
        if ($update) {

            return array('success' => true, 'item' => array(), 'msg' => 'true');
        } else {
            return array('success' => false, 'item' => array(), 'msg' => trans('messages.NO_UPDATE_REQUIRED'));
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function bulkApprove(Request $request)
    {
        $authUser = \Auth::user();
        $update = User::whereIn('id', $request->ids)->update(['is_approved' => $request->status, 'approved_at' => date('Y-m-d')]);
        if ($update) {

            $newBuyers = User::from('users as u')
                ->leftJoin('model_has_roles as mr', 'mr.model_id', '=', 'u.id')
                ->leftJoin('roles as r', 'mr.role_id', '=', 'r.id')
                ->leftJoin('organization_buyer as ob', 'u.id', '=', 'ob.buyer_id')
                ->where('r.name', \Config::get('constants.ROLES.BUYER'))
                ->where('u.is_approved', 0)
                ->groupBy('u.id')
                ->count();

            return array('success' => true, 'item' => array(), 'newBuyers' => $newBuyers, 'msg' => 'true');
        } else {
            return array('success' => false, 'item' => array(), 'msg' => 'No update required');
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function staffBulkUpdate(Request $request)
    {
        $authUser = \Auth::user();
        $update = User::where('organization_id', $authUser->organization_id)->whereIn('id', $request->ids)->update(['status' => $request->status]);
        if ($update) {
            return array('success' => true, 'item' => array(), 'msg' => 'true');
        } else {
            return array('success' => false, 'item' => array(), 'msg' => 'No update required');
        }
    }


    public function getBrandByOrganization(Request $request)
    {
        $organizations = explode(',', $request->organizations);

        $organization_type = \Session::get('organization_type');
        if (isset($organization_type) && $organization_type == 'MULTIPLE') {
            $brands = Brand::select('name', 'id')->where('status', 'active')->whereIn('organization_id', $organizations)->get();
        } else {
            $brands = Brand::select('name', 'id')->where('status', 'active')->get();
        }


        if (!empty($brands->toArray())) {
            return array('brands' => $brands, 'success' => true);
        } else {
            return array('success' => false, 'brands' => array());
        }
    }

    public function setOrganization(Request $request)
    {
        $currentOrganization = \Session::get('currentOrganization');
        $currentOrganizationName = \Session::get('currentOrganizationName');

        $newOrgName = $request->org_name;
        $newOrgId = $request->org_id;

        session()->put('currentOrganization', $newOrgId);
        session()->put('currentOrganizationName', $newOrgName);
        if ($newOrgId != $currentOrganization) {
            return array('success' => true);
        } else {
            return array('success' => false);
        }
    }

    public function import(Request $request)
    {
        return view('user::import');
    }

    public function importBuyer(Request $request)
    {
        try {

            $user = Auth::user();
            $organization_id = $user->organization_id;
            if ($request->hasFile('importFile')) {
                $extension = request()->importFile->getClientOriginalExtension();

                $allowedExtension = \Config::get('constants.allowedImportExtensions');
                if (!in_array($extension, $allowedExtension)) {
                    $allowedExtension = implode(',', $allowedExtension);
                    return redirect('user/import')->with('error', trans('messages.INVALID_FILE_EXTENTION', ['allowedExtension' => $allowedExtension]));
                }

                $fileName = $organization_id . '-' . time() . '-' . date('m-d-Y') . '.' . request()->importFile->getClientOriginalExtension();
                request()->importFile->move(public_path('uploads/buyers/imports/'), $fileName);

                $file = public_path('uploads/buyers/imports/') . $fileName;

                $requiredHeaders = array('first_name', 'last_name', 'email', 'mobile', 'shop_name', 'address', 'state', 'district', 'city', 'pincode', 'category', 'credit_limit');

                $headings = (new HeadingRowImport)->toArray($file);
                $headings = $headings[0][0];

                if ($requiredHeaders != $headings) {
                    $errorColumns   =   array_diff($headings, $requiredHeaders);

                    $columnName = "";
                    if (!empty($errorColumns)) {
                        foreach ($errorColumns as $key => $column) {
                            $columnName .= $column . ", ";
                        }
                    } else {
                        foreach ($headings as $key => $column) {
                            if ($column == "") {
                                $columnName .= "#, ";
                            } else {
                                $columnName .= $column . ", ";
                            }
                        }
                    }

                    $failureMessage = trans('messages.IMPORT_UNKNOWN_COLUMNS', ['columnName' => $columnName]);
                    return redirect('user/import')->with('error', $failureMessage);
                }

                $import = new BuyerImport($organization_id, $request);

                if ($extension == 'xlsx') {
                    $import->import($file, null, \Maatwebsite\Excel\Excel::XLSX);
                } elseif ($extension == 'csv') {
                    $import->import($file, null, \Maatwebsite\Excel\Excel::CSV);
                } elseif ($extension == 'xls') {
                    $import->import($file, null, \Maatwebsite\Excel\Excel::XLS);
                } else {
                    return redirect('user/import')->with('error', trans('messages.UNKNOWN_FILE_TYPE'));
                }
                $importResult = $import->data;

                if (!empty($importResult) && file_exists($file)) {
                    unlink($file);
                }


                if (!empty($importResult['errors'])) {
                    $tempArr = array_unique(array_column($importResult['errors'], 'row'));
                    $errorsCount = count(array_values(array_intersect_key($importResult['errors'], $tempArr)));
                } else {
                    $errorsCount = 0;
                }

                if (!empty($importResult['success'])) {
                    if (!empty($importResult['errors'])) {
                        $message        =  trans('messages.BUYER_IMPORT_SUCCESS_ERROR', ['successCount' => count($importResult['success']), 'errorsCount' => $errorsCount]);
                        return (new BuyerErrorExport($importResult['errors']))->download('buyer_errors' . '.csv', \Maatwebsite\Excel\Excel::XLSX);
                        return redirect('user/import')->with('message', $message);
                    } else {

                        $message        =  trans('messages.BUYER_IMPORT_SUCCESS', ['successCount' => count($importResult['success'])]);

                        return redirect('user/import')->with('message', $message);
                    }
                    $data['errors']             = $importResult['errors'];
                    return $this->sendSuccessResponse($data);
                } else {
                    $message        =  trans('messages.BUYER_IMPORT_SUCCESS_ERROR', ['successCount' => count($importResult['success']), 'errorsCount' => $errorsCount]);

                    return (new BuyerErrorExport($importResult['errors']))->download('buyer_errors' . '.csv', \Maatwebsite\Excel\Excel::XLSX);
                    return redirect('user/import')->with('message', $message);
                }
            }
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            return $this->sendFailureResponse($failures);
        }
    }

    public function logs($user_id)
    {
        $logs = Audit::from('audits as a')
            ->select(
                'a.*',
                DB::Raw('CONCAT(u.name," ", COALESCE(u.last_name,"")) AS username'),
            )
            ->join('users as u', 'u.id', '=', 'a.user_id')
            ->where('auditable_type', 'Modules\User\Entities\User')
            ->where('auditable_id', $user_id)
            ->orderby('a.id', 'desc')
            ->get();
        $logData = array();

        if (!empty($logs->toArray())) {
            foreach ($logs as $key => $log) {

                $newdata = json_decode($log->new_values);
                $olddata = json_decode($log->old_values);
                if ($log->event == 'created') {
                    $detail = 'User created';

                    $logData[] =    '<li class="timeline-item">
                            <div class="timeline-status bg-primary is-outline"></div>
                            <div class="timeline-date logDate">' . date(\Config::get('constants.DATE.DATE_FORMAT'), strtotime($log->created_at)) . ' <em class="icon ni ni-alarm-alt"></em></div>
                            <div class="timeline-data">
                                <div class="timeline-des">
                                    <p class="logText">' . $detail . '</p>
                                    <span class="text-muted fs-10px">by <span class="logBy">' . $log->username . '</span> at <span class="logTime">' . date(\Config::get('constants.DATE.TIME_FORMAT'), strtotime($log->created_at)) . '</span></span>
                                </div>
                            </div>
                        </li>';
                } elseif ($log->event == 'updated') {
                    $fields = array();
                    foreach ($newdata as $key => $value) {
                        if ($key == 'retailer_catagory') {
                            $key = "Buyer Category";
                        }
                        $fields[] = str_replace('_', ' ', $key);
                    }

                    $detail = 'User data updated. Updated fields are : ' . implode(', ', $fields);

                    $logData[] =    '<li class="timeline-item">
                        <div class="timeline-status bg-primary is-outline"></div>
                        <div class="timeline-date logDate">' . date(\Config::get('constants.DATE.DATE_FORMAT'), strtotime($log->created_at)) . ' <em class="icon ni ni-alarm-alt"></em></div>
                        <div class="timeline-data">
                            <div class="timeline-des">
                                <p class="logText">' . $detail . '</p>
                                <span class="text-muted fs-10px">by <span class="logBy">' . $log->username . '</span> at <span class="logTime">' . date(\Config::get('constants.DATE.TIME_FORMAT'), strtotime($log->created_at)) . '</span></span>
                            </div>
                        </div>
                    </li>';
                }
            }
            if (!empty($logData)) {
                return array('success' => true, 'logs' => $logData, 'msg' => 'true');
            } else {
                return array('success' => false, 'logs' => array(), 'msg' => trans('messages.NO_LOGS_FOUND'));
            }
        } else {
            return array('success' => false, 'logs' => array(), 'msg' => trans('messages.NO_LOGS_FOUND'));
        }
    }
}
