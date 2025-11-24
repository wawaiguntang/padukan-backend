<?php

namespace Modules\Authentication\Exceptions;

use App\Exceptions\BaseException;

/**
 * Exception thrown when a user account is inactive
 */
class UserInactiveException extends BaseException
{
    /**
     * Create a new UserInactiveException instance
     *
     * @param string $messageKey The message key for frontend localization
     * @param array $context Additional context data
     */
    public function __construct(string $messageKey = 'auth.user.inactive', array $context = [])
    {
        parent::__construct($messageKey, $context, 'authentication', 403);
    }
}
