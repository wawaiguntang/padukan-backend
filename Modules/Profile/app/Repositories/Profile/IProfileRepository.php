<?php

namespace Modules\Profile\Repositories\Profile;

use Modules\Profile\Models\Profile;

/**
 * Interface for Profile Repository
 *
 * This interface defines the contract for profile data operations
 * in the profile module.
 */
interface IProfileRepository
{
    /**
     * Find a profile by user ID
     *
     * @param string $userId The user ID
     * @return Profile|null The profile model if found, null otherwise
     */
    public function findByUserId(string $userId): ?Profile;

    /**
     * Find a profile by ID
     *
     * @param string $id The profile ID
     * @return Profile|null The profile model if found, null otherwise
     */
    public function findById(string $id): ?Profile;

    /**
     * Create a new profile
     *
     * @param array $data Profile data containing:
     * - user_id: string - The user ID
     * - first_name?: string - First name (optional)
     * - last_name?: string - Last name (optional)
     * - avatar?: string - Avatar path (optional)
     * - gender?: string - Gender (optional)
     * - language?: string - Language preference (optional)
     * @return Profile The created profile model
     */
    public function create(array $data): Profile;

    /**
     * Update an existing profile
     *
     * @param string $id The profile ID
     * @param array $data Profile data to update (same structure as create)
     * @return Profile|null The updated profile model if successful, null otherwise
     */
    public function update(string $id, array $data): ?Profile;

    /**
     * Delete a profile
     *
     * @param string $id The profile ID
     * @return bool True if deletion was successful, false otherwise
     */
    public function delete(string $id): bool;

    /**
     * Check if a profile exists by user ID
     *
     * @param string $userId The user ID
     * @return bool True if profile exists, false otherwise
     */
    public function existsByUserId(string $userId): bool;
}