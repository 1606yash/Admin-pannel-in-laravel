<?php
namespace App;
 
use Illuminate\Support\Facades\DB;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;
 
class SendNotification {
	/**
	 * @param int $user_id User-id
	 * 
	 * @return string
	 */
	public static function sendNotification($notifyData) {
		if(!empty($notifyData)){

			$token = $notifyData['fcm_token'];
			$title = $notifyData['title'];
			$body = $notifyData['body'];

			$optionBuilder = new OptionsBuilder();
			$optionBuilder->setTimeToLive(60*20);

			$notificationBuilder = new PayloadNotificationBuilder($title);
			$notificationBuilder->setBody($body)
							->setSound('default');

			$dataBuilder = new PayloadDataBuilder();
			$dataBuilder->addData([
							'title' => $title,
							'body' => $body,
							'user_id' => $notifyData['user_id'],
							'date' => date('Y-m-d H:i:s'),
						]);

			$option = $optionBuilder->build();
	        $notification = $notificationBuilder->build();
	        $data = $dataBuilder->build();

	        $downstreamResponse = FCM::sendTo($token, $option, $notification, $data);

	        $downstreamResponse->numberSuccess();
			$downstreamResponse->numberFailure();
			$downstreamResponse->numberModification();
			return "Sent";
		}
	}
}