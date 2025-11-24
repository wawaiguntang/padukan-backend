<?php

namespace Modules\Authentication\Services\Verification;

use Illuminate\Support\Facades\Log;
use Modules\Authentication\Enums\IdentifierType;
use Modules\Authentication\Exceptions\InvalidTokenException;
use Modules\Authentication\Exceptions\OtpExpiredException;
use Modules\Authentication\Exceptions\RateLimitExceededException;
use Modules\Authentication\Exceptions\UserNotFoundException;
use Modules\Authentication\Models\VerificationToken;
use Modules\Authentication\Repositories\User\IUserRepository;
use Modules\Authentication\Repositories\Verification\IVerificationRepository;

/**
 * Verification Service Implementation
 *
 * This class handles OTP verification operations including
 * sending, resending, and validating OTP tokens with rate limiting.
 */
class VerificationService implements IVerificationService
{
    /**
     * The verification repository instance
     *
     * @var IVerificationRepository
     */
    protected IVerificationRepository $verificationRepository;

    /**
     * The user repository instance
     *
     * @var IUserRepository
     */
    protected IUserRepository $userRepository;

    /**
     * Constructor
     *
     * @param IVerificationRepository $verificationRepository The verification repository instance
     * @param IUserRepository $userRepository The user repository instance
     */
    public function __construct(IVerificationRepository $verificationRepository, IUserRepository $userRepository)
    {
        $this->verificationRepository = $verificationRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function sendOtp(string $userId, IdentifierType $type): string
    {
        // Verify user exists
        $user = $this->userRepository->findById($userId);
        if (!$user) {
            throw new UserNotFoundException('auth.user.not_found', ['id' => $userId]);
        }

        // Check rate limiting
        if (!$this->verificationRepository->canSendOtp($userId, $type)) {
            throw new RateLimitExceededException('auth.otp.rate_limit_exceeded');
        }

        // Generate 6-digit OTP
        $otp = $this->generateOtp();

        // Create OTP record
        $this->verificationRepository->createOtp($userId, $type, $otp);

        // Send OTP via SMS/Email (placeholder implementation)
        $this->sendOtpToUser($user, $type, $otp);

        return 'auth.otp.sent';
    }

    /**
     * {@inheritDoc}
     */
    public function resendOtp(string $userId, IdentifierType $type): string
    {
        // Verify user exists
        $user = $this->userRepository->findById($userId);
        if (!$user) {
            throw new UserNotFoundException('auth.user.not_found', ['id' => $userId]);
        }

        // Check rate limiting
        if (!$this->verificationRepository->canSendOtp($userId, $type)) {
            throw new RateLimitExceededException('auth.otp.rate_limit_exceeded');
        }

        // Generate 6-digit OTP
        $otp = $this->generateOtp();

        // Create OTP record
        $this->verificationRepository->createOtp($userId, $type, $otp);

        // Send OTP via SMS/Email (placeholder implementation)
        $this->sendOtpToUser($user, $type, $otp);

        return 'auth.otp.resent';
    }

    /**
     * {@inheritDoc}
     */
    public function validateOtp(string $userId, IdentifierType $type, string $token): bool
    {
        // Verify user exists
        $user = $this->userRepository->findById($userId);
        if (!$user) {
            throw new UserNotFoundException('auth.user.not_found', ['id' => $userId]);
        }

        // Validate token format (6 digits)
        if (!preg_match('/^\d{6}$/', $token)) {
            throw new InvalidTokenException('auth.otp.invalid_format');
        }

        // Find OTP (including potentially expired ones)
        $otp = VerificationToken::where('user_id', $userId)
            ->where('type', $type)
            ->where('token', $token)
            ->where('is_used', false)
            ->first();

        if (!$otp) {
            throw new InvalidTokenException('auth.otp.invalid');
        }

        if ($otp->expires_at->isPast()) {
            throw new OtpExpiredException('auth.otp.expired');
        }

        // Mark OTP as used
        $this->verificationRepository->markOtpUsed($otp->id);

        return true;
    }

    /**
     * Generate a 6-digit OTP
     *
     * @return string The generated OTP
     */
    protected function generateOtp(): string
    {
        return str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Send OTP to user via SMS or Email
     *
     * This is a placeholder implementation. In production, this should
     * integrate with actual SMS/Email services.
     *
     * @param mixed $user The user model
     * @param IdentifierType $type The identifier type
     * @param string $otp The OTP token
     * @return void
     */
    protected function sendOtpToUser($user, IdentifierType $type, string $otp): void
    {
        // Placeholder implementation
        // In production, integrate with SMS/Email services

        $identifier = $type === IdentifierType::PHONE ? $user->phone : $user->email;

        // For now, just log the OTP (remove in production)
        Log::info("OTP sent to {$type->value}: {$identifier}", [
            'user_id' => $user->id,
            'otp' => $otp, // Remove in production
            'type' => $type->value,
        ]);

        // TODO: Implement actual SMS/Email sending
        // Example:
        // if ($type === IdentifierType::PHONE) {
        //     $this->smsService->send($user->phone, "Your OTP is: {$otp}");
        // } else {
        //     $this->emailService->send($user->email, 'OTP Verification', "Your OTP is: {$otp}");
        // }
    }
}