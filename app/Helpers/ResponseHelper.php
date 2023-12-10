<?php

use Illuminate\Http\JsonResponse;

if (!function_exists('apiResponse')) {
    /**
     * Create a standardized JSON response for API endpoints.
     *
     * This helper function simplifies the creation of JSON responses by standardizing
     * the structure of the response. It ensures consistent response formatting across
     * different API endpoints. The function structures the response with a message and
     * any associated data, along with an HTTP status code.
     *
     * @param int $code The HTTP status code for the response, default is 200.
     * @param string|null $message The message to be included in the response.
     *                             This can be used for providing summary or description
     *                             of the response or error details.
     * @param array|object|null $data The data to be included in the response. This
     *                                could be any type of data like an array, an object,
     *                                or null if there's no data to return.
     * @return \Illuminate\Http\JsonResponse Returns an instance of JsonResponse
     *                                       containing the formatted response data.
     */
    function apiResponse(int $code = 200 , ?string $message, array|object|null $data = []) : JsonResponse
    {
        return response()->json([
            'message' => $message,
            'data' => $data,
        ], $code);
    }
}
