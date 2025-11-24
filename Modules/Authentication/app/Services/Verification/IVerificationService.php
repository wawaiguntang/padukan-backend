<?php

namespace Modules\Authentication\Services\Verification;

use Modules\Authentication\Enums\IdentifierType;

/**
 * Interface for Verification Service
 *
 * This interface defines the contract for OTP verification operations
 * including sending, resending, and validating OTP tokens.
 */
interface IVerificationService
{
    /**
     * Send OTP to user
     *
     * Generates a 6-digit numeric OTP and sends it to the user's
     * phone or email based on the identifier type. Includes rate limiting.
     *
     * @param string $userId The user's UUID
     * @param IdentifierType $type The identifier type (PHONE or EMAIL)
     * @return string Success message key
     * @throws \Modules\Authentication\Exceptions\RateLimitExceededException
     * @throws \Modules\Authentication\Exceptions\UserNotFoundException
     */
    public function sendOtp(string $userId, IdentifierType $type): string;

    /**
     * Resend OTP to user
     *
     * Resends the existing OTP or generates a new one if expired.
     * Includes rate limiting (same as sendOtp).
     *
     * @param string $userId The user's UUID
     * @param IdentifierType $type The identifier type (PHONE or EMAIL)
     * @return string Success message key
     * @throws \Modules\Authentication\Exceptions\RateLimitExceededException
     * @throws \Modules\Authentication\Exceptions\UserNotFoundException
     */
    public function resendOtp(string $userId, IdentifierType $type): string;

    /**
     * Validate OTP token
     *
     * Validates the provided OTP token for the user and identifier type.
     * Marks the token as used upon successful validation.
     *
     * @param string $userId The user's UUID
     * @param IdentifierType $type The identifier type (PHONE or EMAIL)
     * @param string $token The 6-digit OTP token
     * @return bool True if validation successful
     * @throws \Modules\Authentication\Exceptions\InvalidTokenException
     * @throws \Modules\Authentication\Exceptions\OtpExpiredException
     * @throws \Modules\Authentication\Exceptions\UserNotFoundException
     */
    public function validateOtp(string $userId, IdentifierType $type, string $token): bool;
}