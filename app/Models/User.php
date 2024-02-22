<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Passport\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use DB;
use App\Notifications\EmailVerificationMail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB as FacadesDB;

class User extends Authenticatable implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;
    use HasApiTokens, Notifiable, HasRoles;



    protected $table = 'users';
    protected $guard_name = 'web';


    protected $fillable = [
        'id', 'email', 'phone_no', 'password', 'password_updated_at', 'first_name', 'last_name', 'middle_name',
        'created_at', 'updated_at', 'deleted_at', 'created_by', 'dob', 'gender', 'joining_date', 'adhar_number', 'aadhar_image_path', 'pan_image_path', 'pan_card_number', 'profile_path', 'address', 'district_id', 'state_id', 'employee_id', 'is_active',
        'updated_by', 'is_verified', 'otp', 'otp_expiry', 'is_active', 'role_id', 'token', 'reject_remark', 'reporting_manager_id', 'device_token'
    ];

    protected $hidden = ['login_pin', 'password', 'password_updated_at'];

     /**
     * @name addEmployeeValidationRules
     * @desc validation rules
     * @return array
    */
    public static function addEmployeeValidationRules() {
        return [
            'state_id' => 'required|exists:states,id',
            'district_id' => 'required|exists:districts,id',
            'role_id' => 'required|exists:roles,id',
            'first_name' => 'required',
            'last_name' => 'required',
            'gender' => 'required',
            'dob' => 'required',
            'phone_no' => 'required',
            'pan_card_number' => 'required',
            'pan_image_path' => 'required',
            'adhar_number' => 'required',
            'aadhar_image_path' => 'required',
            'joining_date' => 'required',
            'highest_qualification_id' => 'required|exists:highest_qualification_types,id',
            'bank_id' => 'required|exists:bank_lists,id',
            'account_number' => 'required',
            'ifsc_code' => 'required',
            'bank_proof_image_path' => 'required',
        ];
    }

    /**
     * @leave Validation messages
     * @var array
    */
    public static $addEmployeeValidationMessages = [
        'state_id.required'=> 'State id is required',
        'state_id.exists' => 'Selected State is invalid.',
        'district_id.required' => 'District id is required',
        'district_id.exists' => 'Selected District is invalid.',
        'role_id.required' => 'Role id is required',
        'role_id.exists' => 'Selected Role is invalid.',
        'first_name.required'=> 'First Name is required',
        'last_name.required'=> 'Last Name is required',
        'gender.required'=> 'Gender is required',
        'dob.required'=> 'Dob is required',
        'phone_no.required'=> 'Mobile no is required',
        'pan_card_number.required'=> 'Pan card no is required',
        'pan_image_path.required'=> 'Pan card upload is required',
        'adhar_number.required'=> 'Adhar no is required',
        'aadhar_image_path.required'=> 'Adhar upload is required',
        'joining_date.required'=> 'Joining date is required',
        'highest_qualification_id.required'=> 'Highest qualification id is required',
        'bank_id.required'=> 'Bank id is required',
        'bank_id.exists' => 'Selected Bank is invalid.',
        'account_number.required'=> 'Account number is required',
        'ifsc_code.required'=> 'ifsc code is required',
        'bank_proof_image_path.required'=> 'Bank proof upload is required',
    ]; 

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public static function getUserPermissionsViaRoles($id)
    {
        $user = User::find($id);
        $permissions = $user->getPermissionsViaRoles();
        $permissionList = [];
        if ($permissions && count($permissions) > 0) {
            foreach ($permissions as $permission) {
                array_push($permissionList, trim($permission->name));
            }
        }
        return $permissionList;
    }

    public static function getUserRoles($id)
    {
        $roles = self::findOrfail($id)->getRoleNames();
        return $roles->toArray();
    }

    public function getFullNameAttribute()
    {
        return $this->name . ' ' . $this->last_name;
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = Crypt::encryptString($value);
    }

    public static function userDetails($userId = null)
    {
        $details = self::leftJoin('roles as r', 'r.id', '=', 'users.role_id')
            ->leftJoin('user_academic_details', 'users.id', '=', 'user_academic_details.user_id')
            ->leftJoin('user_bank_details', 'users.id', '=', 'user_bank_details.user_id')
            ->leftJoin('user_license_details', 'users.id', '=', 'user_license_details.user_id')
            ->leftJoin('ambulance_user_mappings', 'users.id', '=', 'ambulance_user_mappings.user_id')
            ->leftJoin('ambulance_shifts', 'ambulance_shifts.ambulance_mapping_id', '=', 'ambulance_user_mappings.id')
            ->leftJoin('departments as d', 'd.id', '=', 'r.department_id')
            ->leftJoin('states', 'states.id', '=', 'users.state_id')
            ->leftJoin('districts', 'districts.id', '=', 'users.district_id')
            ->leftJoin('highest_qualification_types', 'highest_qualification_types.id', '=', 'user_academic_details.highest_qualification_id')
            ->leftJoin('field_of_study_types', 'field_of_study_types.id', '=', 'user_academic_details.field_of_study_id')
            ->leftJoin('bank_lists', 'bank_lists.id', '=', 'user_bank_details.bank_id')
            ->leftJoin('dl_types', 'dl_types.id', '=', 'user_license_details.dl_type_id')
            ->leftJoin('shift_types', 'shift_types.id', '=', 'ambulance_user_mappings.shift_type_id')
            ->leftJoin('ambulances', 'ambulances.id', '=', 'ambulance_user_mappings.ambulance_id')
            ->leftJoin('user_work_experiences', 'users.id', '=', 'user_work_experiences.user_id')
            ->leftJoin('users as reporting_manager', 'reporting_manager.id', '=', 'users.created_by') // New join for the creator
            ->leftJoin('users as creator', 'creator.id', '=', 'users.created_by') // New join for the creator
            //->leftJoin('ambulances', 'ambulances.id', '=', 'pi.ambulance_id')
            //->leftJoin('shift_types', 'shift_types.id', '=', 'pi.shift_id')
            //->leftJoin('users as u2', 'u2.id', '=', 'pi.attendent_id')
            ->where('users.id', $userId)
            ->select(
                'users.id as user_id',
                'users.email',
                'users.phone_no',
                'users.first_name',
                'users.middle_name',
                'users.last_name',
                'users.gender as gender_type',
                'users.is_verified',
                'users.otp',
                'users.otp_expiry',
                'users.is_active',
                'users.role_id',
                'users.token',
                'users.dob',
                'users.address',
                'users.district_id',
                'users.joining_date',
                'users.adhar_number',
                'users.pan_card_number',
                'users.aadhar_image_path',
                'users.pan_image_path',
                'user_bank_details.id as bank_id',
                'user_bank_details.bank_proof_image_path',
                'user_academic_details.marksheet_file_path',
                'user_license_details.license_image_path',
                'r.role_name',
                \DB::raw("CONCAT(reporting_manager.first_name, ' ', reporting_manager.last_name) AS reporting_manager"),
                \DB::raw("CONCAT(creator.first_name, ' ', creator.last_name) AS account_created_by"),
                // 'pi.*',
                // 'pi.id as profile_information_id',
                'states.state_name',
                'districts.district_name',
                'highest_qualification_types.type as highest_qualification_type',
                'field_of_study_types.type as field_of_study_type',
                'user_academic_details.year_of_completion',
                //'gender_types.gender_name as gender_type',
                'dl_types.type as dl_type',
                'shift_types.shift_name as shift_type',
                'bank_lists.bank_name',
                'user_bank_details.account_number',
                'user_bank_details.ifsc_code',
                //'u2.first_name as attendant_name',
                //'ambulances.ambulance_number',
                'users.created_at',
                'd.department_name',
                'user_work_experiences.company_name as last_company_name',
                'user_work_experiences.designation',
                'user_work_experiences.location',
                'user_work_experiences.start_date',
                'user_work_experiences.end_date',
                'ambulances.ambulance_no',
                // 'pi.station_area',
                // 'pi.service_area',
                'ambulance_user_mappings.id as ambulance_shifts_id',
                'ambulance_user_mappings.shift_type_id',
                'ambulance_shifts.type',
            )
            ->first();
        return $details;
    }

    public static function updateUserDetails($userId = null, $data = [])
    {
        return self::where('id', $userId)->update($data);
    }
    public static function updateUser($data = [])
    {
        return self::where('phone_no', $data['phone_no'])->update($data);
    }
    public static function userListing($userRoleId = null, $filter = [])
    {
        $query =  self::leftjoin('roles as r', 'r.id', '=', 'users.role_id')
            ->leftjoin('profile_informations as pi', 'pi.user_id', '=', 'users.id')
            ->where('users.reporting_manager_id', [Auth::user()->id ?? null])
            ->whereNotIn('users.id', [Auth::user()->id ?? null])
            ->orderBy('users.id', 'desc')
            ->select([
                'users.id as user_id', 'users.email', 'users.phone_no', 'users.first_name', 'users.last_name', 'users.middle_name',
                'users.is_verified', 'users.is_active', 'users.role_id', 'users.created_at',
                'r.role_name', 'r.role_description',
                'pi.employee_id',
            ]);
        if (isset($filter) && count($filter) > 0) {
            if (isset($filter['status']) && $filter['status'] != "") {
                $query->where(function ($query) use ($filter) {
                    $query->where('users.is_active', $filter['status'] ?? null);
                });
            }
            if (isset($filter['search']) && $filter['search'] != "") {
                $query->where(function ($query) use ($filter) {
                    $query->where('first_name', 'like', '%' . $filter['search'] . '%');
                });
            }
            if (isset($filter['role_id']) && $filter['role_id'] != "") {
                $query->where(function ($query) use ($filter) {
                    $query->where('users.role_id', $filter['role_id'] ?? null);
                });
            }
            if (isset($filter['list_for']) && $filter['list_for'] == 'Approval') {
                $query->where(function ($query) use ($filter) {
                    $query->where('users.is_verified', '=', 0);
                });
            }
            if (isset($filter['date']) && $filter['date'] != "") {
                $currentDate = Carbon::now('UTC');
                if (isset($filter['date']) && $filter['date'] == "LastThreeMonth") {
                    $startDate = $currentDate->subMonths(3)->startOfDay();
                }
                if (isset($filter['date']) && $filter['date'] == "LastSixMonth") {
                    $startDate = $currentDate->subMonths(6)->startOfDay();
                }
                if (isset($filter['date']) && $filter['date'] == "CurrentYear") {
                    $startDate = Carbon::now()->startOfYear();
                }
                if (isset($filter['date']) && $filter['date'] == "LastYear") {
                    $startDate = Carbon::now()->subYear()->startOfYear();
                    $endDate = Carbon::now()->subYear()->endOfYear();
                }
                if (isset($filter['date']) && $filter['date'] == "ThisWeek") {
                    $startDate = Carbon::now()->startOfWeek()->startOfDay();
                    $endDate = Carbon::now()->endOfWeek()->endOfDay();
                }
                if (isset($filter['date']) && $filter['date'] == "LastWeek") {
                    $startDate = Carbon::now('UTC')->subWeek()->startOfWeek()->startOfDay();
                    $endDate = Carbon::now('UTC')->subWeek()->endOfWeek()->endOfDay();
                }
                if (isset($filter['date']) && $filter['date'] == "LastThreeYear") {
                    $startDate = Carbon::now()->subYears(3)->startOfYear();
                    $endDate = Carbon::now()->subYears(3)->endOfYear();
                }
                if (isset($startDate)) {
                    $query->where('users.created_at', '>=', $startDate->toDateTimeString());
                    if (isset($endDate)) {
                        $query->where('users.created_at', '<=', $endDate->toDateTimeString());
                    }
                }
            }
        }
        $countQuery = clone $query;
        $usersPerPage = $filter['limit'] ?? 10;
        $page = $filter['page'] ?? 1;
        $users = $query->paginate($usersPerPage, ['*'], 'page', $page);
        return $data = [
            'data' => $query->get(),
            'count' => $countQuery->count(),
        ];
    }
    public static function deleteUser($userId = null)
    {
        ProfileInformation::where('user_id', $userId)->delete();
        UserDocument::where('user_id', $userId)->delete();
        return self::where('id', $userId)->delete();
    }
    public static function approveUser($userId = null)
    {
        return self::where('id', $userId)->update(['is_verified' => 1]);
    }
    public static function getAllUsers()
    {
        return self::select(FacadesDB::raw('CONCAT(users.first_name, " ", users.last_name) as user_name'), 'users.*', 'roles.role_name', 'districts.district_name','users.id as user_id')->leftJoin('roles', 'users.role_id', '=', 'roles.id')->leftJoin('districts', 'users.district_id', '=', 'districts.id')->where('is_verified',1)->get();
    }

    public static function getDistrictAnchorList()
    {
        $user  = self::select(FacadesDB::raw('CONCAT(users.first_name, " ", users.last_name) as user_name'), 'users.phone_no', 'districts.district_name', FacadesDB::raw('CONCAT(u2.first_name, " ", u2.last_name) as created_by_name'), 'users.created_at as date_creation', 'users.is_active')
            ->leftJoin('districts', 'users.district_id', '=', 'districts.id')
            ->leftJoin('users as u2', 'users.created_by', '=', 'u2.id')
            ->leftJoin('roles', 'users.role_id', '=', 'roles.id')
            ->where('roles.role_name', 'LIKE', '%district anchor%')->get();
        return $user;
    }

    public static function getDriverList()
    {
        $user  = self::select(FacadesDB::raw('CONCAT(users.first_name, " ", users.last_name) as user_name'), 'users.phone_no', 'districts.district_name', FacadesDB::raw('CONCAT(u2.first_name, " ", u2.last_name) as created_by_name'), 'users.created_at as date_creation', 'users.is_active')
            ->leftJoin('districts', 'users.district_id', '=', 'districts.id')
            ->leftJoin('users as u2', 'users.created_by', '=', 'u2.id')
            ->leftJoin('roles', 'users.role_id', '=', 'roles.id')
            ->where('roles.role_name', 'LIKE', '%driver%')->get();
        return $user;
    }

    public static function getAttendantList()
    {
        $user  = self::select(FacadesDB::raw('CONCAT(users.first_name, " ", users.last_name) as user_name'), 'users.phone_no', 'districts.district_name', FacadesDB::raw('CONCAT(u2.first_name, " ", u2.last_name) as created_by_name'), 'users.created_at as date_creation', 'users.is_active')
            ->leftJoin('districts', 'users.district_id', '=', 'districts.id')
            ->leftJoin('users as u2', 'users.created_by', '=', 'u2.id')
            ->leftJoin('roles', 'users.role_id', '=', 'roles.id')
            ->where('roles.role_name', 'LIKE', '%attendant%')->get();
        return $user;
    }


    public static function getPendingRequestUserByRoleId($roleId)
    {
        $userData = ProfileInformation::leftJoin('users as u1', 'profile_informations.user_id', '=', 'u1.id')
            ->leftJoin('ambulances as ab', 'profile_informations.ambulance_id', '=', 'ab.id')
            ->leftJoin('users as u2', 'u1.created_by', '=', 'u2.id')
            ->leftJoin('districts as dr', 'profile_informations.district_id', '=', 'dr.id')
            ->where('u1.role_id', $roleId)
            ->where('u1.is_verified', null)
            ->select(['profile_informations.*', 'ab.ambulance_number', FacadesDB::raw('CONCAT(u2.first_name, " ", u2.last_name) as created_by_name'), 'dr.district_name', FacadesDB::raw('CONCAT(u1.first_name, " ", u1.last_name) as user_name'), 'u1.is_active as active_status'])
            ->get();

        return $userData;
    }
    public static function getReportingManagers($data)
    {
        $reportingManager = self::leftJoin('roles', 'users.role_id', '=', 'roles.approved_by_role_id')
            ->leftJoin('roles as rm', 'roles.approved_by_role_id', '=', 'rm.id');
        if ($data->role_id == "2") {
            $reportingManager = $reportingManager->where('roles.id', '=', $data->role_id ?? null);
        } else {
            $reportingManager = $reportingManager->leftJoin('profile_informations as pi', 'users.id', '=', 'pi.user_id')
                ->where('roles.id', '=', $data->role_id ?? null)
                ->where('pi.district_id', '=', $data->district_id ?? null);
        }
        $reportingManager = $reportingManager->select([
            'users.id as user_id', FacadesDB::raw("CONCAT(users.first_name, ' ', users.last_name) as user_name"),
            'rm.role_name as reporting_mager_role_name'
        ])
            ->orderBy('users.id')
            ->get();
        return $reportingManager;
    }

    public static function getNotifyUsers($userId)
    {
        return self::whereIn('users.role_id', [1, 7])->select(['users.id as user_id'])->get();
    }
    public static function addRejectReason($userId, $reason)
    {
        $user = self::where('id', $userId)->update(['reject_remark' => $reason]);
        // $deleteUser = User::deleteUser($userId);
        return $user;
    }
    public static function upperUser($userId)
    {
        $userData = self::where('role_id', $userId)->get();
        return $userData;
    }
    public static function getUserDetailsById($userId)
    {
        $query = self::select('users.id as user_id', 'users.state_id', 'users.first_name', 'users.middle_name', 'users.last_name',FacadesDB::raw('CONCAT(users.first_name, IF(users.middle_name IS NOT NULL AND users.middle_name <> "", CONCAT(" ", users.middle_name), ""), " ", users.last_name) as user_name'), 'users.employee_id','roles.role_name','users.gender','users.dob', 'users.phone_no', 'users.email', 'users.pan_card_number', 'users.adhar_number', 'users.address', 'districts.district_name', 'districts.id as district_id', 'states.state_name', FacadesDB::raw('CONCAT(reporting_manager.first_name, IF(reporting_manager.middle_name IS NOT NULL AND reporting_manager.middle_name <> "", CONCAT(" ", reporting_manager.middle_name), ""), " ", reporting_manager.last_name) as reporting_manager_name'), 'highest_qualification_types.type as highest_qualification_type', 'highest_qualification_types.id as highest_qualification_id', 'user_academic_details.year_of_completion', 'field_of_study_types.id as field_of_study_type_id', 'field_of_study_types.type as field_of_study_type', 'bank_lists.bank_name', 'bank_lists.id as bank_id', 'user_bank_details.account_number', 'user_bank_details.ifsc_code', 'users.joining_date', 'users.created_at as account_created_date', FacadesDB::raw('CONCAT(account_created_by.first_name, IF(account_created_by.middle_name IS NOT NULL AND account_created_by.middle_name <> "", CONCAT(" ", account_created_by.middle_name), ""), " ", account_created_by.last_name) as account_created_by_name'), 'users.aadhar_image_path', 'users.pan_image_path', 'user_bank_details.bank_proof_image_path', 'user_academic_details.marksheet_file_path', 'users.is_active', 'user_work_experiences.company_name as last_company_name', 'user_work_experiences.designation as last_company_designation', 'user_work_experiences.location as last_company_location', 'user_work_experiences.start_date as last_company_start_date', 'user_work_experiences.end_date as last_company_end_date', 'user_work_experiences.document_image_path as work_experience_document','users.profile_path','users.reporting_manager_id', 'users.role_id','users.is_verified','user_license_details.license_number', 'user_license_details.dl_type_id', 'user_license_details.expiry_date as license_expiry_date', 'user_license_details.license_image_path', 'dl_types.type as license_type_name')
        
        ->leftJoin('roles', 'users.role_id', '=', 'roles.id')
        ->leftJoin('districts', 'users.district_id', '=', 'districts.id')
        ->leftJoin('states', 'users.state_id', '=', 'states.id')
        ->leftJoin('users as reporting_manager', 'users.reporting_manager_id', '=', 'reporting_manager.id')
        ->leftJoin('users as account_created_by', 'users.created_by', '=', 'account_created_by.id')
        ->leftJoin('user_academic_details', 'users.id', '=', 'user_academic_details.user_id')
        ->leftJoin('highest_qualification_types', 'user_academic_details.highest_qualification_id', '=', 'highest_qualification_types.id')
        ->leftJoin('field_of_study_types', 'user_academic_details.field_of_study_id', '=', 'field_of_study_types.id')
        ->leftJoin('user_bank_details', 'users.id', '=', 'user_bank_details.user_id')
        ->leftJoin('user_license_details', 'users.id', '=', 'user_license_details.user_id')
        ->leftJoin('bank_lists', 'bank_lists.id', '=', 'user_bank_details.bank_id')
        ->leftJoin('user_work_experiences', 'users.id', '=', 'user_work_experiences.user_id')
        //->leftJoin('ambulance_user_mappings', 'users.id', '=', 'ambulance_user_mappings.user_id')
        //->leftJoin('ambulances', 'ambulances.id', '=', 'ambulance_user_mappings.ambulance_id')
        ->leftJoin('dl_types', 'dl_types.id', '=', 'user_license_details.dl_type_id')
        //->leftJoin('shift_types', 'shift_types.id', '=', 'ambulance_shifts.shift_type_id')
        ->where('users.id', $userId);

        $userData =  $query->first();
        return $userData;
    }
    public static function updateUserStatus($userId, $active_status)
    {
        $userData = self::where('id', $userId)->update(['is_active' => $active_status]);
        return $userData;
    }
    public static function checkExistingUserEmail($email = null)
    {
        return self::where('email', $email)->whereNull('deleted_at')->first();
    }
    public static function checkExistingUserPhone($phoneNo = null)
    {
        return self::where('phone_no', $phoneNo)->whereNull('deleted_at')->first();
    }
    public static function checkPhoneOtp($phoneNo = null, $otp = null)
    {
        return self::where('phone_no', $phoneNo)->where('otp', $otp)->first();
    }
    public static function updateOtp($phoneNo = null, $otp = null)
    {
        return self::where('phone_no', $phoneNo)->update(['otp' => $otp]);
    }
    public static function checkExistingUserPhoneNoWithId($phoneNo = null, $userId = null)
    {
        return self::where('phone_no', $phoneNo)->where('id', '!=', $userId)->first();
    }
    public static function checkExistingUserEmailWithId($email = null, $userId = null)
    {
        return self::where('email', $email)->where('id', '!=', $userId)->first();
    }
    public static function checkUserById($userId = null)
    {
        return self::where('id', $userId)->first();
    }

    public static function getDistrictAnchor($districtId)
    {
        // $getDistrictAnchor = self::selectRaw('CONCAT(first_name, " ", last_name) as district_anchor_name, user_id')
        // ->leftJoin('roles', 'users.role_id', '=', 'roles.id')
        // ->leftJoin('profile_informations as pi', 'pi.user_id', '=', 'users.id')
        // ->leftJoin('districts', 'districts.id', '=', 'pi.district_id')
        // ->where('role_name', 'LIKE', '%district anchor%')
        // ->where('districts.id', $districtId)
        //     ->get()->toArray();

        $getDistrictAnchor  = self::select(FacadesDB::raw('CONCAT(users.first_name, " ", users.last_name) as district_anchor_name'), 'users.id as user_id',)
            ->leftJoin('districts', 'users.district_id', '=', 'districts.id')
            ->leftJoin('roles', 'users.role_id', '=', 'roles.id')
            ->where('roles.role_slug', 'LIKE', '%district-anchor%')
            ->where('districts.id', $districtId)
            ->get()->toArray();

        return $getDistrictAnchor;
    }

    public static function getSubAdminAndSuperAdmin()
    {
        // $getSubAdminAndSuperAdmin = self::selectRaw('CONCAT(first_name, " ", last_name) as district_anchor_name, user_id')
        // ->leftJoin('roles', 'users.role_id', '=', 'roles.id')
        // ->leftJoin('profile_informations as pi', 'pi.user_id', '=', 'users.id')
        // ->leftJoin('districts', 'districts.id', '=', 'pi.district_id')
        // ->where('role_name', 'LIKE', '%admin%')
        //     ->get()->toArray();

        $getSubAdminAndSuperAdmin =
            self::select(FacadesDB::raw('CONCAT(users.first_name, " ", users.last_name) as district_anchor_name'), 'users.id as user_id',)
            ->leftJoin('districts', 'users.district_id', '=', 'districts.id')
            ->leftJoin('roles', 'users.role_id', '=', 'roles.id')
            ->where('roles.role_slug', 'LIKE', '%sub-admin%')
            ->orWhere('roles.role_slug', 'LIKE', '%super-admin%')
            ->get()->toArray();

        return $getSubAdminAndSuperAdmin;
    }

    public static function getHumanResource(){
        $getHumanResource =
            self::select(FacadesDB::raw('CONCAT(users.first_name, " ", users.last_name) as district_anchor_name'), 'users.id as user_id',)
            ->leftJoin('districts', 'users.district_id', '=', 'districts.id')
            ->leftJoin('roles', 'users.role_id', '=', 'roles.id')
            ->where('roles.role_slug', 'LIKE', '%human-resource%')
            ->get()->toArray();

        return $getHumanResource;
    }

    public static function getEmployees($userId, $perPage, $skip, $filters)
    {
        $query = self::where('reporting_manager_id', $userId);
        if (!empty($filters['role_id'])) {
            $query->where('role_id', $filters['role_id']);
        }
        
        if (!empty($filters['created_by'])) {
            $query->where('created_by', $filters['created_by']);
        }

        if (!empty($filters['status'])) {
            $query->where('is_active', $filters['status']);
        }
        
        if (!empty($filters['creation_date_range'])) {
            switch ($filters['creation_date_range']) {
                case 'last_3_months':
                    $query->where('created_at', '>=', now()->subMonths(3));
                    break;
                case 'last_6_months':
                    $query->where('created_at', '>=', now()->subMonths(6));
                    break;
                case 'current_year':
                    $query->whereYear('created_at', now()->year);
                    break;
                case 'last_year':
                    $query->whereYear('created_at', now()->subYear()->year);
                    break;
                case 'last_3_years':
                    $query->where('created_at', '>=', now()->subYears(3));
                    break;
                default:
                    break;
            }
        }
        
        $query->skip($skip)->take($perPage);
        return $query->get();
    }

    public static function checkDeviceToken($phoneNo, $deviceToken)
    {
        return self::where('phone_no', $phoneNo)->where('device_token', $deviceToken)->first();
    }
    

    

}
