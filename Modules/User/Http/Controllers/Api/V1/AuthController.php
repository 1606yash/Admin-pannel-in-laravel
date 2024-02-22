<?php

namespace Modules\User\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Modules\User\Entities\User;
use App\Http\Controllers\ApiBaseController;
use App\Models\User as ModelsUser;
use Helpers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use App\Traits\TwilioTrait;

class AuthController extends ApiBaseController
{
    use TwilioTrait;

    public function sendOtp(Request $request)
    {
        try {

            if ($request->has('phone_no')) {
                $userId = ModelsUser::checkExistingUserPhone($request->phone_no ?? null);
                if (!$userId) {
                    return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.USER_NOT_FOUND'));
                }
                $users = Helpers::checkWebLoginUsers();
                $filteredUsers = array_filter($users, function ($user) use ($userId) {
                    return $user['role_id'] == $userId->role_id;
                });
                if (!empty($filteredUsers)) {
                    return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.NOT_AUTHORIZED'));
                }
                $updateData['phone_no'] = $request->phone_no;
                $updateData['otp'] = Helpers::getRandomOTP();
                $updateData['otp_expiry'] = now()->addMinutes(5);
                $updateUser = ModelsUser::updateUser($updateData);
                $otp = $updateData['otp'];
                $body = "Your OTP is: $otp";

                $success = $this->sendOtpToMobile($request->phone_no, $body);
                return $this->sendSuccessResponse((object)[], 200, Config::get('constants.APIMESSAGES.USER_OTP_SENT'));
            }
            request()->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);
            $credentials = ['email' => $request->get('email'), 'password' => $request->get('password'), 'is_verified' => 1];
            if (Auth::attempt($credentials)) {
                $userData = Auth::user();
                $users = Helpers::checkWebLoginUsers();
                $filteredUsers = array_filter($users, function ($user) use ($userData) {
                    return $user['role_id'] == $userData->role_id;
                });
                if (!empty($filteredUsers)) {
                    return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.NOT_AUTHORIZED'));
                }
                $userData->access_token = $userData->createToken('MyApp')->accessToken;
                return $this->sendSuccessResponse($userData, 200, Config::get('constants.APIMESSAGES.USER_LOGIN'));
            } else {
                $user = User::where('email', $request->get('email'))->first();
                if (!$user) {
                    return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.USER_NOT_FOUND'));
                }
                if ($user && !$user->is_verified) {
                    return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.USER_NOT_ACTIVE'));
                }
            }
            return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.USER_INVALID_CREDENTIALS'));
        } catch (\Exception $e) {
            return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.SOMETHING_WENT_WRONG'));
        }
    }
    public function verifyLoginOtp(Request $request)
    {
        try {
            request()->validate([
                'otp' => 'required',
                'phone_no' => 'required',
            ]);
            $checkUser = ModelsUser::checkExistingUserPhone($request->phone_no ?? null);
            if (!$checkUser) {
                return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.USER_NOT_FOUND'));
            }
            $checkUser = ModelsUser::checkPhoneOtp($request->phone_no ?? null, $request->otp ?? null);
            if (!$checkUser) {
                return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.USER_INVALID_OTP'));
            }
            if ($checkUser->is_verified == 1) {
                //$checkUser->access_token = $checkUser->createToken('MyApp')->accessToken;
                //$updateUser = ModelsUser::updateOtp($request->phone_no ?? null, $request->otp ?? null);
                return $this->sendSuccessResponse((object)[], 200, Config::get('constants.APIMESSAGES.USER_OTP_VERIFIED'));
            }
            return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.USER_NOT_VERIFIED'));
        } catch (\Exception $e) {
            return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.SOMETHING_WENT_WRONG'));
        }
    }
    public function logout(Request $request)
    {
        try {
            $request->user()->token()->revoke();
            return $this->sendSuccessResponse((object)[], 200, Config::get('constants.APIMESSAGES.USER_LOGOUT'));
        } catch (\Exception $e) {
            return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.SOMETHING_WENT_WRONG'));
        }
    }
    public function createPin(Request $request)
    {
        try {
            $phoneNumber = $request->phone_no;
            $pin = $request->pin;

            $user = ModelsUser::where('phone_no', $phoneNumber)->leftJoin('roles as r', 'r.id', '=', 'users.role_id')->select('users.*', 'r.role_name as role_name')->first();
            if (!$user) {
                return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.USER_NOT_FOUND'));
            }
            $user->login_pin = $pin;
            $user->save();
            return $this->sendSuccessResponse($user, 200, Config::get('constants.APIMESSAGES.PIN_CREATED_SUCCESSFULLY'));
        } catch (\Exception $e) {
            return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.SOMETHING_WENT_WRONG'));
        }
    }
    public function login(Request $request)
    {
        try {
            $phoneNumber = $request->phone_no;
            $pin = $request->pin;
            $usePin = $request->use_pin; // Flag for PIN login
            $useBiometric = $request->use_biometric; // Flag for biometric login
            $user = ModelsUser::where('phone_no', $phoneNumber)->first();
            //$user->makeVisible($user->login_pin);
            if (!$user) {
                return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.USER_NOT_FOUND'));
            }
            \Log::info("useBiometric");
            \Log::info($useBiometric);
            \Log::info(gettype($useBiometric));
            if (($usePin == true && $pin && $user->login_pin == $pin) || ($useBiometric == true && $user)) {
                $checkDeviceToken = ModelsUser::checkDeviceToken($request->phone_no, $request->device_token);
                if (!$checkDeviceToken) {
                    $user->tokens()->delete();
                }
                $updateData['phone_no'] = $request->phone_no;
                $updateData['device_token'] = $request->device_token;
                $updateUser = ModelsUser::updateUser($updateData);
                $user->device_token = $request->device_token;
                // PIN login successful, generate token
                $user->access_token = $user->createToken('MyApp')->accessToken;
            } else {
                // Invalid credentials
                return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.USER_INVALID_CREDENTIALS'));
            }
            return $this->sendSuccessResponse($user, 200, Config::get('constants.APIMESSAGES.USER_LOGIN'));
        } catch (\Exception $e) {
            return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.SOMETHING_WENT_WRONG'));
        }
    }

    public function uploadFile(Request $request)
    {
        try {
            $data = $request->all();
            $fileName = time() . rand(100, 100000) . '.' . $request->file->extension();
            $data['file']->move(base_path('public/ProfileDocuments'), $fileName);
            $filepath = base_path('public/ProfileDocuments/' . $fileName);
            return $this->sendSuccessResponse($filepath, 200, 'File uploaded successfully');
        } catch (\Exception $e) {
            return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.SOMETHING_WENT_WRONG'));
        }
    }
}
