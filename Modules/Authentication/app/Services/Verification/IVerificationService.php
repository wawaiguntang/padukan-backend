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
     * phone or email based on the identifier. Includes rate limiting.
     *
     * @param string $identifier The user's email or phone number
     * @return string Success message key
     * @throws \Modules\Authentication\Exceptions\RateLimitExceededException
     * @throws \Modules\Authentication\Exceptions\UserNotFoundException
     */
    public function sendOtp(string $identifier): string;

    /**
     * Resend OTP to user
     *
     * Resends the existing OTP or generates a new one if expired.
     * Includes rate limiting (same as sendOtp).
     *
     * @param string $identifier The user's email or phone number
     * @return string Success message key
     * @throws \Modules\Authentication\Exceptions\RateLimitExceededException
     * @throws \Modules\Authentication\Exceptions\UserNotFoundException
     */
    public function resendOtp(string $identifier): string;

    /**
     * Validate OTP token
     *
     * Validates the provided OTP token for the user and identifier type.
     * Marks the token as used upon successful validation.
     *
     * @param string $identifier The user's email or phone number
     * @param IdentifierType $type The identifier type (PHONE or EMAIL)
     * @param string $token The 6-digit OTP token
     * @return bool True if validation successful
     * @throws \Modules\Authentication\Exceptions\InvalidTokenException
     * @throws \Modules\Authentication\Exceptions\OtpExpiredException
     * @throws \Modules\Authentication\Exceptions\UserNotFoundException
     */
    public function validateOtp(string $identifier, IdentifierType $type, string $token): bool;
}