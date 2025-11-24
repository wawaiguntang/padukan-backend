<?php

namespace Modules\Authentication\Exceptions;

use App\Exceptions\BaseException;

/**
 * Exception thrown when user credentials are invalid
 */
class InvalidCredentialsException extends BaseException
{
    /**
     * Create a new InvalidCredentialsException instance
     *
     * @param string $messageKey The message key for frontend localization
     * @param array $context Additional context data
     */
    public function __construct(string $messageKey = 'auth.invalid_credentials', array $context = [])
    {
        parent::__construct($messageKey, $context, 'authentication', 401);
    }
}