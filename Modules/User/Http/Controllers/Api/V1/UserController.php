<?php

namespace Modules\User\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ApiBaseController;
use App\Mail\sendMail;
use App\Models\Ambulance;
use App\Models\PermissionCategory;
use App\Models\ProfileInformation;
use App\Models\RolePermission;
use App\Models\User as ModelsUser;
use App\Models\UserDocument;
use App\PasswordReset;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File as FacadesFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Helpers;
use Illuminate\Support\Facades\Storage;
use App\Models\Salary;
use PDF;

class UserController extends ApiBaseController
{
    public function __construct()
    {
    }
    public function register(Request $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->all();
            $validate =  Validator::make($data, [
                'first_name' => ['required', 'string', 'max:255'],
                'email' => [
                    'required',
                    'string',
                    'max:255',
                    function ($attribute, $value, $fail) {
                        // Check if the phone number is unique and the user's deleted_at column is null.
                        $existingUser = ModelsUser::checkExistingUserEmail($value);

                        if ($existingUser) {
                            $fail("The $attribute has already been taken.");
                        }
                    },
                ],
                'phone_no' => [
                    'required',
                    'string',
                    'max:12',
                    function ($attribute, $value, $fail) {
                        // Check if the phone number is unique and the user's deleted_at column is null.
                        $existingUser = ModelsUser::checkExistingUserPhone($value);

                        if ($existingUser) {
                            $fail("The $attribute has already been taken.");
                        }
                    },
                ]

            ]);
            if ($validate->fails()) {
                $error = $validate->errors()->first();
                return $this->sendFailureResponse($error, 400);
            }
            // $data['password'] = bcrypt($data['password']);
            $data['password'] = bcrypt($data['password'] ?? "Parivaar@123");

            $data['created_by'] = FacadesAuth::user()->id ?? null;
            $data['updated_by'] = FacadesAuth::user()->id ?? null;
            $createUser = ModelsUser::create($data);
            $documentData = [];
            if ($createUser) {
                $data['user_id'] = $createUser->id ?? null;
                if (isset($data['profile']) && $data['profile'] != null && $data['profile']->isValid()) {
                    $fileName = time() . rand(100, 100000) . '.' . $request->profile->extension();
                    $data['profile']->move(base_path('public/ProfileDocuments'), $fileName);
                    $data['profile'] = $fileName;
                }
                if (isset($data['dl_doc']) && $data['dl_doc'] != null && $data['dl_doc']->isValid()) {
                    $fileName = time() . rand(100, 100000) . '.' . $request->dl_doc->extension();
                    $data['dl_doc']->move(base_path('public/ProfileDocuments'), $fileName);
                    $dlDocument['doc_url'] = $fileName;
                    $dlDocument['doc_type'] = 'Driving License';
                    $dlDocument['user_id'] = $createUser->id ?? null;
                    $dlDocument['doc_number'] = $request->dl_no ?? null;
                    $dlDocument['created_by'] = FacadesAuth::user()->id ?? null;
                    $dlDocument['updated_by'] = FacadesAuth::user()->id ?? null;
                    $documentData[] = $dlDocument;
                }
                if (isset($data['pan_doc']) && $data['pan_doc'] != null && $data['pan_doc']->isValid()) {
                    $fileName = time() . rand(100, 100000) . '.' . $request->pan_doc->extension();
                    $data['pan_doc']->move(base_path('public/ProfileDocuments'), $fileName);
                    $panDocument['doc_url'] = $fileName;
                    $panDocument['doc_type'] = 'PAN Card';
                    $panDocument['user_id'] = $createUser->id ?? null;
                    $panDocument['doc_number'] = $request->pan_no ?? null;
                    $panDocument['created_by'] = FacadesAuth::user()->id ?? null;
                    $panDocument['updated_by'] = FacadesAuth::user()->id ?? null;
                    $documentData[] = $panDocument;
                }
                if (isset($data['aadhar_doc']) && $data['aadhar_doc'] != null && $data['aadhar_doc']->isValid()) {
                    $fileName = time() . rand(100, 100000) . '.' . $request->aadhar_doc->extension();
                    $data['aadhar_doc']->move(base_path('public/ProfileDocuments'), $fileName);
                    $aadharDocument['doc_url'] = $fileName;
                    $aadharDocument['doc_type'] = 'Aadhar Card';
                    $aadharDocument['user_id'] = $createUser->id ?? null;
                    $aadharDocument['doc_number'] = $request->aadhar_no ?? null;
                    $aadharDocument['created_by'] = FacadesAuth::user()->id ?? null;
                    $aadharDocument['updated_by'] = FacadesAuth::user()->id ?? null;
                    $documentData[] = $aadharDocument;
                }
                if (isset($data['bank_doc']) && $data['bank_doc'] != null && $data['bank_doc']->isValid()) {
                    $fileName = time() . rand(100, 100000) . '.' . $request->bank_doc->extension();
                    $data['bank_doc']->move(base_path('public/ProfileDocuments'), $fileName);
                    $bankDocument['doc_url'] = $fileName;
                    $bankDocument['doc_type'] = 'Bank Details';
                    $bankDocument['user_id'] = $createUser->id ?? null;
                    $bankDocument['doc_number'] = $request->account_number ?? null;
                    $bankDocument['created_by'] = FacadesAuth::user()->id ?? null;
                    $bankDocument['updated_by'] = FacadesAuth::user()->id ?? null;
                    $documentData[] = $bankDocument;
                }
                if (isset($data['mark_doc']) && $data['mark_doc'] != null && $data['mark_doc']->isValid()) {
                    $fileName = time() . rand(100, 100000) . '.' . $request->mark_doc->extension();
                    $data['mark_doc']->move(base_path('public/ProfileDocuments'), $fileName);
                    $markDocument['doc_url'] = $fileName;
                    $markDocument['doc_type'] = 'Marksheet';
                    $markDocument['user_id'] = $createUser->id ?? null;
                    $markDocument['doc_number'] = null;
                    $markDocument['created_by'] = FacadesAuth::user()->id ?? null;
                    $markDocument['updated_by'] = FacadesAuth::user()->id ?? null;
                    $documentData[] = $markDocument;
                }
                if (isset($data['past_exp__doc']) && $data['past_exp__doc'] != null && $data['past_exp__doc']->isValid()) {
                    $fileName = time() . rand(100, 100000) . '.' . $request->past_exp__doc->extension();
                    $data['past_exp__doc']->move(base_path('public/ProfileDocuments'), $fileName);
                    $pastExpDocument['doc_url'] = $fileName;
                    $pastExpDocument['doc_type'] = 'Past Experience';
                    $pastExpDocument['user_id'] = $createUser->id ?? null;
                    $pastExpDocument['doc_number'] = null;
                    $pastExpDocument['created_by'] = FacadesAuth::user()->id ?? null;
                    $pastExpDocument['updated_by'] = FacadesAuth::user()->id ?? null;
                    $documentData[] = $pastExpDocument;
                }
                $saveProfileInfo = ProfileInformation::addProfileInformation($data);
                if ($saveProfileInfo) {
                    if ($documentData) {
                        $insertUserDocument = UserDocument::addUserDocument($documentData);
                    }
                    if ($saveProfileInfo) {
                        $notifyData = [
                            'data' => $saveProfileInfo,
                            'notification_title' => Config::get('constants.APIMESSAGES.USER_PROFILE_CREATED') ?? "",
                            'notification_description' => Config::get('constants.APIMESSAGES.USER_PROFILE_CREATED') ?? "",
                            'custom_key' => 'USER_PROFILE_CREATED',
                        ];
                        $createNotification = NotificationController::createNotification($notifyData);
                    }
                }
                DB::Commit();
                return $this->sendSuccessResponse($createUser, 200, Config::get('constants.APIMESSAGES.USER_PROFILE_CREATED'));
            }
            DB::rollback();
            return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.USER_PROFILE_NOT_CREATED'), 500);
        } catch (\Exception $exception) {
            DB::rollback();
            return $this->sendFailureResponse($exception->getMessage());
        }
    }

    public function getUserDetails(Request $request)
    {
        $request->user_id ? $id = $request->user_id : $id = FacadesAuth::user()->id;
        $userDetails = ModelsUser::userDetails($id);

        if ($userDetails) {
            // if ($userDetails->profile != null && $userDetails->profile != '') {
            //     $userDetails->profile = url('ProfileDocuments/') . '/' . $userDetails->profile;
            // }
            // $checkDocument = UserDocument::getUserDocument($id ?? null);
            // if ($checkDocument) {
            //     foreach ($checkDocument as $key => $value) {
            //         if ($value->doc_url != null && $value->doc_url != '') {
            //             if ($value->doc_type == 'Driving License') {
            //                 $value->dl_doc = url('ProfileDocuments/') . '/' . $value->doc_url;
            //             }
            //             if ($value->doc_type == 'PAN Card') {
            //                 $value->pan_doc = url('ProfileDocuments/') . '/' . $value->doc_url;
            //             }
            //             if ($value->doc_type == 'Aadhar Card') {
            //                 $value->aadhar_doc = url('ProfileDocuments/') . '/' . $value->doc_url;
            //             }
            //             if ($value->doc_type == 'Bank Details') {
            //                 $value->bank_doc = url('ProfileDocuments/') . '/' . $value->doc_url;
            //             }
            //             if ($value->doc_type == 'Marksheet') {
            //                 $value->mark_doc = url('ProfileDocuments/') . '/' . $value->doc_url;
            //             }
            //             if ($value->doc_type == 'Past Experience') {
            //                 $value->mark_doc = url('ProfileDocuments/') . '/' . $value->doc_url;
            //             } else {
            //                 $value->doc_url = url('ProfileDocuments/') . '/' . $value->doc_url;
            //             }
            //         }
            //     }
            //     $userDetails->profile_documents = $checkDocument;
            // }
            $responseData = $this->arrangeUserDetails($userDetails);
            $permissions = PermissionCategory::getPermissionCategoriesWithPermissions()->toArray();
            $filteredPermissions = [];
            foreach ($permissions as $key3 => $value3) {
                $filteredPermissions[$key3] = $value3;
                $filteredPermissions[$key3]['permissions'] = [];
                foreach ($value3['permissions'] as $key4 => $value4) {
                    $permissionStatus = RolePermission::where('role_id', $userDetails->role_id ?? null)
                        ->where('permission_id', $value4['id'])->first();
                    $status = ($permissionStatus !== null && $permissionStatus->id > 0) ? true : false;
                    $value4['status'] = $status;
                    if ($status) {
                        $filteredPermissions[$key3]['permissions'][] = $value4;
                    }
                }
            }
            $filteredPermissions = array_filter($filteredPermissions, function ($value) {
                return !empty($value['permissions']);
            });
            $userDetails->role_permissions = array_values($filteredPermissions);
            return $this->sendSuccessResponse($responseData, 200, Config::get('constants.APIMESSAGES.USER_DETAILS_RETRIVED_SUCCESSFULLY'));
        }
        return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.USER_DETAILS_NOT_RETRIVED'), 404);
    }

    public function arrangeUserDetails($data)
    {
        $profileInfo = [
            "id" => $data->user_id ?? null,
            "email" => $data->email ?? null,
            "phone_no" => $data->phone_no ?? null,
            "first_name" => $data->first_name    ?? null,
            "middle_name" => $data->middle_name  ?? null,
            "last_name" => $data->last_name    ?? null,
            "is_verified" => $data->is_verified ?? null,
            "is_active" => $data->is_active    ?? null,
            "role_id" => $data->role_id  ?? null,
            "gender" => $data->gender  ?? null,
            "dob" => $data->dob ?? null,
            "address" => $data->address ?? null,
            "ambulance_id" => $data->ambulance_id ?? null,
            "profile" => $data->profile ?? null,
            //"employee_id" => $data->employee_id ?? null,
            //"profile_information_id" => $data->profile_information_id ?? null,
            //"gender_type" => $data->gender_type ?? null,
            'pan_no' => $data->pan_card_number ?? null,
            'aadhar_no' => $data->adhar_number ?? null,
            'reporting_manager' => $data->reporting_manager ?? null,
            'joining_date' => $data->joining_date ?? null,
            'account_created_on' => Helpers::formatDate($data->created_at) ?? null,
            'account_created_by' => $data->account_created_by ?? null,
        ];
        $userRoles = [
            "role_name" => $data->role_name ?? null,
            'role_id' => $data->role_id ?? null,
            //"state_id" => $data->state_id ?? null,
            'state_name' => $data->state_name ?? null,
            'district_name' => $data->district_name ?? null,
        ];
        $educationQualifications = [
            //"highest_qualification_id" => $data->highest_qualification_id ?? null,
            'highest_qualification_type' => $data->highest_qualification_type ?? null,
            'field_of_study_id' => $data->field_of_study_id ?? null,
            'field_of_study_type' => $data->field_of_study_type ?? null,
            'year_of_completion' => $data->year_of_completion ?? null,
        ];
        $drivingLicenceDetails = [
            "dl_type" => $data->dl_type ?? null,
            'license_type_id' => $data->license_type_id ?? null,
            'license_expiry_date' => $data->license_expiry_date ?? null,
        ];
        $bankAccountDetails = [
            "bank_id" => $data->bank_id ?? null,
            'bank_name' => $data->bank_name ?? null,
            'account_number' => $data->account_number ?? null,
            'ifsc_code' => $data->ifsc_code ?? null,

        ];
        $ambulanceDetails = [
            //'ambulance_id' => $data->ambulance_id ?? null,
            //'sift_id' => $data->shift_id ?? null,
            'shift_type' => $data->shift_type ?? null,
            'ambulance_number' => $data->ambulance_number ?? null,
            'ambulance_shifts_id' => $data->ambulance_shifts_id ?? null,
            'shift_type_id' => $data->shift_type_id ?? null,
            'type' => $data->type ?? null,
        ];
        $employmentRecords = [
            'company_name' => 'Parivaar',
            'department_name' => $data->department_name ?? null,
            'role_name' => $data->role_name ?? null,
            'location' => $data->dictrict_name . '' . $data->state_name,
            'employment_start_date' => Helpers::formatDate($data->joining_date) ?? null,
            'employment_end_date' => $data->employment_end_date ?? null,
        ];
        $pastExperiences = [
            "last_company_name" => $data->last_company_name ?? null,
            "designation" => $data->designation ?? null,
            "location" => $data->location ?? null,
            "start_date" => $data->start_date ?? null,
            "end_date" => $data->end_date ?? null,
        ];
        $profileDocuments = [
            "aadhar_image_path" => $data->aadhar_image_path ?? null,
            "pan_image_path" => $data->pan_image_path ?? null,
            "bank_proof_image_path" => $data->bank_proof_image_path ?? null,
            "marksheet_file_path" => $data->marksheet_file_path ?? null,
            "license_image_path" => $data->license_image_path ?? null,
        ];

        return $responseData = [
            'profile_informations' => $profileInfo,
            'user_role' => $userRoles,
            'education_qualifications' => $educationQualifications,
            'driving_license_details' => $drivingLicenceDetails,
            'bank_account_details' => $bankAccountDetails,
            'ambulance_details' => $ambulanceDetails,
            'documents' => $profileDocuments,
            'employement_records' => $employmentRecords,
            'past_experience' => $pastExperiences,
            'salary_details' => [],
            'attendence_details' => [],
        ];
    }
    public function updateUserDetails(Request $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->all();
            $data['updated_by'] = FacadesAuth::user()->id ?? null;
            if (isset($request->user_id) && $request->user_id != null) {
                $userDetails = ModelsUser::getUserDetailsById($request->user_id ?? null);
                $userDetails->update($data);
            } else {
                $userDetails = ModelsUser::create($data);
            }
            $documentData = [];
            if ($userDetails) {
                $data['user_id'] = $userDetails->id ?? null;
                if (isset($data['profile']) && $data['profile'] != null && $data['profile']->isValid()) {
                    $fileName = time() . rand(100, 100000) . '.' . $request->profile->extension();
                    $data['profile']->move(base_path('public/ProfileDocuments'), $fileName);
                    $data['profile'] = $fileName;
                    $fetchProfileDoc = ProfileInformation::profileDoc($userDetails->id ?? null);
                    if ($fetchProfileDoc) {
                        if ($fetchProfileDoc->profile != null && $fetchProfileDoc->profile != '') {
                            $this->deleteprofileImages($fetchProfileDoc->profile ?? null);
                        }
                    }
                }
                if (isset($data['dl_doc']) && $data['dl_doc'] != null && $data['dl_doc']->isValid()) {
                    $fileName = time() . rand(100, 100000) . '.' . $request->dl_doc->extension();
                    $data['dl_doc']->move(base_path('public/ProfileDocuments'), $fileName);
                    $dlDocument['doc_url'] = $fileName;
                    $dlDocument['doc_type'] = 'Driving License';
                    $dlDocument['user_id'] = $userDetails->id ?? null;
                    $dlDocument['doc_number'] = $request->dl_no ?? null;
                    $dlDocument['created_by'] = FacadesAuth::user()->id ?? null;
                    $dlDocument['updated_by'] = FacadesAuth::user()->id ?? null;

                    $documentData[] = $dlDocument;
                    $fetchProfileDoc = UserDocument::getUserDocumentByDocType($userDetails->id ?? null, 'Driving License');
                    if ($fetchProfileDoc) {
                        if ($fetchProfileDoc->doc_url != null && $fetchProfileDoc->doc_url != '') {
                            $this->deleteprofileImages($fetchProfileDoc->doc_url ?? null);
                        }
                    }
                }
                if (isset($data['pan_doc']) && $data['pan_doc'] != null && $data['pan_doc']->isValid()) {
                    $fileName = time() . rand(100, 100000) . '.' . $request->pan_doc->extension();
                    $data['pan_doc']->move(base_path('public/ProfileDocuments'), $fileName);
                    $panDocument['doc_url'] = $fileName;
                    $panDocument['doc_type'] = 'PAN Card';
                    $panDocument['user_id'] = $userDetails->id ?? null;
                    $panDocument['doc_number'] = $request->pan_no ?? null;
                    $panDocument['created_by'] = FacadesAuth::user()->id ?? null;
                    $panDocument['updated_by'] = FacadesAuth::user()->id ?? null;
                    $documentData[] = $panDocument;
                    $fetchProfileDoc = UserDocument::getUserDocumentByDocType($userDetails->id ?? null, 'PAN Card');

                    if ($fetchProfileDoc) {
                        if ($fetchProfileDoc->doc_url != null && $fetchProfileDoc->doc_url != '') {
                            $this->deleteprofileImages($fetchProfileDoc->doc_url ?? null);
                        }
                    }
                }
                if (isset($data['aadhar_doc']) && $data['aadhar_doc'] != null && $data['aadhar_doc']->isValid()) {
                    $fileName = time() . rand(100, 100000) . '.' . $request->aadhar_doc->extension();
                    $data['aadhar_doc']->move(base_path('public/ProfileDocuments'), $fileName);
                    $aadharDocument['doc_url'] = $fileName;
                    $aadharDocument['doc_type'] = 'Aadhar Card';
                    $aadharDocument['user_id'] = $userDetails->id ?? null;
                    $aadharDocument['doc_number'] = $request->aadhar_no ?? null;
                    $aadharDocument['created_by'] = FacadesAuth::user()->id ?? null;
                    $aadharDocument['updated_by'] = FacadesAuth::user()->id ?? null;
                    $documentData[] = $aadharDocument;
                    $fetchProfileDoc = UserDocument::getUserDocumentByDocType($userDetails->id ?? null, 'Aadhar Card');
                    if ($fetchProfileDoc) {
                        if ($fetchProfileDoc->doc_url != null && $fetchProfileDoc->doc_url != '') {
                            $this->deleteprofileImages($fetchProfileDoc->doc_url ?? null);
                        }
                    }
                }
                if (isset($data['bank_doc']) && $data['bank_doc'] != null && $data['bank_doc']->isValid()) {
                    $fileName = time() . rand(100, 100000) . '.' . $request->bank_doc->extension();
                    $data['bank_doc']->move(base_path('public/ProfileDocuments'), $fileName);
                    $bankDocument['doc_url'] = $fileName;
                    $bankDocument['doc_type'] = 'Bank Details';
                    $bankDocument['user_id'] = $userDetails->id ?? null;
                    $bankDocument['doc_number'] = $request->account_number ?? null;
                    $bankDocument['created_by'] = FacadesAuth::user()->id ?? null;
                    $bankDocument['updated_by'] = FacadesAuth::user()->id ?? null;
                    $documentData[] = $bankDocument;
                  $fetchProfileDoc = UserDocument::getUserDocumentByDocType($userDetails->id ?? null, 'Bank Details');
                    if ($fetchProfileDoc) {
                        if ($fetchProfileDoc->doc_url != null && $fetchProfileDoc->doc_url != '') {
                            $this->deleteprofileImages($fetchProfileDoc->doc_url ?? null);
                        }
                    }
                }
                if (isset($data['mark_doc']) && $data['mark_doc'] != null && $data['mark_doc']->isValid()) {
                    $fileName = time() . rand(100, 100000) . '.' . $request->mark_doc->extension();
                    $data['mark_doc']->move(base_path('public/ProfileDocuments'), $fileName);
                    $markDocument['doc_url'] = $fileName;
                    $markDocument['doc_type'] = 'Marksheet';
                    $markDocument['user_id'] = $userDetails->id ?? null;
                    $markDocument['doc_number'] = null;
                    $markDocument['created_by'] = FacadesAuth::user()->id ?? null;
                    $markDocument['updated_by'] = FacadesAuth::user()->id ?? null;
                    $documentData[] = $markDocument;
                    $fetchProfileDoc = UserDocument::getUserDocumentByDocType($userDetails->id ?? null, 'Marksheet');
                    if ($fetchProfileDoc) {
                        if ($fetchProfileDoc->doc_url != null && $fetchProfileDoc->doc_url != '') {
                            $this->deleteprofileImages($fetchProfileDoc->doc_url ?? null);
                        }
                    }
                }
                if (isset($data['past_exp_doc']) && $data['past_exp_doc'] != null && $data['past_exp_doc']->isValid()) {
                    $fileName = time() . rand(100, 100000) . '.' . $request->past_exp_doc->extension();
                    $data['past_exp_doc']->move(base_path('public/ProfileDocuments'), $fileName);
                    $pastExpDocument['doc_url'] = $fileName;
                    $pastExpDocument['doc_type'] = 'Past Experience';
                    $pastExpDocument['user_id'] = $userDetails->id ?? null;
                    $pastExpDocument['doc_number'] = null;
                    $pastExpDocument['created_by'] = FacadesAuth::user()->id ?? null;
                    $pastExpDocument['updated_by'] = FacadesAuth::user()->id ?? null;
                    $documentData[] = $pastExpDocument;
                   $fetchProfileDoc = UserDocument::getUserDocumentByDocType($userDetails->id ?? null, 'Past Experience');
                    if ($fetchProfileDoc) {
                        if ($fetchProfileDoc->doc_url != null && $fetchProfileDoc->doc_url != '') {
                            $this->deleteprofileImages($fetchProfileDoc->doc_url ?? null);
                        }
                    }
                }

                $updateProfileInfo = ProfileInformation::updateProfileInformation($userDetails->id ?? null, $data);
                //check documentData size > 0 then update document
                if ($documentData != null && count($documentData) > 0) {
                    foreach ($documentData as $document) {
                        $updateUserDocument = UserDocument::updateUserDocument($userDetails->id ?? null, $document);
                    }
                }
                DB::Commit();
                return $this->sendSuccessResponse($userDetails, 200, Config::get('constants.APIMESSAGES.USER_PROFILE_UPDATED'));
            }
            DB::rollback();
            return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.USER_PROFILE_NOT_CREATED'), 500);
        } catch (\Exception $exception) {
            DB::rollback();
            return $this->sendFailureResponse($exception->getMessage());
        }
    }
    public function deleteprofileImages($imagePath = null)
    {
        if ($imagePath != null) {
            $imagePath = public_path('ProfileDocuments/' . $imagePath);
            $checkPathExist = FacadesFile::exists($imagePath);
            if ($checkPathExist) {
                unlink($imagePath);
            }
        }
    }
    public function userListing(Request $request)
    {
        try {

            $user = FacadesAuth::user();
            $filterData = $request->all();
            $userRoleId = FacadesAuth::user()->role_id ?? null;
            $userListing = ModelsUser::userListing($userRoleId ?? null, $filterData);
            if (!$userListing) {
                return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.USER_LISTING_NOT_RETRIVED'), 404);
            }
            return $this->sendSuccessResponse($userListing, 200, Config::get('constants.APIMESSAGES.USER_LISTING_RETRIVED_SUCCESSFULLY'));
        } catch (\Exception $exception) {
            return $this->sendFailureResponse($exception->getMessage());
        }
    }
    public function approveRejectUser(Request $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->all();
            $data['updated_by'] = FacadesAuth::user()->id;
            unset($data['user_id']);
            $updateUser = ModelsUser::updateUserDetails($request->user_id ?? null, $data);
            if ($updateUser) {
                if ($data['is_verified'] == 1) {
                    DB::commit();
                    return $this->sendSuccessResponse((object) [], 200, Config::get('constants.APIMESSAGES.USER_PROFILE_APPROVED'));
                } elseif ($data['is_verified'] == 0) {
                    DB::commit();
                    return $this->sendSuccessResponse((object) [], 200, Config::get('constants.APIMESSAGES.USER_PROFILE_REJECTED'));
                }
                DB::rollBack();
                return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.SOMETHING_WENT_WRONG'), 500);
            }
            DB::rollBack();
            return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.SOMETHING_WENT_WRONG'), 500);
        } catch (\Exception $exception) {
            DB::rollback();
            return $this->sendFailureResponse($exception->getMessage());
        }
    }
    public function deleteUser(Request $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->all();
            $data['updated_by'] = FacadesAuth::user()->id;
            $deleteUser = ModelsUser::deleteUser($request->user_id ?? null);
            if ($deleteUser) {
                DB::commit();
                return $this->sendSuccessResponse((object) [], 200, Config::get('constants.APIMESSAGES.USER_PROFILE_DELETED'));
            }
            DB::rollBack();
            return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.USER_PROFILE_NOT_DELETED'), 500);
        } catch (\Exception $exception) {
            DB::rollback();
            return $this->sendFailureResponse($exception->getMessage());
        }
    }
    public function getReportingManager(Request $request)
    {
        try {
            $data = $request->all();
            $reportingManager = ModelsUser::getReportingManagers($request);
            return $this->sendSuccessResponse($reportingManager, 200, Config::get('constants.APIMESSAGES.REPORTING_MANAGER_RETRIVED_SUCCESSFULLY'));
        } catch (\Exception $exception) {
            DB::rollback();
            return $this->sendFailureResponse($exception->getMessage());
        }
    }
    public function getAmbulanceMaster(Request $request)
    {
        try {
            $data = $request->all();
            $ambulances = Ambulance::getAmbulancesByDistrictID($data);
            return $this->sendSuccessResponse($ambulances, 200, Config::get('constants.APIMESSAGES.AMBULANCE_RETRIVED_SUCCESSFULLY'));
        } catch (\Exception $exception) {
            DB::rollback();
            return $this->sendFailureResponse($exception->getMessage());
        }
    }

    public function downloadPdf(Request $request)
    {
        try {
            if($request->type == 'payslip') {
                $data = Salary::findorfail($request->id);
            }
            $pdf = PDF::loadView('pdf', compact('data'));
            $filename = 'document.pdf';
            // Return the PDF as a response
            return $pdf->download($filename);  
        } catch (\Exception $exception) {
            DB::rollback();
            return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.SOMETHING_WENT_WRONG'));
        }
    }
}
