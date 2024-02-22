<?php

namespace Modules\Master\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ApiBaseController;
use App\Models\Bank;
use App\Models\District;
use App\Models\DrivingLicenceType;
use App\Models\FieldOfStudy;
use App\Models\Gender;
use App\Models\HighestQualification;
use App\Models\Role;
use App\Models\ShiftType;
use App\Models\State;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use App\Models\LeaveType;
use App\Models\ResignationReason;
use App\Models\Vendor;
use App\Models\Ambulance;
use App\Models\ExpenseType;

class MasterController extends ApiBaseController
{
    public function __construct()
    {
    }

    public function allMaster(Request $request)
    {
        try {
           $states = State::allStates();
           $districts = District::allDistricts();
           $ambulanceShift = ShiftType::allShifts();
           $allBanks = Bank::allBanks();
           $DrivingLicenceType = DrivingLicenceType::allDrivingLicenceTypes();
           $fieldOfStudy = FieldOfStudy::allFieldOfStudyTypes();
           $highestQualification = HighestQualification::allHighestQualificationTypes();
           //$gender = Gender::allGenderTypes();
           $userDetails = User::userDetails(Auth::user()->id ?? null);
           $allRoles = Role::allRoles();
           $leaveTypes = LeaveType::allLeaveTypes();
           $resignationReasons = ResignationReason::allResignationReasons();
           $userData = User::with('role')->get();
           $vendors = Vendor::allVendors();
           $ambulances = Ambulance::getAllAmbulances();
           $expenseTypes = ExpenseType::get();

           $data = [
               'states' => $states,
               'districts' => $districts,
               //'ambulance_shift' => $ambulanceShift,
               'all_banks' => $allBanks,
               'license_type' => $DrivingLicenceType,
               'field_of_study' => $fieldOfStudy,
               'highest_qualification' => $highestQualification,
               //'gender' => $gender,
               'all_roles' => $allRoles,
               'leave_types' => $leaveTypes,
               'resignation_reasons' => $resignationReasons,
               'vendors' => $vendors,
               'expense_types' => $expenseTypes,
               'ambulances' => $ambulances,
               'users' => $userData
           ];
            return $this->sendSuccessResponse($data, 200, Config::get('constants.APIMESSAGES.MASTER_RETRIVED_SUCCESSFULLY'));
        } catch (\Exception $exception) {
            return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.SOMETHING_WENT_WRONG'));
        }
    }
}
