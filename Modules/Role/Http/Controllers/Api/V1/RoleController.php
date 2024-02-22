<?php

namespace Modules\Role\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ApiBaseController;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;


class RoleController extends ApiBaseController
{
    public function __construct()
    {
    }

    public function allRoles(Request $request)
    {
        try {
            $userDetails = User::userDetails(Auth::user()->id ?? null);
            
            $allRoles = Role::allRoles($userDetails->role_name ?? null);
            if($allRoles){
                return $this->sendSuccessResponse($allRoles, 200, Config::get('constants.APIMESSAGES.ROLE_LISTING_RETRIVED_SUCCESSFULLY'));
            }
            return $this->sendSuccessResponse($allRoles, 200, Config::get('constants.APIMESSAGES.ROLE_LISTING_NOT_RETRIVED'));
        } catch (\Exception $exception) {
            return $this->sendFailureResponse($exception->getMessage());
        }
    }
}
