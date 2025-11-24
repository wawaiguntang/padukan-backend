<?php

namespace Modules\Authentication\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Authentication\Enums\IdentifierType;
use Modules\Authentication\Repositories\Verification\IVerificationRepository;
use Symfony\Component\HttpFoundation\Response;

/**
 * Throttle OTP Middleware
 *
 * Rate limits OTP sending requests to prevent abuse
 */
class ThrottleOtp
{
    /**
     * The verification repository instance
     *
     * @var IVerificationRepository
     */
    protected IVerificationRepository $verificationRepository;

    /**
     * Rate limit interval in minutes
     *
     * @var int
     */
    protected int $rateLimitMinutes = 1;

    /**
     * Constructor
     *
     * @param IVerificationRepository $verificationRepository
     */
    public function __construct(IVerificationRepository $verificationRepository)
    {
        $this->verificationRepository = $verificationRepository;
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param string $type The identifier type (phone|email)
     * @return Response
     */
    public function handle(Request $request, Closure $next, string $type = 'phone'): Response
    {
        $userId = $request->input('user_id');

        if (!$userId) {
            return $next($request);
        }

        $identifierType = IdentifierType::tryFrom($type) ?? IdentifierType::PHONE;

        // Check if OTP can be sent
        if (!$this->verificationRepository->canSendOtp($userId, $identifierType)) {
            return response()->json([
                'message' => 'auth.otp.rate_limit_exceeded',
                'retry_after' => $this->getRetryAfterTime($userId, $identifierType),
            ], 429);
        }

        return $next($request);
    }

    /**
     * Get the retry after time in seconds
     *
     * @param string $userId
     * @param IdentifierType $type
     * @return int
     */
    protected function getRetryAfterTime(string $userId, IdentifierType $type): int
    {
        $lastSentAt = $this->verificationRepository->getLastOtpSentAt($userId, $type);

        if (!$lastSentAt) {
            return 0;
        }

        $retryAfter = $lastSentAt->addMinutes($this->rateLimitMinutes)->diffInSeconds(now());

        return max(0, $retryAfter);
    }
}