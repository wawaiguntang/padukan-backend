<?php

namespace Modules\Customer\Services\Profile;

use Modules\Customer\Enums\GenderEnum;
use Modules\Customer\Models\Profile;

/**
 * Interface for Profile Service
 *
 * This interface defines the contract for profile business logic operations
 * in the customer module.
 */
interface IProfileService
{
    /**
     * Create a new profile for a user
     *
     * @param string $userId The user's UUID
     * @param array $data Profile data
     * @return Profile The created profile model
     */
    public function createProfile(string $userId, array $data): Profile;

    /**
     * Get profile by user ID
     *
     * @param string $userId The user's UUID
     * @return Profile|null The profile model if found, null otherwise
     */
    public function getProfileByUserId(string $userId): ?Profile;

    /**
     * Get profile by ID
     *
     * @param string $profileId The profile's UUID
     * @return Profile|null The profile model if found, null otherwise
     */
    public function getProfileById(string $profileId): ?Profile;

    /**
     * Update profile information
     *
     * @param string $userId The user's UUID
     * @param array $data Profile data to update
     * @return bool True if update was successful, false otherwise
     */
    public function updateProfile(string $userId, array $data): bool;

    /**
     * Delete a profile
     *
     * @param string $userId The user's UUID
     * @return bool True if deletion was successful, false otherwise
     */
    public function deleteProfile(string $userId): bool;

    /**
     * Update profile gender
     *
     * @param string $userId The user's UUID
     * @param GenderEnum $gender The new gender
     * @return bool True if update was successful, false otherwise
     */
    public function updateGender(string $userId, GenderEnum $gender): bool;

    /**
     * Check if user has a profile
     *
     * @param string $userId The user's UUID
     * @return bool True if profile exists, false otherwise
     */
    public function hasProfile(string $userId): bool;

    /**
     * Upload and update profile avatar
     *
     * @param string $userId The user's UUID
     * @param \Illuminate\Http\UploadedFile $avatarFile The avatar file to upload
     * @return array Returns array with avatar information including URL
     * @throws \Exception If upload fails or user has no profile
     */
    public function uploadAvatar(string $userId, \Illuminate\Http\UploadedFile $avatarFile): array;

    /**
     * Delete profile avatar
     *
     * @param string $userId The user's UUID
     * @return bool True if avatar was deleted successfully, false otherwise
     */
    public function deleteAvatar(string $userId): bool;

    /**
     * Update profile verification status
     *
     * @param string $userId The user's UUID
     * @param bool $isVerified Whether the profile is verified
     * @param string|null $verificationStatus The verification status enum value
     * @return bool True if update was successful, false otherwise
     */
    public function updateVerificationStatus(string $userId, bool $isVerified, ?string $verificationStatus = null): bool;

    /**
     * Get profile verification status with documents
     *
     * @param string $userId The user's UUID
     * @return array|null Verification status data or null if profile not found
     */
    public function getVerificationStatus(string $userId): ?array;

    /**
     * Resubmit profile verification documents
     *
     * @param string $userId The user's UUID
     * @param array $data Verification data including files and metadata
     * @return array|null Resubmission result or null if not allowed
     */
    public function resubmitVerification(string $userId, array $data): ?array;
}
