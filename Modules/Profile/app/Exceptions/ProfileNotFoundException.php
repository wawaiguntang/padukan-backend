<?php

namespace Modules\Profile\Exceptions;

use App\Exceptions\BaseException;

/**
 * Exception thrown when a profile is not found
 */
class ProfileNotFoundException extends BaseException
{
    /**
     * Create a new ProfileNotFoundException instance
     *
     * @param string $profileId The profile ID that was not found
     */
    public function __construct(string $profileId)
    {
        parent::__construct('validation.profile_not_found', ['id' => $profileId], 'profile', 404);
    }
}