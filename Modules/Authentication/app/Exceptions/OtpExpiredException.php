<?php

namespace Modules\Authentication\Exceptions;

use App\Exceptions\BaseException;

/**
 * Exception thrown when OTP token has expired
 */
class OtpExpiredException extends BaseException
{
    /**
     * Create a new OtpExpiredException instance
     *
     * @param string $messageKey The message key for frontend localization
     * @param array $context Additional context data
     */
    public function __construct(string $messageKey = 'auth.otp.expired', array $context = [])
    {
        parent::__construct($messageKey, $context, 'authentication', 400);
    }
}