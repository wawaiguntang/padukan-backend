<?php

namespace Modules\Authentication\Services\PasswordReset;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Modules\Authentication\Exceptions\InvalidTokenException;
use Modules\Authentication\Exceptions\UserNotFoundException;
use Modules\Authentication\Repositories\PasswordReset\IPasswordResetRepository;
use Modules\Authentication\Repositories\User\IUserRepository;

/**
 * Password Reset Service Implementation
 *
 * This class handles password reset operations including
 * requesting reset tokens and resetting passwords.
 */
class PasswordResetService implements IPasswordResetService
{
    /**
     * The password reset repository instance
     *
     * @var IPasswordResetRepository
     */
    protected IPasswordResetRepository $passwordResetRepository;

    /**
     * The user repository instance
     *
     * @var IUserRepository
     */
    protected IUserRepository $userRepository;

    /**
     * Constructor
     *
     * @param IPasswordResetRepository $passwordResetRepository The password reset repository instance
     * @param IUserRepository $userRepository The user repository instance
     */
    public function __construct(IPasswordResetRepository $passwordResetRepository, IUserRepository $userRepository)
    {
        $this->passwordResetRepository = $passwordResetRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function forgotPassword(string $identifier): string
    {
        // Find user by identifier
        $user = $this->userRepository->findByIdentifier($identifier);

        if (!$user) {
            throw new UserNotFoundException('auth.user.not_found', ['identifier' => $identifier]);
        }

        // Generate secure reset token
        $token = $this->generateResetToken();

        // Create password reset record
        $this->passwordResetRepository->createResetToken($user->id, $token);

        // Send reset token to user (placeholder implementation)
        $this->sendResetTokenToUser($user, $token);

        return 'auth.password_reset.sent';
    }

    /**
     * {@inheritDoc}
     */
    public function resetPassword(string $token, string $newPassword): string
    {
        // Validate password strength
        $this->validatePasswordStrength($newPassword);

        // Find valid reset token
        $resetToken = $this->findValidResetTokenByToken($token);

        if (!$resetToken) {
            throw new InvalidTokenException('auth.password_reset.invalid_token');
        }

        // Get user
        $user = $this->userRepository->findById($resetToken->user_id);

        if (!$user) {
            throw new UserNotFoundException('auth.user.not_found', ['id' => $resetToken->user_id]);
        }

        // Update password
        $this->userRepository->update($user->id, [
            'password' => Hash::make($newPassword),
        ]);

        // Mark token as used
        $this->passwordResetRepository->markResetTokenUsed($resetToken->id);

        return 'auth.password_reset.success';
    }

    /**
     * Find valid reset token by token string
     *
     * This is a helper method to find the reset token and associated user
     * for password reset operations.
     *
     * @param string $token The reset token
     * @return mixed|null The reset token model or null
     */
    protected function findValidResetTokenByToken(string $token)
    {
        return $this->passwordResetRepository->findValidResetTokenByToken($token);
    }

    /**
     * Generate a secure reset token
     *
     * @return string The generated token
     */
    protected function generateResetToken(): string
    {
        return Str::random(64); // 64 character random string
    }

    /**
     * Validate password strength
     *
     * @param string $password The password to validate
     * @return void
     * @throws \InvalidArgumentException
     */
    protected function validatePasswordStrength(string $password): void
    {
        if (strlen($password) < 8) {
            throw new \InvalidArgumentException('Password must be at least 8 characters long');
        }

        if (!preg_match('/[A-Z]/', $password)) {
            throw new \InvalidArgumentException('Password must contain at least one uppercase letter');
        }

        if (!preg_match('/[a-z]/', $password)) {
            throw new \InvalidArgumentException('Password must contain at least one lowercase letter');
        }

        if (!preg_match('/[0-9]/', $password)) {
            throw new \InvalidArgumentException('Password must contain at least one number');
        }
    }

    /**
     * Send reset token to user
     *
     * This is a placeholder implementation. In production, this should
     * send the reset token via email or SMS with a proper reset link.
     *
     * @param mixed $user The user model
     * @param string $token The reset token
     * @return void
     */
    protected function sendResetTokenToUser($user, string $token): void
    {
        // Placeholder implementation
        // In production, integrate with email/SMS services

        $identifier = $user->email ?: $user->phone;
        $type = $user->email ? 'email' : 'phone';

        // For now, just log the token (remove in production)
        \Illuminate\Support\Facades\Log::info("Password reset token sent to {$type}: {$identifier}", [
            'user_id' => $user->id,
            'token' => $token, // Remove in production
        ]);

        // TODO: Implement actual email/SMS sending
        // Example:
        // $resetLink = url("/password/reset?token={$token}");
        // $this->emailService->send($user->email, 'Password Reset', "Reset your password: {$resetLink}");
    }
}