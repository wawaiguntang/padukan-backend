<?php

namespace Modules\Customer\Policies\ProfileOwnership;

interface IProfileOwnershipPolicy
{
    /**
     * Check if user owns the profile
     */
    public function ownsProfile(string $userId, string $profileId): bool;
}
