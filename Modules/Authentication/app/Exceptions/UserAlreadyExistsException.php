<?php

namespace Modules\Authentication\Exceptions;

use App\Exceptions\BaseException;

/**
 * Exception thrown when user already exists
 */
class UserAlreadyExistsException extends BaseException
{
    /**
     * Create a new UserAlreadyExistsException instance
     *
     * @param string $messageKey The message key for frontend localization
     * @param array $context Additional context data
     */
    public function __construct(string $messageKey = 'auth.user.already_exists', array $context = [])
    {
        parent::__construct($messageKey, $context, 'authentication', 409);
    }
}