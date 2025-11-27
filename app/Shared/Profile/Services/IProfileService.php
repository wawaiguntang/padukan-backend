<?php

namespace App\Shared\Profile\Services;

use Modules\Profile\Models\Profile;

/**
 * Interface for Profile Service
 *
 * This interface defines the contract for profile business logic operations.
 */
interface IProfileService
{
    /**
     * Get or create a profile for a user
     *
     * @param string $userId The user ID
     * @return Profile The profile model
     */
    public function getOrCreateProfile(string $userId): Profile;

    /**
     * Update profile information
     *
     * @param string $userId The user ID
     * @param array $data Profile data to update
     * @return Profile The updated profile model
     */
    public function updateProfile(string $userId, array $data): Profile;

    /**
     * Get profile with all related data
     *
     * @param string $userId The user ID
     * @return Profile|null The profile with relationships loaded
     */
    public function getProfileWithRelations(string $userId): ?Profile;

    /**
     * Delete a user's profile
     *
     * @param string $userId The user ID
     * @return bool True if deletion was successful
     */
    public function deleteProfile(string $userId): bool;

    /**
     * Check if user has a profile
     *
     * @param string $userId The user ID
     * @return bool True if profile exists
     */
    public function hasProfile(string $userId): bool;
}