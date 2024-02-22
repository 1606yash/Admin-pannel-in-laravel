<?php

namespace Modules\Task\Http\Controllers;

use App\Models\Notification;
use App\Models\Task;
use App\Models\TaskAttachment;
use App\Models\User;
use App\Models\Role;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Yajra\Datatables\Datatables;
use Helpers;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        $taskData = Task::select(
            'tasks.id',
            'tasks.title',
            'u2.role_id',
            \DB::raw('COALESCE(CONCAT(users.first_name, " ", users.last_name)) as task_created_by'),
            \DB::raw('COALESCE(roles.role_name) as task_created_user_role'),
            \DB::raw('COALESCE(districts.district_name) as task_created_user_district'),
            \DB::raw('COALESCE(DATE_FORMAT(tasks.created_at, "%d/%m/%Y")) as task_created_date'),
            \DB::raw('COALESCE(CONCAT(u2.first_name, " ", u2.last_name)) as task_assigned_to'),
            \DB::raw('COALESCE(r2.role_name) as task_assigned_user_role'),
            \DB::raw('COALESCE(tasks.priority) as priority'),
            \DB::raw('COALESCE(tasks.status) as status')
        )
            ->leftJoin('users', 'tasks.created_by', '=', 'users.id')
            ->leftJoin('users as u2', 'tasks.assigned_to', '=', 'u2.id')
            ->leftJoin('districts', 'users.district_id', '=', 'districts.id')
            ->leftJoin('roles', 'users.role_id', '=', 'roles.id')
            ->leftJoin('roles as r2', 'u2.role_id', '=', 'r2.id');

        $taskData = $taskData->where(function ($query) use ($request) {
            if (!empty($request->toArray())) {
                if (isset($request->taskTitle) && !empty($request->taskTitle)) {
                    $query->where('title', 'LIKE', '%' . $request->taskTitle . '%');
                }
                if (isset($request->role_id) && !empty($request->role_id)) {
                    $query->where('u2.role_id', $request->role_id);
                }
                if (isset($request->priority) && !empty($request->priority)) {
                    $query->where('priority', 'LIKE', '%' . $request->priority . '%');
                }
                if (isset($request->status) && !empty($request->status)) {
                    $query->where('status', $request->status);
                }
                if (isset($request->created_byuser_id) && !empty($request->created_byuser_id)) {
                    $query->where('tasks.created_by', $request->created_byuser_id);
                }
                if (!empty($request->fromDate) && !empty($request->toDate)) {
                    $query->whereRaw("tasks.created_at between '" . date('Y-m-d 00:00:00', strtotime($request->fromDate)) . "' AND '" . date('Y-m-d 23:59:59', strtotime($request->toDate)) . "'");
                }
                //  Add more conditions as needed
            }
        });
        $taskData = $taskData->orderBy('tasks.id', 'desc');

        //dd($taskData->toSql());
        $taskData = $taskData->get();


        $count['assignedTask'] = $taskData->where('status', 'Assigned')->count();
        $count['unassignedTask'] = $taskData->where('status', 'Created')->count();
        $count['cancelledTask'] = $taskData->where('status', 'Cancelled')->count();
        $count['inProgressTask'] = $taskData->where('status', 'In Progress')->count();
        $count['completedTask'] = $taskData->where('status', 'Completed')->count();
        $count['totalTask'] = $taskData->count();



        if ($request->ajax()) {
            return Datatables::of($taskData)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '';
                    $btn .= "<ul class='nk-tb-actions gx-1'><li><div class='drodown mr-n1'><a href='#' class='dropdown-toggle btn btn-icon btn-trigger' data-toggle='dropdown'><em class='icon ni ni-more-h'></em></a><div class='dropdown-menu dropdown-menu-right'><ul class='link-list-opt no-bdr'>";
                    if (true) {
                        $btn .= "<li>
                                            <a href='task/get-task-details/" . $row->id . "/' data-id='" . $row->id . "' class='nav-link'>
                                                <em class='icon ni ni-eye'></em> <span>View Task Details</span>
                                            </a>
                                        </li>";
                    }
                    $btn .= "</ul></div></div></li></ul>";
                    return $btn;
                })
                ->editColumn(
                    'status',
                    function ($row) {
                        if ($row->status == 'Created') {
                            return '<div class="progress rounded-pill " style="height: 30px; margin-bottom: 10px;">
                                        <div class="progress-bar bg-secondary rounded-pill" role="progressbar" style="width: 100%; color: white;">Unassigned</div>
                                    </div>';
                        } elseif ($row->status == 'In Progress') {
                            return '<div class="progress rounded-pill " style="height: 30px; margin-bottom: 10px;">
                                        <div class="progress-bar bg-warning rounded-pill" role="progressbar" style="width: 100%; color: white;">In Progress</div>
                                    </div>';
                        } elseif ($row->status == 'Completed') {
                            return '<div class="progress rounded-pill " style="height: 30px; margin-bottom: 10px;">
                                        <div class="progress-bar bg-success rounded-pill" role="progressbar" style="width: 100%; color: white;">Completed</div>
                                    </div>';
                        } elseif ($row->status == 'Cancelled') {
                            return '<div class="progress rounded-pill " style="height: 30px; margin-bottom: 10px;">
                                        <div class="progress-bar bg-danger rounded-pill" role="progressbar" style="width: 100%; color: white;">Cancelled</div>
                                    </div>';
                        } else {
                            return '<div class="progress rounded-pill " style="height: 30px; margin-bottom: 10px;">
                                        <div class="progress-bar bg-primary rounded-pill" role="progressbar" style="width: 100%; color: white;">Assigned</div>
                                    </div>';
                        }
                    }
                )
                ->editColumn(
                    'priority',
                    function ($row) {
                        // if ($row->priority == 'High') {
                        //     return '<span class="badge badge-sm badge-dot badge-circle"></span>';
                        // }
                        return ucwords($row->priority);
                    }
                )
                ->rawColumns(['action', 'priority', 'status'])
                ->make(true);
        }
        return view('task::index', ['count' => $count]);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $users = User::getAllUsers();
        return view('task::createTask', ['users' => $users]);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        try {
            $validator = \Validator::make($request->all(), [
                'title' => 'required',
                'description' => 'required',
                'priority' => 'required',
                'assigned_to' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 'Fail', 'message' => $validator->errors()->first()]);
            }
            DB::beginTransaction();
            if ($request->has("id") && $request->input("id") != '0' && $request->input("id") != '') {
                $task = Task::find($request->input("id"));
                $task->title = $request->input("title");
                $task->description = $request->input("description");
                $task->priority = $request->input("priority");
                $task->assigned_to = $request->input("assigned_to");
                $task->updated_by = auth()->id();
                $task->save();
            } else {
                $task = new Task();
                $task->title = $request->input("title");
                $task->description = $request->input("description");
                $task->priority = $request->input("priority");
                $task->assigned_to = $request->input("assigned_to");
                if (!empty($request->input("assigned_to"))) {
                    $task->status = 'Assigned';
                }
                $task->created_by = auth()->id();
                $task->save();
            }

            if ($request->hasFile('attach_file')) {
                $folderName = 'Task';
                foreach ($request->file('attach_file') as $file) {
                    $attachmentUrl = Helpers::uploadAttachment($file, $folderName, $task->id);

                    // Create a new attachment record in the database
                    $attachment = new TaskAttachment([
                        'attachment_path' => $attachmentUrl,
                        'task_id' => $task->id,
                        'file_name' => $file->getClientOriginalName()
                    ]);

                    // Associate the attachment with the task
                    $attachment->save();
                }
            }

            if ($task) {
                if ($request->has("id") && $request->input("id") != '0' && $request->input("id") != '') {
                    DB::Commit();
                    return response()->json(['status' => 'success', 'message' => trans('messages.TASK_UPDATED')]);
                } else {
                    $notificationData = [];
                    $notificationData['related_resource_id'] = $task->id ?? null;
                    $notificationData['related_resource_user_id'] = $request->input("assigned_to") ?? null;
                    $notificationData['related_resource_type'] = 'task/get-task-details/' . $task->id;
                    $notificationData['notification_title'] = 'New Task Assignment';
                    $notificationData['notification_description'] = 'New Task has been assigned to you.';
                    $notificationData['created_by'] = auth()->id() ?? null;
                    $notification = Notification::create($notificationData);
                    if($notification){
                        DB::Commit();
                        return response()->json(['status' => 'success', 'message' => trans('messages.TASK_ADDED')]);
                    }
                    DB::rollback();
                    return response()->json(['status' => 'Fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
                }
            } else {
                DB::rollback();
                return response()->json(['status' => 'Fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 'Fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
        }
    }


    public function viewTask(Request $request)
    {
        $taskId = $request->id;
        $taskDetails = Task::select(
            'tasks.id',
            'tasks.title',
            'tasks.attached_file',
            'tasks.description',
            'tasks.remark',
            'tasks.updated_by',
            'tasks.assigned_to',
            'u2.role_id',
            'roles.role_name',
            //'task_attachments.attachment_path',
            \DB::raw('COALESCE(roles.role_name) as task_assigned_user_role'),
            \DB::raw('COALESCE(CONCAT(users.first_name, " ", users.last_name)) as task_created_by'),
            \DB::raw('COALESCE(DATE_FORMAT(tasks.updated_at, "%d-%m-%y")) as task_updated_date'),
            \DB::raw('COALESCE(DATE_FORMAT(tasks.created_at, "%d-%m-%y")) as task_created_date'),
            \DB::raw('COALESCE(CONCAT(u2.first_name, " ", u2.last_name)) as task_assigned_to'),
            \DB::raw('COALESCE(tasks.priority) as priority'),
            \DB::raw('COALESCE(tasks.status) as status')
        )
            ->leftJoin('users', 'tasks.created_by', '=', 'users.id')
            ->leftJoin('users as u2', 'tasks.assigned_to', '=', 'u2.id')
            ->leftJoin('roles', 'u2.role_id', '=', 'roles.id')
            //->leftJoin('task_attachments', 'tasks.id', '=', 'task_attachments.task_id')
            ->where('tasks.id', $taskId)
            ->first();

        $attachments = TaskAttachment::where('task_id', $taskId)->get();
        $roles = Role::select('id', 'role_name')->get();
        return view('task::viewTask', ['taskDetails' => $taskDetails, 'roles' => $roles, 'attachments' => $attachments]);
    }

    public function cancelTask(Request $request)
    {
        try {
            DB::beginTransaction();
            $taskId = $request->task_id;
            $taskDetails = Task::where('id', $taskId)->first();
            $taskDetails->status = 'Cancelled';
            $taskDetails->remark = $request->reasonForCancellation;
            $taskDetails->updated_by = auth()->id();
            if ($taskDetails->update()) {
                $notificationData = [];
                $notificationData['related_resource_id'] =  $taskId ?? null;
                $notificationData['related_resource_user_id'] = $taskDetails->assigned_to ?? null;
                $notificationData['related_resource_type'] = 'task/get-task-details/' .  $taskId;
                $notificationData['notification_title'] = 'Task Cancelled';
                $notificationData['notification_description'] = 'Your Task has been cancelled.';
                $notificationData['created_by'] = auth()->id() ?? null;
                $notification = Notification::create($notificationData);
                if($notification){
                    DB::Commit();
                    return response()->json(['status' => 'success', 'message' => trans('messages.TASK_CANCELLED')]);
                }
                DB::rollback();
                return response()->json(['status' => 'Fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
            } else {
                DB::rollback();
                return response()->json(['status' => 'Fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 'Fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
        }
    }

    public function assignTask(Request $request)
    {
        try {
            DB::beginTransaction();
            $assignedUserId = $request->user_id;
            $taskId = $request->assign_task_id;
            $task = Task::where('id', $taskId)->first();
            $task->assigned_to = $assignedUserId;
            $task->updated_by = auth()->id();
            if (!empty($request->assign_task_id)) {
                $task->status = 'Assigned';
            }
            if ($task->update()) {
                $notificationData = [];
                $notificationData['related_resource_id'] =  $taskId ?? null;
                $notificationData['related_resource_user_id'] = $assignedUserId ?? null;
                $notificationData['related_resource_type'] = 'task/get-task-details/' .  $taskId;
                $notificationData['notification_title'] = 'New Task Assignment';
                $notificationData['notification_description'] = 'New Task has been assigned to you.';
                $notificationData['created_by'] = auth()->id() ?? null;
                $notification = Notification::create($notificationData);
                if($notification){
                    DB::Commit();
                    return response()->json(['status' => 'success', 'message' => trans('messages.TASK_ASSIGNED')]);
                }
                DB::rollback();
                return response()->json(['status' => 'Fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
            } else {
                DB::rollback();
                return response()->json(['status' => 'Fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 'Fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
        }
    }

    public function getDetailsOfAssignedUser(Request $request)
    {
        try {
            $taskId = $request->assign_task_id;
            $taskUserId = Task::select('assigned_to')->where('id', $taskId)->first();
            $roleId = Role::getRoleIDByUserID($taskUserId->assigned_to);
            return response()->json(['roleId' => $roleId, 'userId' => $taskUserId]);
        }catch (Exception $e) {
            return response()->json(['status' => 'Fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
        }
    }
}
