<?php

use App\Mail\sendMail;
use App\Models\ProfileInformation;
use App\Models\Role;
use App\Models\User as ModelsUser;
use App\Models\Attendance;
use App\Models\Leave;
use App\Models\LeaveType;
use App\Models\Holiday;
use App\Models\ShiftType;
use Illuminate\Support\Facades\Config;
use Modules\User\Entities\User;
use Modules\User\Entities\OrganizationBuyer;
use Modules\User\Entities\RetailerCategories;
use App\Models\Notification as ModelsNotification;
use App\SendNotification as SendNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Kreait\Firebase;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Kreait\Firebase\Database;
use Modules\User\Entities\OrganizationStaff;
use Aws\S3\S3Client;
use Illuminate\Support\Facades\Storage;

class Helpers
{



	/**
	 * function for calculate percentage
	 * @param $current
	 * @param $total
	 * @return float
	 */
	public static function calculatePercentage($current, $total)
	{
		$percentage = ($current / $total) * 100;
		return round($percentage, 2);
	}


	/**
	 * function for conver array keys
	 * @param $array
	 * @return array
	 */
	public static function convertArrayKeys($array)
	{
		$keys = array_keys($array);
		//Map keys to format function
		$keys = array_map(@[self, 'map'], $keys);

		//Use array_combine to map formatted keys to array values
		$array = array_combine($keys, $array);

		//Replace nulls and .00 from array
		return self::replaceNulls($array);
	}


	public static function map($key)
	{
		// return str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));
		return lcfirst(implode('', array_map('ucfirst', explode('_', $key))));
	}

	public static function replaceNulls($array)
	{
		array_walk_recursive($array, @[self, 'array_replacing']);
		return $array;
	}

	public static function array_replacing(&$item, $key)
	{
		if ($item == null || $item == NULL) {
			$item = "";
		} elseif ($item == ".00") {
			$item = 0;
		}
		// else{
		//     $item = trim($item);
		// }

	}

	public static function clean($string)
	{
		$string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
		return preg_replace('/[^A-Za-z0-9\-.]/', '', $string); // Removes special chars.
	}

	public static function getAcronym($words)
	{
		$words = preg_replace('/\s+/', ' ', $words);
		$words = explode(" ", $words);

		$acronym = "";
		foreach ($words as $w) {
			if (strlen($acronym) < 2 && trim($w) != "") {
				$acronym .= $w[0];
			}
		}
		return strtoupper($acronym);
	}


	public static function getFeaturePermission($feature)
	{
		$permission =   OrganizationPermission::select('read_own', 'read_all', 'edit_own', 'edit_all', 'delete_own', 'delete_all')
			->where('role_id', Auth::user()->role)
			->where('feature_id', $feature)
			->first();
		if ($permission) {
			return $permission->toArray();
		} else {
			return array();
		}
	}

	public static function checkDiscount()
	{


		// $organization_id = \Auth::user()->organization_id;

		$settings = \DB::select("select value from settings where code = 'ecommerce_discount'");

		if (!empty($settings)) {
			if ($settings[0]->value == 'true') {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	public static function getRetailerCategory($retailer_id)
	{


		// $organization_id = \Auth::user()->organization_id;

		$category = OrganizationBuyer::select('buyer_category as retailer_category')->where('buyer_id', $retailer_id)->first();

		if ($category) {
			return $category;
		} else {
			return false;
		}
	}

	/*
	 * Method to strip tags globally.
	 */

	public static function calculateRetailerDiscount($products, $percent, $otherDiscount = 0)
	{
		if (!empty($products->toArray())) {
			foreach ($products as $key => $product) {


				if ($product->price != $product->regular_price) {
					$product->prodcutDiscount = (($product->regular_price - $product->price) * 100) / $product->regular_price;
					$product->prodcutDiscount = number_format((float) $product->prodcutDiscount, 2, '.', '');
				} else {
					$product->prodcutDiscount = 0;
				}

				$product->discount_price = $product->regular_price - ($product->regular_price * ($percent / 100));
				$product->discount_price = number_format((float) $product->discount_price, 2, '.', '');
				$product->discount_regular_price = $product->regular_price - ($product->regular_price * ($percent / 100));
				$product->discount_regular_price = number_format((float) $product->discount_regular_price, 2, '.', '');

				if ($product->prodcutDiscount > 0 && $otherDiscount == 0) {

					$percentPro = $product->prodcutDiscount;

					if (isset($product->discount_price)) {
						$price = $product->discount_price;
					} else {
						$price = $product->price;
					}

					$product->discount_price = $price - ($price * ($percentPro / 100));
					$product->discount_price = number_format((float) $product->discount_price, 2, '.', '');
					$product->discount_regular_price = $product->regular_price - ($product->regular_price * ($percentPro / 100));
					$product->discount_regular_price = number_format((float) $product->discount_regular_price, 2, '.', '');
				}
			}

			return $products;
		}
	}

	/*
	 * Method to strip tags globally.
	 */

	public static function calculateRetailerDiscountSingleProduct($product, $percent, $otherDiscount = 0)
	{


		if ($product->price != $product->regular_price) {
			$product->prodcutDiscount = (($product->regular_price - $product->price) * 100) / $product->regular_price;
			$product->prodcutDiscount = number_format((float) $product->prodcutDiscount, 2, '.', '');
		} else {
			$product->prodcutDiscount = 0;
		}

		$product->discount_price = $product->regular_price - ($product->regular_price * ($percent / 100));
		$product->discount_price = number_format((float) $product->discount_price, 2, '.', '');
		$product->discount_regular_price = $product->regular_price - ($product->regular_price * ($percent / 100));
		$product->discount_regular_price = number_format((float) $product->discount_regular_price, 2, '.', '');
		if ($product->prodcutDiscount > 0 && $otherDiscount == 0) {

			$percent = $product->prodcutDiscount;

			if (isset($product->discount_price)) {
				$price = $product->discount_price;
			} else {
				$price = $product->price;
			}

			$product->discount_price = $price - ($price * ($percent / 100));
			$product->discount_price = number_format((float) $product->discount_price, 2, '.', '');
			$product->discount_regular_price = $product->regular_price - ($product->regular_price * ($percent / 100));
			$product->discount_regular_price = number_format((float) $product->discount_regular_price, 2, '.', '');
		}
		return $product;
	}

	public static function calculateCategoryDiscountsSingleProduct($product, $discounts, $retailer_id = 0)
	{
		$category = 0;

		if ($retailer_id	!= 0) {
			/*$user = User::select('retailer_category')->where('id',$retailer_id)->first();
				$category = $user->retailer_category;*/
			// $organization_id = \Auth::user()->organization_id;
			$categoryData = OrganizationBuyer::select('buyer_category as retailer_category')->where('buyer_id', $retailer_id)->first();
			$category = $categoryData->retailer_category;
		}


		$product_category = $retailer_category = array();

		if (isset($discounts['product_category'])) {
			$product_category = $discounts['product_category'];
		}

		if (isset($discounts['retailer_category'])) {
			$retailer_category = $discounts['retailer_category'];
		}

		if ($product->price != $product->regular_price) {
			$product->prodcutDiscount = (($product->regular_price - $product->price) * 100) / $product->regular_price;
			$product->prodcutDiscount = number_format((float) $product->prodcutDiscount, 2, '.', '');
		} else {
			$product->prodcutDiscount = 0;
		}

		if ($category != 0 && array_key_exists($category, $retailer_category)) {
			$percent = $retailer_category[$category];

			if (isset($product->discount_price)) {
				$price = $product->discount_price;
			} else {
				$price = $product->regular_price;
			}

			$product->discount_price = $price - ($price * ($percent / 100));
			$product->discount_price = number_format((float) $product->discount_price, 2, '.', '');
			$product->discount_regular_price = $product->regular_price - ($product->regular_price * ($percent / 100));
			$product->discount_regular_price = number_format((float) $product->discount_regular_price, 2, '.', '');
		}


		if (!empty($product_category)) {
			$productCategories = explode(',', $product->category_ids);
			foreach ($productCategories as $key => $pcategory) {
				if (array_key_exists($pcategory, $product_category)) {

					$percent = $product_category[$pcategory];

					if (isset($product->discount_price)) {
						$price = $product->discount_price;
					} else {
						$price = $product->regular_price;
					}

					$product->discount_price = $price - ($price * ($percent / 100));
					$product->discount_price = number_format((float) $product->discount_price, 2, '.', '');
					$product->discount_regular_price = $product->regular_price - ($product->regular_price * ($percent / 100));
					$product->discount_regular_price = number_format((float) $product->discount_regular_price, 2, '.', '');
				}
			}
		}

		if ($product->prodcutDiscount > 0) {

			$percent = $product->prodcutDiscount;

			if (isset($product->discount_price)) {
				$price = $product->discount_price;
			} else {
				$price = $product->price;
			}

			$product->discount_price = $price - ($price * ($percent / 100));
			$product->discount_price = number_format((float) $product->discount_price, 2, '.', '');
			$product->discount_regular_price = $product->regular_price - ($product->regular_price * ($percent / 100));
			$product->discount_regular_price = number_format((float) $product->discount_regular_price, 2, '.', '');
		}

		return $product;
	}

	/*
	 * Method to strip tags globally.
	 */

	public static function calculateCategoryDiscounts($products, $discounts, $retailer_id = 0)
	{
		$category = 0;

		if ($retailer_id	!= 0) {
			// $organization_id = \Auth::user()->organization_id;
			$categoryData = OrganizationBuyer::select('buyer_category as retailer_category')->where('buyer_id', $retailer_id)->first();
			$category = $categoryData->retailer_category;
			// $user = User::select('retailer_category')->where('id',$retailer_id)->first();
			// $category = $user->retailer_category;
		}


		$product_category = $retailer_category = array();

		if (isset($discounts['product_category'])) {
			$product_category = $discounts['product_category'];
		}

		if (isset($discounts['retailer_category'])) {
			$retailer_category = $discounts['retailer_category'];
		}

		foreach ($products as $key => $product) {

			if ($product->price != $product->regular_price) {
				$product->prodcutDiscount = (($product->regular_price - $product->price) * 100) / $product->regular_price;
				$product->prodcutDiscount = number_format((float) $product->prodcutDiscount, 2, '.', '');
			} else {
				$product->prodcutDiscount = 0;
			}

			if ($category != 0 && array_key_exists($category, $retailer_category)) {
				$percent = $retailer_category[$category];

				if (isset($product->discount_price)) {
					$price = $product->discount_price;
				} else {
					$price = $product->regular_price;
				}

				$product->discount_price = $price - ($price * ($percent / 100));

				$product->discount_price = number_format((float) $product->discount_price, 2, '.', '');

				$product->discount_regular_price = $product->regular_price - ($product->regular_price * ($percent / 100));
				$product->discount_regular_price = number_format((float) $product->discount_regular_price, 2, '.', '');
			}


			if (!empty($product_category)) {
				$productCategories = explode(',', $product->category_ids);

				foreach ($productCategories as $key => $pcategory) {
					if (array_key_exists($pcategory, $product_category)) {

						$percent = $product_category[$pcategory];

						if (isset($product->discount_price)) {
							$price = $product->discount_price;
						} else {
							$price = $product->regular_price;
						}

						$product->discount_price = $price - ($price * ($percent / 100));
						$product->discount_price = number_format((float) $product->discount_price, 2, '.', '');
						$product->discount_regular_price = $product->regular_price - ($product->regular_price * ($percent / 100));
						$product->discount_regular_price = number_format((float) $product->discount_regular_price, 2, '.', '');
					}
				}
			}

			if ($product->prodcutDiscount > 0) {
				$percent = $product->prodcutDiscount;

				if (isset($product->discount_price)) {
					$price = $product->discount_price;
				} else {
					$price = $product->price;
				}

				$product->discount_price = $price - ($price * ($percent / 100));
				$product->discount_price = number_format((float) $product->discount_price, 2, '.', '');
				$product->discount_regular_price = $product->regular_price - ($product->regular_price * ($percent / 100));
				$product->discount_regular_price = number_format((float) $product->discount_regular_price, 2, '.', '');
			}
		}

		return $products;
	}

	// public static function sendWaNotification($message = "", $numbers)
	// {
	// 	$url = "https://wa.notifyabhi.com/api/sm";

	// 	$waKey = \DB::select("select value from settings where code = 'wa_auth_key'");

	// 	if (!empty($waKey)) {
	// 		$authkey = $waKey[0]->value;

	// 		if (!empty($authkey)) {
	// 			$ch = curl_init();
	// 			curl_setopt($ch, CURLOPT_URL, $url);
	// 			curl_setopt($ch, CURLOPT_POST, 1);
	// 			curl_setopt($ch, CURLOPT_POSTFIELDS, "authkey=" . $authkey . "&to=" . $numbers . "&msg=" . $message);
	// 			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	// 			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	// 			//curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	// 			$output = curl_exec($ch);
	// 			curl_close($ch);

	// 			$response = json_decode($output);
	// 		} else {
	// 			return false;
	// 		}
	// 	} else {
	// 		return false;
	// 	}
	// }

	public static function getUserDetails($user_id = 0)
	{

		if ($user_id != 0) {
			$user = User::findorfail($user_id);
			if ($user) {
				return $user;
			}
		}
		return false;
	}


	public static function sendNotifications($receiver = array(), $bodies = array(), $channels = array(), $mailSubject = '', $details = array())
	{

		if (!empty($channels)) {

			foreach ($channels as $key => $channel) {
				if (!empty($bodies) && isset($bodies[$channel])) {
					// Push,In-app/WA/Email
					if ($channel == 'wa') {
						$notifyNumbers = '91' . $receiver->phone_number;
						if (!empty($notifyNumbers)) {
							$broadcast = self::sendWaNotification($bodies[$channel], $notifyNumbers);
						}
					}

					if ($channel == 'database') {

						if (!empty($details) && $details['fcm_token'] != "") {
							$sendNotification = SendNotification::sendNotification($details);
						}


						/*$serviceAccount = ServiceAccount::fromJsonFile(__DIR__.'/profitley-firebase.json');
						$firebase = (new Factory)
						->withServiceAccount($serviceAccount)
						->withDatabaseUri(\Config::get('constants.FIREBASE.DATABASEURI'))
						->create();
						$database = $firebase->getDatabase();*/


						if (\Auth::user()) {
							$organization_id = \Auth::user()->organization_id;
						} else {
							$organization_id = $details['organization_id'];
						}

						$factory = (new Factory)->withServiceAccount(__DIR__ . '/profitley-firebase.json')->withDatabaseUri(\Config::get('constants.FIREBASE.DATABASEURI'));
						$database = $factory->createDatabase();
						$details['date'] = date('Y-m-d H:i:s');
						$newPost = $database
							// ->getReference(\Config::get('constants.FIREBASE.REFERENCE').'/user_id_'.$receiver->id)
							->getReference(\Config::get('constants.FIREBASE.REFERENCE') . '/org_' . $organization_id . '_user_id_' . $receiver->id)
							->push($details);
					}

					if ($channel == 'mail') {

						$to_name = $receiver->name;
						$to_email = $receiver->email;
						$data = array('name' => $receiver->name, "body" => $bodies[$channel], 'mailSubject' => $mailSubject);
						$mailBody = $bodies[$channel];

						// Mail::send('emails.email_template', $data, function ($message)  use ($to_name, $to_email,$mailBody,$mailSubject) {
						// 	// $message->to($to_email, $to_name)
						// 	$message->to($to_email, $to_name)
						// 	->subject($mailSubject)
						// 	->from('support@profitley.com','Softude');
						// 	// ->setBody($mailBody, 'text/html');
						// });


						// Mail::send(‘emails.mail’, $data, function($message) use ($to_name, $to_email) {
						// $message->to($to_email, $to_name)
						// ->subject(Laravel Test Mail’);
						// $message->from(‘SENDER_EMAIL_ADDRESS’,’Test Mail’);
						// });

					}
				}
			}
		}
	}

	public static function get_dates_of_quarter($quarter = 'current', $year = null, $format = null)
	{
		if (!is_int($year)) {
			$year = (new DateTime)->format('Y');
		}
		$current_quarter = ceil((new DateTime)->format('n') / 3);
		switch (strtolower($quarter)) {
			case 'this':
			case 'current':
				$quarter = ceil((new DateTime)->format('n') / 3);
				break;

			case 'previous':
				$year = (new DateTime)->format('Y');
				if ($current_quarter == 1) {
					$quarter = 4;
					$year--;
				} else {
					$quarter =  $current_quarter - 1;
				}
				break;

			case 'first':
				$quarter = 1;
				break;

			case 'last':
				$quarter = 4;
				break;

			default:
				$quarter = (!is_int($quarter) || $quarter < 1 || $quarter > 4) ? $current_quarter : $quarter;
				break;
		}
		if ($quarter === 'this') {
			$quarter = ceil((new DateTime)->format('n') / 3);
		}
		$start = new DateTime($year . '-' . (3 * $quarter - 2) . '-1 00:00:00');
		$end = new DateTime($year . '-' . (3 * $quarter) . '-' . ($quarter == 1 || $quarter == 4 ? 31 : 30) . ' 23:59:59');

		return array(
			'startDate' => $format ? $start->format($format) : $start,
			'endDate' => $format ? $end->format($format) : $end,
		);
	}


	public static function getUserOrganizations($user_id = 0)
	{
		if ($user_id != 0) {
			$userOrgs = OrganizationStaff::select(\DB::Raw('group_concat(organization_id) as orgs'))->where('user_id', $user_id)->first();
			if ($userOrgs->orgs != "") {
				$userOrgs = explode(',', $userOrgs->orgs);
			} else {
				$userOrgs = array();
			}
		}
		return $userOrgs;
	}

	public static function checkWebLoginUsers()
	{
		return $users = [
			['role_id' => 1],
			['role_id' => 7]
		];
	}
	public static function sendEmailForApprovalReject($data)
	{
		$findSuperiorUser = ProfileInformation::findSuperiorUser($data['user_id'] ?? null);
		$checkDistrictHead = ModelsUser::checkUserByID($findSuperiorUser->reporting_manager_id ?? null);
		$emails = [];
		if ($findSuperiorUser) {
			$emails[] = [
				'email' => $findSuperiorUser->email ?? "",
				'first_name' => $findSuperiorUser->first_name ?? "",
				'user_type' => 'user',
				'user_id' => $findSuperiorUser->user_id ?? null,
			];
		}
		if ($checkDistrictHead) {
			$emails[] = [
				'email' => $checkDistrictHead->email,
				'first_name' => $checkDistrictHead->first_name ?? "",
				'user_type' => 'superior_user',
				'user_id' => $checkDistrictHead->id,
			];
			if ($checkDistrictHead->email != Auth::user()->email) {
				$emails[] = [
					'email' => Auth::user()->email ?? "",
					'first_name' => Auth::user()->first_name ?? "",
					'user_type' => 'admin_user',
					'user_id' => Auth::user()->id ?? null,
				];
			}
		}
		foreach ($emails as $key => $fordata) {
			$link =  env('PARIVAAR_URL') . '/login';
			$mailData = [
				'first_name' => $fordata['first_name'] ?? "",
				'email' => $fordata['email'] ?? "",
				'mail_type' => $data['is_verified'] == 1 ? 'user-approved' : 'user-rejected',
				'user_email' => ModelsUser::checkUserByID($data['user_id'] ?? null)->email ?? "",
				'user_type' => $fordata['user_type'] ?? "",
				'login_link' => $link,
				'view' => 'emails.approve_reject',
				'reject_reason' => $data['reject_reason'] ?? "",
			];
			Mail::to($fordata['email'] ?? null)->send(new sendMail($mailData));
		}
		return true;
	}

	/**
	 * generate random number
	 * @param $mime
	 * @return bool
	 */
	public static function getRandomOTP()
	{
		$data = mt_rand(1000, 9999);
		return $data;
	}

	public static function getRoleIdByRoleName($rolename)
	{
		$roleId = '';
		$role = Role::where('role_name', $rolename)->first();

		// Check if $role is not null before accessing its id property
		if ($role) {
			$roleId = $role->id;
		}

		return $roleId;
	}

	public static function getAllRoles()
	{
		$roles = Role::select('id', 'role_name')->get();
		return $roles;
	}

	public static function getSubAdminChildRoles()
	{
		$childRole = Role::where('parent_id', '!=', 0)->get();
		return $childRole;
	}

	public static function getAllUsers()
	{
		// Select user IDs and concatenate first and last names as full_name
		$users = User::select('id', 'first_name', 'last_name')->where('is_verified', 1)->get();

		// Return the collection of users
		return $users;
	}

	public static function getProfileDetailsOnDashboard()
	{
		$user = User::select(\DB::raw('COALESCE(CONCAT(users.first_name, " ", users.last_name)) as profile_name'), 'roles.role_name', 'users.profile_path')->leftJoin('roles', 'users.role_id', '=', 'roles.id')->where('users.id', auth()->id())->first();
		return $user;
	}

	public static function getNoticationList()
	{
		$notifications = ModelsNotification::where('related_resource_id', auth()->id())->orderBy('created_at', 'desc')->limit(5)->get();
		return $notifications;
	}

	public static function getNotificationCount()
	{
		$notificationCount = ModelsNotification::where('related_resource_id', auth()->id())->whereNull('read_at')->count();
		return $notificationCount;
	}

	public static function createNotification($data = [])
	{
		$notification = ModelsNotification::create($data);
		return $notification;
	}


	/**
	 * upload files to s3 bucket
	 */
	public static function uploadAttachment($file, $folder, $id)
	{
		try {
			$fileName = uniqid() . '.' . $file->getClientOriginalExtension();
			$path = "$folder/$id/$fileName";
			Storage::disk('s3')->put($path, file_get_contents($file));
			return Storage::disk('s3')->url($path);
		} catch (\Exception $e) {
			return $e->getMessage();
		}
	}

	public static function formatDate($dateTimeString)
	{
		try {
			$dateTime = new DateTime($dateTimeString);
			return $dateTime->format('d/m/Y');
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}

	public static function createFilter($constantType, $constantNames)
	{
		$filters = [];
		foreach ($constantNames as $constantName) {
			$filters[] = [
				Config::get("constants.$constantType.$constantName") => Config::get("constants.$constantType.$constantName"),
			];
		}
		return $filters;
	}

	public static function createEmployeeId($id)
	{
		$empId = 'EMP' . $id;
		return $empId;
	}

	public static function convertFiguresIntoWords($netPayable)
	{
		$formatter = new NumberFormatter("en", NumberFormatter::SPELLOUT);
		$netPayableInWords = $formatter->format($netPayable);
		return ucwords($netPayableInWords . ' Only');
	}

	public static function getAttendanceLogDetails($attendanceDate, $userId)
	{
		$firstDayOfMonth = date('Y-m-01', strtotime($attendanceDate));
		$lastDayOfMonth = date('Y-m-t', strtotime($attendanceDate));

		$attendanceLogs = Attendance::getUserAttendanceLogsForDate($userId, $attendanceDate);
		$totalPresentDays = Attendance::getUserPresentDaysCountForMonth($userId, $firstDayOfMonth, $lastDayOfMonth);
		$totalHolidays = Holiday::getHolidaysCountForMonth($firstDayOfMonth, $lastDayOfMonth);
		$holiday = Holiday::getHolidayForDay($attendanceDate);
		$currentDayStatus = Attendance::getUserAttendanceStatusForDate($userId, $attendanceDate);
		$totalLeaves = Leave::getUserLeaveCountBetweenDates($userId, $firstDayOfMonth, $lastDayOfMonth);
		$currentDate = now()->format('Y-m-d');
		if ($holiday > 0) {
			$attendanceStatus = Config::get('constants.ATTENDENCE_STATUS.Holiday');
		} else {
			$leaveStatus = Leave::getUserLeaveStatusForDay($userId, $attendanceDate);
			if ($leaveStatus) {
				$leaveType = LeaveType::find($leaveStatus->leave_type_id)->name;
				$attendanceStatus = "on $leaveType";
			} else {
				if ($attendanceDate < $currentDate && count($attendanceLogs) === 0) {
					// For previous dates without logs, mark as absent
					$attendanceStatus = Config::get('constants.ATTENDENCE_STATUS.Absent');
				} elseif ($attendanceDate == $currentDate) {
					// For the current date, check if the user is checked in, not logged in, or checked out
					$attendanceStatus = $currentDayStatus ? ($currentDayStatus->logout_time ? Config::get('constants.ATTENDENCE_STATUS.CheckedOut') : Config::get('constants.ATTENDENCE_STATUS.CheckedIn')) : Config::get('constants.ATTENDENCE_STATUS.NotLoggedIn');
				} elseif ($attendanceDate > $currentDate) {
					// For future dates, show "empty status"
					//$attendanceStatus = '';
					$attendanceStatus = $currentDayStatus ? ($currentDayStatus->logout_time ? Config::get('constants.ATTENDENCE_STATUS.CheckedOut') : Config::get('constants.ATTENDENCE_STATUS.CheckedIn')) : Config::get('constants.ATTENDENCE_STATUS.NotLoggedIn');
				} else {
					// For previous dates with logs, mark as present
					$attendanceStatus = Config::get('constants.ATTENDENCE_STATUS.Present');
				}
			}
		}

		$response = [
			'attendanceLogs' => $attendanceLogs,
			'totalPresentDays' => $totalPresentDays,
			'totalHolidays' => $totalHolidays,
			'totalLeaves' => $totalLeaves,
			'attendanceStatus' => $attendanceStatus,
		];
		return $response;
	}

	public static function calculateSalary($basicSalary, $hra, $conveyance, $specialAllowance, $professionalTax)
	{
		$data = [];
		// Calculate PF Contribution (assuming PF rate is 10%)
		$pfRate = 0.10;
		$data['pf'] = $basicSalary * $pfRate;

		// Calculate Gross Salary
		$data['grossSalary'] = $basicSalary + $hra + $conveyance + $specialAllowance;

		// Calculate Net Payable Salary
		$data['netPayableSalary'] = $data['grossSalary'] - $professionalTax - $data['pf'];

		return $data;
	}
}
