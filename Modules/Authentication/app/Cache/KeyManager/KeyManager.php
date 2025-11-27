<?php

namespace Modules\Authentication\Cache\KeyManager;

class KeyManager implements IKeyManager
{
    /**
     * Cache key prefix for authentication module
     */
    private const PREFIX = 'authentication';

    // ==========================================
    // JWT TOKEN CACHE KEYS (Service Layer - Business Logic)
    // ==========================================

    /**
     * {@inheritDoc}
     * Category: JWT Token Cache
     * Layer: Service (JWT business logic)
     * TTL: 30 days (refresh token expiration)
     */
    public static function refreshToken(string $refreshToken): string
    {
        return self::PREFIX . ":refresh_token:{$refreshToken}";
    }

    // ==========================================
    // USER DATA CACHE KEYS (Repository Layer)
    // ==========================================

    /**
     * {@inheritDoc}
     * Category: User Data Cache
     * Layer: Repository
     * TTL: 15 minutes (user lookup data)
     */
    public static function userByIdentifier(string $identifier): string
    {
        return self::PREFIX . ":user:identifier:{$identifier}";
    }

    // ==========================================
    // CACHE INVALIDATION PATTERNS
    // ==========================================

    /**
     * {@inheritDoc}
     * Category: Cache Invalidation Pattern
     * Usage: Invalidate all user-related caches by identifier
     */
    public static function userPattern(string $identifier): string
    {
        return self::PREFIX . ":user:identifier:{$identifier}";
    }

    /**
     * {@inheritDoc}
     * Category: Cache Invalidation Pattern
     * Usage: Invalidate specific refresh token cache
     */
    public static function refreshTokenPattern(string $refreshToken): string
    {
        return self::PREFIX . ":refresh_token:{$refreshToken}";
    }

    /**
     * {@inheritDoc}
     * Category: Cache Invalidation Pattern
     * Usage: Invalidate ALL authentication caches (dangerous!)
     */
    public static function allAuthenticationPattern(): string
    {
        return self::PREFIX . ":*";
    }
}