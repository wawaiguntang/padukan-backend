<?php

namespace Modules\Authorization\Exceptions;

use App\Exceptions\BaseException;

/**
 * Exception thrown when a policy is not found
 */
class PolicyNotFoundException extends BaseException
{
    /**
     * Create a new PolicyNotFoundException instance
     *
     * @param string $messageKey The message key for frontend localization
     * @param array $context Additional context data
     */
    public function __construct(string $messageKey = 'policy.not_found', array $context = [])
    {
        parent::__construct($messageKey, $context, 'authorization', 404);
    }
}