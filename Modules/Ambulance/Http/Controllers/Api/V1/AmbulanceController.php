<?php

namespace Modules\Ambulance\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ApiBaseController;
use Illuminate\Support\Facades\Config;
use App\Models\Ambulance;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Helpers;

class AmbulanceController extends ApiBaseController
{
   /**
     * @OA\Get(
     *     path="/api/v1/ambulances",
     *     tags={"Ambulance"},
     *     security={
     *      {"bearerAuth": {}},
     *     },
     *     description="API to get ambulances",
     *     operationId="indexad",
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\Schema(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function getAmbulances(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10);
            $currentPage = $request->input('page', 1);
            $skip = ($currentPage - 1) * $perPage;
            $search = $request->input('search');
            $filters = [
                'status' => $request->input('status', []),
                'station_location' => $request->input('station_location'),
                'service_location' => $request->input('service_location')
            ];
            $user = FacadesAuth::user();
            $districtId = $user->district_id;
            $ambulances = Ambulance::getAmbulancesByDistrictID($districtId, $perPage, $skip, $filters, $search);
            $statusFilters = Helpers::createFilter('AMBULANCE_STATUS', [Config::get('constants.AMBULANCE_STATUS.Running'), Config::get('constants.AMBULANCE_STATUS.Not Running')]);
            $response = [
                'ambulances' => $ambulances,
                'filters' => [
                    'status' => array_map('current', $statusFilters)
                ],
            ];   
            if ($ambulances->isEmpty()) {
                return $this->sendSuccessResponse($response, 200, Config::get('constants.APIMESSAGES.NO_DATA_FOUND'));
            } else {
                return $this->sendSuccessResponse($response, 200, Config::get('constants.APIMESSAGES.AMBULANCES_RETRIVED_SUCCESSFULLY'));
            }
        } catch (\Exception $exception) {
            DB::rollback();
            return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.SOMETHING_WENT_WRONG'));
        }
    }

    public function getAmbulanceDetails(Request $request)
    {
        try {
            $ambulanceDetails = Ambulance::ambulanceDetails($request->id);
            if (!$ambulanceDetails) {
                return $this->sendSuccessResponse((object)[], 200, Config::get('constants.APIMESSAGES.NO_DATA_FOUND'));
            } else {
                return $this->sendSuccessResponse($ambulanceDetails, 200, Config::get('constants.APIMESSAGES.AMBULANCES_DETAILS_RETRIVED_SUCCESSFULLY'));
            }
        } catch (\Exception $exception) {
            return $exception;
            DB::rollback();
            return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.SOMETHING_WENT_WRONG'));
        }
    }
}
