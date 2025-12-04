<?php

namespace Modules\Merchant\Repositories\Profile;

use Modules\Merchant\Models\Profile;

/**
 * Interface for Profile Repository
 *
 * This interface defines the contract for profile data operations
 * in the merchant module.
 */
interface IProfileRepository
{
    /**
     * Create a new profile
     *
     * @param array $data Profile data
     * @return Profile The created profile model
     */
    public function create(array $data): Profile;

    /**
     * Find profile by user ID
     *
     * @param string $userId The user's UUID
     * @return Profile|null The profile model if found, null otherwise
     */
    public function findByUserId(string $userId): ?Profile;

    /**
     * Find profile by ID
     *
     * @param string $profileId The profile's UUID
     * @return Profile|null The profile model if found, null otherwise
     */
    public function findById(string $profileId): ?Profile;

    /**
     * Update profile by user ID
     *
     * @param string $userId The user's UUID
     * @param array $data Profile data to update
     * @return bool True if update was successful, false otherwise
     */
    public function updateByUserId(string $userId, array $data): bool;

    /**
     * Update profile by ID
     *
     * @param string $profileId The profile's UUID
     * @param array $data Profile data to update
     * @return bool True if update was successful, false otherwise
     */
    public function update(string $profileId, array $data): bool;

    /**
     * Delete profile by user ID
     *
     * @param string $userId The user's UUID
     * @return bool True if deletion was successful, false otherwise
     */
    public function deleteByUserId(string $userId): bool;

    /**
     * Delete profile by ID
     *
     * @param string $profileId The profile's UUID
     * @return bool True if deletion was successful, false otherwise
     */
    public function delete(string $profileId): bool;

    /**
     * Update profile gender
     *
     * @param string $profileId The profile's UUID
     * @param \Modules\Merchant\Enums\GenderEnum $gender The new gender
     * @return bool True if update was successful, false otherwise
     */
    public function updateGender(string $profileId, \Modules\Merchant\Enums\GenderEnum $gender): bool;

    /**
     * Check if user has a profile
     *
     * @param string $userId The user's UUID
     * @return bool True if profile exists, false otherwise
     */
    public function existsByUserId(string $userId): bool;

    /**
     * Get merchants count for a profile
     *
     * @param string $profileId The profile's UUID
     * @return int Number of merchants for this profile
     */
    public function countMerchantsByProfileId(string $profileId): int;
}
