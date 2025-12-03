<?php

namespace Modules\Merchant\Cache\KeyManager;

class KeyManager implements IKeyManager
{
    /**
     * Cache key prefix for merchant module
     */
    private const PREFIX = 'merchant';

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
    public static function profileById(string $id): string
    {
        return self::PREFIX . ":profile:id:{$id}";
    }

    // ==========================================
    // DOCUMENT DATA CACHE KEYS (Repository Layer)
    // ==========================================

    /**
     * {@inheritDoc}
     * Category: Document Data Cache
     * Layer: Repository
     * TTL: 10 minutes (document data)
     */
    public static function documentsByProfileId(string $profileId): string
    {
        return self::PREFIX . ":document:profile_id:{$profileId}";
    }

    /**
     * {@inheritDoc}
     * Category: Document Data Cache
     * Layer: Repository
     * TTL: 10 minutes (document data)
     */
    public static function documentById(string $id): string
    {
        return self::PREFIX . ":document:id:{$id}";
    }

    // ==========================================
    // ADDRESS DATA CACHE KEYS (Repository Layer)
    // ==========================================

    /**
     * {@inheritDoc}
     * Category: Address Data Cache
     * Layer: Repository
     * TTL: 15 minutes (address data)
     */
    public static function addressesByProfileId(string $profileId): string
    {
        return self::PREFIX . ":address:profile_id:{$profileId}";
    }

    /**
     * {@inheritDoc}
     * Category: Address Data Cache
     * Layer: Repository
     * TTL: 15 minutes (address data)
     */
    public static function addressById(string $id): string
    {
        return self::PREFIX . ":address:id:{$id}";
    }

    // ==========================================
    // CACHE INVALIDATION PATTERNS
    // ==========================================

    /**
     * {@inheritDoc}
     * Category: Cache Invalidation Pattern
     * Usage: Invalidate all profile-related caches by user ID
     */
    public static function profilePattern(string $userId): string
    {
        return self::PREFIX . ":profile:user_id:{$userId}";
    }

    /**
     * {@inheritDoc}
     * Category: Cache Invalidation Pattern
     * Usage: Invalidate all document-related caches by profile ID
     */
    public static function documentPattern(string $profileId): string
    {
        return self::PREFIX . ":document:profile_id:{$profileId}";
    }

    /**
     * {@inheritDoc}
     * Category: Cache Invalidation Pattern
     * Usage: Invalidate all address-related caches by profile ID
     */
    public static function addressPattern(string $profileId): string
    {
        return self::PREFIX . ":address:profile_id:{$profileId}";
    }

    /**
     * {@inheritDoc}
     * Category: Cache Invalidation Pattern
     * Usage: Invalidate ALL merchant caches (dangerous!)
     */
    public static function allMerchantPattern(): string
    {
        return self::PREFIX . ":*";
    }
}
