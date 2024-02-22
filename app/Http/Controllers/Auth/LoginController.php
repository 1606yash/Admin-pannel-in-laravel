<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\ApiBaseController;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\User\Entities\User;
use Modules\Saas\Entities\Organization;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Handle user login.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postLogin(Request $request)
    {
        try {
            // Validate the incoming request data
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            // Prepare login credentials with additional condition for active users
            $credentials = [
                'email' => $request->input('email'),
                'password' => $request->input('password'),
                'is_active' => 1,
            ];

            // Attempt to authenticate the user
            if (Auth::attempt($credentials)) {
                $user = Auth::user();

                // Check if the user has the appropriate role for authorization
                if ($user->role_id == 1 || $user->role_id == 7) {
                    // Return a success response for authorized users
                    return response()->json(['status' => 'success', 'message' => 'Login successful.']);
                }

                // Return a failure response for unauthorized users
                return response()->json(['status' => 'Fail', 'message' => 'Oops! You are not authorized']);
            } else {
                // If authentication fails, check for inactive user status
                $user = User::where('email', $request->input('email'))->first();

                // If user exists but is not active, return an inactive account response
                if ($user && !$user->is_active) {
                    return response()->json([
                        'status' => 'Fail', 'message' => 'Oops! Your account is not active'
                    ]);
                }
            }
            // Return a generic failure response for invalid credentials
            return response()->json(['status' => 'Fail', 'message' => 'Oops! You have entered invalid credentials']);
        } catch (Exception $e) {
            return response()->json(['status' => 'fail', 'message' => trans('messages.SOMETHING_WENT_WRONG')]);
        }
    }

    /**
     * Log the user out of the application.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function logout(Request $request)
    {
        // Log the user out of the application
        Auth::logout();

        // Invalidate the user's session
        $request->session()->invalidate();

        // Regenerate the CSRF token to enhance security
        $request->session()->regenerateToken();

        // Redirect the user to the home page after logout
        return redirect('/');
    }
}
