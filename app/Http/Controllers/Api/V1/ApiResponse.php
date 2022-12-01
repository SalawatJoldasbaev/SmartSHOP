<?php

namespace App\Http\Controllers\Api\V1;

class ApiResponse
{
    public static function message($message = 'message', $code = 200)
    {
        return response([
            'message' => $message,
        ], $code);
    }

    public static function error(string $message = 'text', int $code = 200, $data = [])
    {
        return response([
            'successful' => false,
            'code' => $code,
            'message' => $message,
            'payload' => $data,
        ], $code);
    }

    public static function success(string $message = 'successful', int $code = 200, $data = [])
    {
        return response([
            'successful' => true,
            'code' => $code,
            'message' => $message,
            'payload' => $data,
        ], $code);
    }

    public static function data(bool $successful = false, string $message = 'text', array $payload = [], int $code = 200)
    {
        return response([
            'successful' => $successful,
            'code' => $code,
            'message' => $message,
            'payload' => $payload,
        ], $code);
    }
}
