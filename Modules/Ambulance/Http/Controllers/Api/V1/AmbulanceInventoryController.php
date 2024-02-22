<?php

namespace Modules\Ambulance\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ApiBaseController;
use Illuminate\Support\Facades\Config;
use App\Models\AmbulanceInventory;
use App\Models\InventoryManagement;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AmbulanceInventoryController extends ApiBaseController
{
    /**
     * @OA\Get(
     *     path="/api/v1/ambulances",
     *     tags={"AmbulanceInventory"},
     *     security={
     *      {"bearerAuth": {}},
     *     },
     *     description="API to get ambulance inventories",
     *     operationId="indexad",
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\Schema(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function getAmbulanceInventories(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10);
            $currentPage = $request->input('page', 1);
            $skip = ($currentPage - 1) * $perPage;
            $ambulanceInvetories = AmbulanceInventory::getAmbulanceInventoriesByAmbulanceId($request->input('ambulance_id'), $perPage, $skip);
            if ($ambulanceInvetories->isEmpty()) {
                return $this->sendSuccessResponse($ambulanceInvetories, 200, Config::get('constants.APIMESSAGES.NO_DATA_FOUND'));
            } else {
                return $this->sendSuccessResponse($ambulanceInvetories, 200, Config::get('constants.APIMESSAGES.AMBULANCE_INVENTORIES_RETRIVED_SUCCESSFULLY'));
            }
        } catch (\Exception $exception) {
            DB::rollback();
            return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.SOMETHING_WENT_WRONG'));
        }
    }

    public function addAmbulanceInventory(Request $request)
    {
        try {
            DB::beginTransaction();
            $validations = AmbulanceInventory::validationRules();
            $validator = Validator::make($request->all(),$validations, AmbulanceInventory::$validationMessages);
            if($validator->fails()) {
                $errors = $validator->errors();
                $errorsMsg = "";

                if ($errors->first()) {
                    $errorsMsg .= " " . $errors->first();
                }

                return $this->sendFailureResponse($errorsMsg);
            } else {
                $ambulanceInventory = new AmbulanceInventory();
                $ambulanceInventory->ambulance_id = $request->ambulance_id;
                $ambulanceInventory->name = $request->name;
                $ambulanceInventory->unit_of_measurement = $request->unit_of_measurement;
                $ambulanceInventory->capacity = $request->capacity;
                $ambulanceInventory->quantity = $request->quantity;
                $ambulanceInventory->created_by = $request->user_id;
                $ambulanceInventory->date = date('Y-m-d');
                if($ambulanceInventory->save()) {
                    DB::commit();                                
                    return $this->sendSuccessResponse($ambulanceInventory, 200, Config::get('constants.APIMESSAGES.AMBULANCE_INVENTORY_ADDED_SUCCESSFULLY'));
                } else {
                    DB::rollback();
                    return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.SOMETHING_WENT_WRONG'));
                }
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.SOMETHING_WENT_WRONG'));
        }
    }

    public function updateAmbulanceInventory(Request $request)
    {
        try {
            DB::beginTransaction();
            $validations = AmbulanceInventory::validationUpdateRules();
            $validator = Validator::make($request->all(),$validations, AmbulanceInventory::$validationUpdateMessages);
            if($validator->fails()) {
                $errors = $validator->errors();
                $errorsMsg = "";

                if ($errors->first()) {
                    $errorsMsg .= " " . $errors->first();
                }

                return $this->sendFailureResponse($errorsMsg);
            } else {
                $inventoryId = $request->id;
                $changeType = $request->status;
                $quantity = $request->quantity;
                $updatedBy = $request->updated_by;
                $dateOfChange = $request->date;
                $ambulanceInventory = AmbulanceInventory::findOrFail($request->id);
                $previousQuantity = $ambulanceInventory->quantity;
                if($changeType == 'Added') {
                    $ambulanceInventory->quantity = $previousQuantity + $quantity;
                } else {
                    if(($previousQuantity - $quantity) < 0) {
                        DB::rollback();
                        return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.NOT_ENOUGH_CAPACITY'));
                    }
                    $ambulanceInventory->quantity = $previousQuantity - $quantity;
                }
                $ambulanceInventory->updated_by = $updatedBy;
                $ambulanceInventory->date = $dateOfChange;
                $ambulanceInventory->status = $changeType;
                $updatedinventoryData = $ambulanceInventory->update();
                if ($updatedinventoryData) {
                    $data = [];
                    $data['inventory_id'] = $inventoryId;
                    $data['type'] = $changeType;
                    $data['quantity'] = $quantity;
                    $data['unit_of_measurement'] = $ambulanceInventory->unit_of_measurement;
                    $data['created_by'] = $updatedBy;
                    $data['date'] = $dateOfChange;
                    $createInventoryManagement = InventoryManagement::create($data);
                    if($createInventoryManagement) {
                        DB::commit();                                
                        return $this->sendSuccessResponse($ambulanceInventory, 200, Config::get('constants.APIMESSAGES.AMBULANCE_INVENTORY_UPDATED_SUCCESSFULLY'));            
                    } else {
                        DB::rollback();
                        return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.SOMETHING_WENT_WRONG'));            
                    }
                } else {
                    DB::rollback();
                    return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.SOMETHING_WENT_WRONG'));            
                }
            }
        } catch (ModelNotFoundException $ex) {
            return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.NO_DATA_FOUND'));
        } catch (\Exception $exception) {
            return $exception;
            DB::rollback();
            return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.SOMETHING_WENT_WRONG'));
        }
    }

    public function getAmbulanceInventoriesById(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10);
            $currentPage = $request->input('page', 1);
            $skip = ($currentPage - 1) * $perPage;
            $ambulanceInvetoriesById = AmbulanceInventory::getAmbulanceInventoriesById($request->id, $perPage, $skip);
            $currentData = AmbulanceInventory::findOrFail($request->id);
            $response = [
                'previous_data' => $ambulanceInvetoriesById,
                'current_data' => $currentData,
            ];  
            if (empty($ambulanceInvetoriesById)) {
                return $this->sendSuccessResponse($response, 200, Config::get('constants.APIMESSAGES.NO_DATA_FOUND'));
            } else {
                return $this->sendSuccessResponse($response, 200, Config::get('constants.APIMESSAGES.AMBULANCE_INVENTORIES_RETRIVED_SUCCESSFULLY'));
            }
        } catch (ModelNotFoundException $ex) {
            return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.NO_DATA_FOUND'));
        } catch (\Exception $exception) {
            DB::rollback();
            return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.SOMETHING_WENT_WRONG'));
        }
    }
}
