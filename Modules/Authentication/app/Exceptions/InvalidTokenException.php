<?php

namespace Modules\Authentication\Exceptions;

use App\Exceptions\BaseException;

/**
 * Exception thrown when token is invalid
 */
class InvalidTokenException extends BaseException
{
    /**
     * Create a new InvalidTokenException instance
     *
     * @param string $messageKey The message key for frontend localization
     * @param array $context Additional context data
     */
    public function __construct(string $messageKey = 'auth.token.invalid', array $context = [])
    {
        parent::__construct($messageKey, $context, 'authentication', 400);
    }
}