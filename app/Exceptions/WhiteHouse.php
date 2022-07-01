<?php

namespace App\Exceptions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class WhiteHouse
{

    public static function generalResponse(int $errorCode, string $data)
    {
        if (($errorCode >= Response::HTTP_OK) and ($errorCode < Response::HTTP_MULTIPLE_CHOICES)) {
            return self::generalSuccessResponse($errorCode, $data);
        }

        return self::generalExceptionResponse($errorCode, $data);
    }



    public static function generalExceptionResponse(
        int $errorCode = Response::HTTP_INTERNAL_SERVER_ERROR,
        string $errorMessage = 'Server Error'
    ): JsonResponse {


        return response()->json([
            'error' => [
                'code'    => $errorCode,
                'message' => $errorMessage,
            ],
        ], $errorCode);
    }



    public static function generalSuccessResponse(int $errorCode, $data): JsonResponse
    {
        return response()->json([
            'data' => $data,
        ], $errorCode);
    }
}
