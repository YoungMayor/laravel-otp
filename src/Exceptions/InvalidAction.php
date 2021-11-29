<?php

namespace YoungMayor\LaravelOtp\Exceptions;

use Exception;
use YoungMayor\LaravelOtp\Models\OneTimePin;

class InvalidAction extends Exception
{
    public function render($request)
    {
        $errorCode = OneTimePin::INVALID_ACTION_CODE;

        return response()->json([
            'status' => 500,
            'message' => "Failed to process OTP: {$errorCode}"
        ], 500);
    }
}
