<?php

namespace Modules\Task\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiBaseController;
use App\Models\Task;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use DB;
use Helpers;
use App\Models\TaskAttachment;
use App\Models\User;
use App\Notifications\SendNotification;

class TaskController extends ApiBaseController
{
  /**
     * @OA\Get(
     *     path="/api/v1/tasks",
     *     tags={"Task"},
     *     security={
     *      {"bearerAuth": {}},
     *     },
     *     description="API to get tasks of user",
     *     operationId="indexad",
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\Schema(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function getUserTasks(Request $request)
    {
        try {
            $validations = Task::getTasksValidationRules();
            $validator = Validator::make($request->all(),$validations, Task::$getTasksValidationMessages);
            if($validator->fails()) {
                $errors = $validator->errors();
                $errorsMsg = "";

                if ($errors->first()) {
                    $errorsMsg .= " " . $errors->first();
                }

                return $this->sendFailureResponse($errorsMsg);
            } else {
                $userId = $request->input('user_id');
                $perPage = $request->input('per_page', 10);
                $currentPage = $request->input('page', 1);
                $skip = ($currentPage - 1) * $perPage;
                $tasks = Task::getTasks($userId, $perPage, $skip);
                if ($tasks->isEmpty()) {
                    return $this->sendSuccessResponse($tasks, 200, Config::get('constants.APIMESSAGES.NO_DATA_FOUND'));
                } else {
                    return $this->sendSuccessResponse($tasks, 200, Config::get('constants.APIMESSAGES.TASKS_RETRIVED_SUCCESSFULLY'));
                }
            }
        } catch (\Exception $exception) {
            DB::rollback();
            return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.SOMETHING_WENT_WRONG'));
        }
    }

    public function addTask(Request $request)
    {
        try {
            DB::beginTransaction();
            $validations = Task::validationRules();
            $validator = Validator::make($request->all(),$validations, Task::$validationMessages);
            if($validator->fails()) {
                $errors = $validator->errors();
                $errorsMsg = "";

                if ($errors->first()) {
                    $errorsMsg .= " " . $errors->first();
                }

                return $this->sendFailureResponse($errorsMsg);
            } else {
                $task = new Task();
                $userId = $request->user_id;
                $task->created_by = $userId;
                $task->title = $request->title;
                $task->description = $request->description;
                $task->priority = $request->priority;
                $task->assigned_to = $request->assigned_to;
                $task->status = Config::get('constants.TASK_STATUS.Assigned');
                $task->remark = $request->remark;
                if($task->save()) {
                    if ($request->hasFile('attachments')) {
                        $folderName = 'task_attachments';
                        foreach ($request->file('attachments') as $file) {
                            $attachmentUrl = Helpers::uploadAttachment($file, $folderName, $task->id);
                            $attachment = new TaskAttachment();
                            $attachment->task_id = $task->id;
                            $attachment->attachment_path = $attachmentUrl;
                            $attachment->save();
                        }
                    }
                    $notification = new SendNotification();
                    $user = User::findOrFail($request->assigned_to);
                    $title = 'You have assigned task: '. $request->title;
                    $user->message = $title;
                    $user->notify($notification);
                    DB::commit();                                
                    return $this->sendSuccessResponse($task, 200, Config::get('constants.APIMESSAGES.TASK_ADDED_SUCCESSFULLY'));
                } else {
                    DB::rollback();
                    return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.SOMETHING_WENT_WRONG'));
                }
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.SOMETHING_WENT_WRONG'));
        }
    }

    public function updateTaskStatus(Request $request)
    {
        try {
            DB::beginTransaction();
            $userId = FacadesAuth::user()->id;
            $task = Task::findOrFail($request->id);
            $task->status = $request->status;
            $task->remark = $request->remark;
            $task->updated_by = $userId;
            if($task->save()) {
                DB::commit();                                
                return $this->sendSuccessResponse($task, 200, Config::get('constants.APIMESSAGES.TASK_STATUS_UPDATED_SUCCESSFULLY'));
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
