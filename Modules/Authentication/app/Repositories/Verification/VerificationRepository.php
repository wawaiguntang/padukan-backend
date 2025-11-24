<?php

namespace Modules\Authentication\Repositories\Verification;

use Carbon\Carbon;
use Illuminate\Contracts\Cache\Repository as Cache;
use Modules\Authentication\Enums\IdentifierType;
use Modules\Authentication\Models\VerificationToken;

/**
 * Verification Token Repository Implementation
 *
 * This class handles all OTP verification token operations
 * for the authentication module with caching support.
 */
class VerificationRepository implements IVerificationRepository
{
    /**
     * The VerificationToken model instance
     *
     * @var VerificationToken
     */
    protected VerificationToken $model;

    /**
     * The cache repository instance
     *
     * @var Cache
     */
    protected Cache $cache;

    /**
     * Cache TTL in seconds (30 minutes for OTP)
     *
     * @var int
     */
    protected int $cacheTtl = 1800;

    /**
     * Rate limiting interval in minutes
     *
     * @var int
     */
    protected int $rateLimitMinutes = 1;

    /**
     * Constructor
     *
     * @param VerificationToken $model The VerificationToken model instance
     * @param Cache $cache The cache repository instance
     */
    public function __construct(VerificationToken $model, Cache $cache)
    {
        $this->model = $model;
        $this->cache = $cache;
    }

    /**
     * {@inheritDoc}
     */
    public function createOtp(string $userId, IdentifierType $type, string $token, int $expiresInMinutes = 5): VerificationToken
    {
        $otp = $this->model->create([
            'user_id' => $userId,
            'type' => $type,
            'token' => $token,
            'is_used' => false,
            'expires_at' => Carbon::now()->addMinutes($expiresInMinutes),
        ]);

        // Invalidate rate limiting cache when new OTP is created
        $this->cache->forget("otp:can_send:{$userId}:{$type->value}");

        return $otp;
    }

    /**
     * {@inheritDoc}
     */
    public function findValidOtp(string $userId, IdentifierType $type, string $token): ?VerificationToken
    {
        $cacheKey = "otp:valid:{$userId}:{$type->value}:{$token}";

        $otp = $this->cache->remember($cacheKey, 180, function () use ($userId, $type, $token) {
            return $this->model
                ->where('user_id', $userId)
                ->where('type', $type)
                ->where('token', $token)
                ->where('is_used', false)
                ->where('expires_at', '>', Carbon::now())
                ->first();
        });

        // If OTP exists but is expired, clear cache and return null
        if ($otp && $otp->expires_at->isPast()) {
            $this->cache->forget($cacheKey);
            return null;
        }

        return $otp;
    }

    /**
     * {@inheritDoc}
     */
    public function markOtpUsed(string $id): bool
    {
        $token = $this->model->find($id); // Don't use cached version for updates

        if (!$token) {
            return false;
        }

        $result = $token->update(['is_used' => true]);

        if ($result) {
            // Invalidate related caches
            $this->invalidateOtpCaches($token);
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function deleteExpiredOtps(): int
    {
        return $this->model
            ->where('expires_at', '<=', Carbon::now())
            ->delete();
    }

    /**
     * {@inheritDoc}
     */
    public function canSendOtp(string $userId, IdentifierType $type): bool
    {
        $cacheKey = "otp:can_send:{$userId}:{$type->value}";

        return $this->cache->remember($cacheKey, 60, function () use ($userId, $type) {
            $lastSentAt = $this->getLastOtpSentAt($userId, $type);

            if (!$lastSentAt) {
                return true;
            }

            return $lastSentAt->addMinutes($this->rateLimitMinutes)->isPast();
        });
    }

    /**
     * {@inheritDoc}
     */
    public function getLastOtpSentAt(string $userId, IdentifierType $type): ?Carbon
    {
        $token = $this->model
            ->where('user_id', $userId)
            ->where('type', $type)
            ->latest('created_at')
            ->first();

        return $token ? $token->created_at : null;
    }

    /**
     * {@inheritDoc}
     */
    public function findById(string $id): ?VerificationToken
    {
        return $this->model->find($id);
    }

    /**
     * {@inheritDoc}
     */
    public function delete(string $id): bool
    {
        $token = $this->model->find($id); // Don't use cached version for deletes

        if (!$token) {
            return false;
        }

        $result = $token->delete();

        if ($result) {
            // Invalidate related caches
            $this->invalidateOtpCaches($token);
        }

        return $result;
    }

    /**
     * Invalidate all cache keys related to an OTP token
     *
     * @param VerificationToken $token The OTP token to invalidate caches for
     * @return void
     */
    protected function invalidateOtpCaches(VerificationToken $token): void
    {
        // Invalidate specific OTP validation cache
        $this->cache->forget("otp:valid:{$token->user_id}:{$token->type->value}:{$token->token}");

        // Invalidate rate limiting cache for this user/type
        $this->cache->forget("otp:can_send:{$token->user_id}:{$token->type->value}");
    }
}
