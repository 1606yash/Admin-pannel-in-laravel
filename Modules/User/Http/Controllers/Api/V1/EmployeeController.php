<?php

namespace Modules\User\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ApiBaseController;
use App\Models\User as ModelsUser;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Helpers;
use App\Models\UserAcademicDetail as ModelsUserAcademicDetail;
use App\Models\UserBankDetail as ModelsUserBankDetail;
use App\Models\UserLicenseDetail as ModelsUserLicenseDetail;
use App\Models\UserWorkExperience as ModelsUserWorkExperience;
use App\Models\Leave;
use App\Models\LeaveType;
use App\Models\Expense;
use App\Models\Attendance;

class EmployeeController extends ApiBaseController
{
  /**
     * @OA\Get(
     *     path="/api/v1/employees",
     *     tags={"Employee"},
     *     security={
     *      {"bearerAuth": {}},
     *     },
     *     description="API to get employees of di",
     *     operationId="indexad",
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\Schema(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function getEmployees(Request $request)
    {
        try {
            $userId = FacadesAuth::user()->id;
            $perPage = $request->input('per_page', 10);
            $currentPage = $request->input('page', 1);
            $skip = ($currentPage - 1) * $perPage;
            $filters = [
                'role_id' => $request->input('role_id'),
                'created_by' => $request->input('created_by'),
                'status' => $request->input('status'),
                'creation_date_range' => $request->input('creation_date_range'),
            ];
            $employees = ModelsUser::getEmployees($userId, $perPage, $skip, $filters);
            if ($employees->isEmpty()) {
                return $this->sendSuccessResponse($employees, 200, Config::get('constants.APIMESSAGES.NO_DATA_FOUND'));
            } else {
                return $this->sendSuccessResponse($employees, 200, Config::get('constants.APIMESSAGES.EMPLOYEES_RETRIVED_SUCCESSFULLY'));
            }
        } catch (\Exception $exception) {
            DB::rollback();
            return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.SOMETHING_WENT_WRONG'));
        }
    }

    public function AddEmployee(Request $request)
    {
        try {
            DB::beginTransaction();
            $validations = ModelsUser::addEmployeeValidationRules();
            $validator = Validator::make($request->all(),$validations, ModelsUser::$addEmployeeValidationMessages);
            if($validator->fails()) {
                $errors = $validator->errors();
                $errorsMsg = "";

                if ($errors->first()) {
                    $errorsMsg .= " " . $errors->first();
                }

                return $this->sendFailureResponse($errorsMsg);
            } else {
                $roleSlug = $request->role_slug;
                $userId = FacadesAuth::user()->id;
                $data = [];
                $data['state_id'] = $request->state_id;
                $data['district_id'] = $request->district_id;
                $data['role_id'] = $request->role_id;
                $data['reporting_manager_id'] = $request->reporting_manager_id ?? null;
                $data['first_name'] = $request->first_name;
                $data['middle_name'] = $request->middle_name ?? null;
                $data['last_name'] = $request->last_name;
                $data['gender'] = $request->gender;
                $data['dob'] = $request->dob;
                $data['phone_no'] = $request->phone_no;
                $data['email'] = $request->email ?? null;
                $data['pan_card_number'] = $request->pan_card_number;
                if ($request->hasFile('pan_image_path')) {
                    $folderName = 'pan_doc';
                    $attachmentPanCardUrl = Helpers::uploadAttachment($request->file('pan_image_path'), $folderName, time() . rand(100, 100000));
                    $data['pan_image_path'] = $attachmentPanCardUrl;
                }
                $data['adhar_number'] = $request->adhar_number;
                if ($request->hasFile('aadhar_image_path')) {
                    $folderName = 'aadhar_doc';
                    $attachmentAdharCardUrl = Helpers::uploadAttachment($request->file('aadhar_image_path'), $folderName, time() . rand(100, 100000));
                    $data['aadhar_image_path'] = $attachmentAdharCardUrl;
                }
                $data['joining_date'] = $request->joining_date;
                $data['address'] = $request->address ?? null;
                $createUser = ModelsUser::create($data);
                if ($createUser) {
                    $userId = $createUser->id;
                    $data['employee_id'] = Helpers::createEmployeeId($userId);
                    $userUpdate = ModelsUser::where('id', $userId)->first();
                    $userUpdate->employee_id = $data['employee_id'];
                    $userUpdate = $userUpdate->update();

                    $academicDetails = [];
                    $academicDetails['user_id'] = $userId;
                    $academicDetails['highest_qualification_id'] = $request->highest_qualification_id;
                    $academicDetails['year_of_completion'] = $request->year_of_completion ?? null;
                    $academicDetails['field_of_study_id'] = $request->field_of_study_id ?? null;

                    if ($request->hasFile('marksheet_file_path')) {
                        $folderName = 'marksheets';
                        $marksheetAttachmentUrl = Helpers::uploadAttachment($request->file('marksheet_file_path'), $folderName, time() . rand(100, 100000));
                        $academicDetails['marksheet_file_path'] = $marksheetAttachmentUrl;
                    }
                    $createUserAcademicDetails = ModelsUserAcademicDetail::create($academicDetails);

                    if (!empty($request->company_name)) {
                        $workExperienceDetails = [];
                        $workExperienceDetails['user_id'] = $userId;
                        $workExperienceDetails['company_name'] = $request->company_name;
                        $workExperienceDetails['designation'] = $request->designation;
                        $workExperienceDetails['location'] = $request->location;
                        $workExperienceDetails['end_date'] = $request->end_date;

                        if ($request->hasFile('past_experience_document')) {
                            $folderName = 'past_experience_document';
                            $workExperienceAttachmentUrl = Helpers::uploadAttachment($request->file('past_experience_document'), $folderName, time() . rand(100, 100000));
                            $workExperienceDetails['document_image_path'] = $workExperienceAttachmentUrl;
                        }

                        $createWorkExperienceDetails = ModelsUserWorkExperience::create($workExperienceDetails);
                    }

                    $bankDetails = [];
                    $bankDetails['user_id'] = $userId;
                    $bankDetails['bank_id'] = $request->bank_id;
                    $bankDetails['account_number'] = $request->account_number;
                    $bankDetails['ifsc_code'] = $request->ifsc_code;

                    if ($request->hasFile('bank_proof_image_path')) {
                        $folderName = 'bank_doc';
                        $bankAttachmentUrl = Helpers::uploadAttachment($request->file('bank_proof_image_path'), $folderName, time() . rand(100, 100000));
                        $bankDetails['bank_proof_image_path'] = $bankAttachmentUrl;
                    }

                    $createUserBankDetails = ModelsUserBankDetail::create($bankDetails);

                    if ($roleSlug == 'driver') {
                        $driverLicenseDetails = [];
                        $driverLicenseDetails['user_id'] = $userId;
                        $driverLicenseDetails['license_number'] = $request->license_number;
                        $driverLicenseDetails['dl_type_id'] = $request->dl_type_id;
                        $driverLicenseDetails['expiry_date'] = $request->expiry_date;

                        // upload driving license in AWS S3 bucket
                        if ($request->hasFile('license_image_path')) {
                            $folderName = 'dl_doc';
                            $driverLicenseAttachnmentURL = Helpers::uploadAttachment($request->file('license_image_path'), $folderName, time() . rand(100, 100000));
                            $driverLicenseDetails['license_image_path'] = $driverLicenseAttachnmentURL;
                        }
                        $createDriverLicenseDetails = ModelsUserLicenseDetail::create($driverLicenseDetails);
                    }
                    DB::commit();                                
                    return $this->sendSuccessResponse($createUser, 200, Config::get('constants.APIMESSAGES.EMPLOYEE_ADDED_SUCCESSFULLY'));
                } else {
                    DB::rollback();
                    return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.SOMETHING_WENT_WRONG'));
                }
            }
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.SOMETHING_WENT_WRONG'));
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.SOMETHING_WENT_WRONG'));
        }
    }

    public function getEmployeesLeaveRequests(Request $request)
    {
        try {
            $userId = FacadesAuth::user()->id;
            $perPage = $request->input('per_page', 10);
            $currentPage = $request->input('page', 1);
            $skip = ($currentPage - 1) * $perPage;
            $filters = [
                'year' => $request->input('year'),
                'month' => $request->input('month'),
                'role_id' => $request->input('role_id'),
                'status' => $request->input('status'),
                'creation_date_range' => $request->input('creation_date_range'),
            ];
            $employeesLeaveRequests = Leave::getEmployeesLeaveRequests($userId, $perPage, $skip, $filters);
            $leaveTypeFilters = LeaveType::select('id', 'name')->get();
            $leaveStatusFilters = Helpers::createFilter('LEAVE_STATUS', [Config::get('constants.LEAVE_STATUS.Pending'), Config::get('constants.LEAVE_STATUS.Approved'), Config::get('constants.LEAVE_STATUS.Rejected')]);
            $response = [
                'employee_leave_requests' => $employeesLeaveRequests,
                'filters' => [
                    'leave_status' => array_map('current', $leaveStatusFilters),
                    'leave_type' => $leaveTypeFilters
                ],
            ];
            if ($employeesLeaveRequests->isEmpty()) {
                return $this->sendSuccessResponse((object)[], 200, Config::get('constants.APIMESSAGES.NO_DATA_FOUND'));
            } else {
                return $this->sendSuccessResponse($response, 200, Config::get('constants.APIMESSAGES.EMPLOYEES_RETRIVED_SUCCESSFULLY'));
            }    
        } catch (\Exception $exception) {
            DB::rollback();
            return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.SOMETHING_WENT_WRONG'));
        }
    }

    public function updateEmployeeExpenseStatus(Request $request)
    {
        try {
            DB::beginTransaction();
            $expense = Expense::findOrFail($request->input('id'));
            $expense->claim_status = $request->input('status');
            $expense->rejection_reason = $request->input('rejection_reason');
            if($expense->save()) {
                DB::commit();                                
                return $this->sendSuccessResponse($expense, 200, Config::get('constants.APIMESSAGES.EMPLOYEE_EXPENSE_STATUS_UPDATED_SUCCESSFULLY'));
            } else {
                DB::rollback();
                return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.SOMETHING_WENT_WRONG'));
            }
        } catch (ModelNotFoundException $ex) {
            return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.SOMETHING_WENT_WRONG'));
        } catch (\Exception $exception) {
            DB::rollback();
            return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.SOMETHING_WENT_WRONG'));
        }
    }

    public function getEmployeeAttendance(Request $request)
    {
        try {
            $userId = $request->input('user_id');
            $perPage = $request->input('per_page', 10);
            $currentPage = $request->input('page', 1);
            $skip = ($currentPage - 1) * $perPage;
            $filters = [
                'year' => $request->input('year'),
                'month' => $request->input('month')
            ];
            $employeeAttendance = Attendance::getEmployeeAttendance($userId, $perPage, $skip, $filters);
            if ($employeeAttendance) {
                return $this->sendSuccessResponse($employeeAttendance, 200, Config::get('constants.APIMESSAGES.ATTENDANCE_RETRIVED_SUCCESSFULLY'));
            } else {
                return $this->sendSuccessResponse($employeeAttendance, 200, Config::get('constants.APIMESSAGES.NO_DATA_FOUND'));
            }
        } catch (\Exception $exception) {
            DB::rollback();
            return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.SOMETHING_WENT_WRONG'));
        }
    }
}
