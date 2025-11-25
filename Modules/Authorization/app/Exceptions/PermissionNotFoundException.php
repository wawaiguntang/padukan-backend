<?php

namespace Modules\Authorization\Exceptions;

use App\Exceptions\BaseException;

/**
 * Exception thrown when a permission is not found
 */
class PermissionNotFoundException extends BaseException
{
    /**
     * Create a new PermissionNotFoundException instance
     *
     * @param string $messageKey The message key for frontend localization
     * @param array $context Additional context data
     */
    public function __construct(string $messageKey = 'permission.not_found', array $context = [])
    {
        parent::__construct($messageKey, $context, 'authorization', 404);
    }
}