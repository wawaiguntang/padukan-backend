<?php

namespace Modules\Authentication\Cache\KeyManager;

interface IKeyManager
{
    // ==========================================
    // JWT TOKEN CACHE KEYS (Service Layer - Business Logic)
    // ==========================================

    /**
     * Generate cache key for refresh token storage
     * Used in: JWTService::storeRefreshToken()
     * TTL: Based on refresh token expiration (30 days)
     */
    public static function refreshToken(string $refreshToken): string;

    // ==========================================
    // USER DATA CACHE KEYS (Repository Layer)
    // ==========================================

    /**
     * Generate cache key for user lookup by identifier (phone/email)
     * Used in: UserRepository::findByIdentifier()
     * TTL: 15 minutes (user data)
     */
    public static function userByIdentifier(string $identifier): string;

    // ==========================================
    // CACHE INVALIDATION PATTERNS
    // ==========================================

    /**
     * Generate pattern for user-related cache keys (Invalidation)
     * Pattern: authentication:user:{identifier}:*
     */
    public static function userPattern(string $identifier): string;

    /**
     * Generate pattern for refresh token cache keys (Invalidation)
     * Pattern: authentication:refresh_token:{token}
     */
    public static function refreshTokenPattern(string $refreshToken): string;

    /**
     * Generate pattern for all authentication cache keys (Invalidation)
     * Pattern: authentication:*
     */
    public static function allAuthenticationPattern(): string;
}