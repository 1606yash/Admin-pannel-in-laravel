<?php

namespace Modules\User\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Notifications\PasswordResetRequest;
use App\Notifications\PasswordResetSuccess;
use Modules\User\Entities\User;
use App\PasswordReset;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ApiBaseController;
use App\Models\User as ModelsUser;
use Illuminate\Support\Facades\Hash;
use SendGrid\Mail\Mail;
use Config;
use Illuminate\Support\Str;

class PasswordResetController extends ApiBaseController
{

    public function create(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|string|email',
            ]);
            $user = ModelsUser::checkExistingUserEmail($request->email ?? null);
            if (!$user) {
                return $this->sendFailureResponse('This e-mail address is not linked to any user account.');
            }
            $passwordReset = PasswordReset::updateOrCreate(
                ['email' => $user->email],
                [
                    'email' => $user->email,
                    'token' => Str::random(60)
                ]
            );
            if ($user && $passwordReset) {
                $url = $request->appUrl . '/' . $passwordReset->token;
                $to_name = $user->FullName;
                $to_email = $user->email;

                $data = array('url' => $url, "name" => $to_name);

                // \Mail::send('emails.reset_password', $data, function ($message)  use ($to_name, $to_email) {
                //     $message->to($to_email, $to_name)
                //     ->subject('Reset Password')
                //     ->from('support@profitley.com','Profitley');
                // });

                // $user->notify(
                //     new PasswordResetRequest($passwordReset->token,$request->appUrl,$user->FullName,$user->email)
                // );
            }
            return $this->sendSuccessResponse('We have e-mailed your password reset link!', 200);
        } catch (\Exception $e) {
            return $this->sendFailureResponse($e->getMessage());
        }
    }
    public function find($token)
    {
        try {
            $passwordReset = PasswordReset::where('token', $token)
                ->first();
            if (!$passwordReset)
                return $this->sendFailureResponse('This link has expired.');
            if (Carbon::parse($passwordReset->updated_at)->addMinutes(720)->isPast()) {
                $passwordReset->delete();
                return $this->sendFailureResponse('This link has expired.');
            }
            return $this->sendSuccessResponse($passwordReset);
        } catch (\Exception $e) {
            return $this->sendFailureResponse($e->getMessage());
        }
    }

    public function reset(Request $request)
    {
        try {
            $request->validate([
                // 'email' => 'required|string|email',
                'password' => 'required|string',
                'token' => 'required|string'
            ]);
            $passwordReset = PasswordReset::where([
                ['token', $request->token],
            ])->first();
            // ['email', $request->email]
            if (!$passwordReset)
                return $this->sendFailureResponse('This link has expired.');

            $user = User::where('email', $passwordReset->email)->first();
            if (!$user)
                return $this->sendFailureResponse('This e-mail address is not linked to any user account.');

            $user->password = bcrypt($request->password);
            $user->save();
            $passwordReset->delete();
            // $user->notify(new PasswordResetSuccess($passwordReset));

            /*$email = new \SendGrid\Mail\Mail();
        $email->setFrom(Config::get('constants.EMAIL_CONFIG.FROM_EMAIL'), Config::get('constants.EMAIL_CONFIG.FROM_NAME'));
        $email->addTo(
            $user->email,
            $user->name_en,
            [
                "name" => $user->name_en,
            ]
        );
        $email->setTemplateId(Config::get('constants.EMAIL_TEMPLATES.PASSWORD_RESET_SUCCESS'));
        $sendgrid = new \SendGrid(Config::get('constants.EMAIL_CONFIG.SENDGRID_API_KEY'));

        $response = $sendgrid->send($email);*/

            $data['message'] = 'Your Password has been updated successfully.';
            return $this->sendSuccessResponse($data);
        } catch (\Exception $e) {
            return $this->sendFailureResponse($e->getMessage());
        }
    }

    public function changePassword(Request $request)
    {
        try {
            $request->validate([
                'old_password' => 'required|string',
                'new_password' => 'required|string',
            ]);

            if (!(Hash::check($request->old_password, Auth::user()->password))) {
                return $this->sendFailureResponse('Old password is incorrect');
            } elseif (strcmp($request->old_password, $request->new_password) == 0) {
                return $this->sendFailureResponse('Old password and new password are same');
            } else {
                $user = Auth::user();
                $user_id = $user->id;

                $userData = User::where('id', $user_id)
                    ->update(array(
                        'password' => bcrypt($request->new_password)
                    ));
                if ($userData) {

                    /*$email = new \SendGrid\Mail\Mail();
                $email->setFrom(Config::get('constants.EMAIL_CONFIG.FROM_EMAIL'), Config::get('constants.EMAIL_CONFIG.FROM_NAME'));
                $email->addTo(
                    $user->email,
                    $user->name_en,
                    [
                        "name" => $user->name_en,
                    ]
                );
                $email->setTemplateId(Config::get('constants.EMAIL_TEMPLATES.PASSWORD_CHANGED'));
                $sendgrid = new \SendGrid(Config::get('constants.EMAIL_CONFIG.SENDGRID_API_KEY'));

                $response = $sendgrid->send($email);*/

                    $tokens = Auth::user()->tokens;
                    foreach ($tokens as $token) {
                        $token->revoke();
                        $token->delete();
                    }
                    $user = Auth::user();
                    $newtoken =  $user->createToken('MyApp')->accessToken;

                    $data['message'] = 'Your Password has been updated successfully.';
                    $data['token'] = $newtoken;
                    return $this->sendSuccessResponse($data);
                }
            }
        } catch (\Exception $e) {
            return $this->sendFailureResponse($e->getMessage());
        }
    }
}
