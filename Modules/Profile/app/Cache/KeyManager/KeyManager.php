<?php

namespace Modules\Profile\Cache\KeyManager;

/**
 * Profile Cache Key Manager Implementation
 *
 * This class generates cache keys for the profile module
 * following consistent naming conventions.
 */
class KeyManager implements IKeyManager
{
    /**
     * Cache key prefix for profile module
     */
    private const PREFIX = 'profile';

    // ==========================================
    // PROFILE DATA CACHE KEYS (Repository Layer)
    // ==========================================

    /**
     * {@inheritDoc}
     * Category: Profile Data Cache
     * Layer: Repository
     * TTL: 15 minutes (profile lookup data)
     */
    public static function profileByUserId(string $userId): string
    {
        return self::PREFIX . ":profile:user_id:{$userId}";
    }

    /**
     * {@inheritDoc}
     * Category: Profile Data Cache
     * Layer: Repository
     * TTL: 15 minutes (profile lookup data)
     */
    public static function profileById(string $profileId): string
    {
        return self::PREFIX . ":profile:id:{$profileId}";
    }

    /**
     * {@inheritDoc}
     * Category: Profile Data Cache
     * Layer: Repository
     * TTL: 15 minutes (addresses lookup data)
     */
    public static function addressesByProfileId(string $profileId): string
    {
        return self::PREFIX . ":addresses:profile_id:{$profileId}";
    }

    /**
     * {@inheritDoc}
     * Category: Profile Data Cache
     * Layer: Repository
     * TTL: 1 hour (master data)
     */
    public static function bankById(string $bankId): string
    {
        return self::PREFIX . ":bank:id:{$bankId}";
    }

    /**
     * {@inheritDoc}
     * Category: Profile Data Cache
     * Layer: Repository
     * TTL: 15 minutes (driver profile lookup data)
     */
    public static function driverProfileByProfileId(string $profileId): string
    {
        return self::PREFIX . ":driver_profile:profile_id:{$profileId}";
    }

    /**
     * {@inheritDoc}
     * Category: Profile Data Cache
     * Layer: Repository
     * TTL: 15 minutes (merchant profile lookup data)
     */
    public static function merchantProfileByProfileId(string $profileId): string
    {
        return self::PREFIX . ":merchant_profile:profile_id:{$profileId}";
    }

    /**
     * {@inheritDoc}
     * Category: Profile Data Cache
     * Layer: Repository
     * TTL: 15 minutes (customer profile lookup data)
     */
    public static function customerProfileByProfileId(string $profileId): string
    {
        return self::PREFIX . ":customer_profile:profile_id:{$profileId}";
    }

    // ==========================================
    // CACHE INVALIDATION PATTERNS
    // ==========================================

    /**
     * {@inheritDoc}
     * Category: Cache Invalidation Pattern
     * Usage: Invalidate all profile-related caches by user ID
     */
    public static function profilePatternByUserId(string $userId): string
    {
        return self::PREFIX . ":profile:user_id:{$userId}";
    }

    /**
     * {@inheritDoc}
     * Category: Cache Invalidation Pattern
     * Usage: Invalidate ALL profile caches (dangerous!)
     */
    public static function allProfilePattern(): string
    {
        return self::PREFIX . ":*";
    }
}