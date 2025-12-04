<?php

namespace Modules\Merchant\Exceptions;

use App\Exceptions\BaseException;

/**
 * Exception thrown when a profile is not found
 */
class ProfileNotFoundException extends BaseException
{
    /**
     * Create a new ProfileNotFoundException instance
     *
     * @param string $messageKey The message key for frontend localization
     * @param array $context Additional context data
     */
    public function __construct(string $messageKey = 'exception.profile.not_found', array $context = [])
    {
        parent::__construct($messageKey, $context, 'merchant', 404);
    }
}
