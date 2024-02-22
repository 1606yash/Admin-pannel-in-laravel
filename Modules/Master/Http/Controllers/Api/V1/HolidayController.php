<?php

namespace Modules\Master\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiBaseController;
use App\Models\Holiday;
use Illuminate\Support\Facades\Config;

class HolidayController extends ApiBaseController
{
    /**
     * @OA\Get(
     *     path="/api/v1/holidays",
     *     tags={"Holiday"},
     *     security={
     *      {"bearerAuth": {}},
     *     },
     *     description="API to get holidays",
     *     operationId="indexad",
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\Schema(ref="#/components/schemas/Error")
     *     )
     * )
    */
    public function getHolidays(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10);
            $currentPage = $request->input('page', 1);
            $skip = ($currentPage - 1) * $perPage;
            $filter = [
                'year' => $request->input('year')
            ];
            $holidays = Holiday::getHolidays($perPage, $skip, $filter);
            if ($holidays->isEmpty()) {
                return $this->sendSuccessResponse($holidays, 200, Config::get('constants.APIMESSAGES.NO_DATA_FOUND'));
            } else {
                return $this->sendSuccessResponse($holidays, 200, Config::get('constants.APIMESSAGES.HOLIDAYS_RETRIVED_SUCCESSFULLY'));
            }
        } catch (\Exception $exception) {
            return $this->sendFailureResponse(Config::get('constants.APIMESSAGES.SOMETHING_WENT_WRONG'));
        }
    }
}
