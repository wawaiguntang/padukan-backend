<?php

namespace Modules\Profile\Exceptions;

use App\Exceptions\BaseException;

/**
 * Exception thrown when a driver profile is not found
 */
class DriverProfileNotFoundException extends BaseException
{
    /**
     * Create a new DriverProfileNotFoundException instance
     *
     * @param string $userId The user ID that was not found
     */
    public function __construct(string $userId)
    {
        parent::__construct('messages.driver_profile_not_found', ['user_id' => $userId], 'profile', 404);
    }
}