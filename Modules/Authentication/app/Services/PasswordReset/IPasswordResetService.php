<?php

namespace Modules\Authentication\Services\PasswordReset;

/**
 * Interface for Password Reset Service
 *
 * This interface defines the contract for password reset operations
 * including requesting reset and resetting password with token.
 */
interface IPasswordResetService
{
    /**
     * Request password reset
     *
     * Generates a password reset token and sends it to the user's
     * email or phone. The user can use this token to reset their password.
     *
     * @param string $identifier User's phone number or email address
     * @return string Success message key
     * @throws \Modules\Authentication\Exceptions\UserNotFoundException
     */
    public function forgotPassword(string $identifier): string;

    /**
     * Reset password using token
     *
     * Validates the reset token and updates the user's password.
     * The token is marked as used after successful password reset.
     *
     * @param string $token The password reset token
     * @param string $newPassword The new password (will be hashed)
     * @return string Success message key
     * @throws \Modules\Authentication\Exceptions\InvalidTokenException
     * @throws \Modules\Authentication\Exceptions\UserNotFoundException
     */
    public function resetPassword(string $token, string $newPassword): string;
}