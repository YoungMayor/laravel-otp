<?php

namespace YoungMayor\LaravelOtp\Traits;

use YoungMayor\LaravelOtp\Models\OneTimePin;

trait OTPActions
{
    public $supportsOTP = true;

    /**
     * Relationships
     */
    public function otps()
    {
        return $this->morphMany(OneTimePin::class, 'source');
    }


    /**
     * Methods
     */
    public function generateOTP(string $action, $payload = [])
    {
        return OneTimePin::generateOTP(
            $this,
            $action,
            $this->attributes[$this->otpEmailKey ?? 'email'],
            $payload,
        );
    }

    public function validateOTP($pin, $action)
    {
        return OneTimePin::validateOTP(
            $pin,
            $this,
            $action
        );
    }
}
