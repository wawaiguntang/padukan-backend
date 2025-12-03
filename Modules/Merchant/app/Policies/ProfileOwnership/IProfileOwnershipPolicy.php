<?php

namespace Modules\Merchant\Policies\ProfileOwnership;

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

    /**
     * Check if user can upload avatar
     */
    public function canUploadAvatar(string $userId, string $profileId): bool;

    /**
     * Check if user can delete avatar
     */
    public function canDeleteAvatar(string $userId, string $profileId): bool;

    /**
     * Check if user can submit profile verification
     */
    public function canSubmitVerification(string $userId, string $profileId): bool;

    /**
     * Check if user can resubmit profile verification
     */
    public function canResubmitVerification(string $userId, string $profileId): bool;
}
