<?php

namespace Modules\Authentication\Repositories\PasswordReset;

use Modules\Authentication\Models\PasswordResetToken;

/**
 * Interface for Password Reset Repository
 *
 * This interface defines the contract for password reset token operations
 * in the authentication module.
 */
interface IPasswordResetRepository
{
    /**
     * Create a new password reset token
     *
     * @param string $userId The user's UUID
     * @param string $token The password reset token
     * @param int $expiresInMinutes Token expiration time in minutes (default: 60)
     * @return PasswordResetToken The created password reset token model
     */
    public function createResetToken(string $userId, string $token, int $expiresInMinutes = 60): PasswordResetToken;

    /**
     * Find a valid (not used and not expired) password reset token
     *
     * @param string $userId The user's UUID
     * @param string $token The password reset token to validate
     * @return PasswordResetToken|null The password reset token if valid, null otherwise
     */
    public function findValidResetToken(string $userId, string $token): ?PasswordResetToken;

    /**
     * Mark a password reset token as used
     *
     * @param string $id The password reset token's UUID
     * @return bool True if marking was successful, false otherwise
     */
    public function markResetTokenUsed(string $id): bool;

    /**
     * Delete all expired password reset tokens
     *
     * @return int Number of deleted tokens
     */
    public function deleteExpiredResetTokens(): int;

    /**
     * Find password reset token by ID
     *
     * @param string $id The password reset token's UUID
     * @return PasswordResetToken|null The password reset token if found, null otherwise
     */
    public function findById(string $id): ?PasswordResetToken;

    /**
     * Delete a password reset token
     *
     * @param string $id The password reset token's UUID
     * @return bool True if deletion was successful, false otherwise
     */
    public function delete(string $id): bool;

    /**
     * Find valid password reset token by token string only
     *
     * @param string $token The password reset token
     * @return PasswordResetToken|null The password reset token if valid, null otherwise
     */
    public function findValidResetTokenByToken(string $token): ?PasswordResetToken;
}
