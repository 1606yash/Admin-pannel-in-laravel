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
use Illuminate\Support\Facades\Validator;


class RegisterController extends Controller
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
        $this->middleware('guest');
    }


    public function register(Request $request)
    {
        $newcontroller = new ApiBaseController();

        $data = $request->all();
        $validate =  Validator::make($data, [
            'first_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
        ]);
        if ($validate->fails()) {
            $error = $validate->errors()->first();
            return $newcontroller->sendFailureResponse($error, 400);
        }
        $createUser = new User();
        $createUser->first_name = $request->first_name;
        $createUser->email = $request->email;
        $createUser->password = bcrypt($request->password);
        $createUser->save();

        return $newcontroller->sendSuccessResponse($createUser, 200);
    }
}
