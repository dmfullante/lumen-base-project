<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

class ResponseHelper
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Function : Common function to display error - JSON Response
     * @param array $data
     * @param null $message
     * @param int $statusCode default 400 error
     * @param string $status
     * @return JsonResponse
     */
    public static function error(
        $data,
        $message = null,
        int $statusCode = 422,
        $status = false
    ): JsonResponse {
        return response()->json([
            'status' => $status,
            'data' => $data,
            'message' => $message,
            'http_code' => $statusCode
        ], $statusCode);
    }

    /**
     * Function : Common function to display success - JSON Response
     * @param array $data
     * @param null $message
     * @param int $statusCode
     * @param string $status
     * @return JsonResponse
     */
    public static function success(
        $data,
        $message = null,
        int $statusCode = 200,
        $status = true
    ): JsonResponse {
        return response()->json([
           'status' => $status,
           'data' => $data,
           'message' => $message,
           'http_code' => $statusCode
        ], $statusCode);
    }
}
