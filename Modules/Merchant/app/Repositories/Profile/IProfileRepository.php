<?php

namespace Modules\Merchant\Repositories\Profile;

use Modules\Merchant\Enums\GenderEnum;
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
     * Find a profile by user ID
     *
     * @param string $userId The user's UUID
     * @return Profile|null The profile model if found, null otherwise
     */
    public function findByUserId(string $userId): ?Profile;

    /**
     * Find a profile by ID
     *
     * @param string $id The profile's UUID
     * @return Profile|null The profile model if found, null otherwise
     */
    public function findById(string $id): ?Profile;

    /**
     * Create a new profile
     *
     * @param array $data Profile data containing:
     * - user_id: string - User's UUID
     * - first_name?: string - User's first name (optional)
     * - last_name?: string - User's last name (optional)
     * - avatar?: string - Avatar URL (optional)
     * - gender?: GenderEnum - User's gender (optional)
     * - language?: string - Preferred language (optional)
     * @return Profile The created profile model
     */
    public function create(array $data): Profile;

    /**
     * Update an existing profile
     *
     * @param string $id The profile's UUID
     * @param array $data Profile data to update (same structure as create)
     * @return bool True if update was successful, false otherwise
     */
    public function update(string $id, array $data): bool;

    /**
     * Delete a profile
     *
     * @param string $id The profile's UUID
     * @return bool True if deletion was successful, false otherwise
     */
    public function delete(string $id): bool;

    /**
     * Check if a profile exists by user ID
     *
     * @param string $userId The user's UUID
     * @return bool True if profile exists, false otherwise
     */
    public function existsByUserId(string $userId): bool;

    /**
     * Update profile's gender
     *
     * @param string $id The profile's UUID
     * @param GenderEnum $gender The new gender
     * @return bool True if update was successful, false otherwise
     */
    public function updateGender(string $id, GenderEnum $gender): bool;
}
