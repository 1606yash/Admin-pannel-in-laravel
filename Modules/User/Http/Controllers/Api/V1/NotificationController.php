<?php

namespace Modules\User\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ApiBaseController;
use App\Models\Notification;
use App\Models\User as ModelsUser;
use App\Models\UserNotification;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Facades\Config;


class NotificationController extends ApiBaseController
{
    public function __construct()
    {
    }

    public static function createNotification($data = [])
    {
        try {
            $notificationData = [
                'notification_title' => $data['notification_title'] ?? null,
                'notification_description' => $data['notification_description'] ?? null,
                'created_by' => FacadesAuth::user()->id ?? null,
                'updated_by' => FacadesAuth::user()->id ?? null,
            ];
            $createNotification = Notification::createNotification($notificationData);
            if ($data['custom_key'] === "USER_PROFILE_CREATED") {
                $getNotifyUsers = ModelsUser::getNotifyUsers($data['data']->user_id ?? null);
                foreach ($getNotifyUsers as $key => $value) {
                    $value['notification_id'] = $createNotification->id ?? null;
                }
                $createUserNotification = UserNotification::createUserNotification($getNotifyUsers->toArray() ?? []);
                return true;
            }
            return false;
        } catch (\Exception $exception) {
            DB::rollback();
        }
    }
}
