<?php

namespace Modules\Driver\Repositories\Profile;

use Modules\Driver\Models\Profile;

/**
 * Interface for Profile Repository
 *
 * This interface defines the contract for profile data operations
 * in the driver module.
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
     * Create a new profile
     *
     * @param array $data Profile data containing:
     * - user_id: string - User's UUID
     * - first_name?: string - User's first name (optional)
     * - last_name?: string - User's last name (optional)
     * - avatar?: string - Avatar URL (optional)
     * - gender?: GenderEnum - User's gender (optional)
     * - language?: string - Preferred language (optional)
     * - is_verified?: bool - Verification status (optional)
     * - verification_status?: VerificationStatusEnum - Verification status (optional)
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
     * Check if a profile exists by user ID
     *
     * @param string $userId The user's UUID
     * @return bool True if profile exists, false otherwise
     */
    public function existsByUserId(string $userId): bool;
}
