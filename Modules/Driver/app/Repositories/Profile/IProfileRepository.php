<?php

namespace Modules\Driver\Repositories\Profile;

use Modules\Driver\Enums\GenderEnum;
use Modules\Driver\Models\Profile;
use App\Enums\ServiceTypeEnum;

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

    /**
     * Update profile verification status
     *
     * @param string $id The profile's UUID
     * @param bool $isVerified The verification status
     * @param string|null $verificationStatus The verification status enum value
     * @return bool True if update was successful, false otherwise
     */
    public function updateVerificationStatus(string $id, bool $isVerified, ?string $verificationStatus = null): bool;

    /**
     * Update verified services for profile
     *
     * @param string $id The profile's UUID
     * @param array $verifiedServices Array of verified service types
     * @return bool True if update was successful, false otherwise
     */
    public function updateVerifiedServices(string $id, array $verifiedServices): bool;

    /**
     * Get available services for a profile based on verified vehicles
     *
     * @param string $id The profile's UUID
     * @return array Array of available service types
     */
    public function getAvailableServices(string $id): array;

    /**
     * Check if profile can provide a specific service
     *
     * @param string $id The profile's UUID
     * @param ServiceTypeEnum $service The service to check
     * @return bool True if service is available, false otherwise
     */
    public function canProvideService(string $id, ServiceTypeEnum $service): bool;
}
