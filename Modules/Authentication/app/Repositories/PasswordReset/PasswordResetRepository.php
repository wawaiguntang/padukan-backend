<?php

namespace Modules\Authentication\Repositories\PasswordReset;

use Carbon\Carbon;
use Illuminate\Contracts\Cache\Repository as Cache;
use Modules\Authentication\Models\PasswordResetToken;

/**
 * Password Reset Repository Implementation
 *
 * This class handles all password reset token operations
 * for the authentication module with caching support.
 */
class PasswordResetRepository implements IPasswordResetRepository
{
    /**
     * The PasswordResetToken model instance
     *
     * @var PasswordResetToken
     */
    protected PasswordResetToken $model;

    /**
     * The cache repository instance
     *
     * @var Cache
     */
    protected Cache $cache;

    /**
     * Cache TTL in seconds (1 hour for password reset)
     *
     * @var int
     */
    protected int $cacheTtl = 3600;

    /**
     * Constructor
     *
     * @param PasswordResetToken $model The PasswordResetToken model instance
     * @param Cache $cache The cache repository instance
     */
    public function __construct(PasswordResetToken $model, Cache $cache)
    {
        $this->model = $model;
        $this->cache = $cache;
    }

    /**
     * {@inheritDoc}
     */
    public function createResetToken(string $userId, string $token, int $expiresInMinutes = 60): PasswordResetToken
    {
        return $this->model->create([
            'user_id' => $userId,
            'token' => $token,
            'is_used' => false,
            'expires_at' => Carbon::now()->addMinutes($expiresInMinutes),
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function findValidResetToken(string $userId, string $token): ?PasswordResetToken
    {
        $cacheKey = "password_reset:valid:{$userId}:{$token}";

        $resetToken = $this->cache->remember($cacheKey, 300, function () use ($userId, $token) {
            return $this->model
                ->where('user_id', $userId)
                ->where('token', $token)
                ->where('is_used', false)
                ->where('expires_at', '>', Carbon::now())
                ->first();
        });

        // If token exists but is expired, clear cache and return null
        if ($resetToken && $resetToken->expires_at->isPast()) {
            $this->cache->forget($cacheKey);
            return null;
        }

        return $resetToken;
    }

    /**
     * {@inheritDoc}
     */
    public function markResetTokenUsed(string $id): bool
    {
        $token = $this->model->find($id); // Don't use cached version for updates

        if (!$token) {
            return false;
        }

        $result = $token->update(['is_used' => true]);

        if ($result) {
            // Invalidate related caches
            $this->invalidateResetTokenCaches($token);
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function deleteExpiredResetTokens(): int
    {
        return $this->model
            ->where('expires_at', '<=', Carbon::now())
            ->delete();
    }

    /**
     * {@inheritDoc}
     */
    public function findById(string $id): ?PasswordResetToken
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
            $this->invalidateResetTokenCaches($token);
        }

        return $result;
    }

    /**
     * Invalidate all cache keys related to a password reset token
     *
     * @param PasswordResetToken $token The password reset token to invalidate caches for
     * @return void
     */
    protected function invalidateResetTokenCaches(PasswordResetToken $token): void
    {
        // Invalidate specific token validation cache
        $this->cache->forget("password_reset:valid:{$token->user_id}:{$token->token}");
        $this->cache->forget("password_reset:valid_by_token:{$token->token}");
    }

    /**
     * {@inheritDoc}
     */
    public function findValidResetTokenByToken(string $token): ?PasswordResetToken
    {
        $cacheKey = "password_reset:valid_by_token:{$token}";

        $resetToken = $this->cache->remember($cacheKey, 300, function () use ($token) {
            return $this->model
                ->where('token', $token)
                ->where('is_used', false)
                ->where('expires_at', '>', Carbon::now())
                ->first();
        });

        // If token exists but is expired, clear cache and return null
        if ($resetToken && $resetToken->expires_at->isPast()) {
            $this->cache->forget($cacheKey);
            return null;
        }

        return $resetToken;
    }
}
