<?php

namespace Modules\Authentication\Repositories\Verification;

use Carbon\Carbon;
use Modules\Authentication\Enums\IdentifierType;
use Modules\Authentication\Models\VerificationToken;

/**
 * Interface for Verification Token Repository
 *
 * This interface defines the contract for OTP verification token operations
 * in the authentication module.
 */
interface IVerificationRepository
{
    /**
     * Create a new OTP verification token
     *
     * @param string $userId The user's UUID
     * @param IdentifierType $type The identifier type (EMAIL or PHONE)
     * @param string $token The 6-digit numeric OTP token
     * @param int $expiresInMinutes Token expiration time in minutes (default: 5)
     * @return VerificationToken The created verification token model
     */
    public function createOtp(string $userId, IdentifierType $type, string $token, int $expiresInMinutes = 5): VerificationToken;

    /**
     * Find a valid (not used and not expired) OTP token
     *
     * @param string $userId The user's UUID
     * @param IdentifierType $type The identifier type (EMAIL or PHONE)
     * @param string $token The OTP token to validate
     * @return VerificationToken|null The verification token if valid, null otherwise
     */
    public function findValidOtp(string $userId, IdentifierType $type, string $token): ?VerificationToken;

    /**
     * Mark an OTP token as used
     *
     * @param string $id The verification token's UUID
     * @return bool True if marking was successful, false otherwise
     */
    public function markOtpUsed(string $id): bool;

    /**
     * Delete all expired OTP tokens
     *
     * @return int Number of deleted tokens
     */
    public function deleteExpiredOtps(): int;

    /**
     * Check if OTP can be sent (rate limiting check)
     *
     * @param string $userId The user's UUID
     * @param IdentifierType $type The identifier type (EMAIL or PHONE)
     * @return bool True if OTP can be sent, false if rate limited
     */
    public function canSendOtp(string $userId, IdentifierType $type): bool;

    /**
     * Get the timestamp of the last OTP sent for a user and type
     *
     * @param string $userId The user's UUID
     * @param IdentifierType $type The identifier type (EMAIL or PHONE)
     * @return Carbon|null The timestamp if found, null otherwise
     */
    public function getLastOtpSentAt(string $userId, IdentifierType $type): ?Carbon;

    /**
     * Find verification token by ID
     *
     * @param string $id The verification token's UUID
     * @return VerificationToken|null The verification token if found, null otherwise
     */
    public function findById(string $id): ?VerificationToken;

    /**
     * Delete a verification token
     *
     * @param string $id The verification token's UUID
     * @return bool True if deletion was successful, false otherwise
     */
    public function delete(string $id): bool;
}
