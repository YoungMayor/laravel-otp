<?php

namespace YoungMayor\LaravelOtp\Models;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Crypt;
use YoungMayor\LaravelOtp\Exceptions\InvalidAction;
use YoungMayor\LaravelOtp\Exceptions\InvalidSource;
use YoungMayor\LaravelOtp\Notifications\OTPGeneratedNotification;

class OneTimePin extends Model
{
    use HasFactory, Notifiable;

    protected $guarded = [];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public const INVALID_ACTION_CODE = "IAC-228466";
    public const INVALID_SOURCE_CODE = "ISC-768723";

    /**
     * Relationships
     */
    public function source()
    {
        return $this->morphTo();
    }

    /**
     * Accessors
     */
    public function getPayloadAttribute($value)
    {
        try {
            return Crypt::decrypt($value);
        } catch (DecryptException $e) {
            report($e);
            return [];
        }
    }

    public function getIsExpiredAttribute()
    {
        return $this->expires_at < now();
    }

    public function getPropertiesAttribute()
    {
        return self::getActionProperties($this->action, true);
    }

    /**
     * Methods
     */
    protected static function validateParameters(
        Model $source,
        string $action
    ) {
        if (
            !in_array(
                $action,
                array_keys(config('laravel-otp.actions'))
            )
        ) {
            throw new InvalidAction();
        }

        if (!$source->supportsOTP) {
            throw new InvalidSource();
        }
    }

    public static function generateOTP(
        Model $source,
        string $action,
        string $email,
        $payload = null
    ) {
        self::validateParameters($source, $action);

        $properties = self::getActionProperties($action, true);

        $pin = self::createPin($properties['length']);

        $oneTimePin = self::updateOrCreate([
            'action' => $action,
            'source_type' => get_class($source),
            'source_id' => $source->getKey(),
        ], [
            'pin' => $pin,
            'email' => $email,
            'payload' => $payload ? Crypt::encrypt($payload) : null,
            'expires_at' => now()->addMinutes($properties['decay'])
        ]);

        $oneTimePin->notify(new OTPGeneratedNotification($oneTimePin));

        return $oneTimePin;
    }

    public static function validateOTP(
        string $pin,
        Model $source,
        string $action
    ) {
        self::validateParameters($source, $action);

        return $source->otps->where('action', $action)->where('pin', $pin)->first();
    }

    public function refreshOTP()
    {
        return self::generateOTP(
            $this->source,
            $this->action,
            $this->email,
            $this->payload
        );
    }

    private static function getActionProperties($action, $override = false)
    {
        @$props = config("laravel-otp.actions")[$action];

        if (!$props) {
            throw new InvalidAction();
        }

        $default = $override ? [
            'subject' => 'OTP',
            'greeting' => 'Greetings Chief',
            'message' => 'Your OTP Code',
            'decay' => 30,
            'length' => 6
        ] : [];

        return array_merge($default, $props);
    }

    private static function createPin(int $length)
    {
        $length = $length > 9 || $length < 4 ? 6 : $length;

        $arr = array_fill(1, $length, 9);

        return rand(
            (int) implode('', array_keys($arr)),
            (int) implode('', $arr)
        );
    }
}
