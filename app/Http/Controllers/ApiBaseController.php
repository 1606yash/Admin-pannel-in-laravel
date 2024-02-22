<?php

namespace App\Http\Controllers;

use OpenApi as OA;

class ApiBaseController extends Controller
{

    /**
     * @OA\Info(
     *      version="1.0.0",
     *      title="L5 OpenApi",
     *      description="L5 Swagger OpenApi description"
     * )
     * @OA\Server(
     *      url=L5_SWAGGER_CONST_HOST,
     *      description="Demo API Server"
     * )
     * @OA\SecurityScheme(
     *   securityScheme="bearerAuth",
     *   type="http",
     *   scheme="bearer"
     * )
     *
     */

/**
 * @OA\Get(
 *     path="/",
 *     description="Home page",
 *     @OA\Response(response="default", description="Welcome page")
 * )
 */

    public function sendSuccessResponse($data = [], $statusCode = 200, $message = '' , $token = '')
    {

        if (is_array($data) && count($data) == 0) {
            $data = (object) $data;
        }
        $response = [
            'status' => $statusCode,
            'message' => $message,
            'data' => $data
        ];
        if ($token && $token != '') {
            return response()->json($response, $statusCode)->header('token', $token);
        }
        return response()->json($response, $statusCode);
    }

    /*
     * function for send failure response
     */
    public function sendFailureResponse($message = 'Something went wrong.', $code = 422)
    {
        $response = [
            'error' => $message,
        ];

        return response($response, $code);
    }
}
