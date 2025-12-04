<?php

namespace Modules\Customer\Exceptions;

use App\Exceptions\BaseException;

/**
 * Exception thrown when a profile already exists for a user
 */
class ProfileAlreadyExistsException extends BaseException
{
    /**
     * Create a new ProfileAlreadyExistsException instance
     *
     * @param string $messageKey The message key for frontend localization
     * @param array $context Additional context data
     */
    public function __construct(string $messageKey = 'exception.profile.already_exists', array $context = [])
    {
        parent::__construct($messageKey, $context, 'customer', 409);
    }
}
