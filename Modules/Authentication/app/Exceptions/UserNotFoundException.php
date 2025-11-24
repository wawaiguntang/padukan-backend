<?php

namespace Modules\Authentication\Exceptions;

use App\Exceptions\BaseException;

/**
 * Exception thrown when a user is not found
 */
class UserNotFoundException extends BaseException
{
    /**
     * Create a new UserNotFoundException instance
     *
     * @param string $messageKey The message key for frontend localization
     * @param array $context Additional context data
     */
    public function __construct(string $messageKey = 'auth.user.not_found', array $context = [])
    {
        parent::__construct($messageKey, $context, 'authentication', 404);
    }
}