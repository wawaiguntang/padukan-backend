<?php

namespace Modules\Authorization\Exceptions;

use App\Exceptions\BaseException;

/**
 * Exception thrown when a role is not found
 */
class RoleNotFoundException extends BaseException
{
    /**
     * Create a new RoleNotFoundException instance
     *
     * @param string $messageKey The message key for frontend localization
     * @param array $context Additional context data
     */
    public function __construct(string $messageKey = 'role.not_found', array $context = [])
    {
        parent::__construct($messageKey, $context, 'authorization', 404);
    }
}