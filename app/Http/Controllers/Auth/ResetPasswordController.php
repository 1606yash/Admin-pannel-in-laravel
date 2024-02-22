<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\sendMail;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\ResetsPasswords;


use App\PasswordReset;

use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use Helpers;
use App\Models\User;
use Uuid;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Password;
use App\Notifications\PasswordResetRequest;
use App\Notifications\PasswordResetSuccess;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function sendPasswordLink(Request $request)
    {
        try {
            $validator = \Validator::make($request->all(), [
                'email' => [
                    'required'
                ],
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 'Fail', 'message' => $validator->errors()->first()]);
            }

            $user = User::where('email', $request->email)->first();
            if ($user) {
                $passwordReset = PasswordReset::updateOrCreate(
                    ['email' => $user->email],
                    [
                        'email' => $user->email,
                        // 'token' => Uuid::generate()->string
                        'token' => Str::random(60)
                    ]
                );
                $data = [
                    'first_name' => ($user->first_name ?? ""),
                    'email' => $user->email ?? null,
                    'link' => url('forgot-password/' . $passwordReset->token),
                    'mail_type' => 'forgot-password',
                ];
                $mailData = [
                    'data' => $data,
                ];
                $mailsend =  Mail::to($user->email)->send(new sendMail($data));
                //return redirect('password/reset')->with('message', 'A reset password link sent on your email id.');
                return response()->json(['status' => 'success', 'message' => 'A reset password link sent on your email id.']);
                print_r($mailsend);
                die;
                if ($mailsend) {
                }
            } else {
                // return redirect('password/reset')->with('error', 'Email not found');
                return response()->json(['status' => 'Fail', 'message' => 'Email not found']);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 'fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
        }
    }

    public function find($token)
    {

        $passwordReset = PasswordReset::where('token', $token)
            ->first();
        if (!$passwordReset) {
            return redirect('password/reset')->with('error', 'Link Expired');
        }

        if (Carbon::parse($passwordReset->updated_at)->addMinutes(720)->isPast()) {
            $passwordReset->delete();
            return redirect('password/reset')->with('error', 'Link Expired');
        }
        return view('auth/passwords/reset_password', ['token' => $token]);
    }

    public function reset_password(Request $request)
    {

        $validator = \Validator::make($request->all(), [
            'new_password' => 'required',
            'confirm_password' => 'required|same:new_password',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'Fail', 'message' => $validator->errors()->first()]);
        }

        $passwordReset = PasswordReset::where('token', $request->token)->first();

        if (!$passwordReset) {
            return response()->json(['status' => 'Fail', 'message' => 'Link Expired']);
        }

        $user = User::where('email', $passwordReset->email)->first();

        if (!$user) {
            return response()->json(['status' => 'Fail', 'message' => 'User not exists']);
        }

        $users = User::where('email', $passwordReset->email)->update([
            'password' => bcrypt($request->new_password),
            'password_updated_at' => now(),
        ]);
        if ($users) {
            $passwordReset->delete();
            return response()->json(['status' => 'success', 'message' => 'Password updated successfully.']);
        } else {
            return response()->json(['status' => 'Fail', 'message' => 'Something went wrong']);
        }
    }
}
