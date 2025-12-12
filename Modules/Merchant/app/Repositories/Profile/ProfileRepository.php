<?php

namespace Modules\Merchant\Repositories\Profile;

use Modules\Merchant\Models\Profile;

/**
 * Profile Repository Implementation
 *
 * Handles profile data operations
 */
class ProfileRepository implements IProfileRepository
{
    public function __construct() {}

    /**
     * Create a new profile
     */
    public function create(array $data): Profile
    {
        return Profile::create($data);
    }

    /**
     * Find profile by user ID
     */
    public function findByUserId(string $userId): ?Profile
    {
        return Profile::where('user_id', $userId)->first();
    }

    /**
     * Find profile by ID
     */
    public function findById(string $profileId): ?Profile
    {
        return Profile::find($profileId);
    }

    /**
     * Update profile by user ID
     */
    public function updateByUserId(string $userId, array $data): bool
    {
        $profile = Profile::where('user_id', $userId)->first();

        if (!$profile) {
            return false;
        }

        return $profile->update($data);
    }

    /**
     * Update profile by ID
     */
    public function update(string $profileId, array $data): bool
    {
        $profile = Profile::find($profileId);

        if (!$profile) {
            return false;
        }

        return $profile->update($data);
    }

    /**
     * Delete profile by user ID
     */
    public function deleteByUserId(string $userId): bool
    {
        $profile = Profile::where('user_id', $userId)->first();

        if (!$profile) {
            return false;
        }

        return $profile->delete();
    }

    /**
     * Delete profile by ID
     */
    public function delete(string $profileId): bool
    {
        $profile = Profile::find($profileId);

        if (!$profile) {
            return false;
        }

        return $profile->delete();
    }

    /**
     * Update profile gender
     */
    public function updateGender(string $profileId, \Modules\Merchant\Enums\GenderEnum $gender): bool
    {
        return $this->update($profileId, ['gender' => $gender]);
    }

    /**
     * Check if user has a profile
     */
    public function existsByUserId(string $userId): bool
    {
        return Profile::where('user_id', $userId)->exists();
    }

    /**
     * Get merchants count for a profile
     */
    public function countMerchantsByProfileId(string $profileId): int
    {
        return Profile::find($profileId)?->merchants()->count() ?? 0;
    }
}
