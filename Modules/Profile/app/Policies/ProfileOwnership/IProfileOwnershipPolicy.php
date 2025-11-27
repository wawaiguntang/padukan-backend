<?php

namespace Modules\Profile\Policies\ProfileOwnership;

interface IProfileOwnershipPolicy
{
    /**
     * Check if user owns the profile
     */
    public function ownsProfile(string $userId, string $profileId): bool;

    /**
     * Check if user can access profile data
     */
    public function canAccessProfile(string $userId, string $profileId): bool;

    /**
     * Check if user can modify profile data
     */
    public function canModifyProfile(string $userId, string $profileId): bool;
}