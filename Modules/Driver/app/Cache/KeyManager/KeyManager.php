<?php

namespace Modules\Driver\Cache\KeyManager;

class KeyManager implements IKeyManager
{
    /**
     * Cache key prefix for driver module
     */
    private const PREFIX = 'driver';

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
    // VEHICLE DATA CACHE KEYS (Repository Layer)
    // ==========================================

    /**
     * {@inheritDoc}
     * Category: Vehicle Data Cache
     * Layer: Repository
     * TTL: 15 minutes (vehicle data)
     */
    public static function vehiclesByProfileId(string $profileId): string
    {
        return self::PREFIX . ":vehicle:profile_id:{$profileId}";
    }

    /**
     * {@inheritDoc}
     * Category: Vehicle Data Cache
     * Layer: Repository
     * TTL: 15 minutes (vehicle data)
     */
    public static function vehicleById(string $id): string
    {
        return self::PREFIX . ":vehicle:id:{$id}";
    }

    // ==========================================
    // DRIVER STATUS DATA CACHE KEYS (Repository Layer)
    // ==========================================

    /**
     * {@inheritDoc}
     * Category: Driver Status Data Cache
     * Layer: Repository
     * TTL: 5 minutes (status data - frequently updated)
     */
    public static function driverStatusByProfileId(string $profileId): string
    {
        return self::PREFIX . ":status:profile_id:{$profileId}";
    }

    /**
     * {@inheritDoc}
     * Category: Driver Status Data Cache
     * Layer: Repository
     * TTL: 5 minutes (status data - frequently updated)
     */
    public static function driverStatusById(string $id): string
    {
        return self::PREFIX . ":status:id:{$id}";
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
     * Usage: Invalidate all vehicle-related caches by profile ID
     */
    public static function vehiclePattern(string $profileId): string
    {
        return self::PREFIX . ":vehicle:profile_id:{$profileId}";
    }

    /**
     * {@inheritDoc}
     * Category: Cache Invalidation Pattern
     * Usage: Invalidate ALL driver caches (dangerous!)
     */
    public static function allDriverPattern(): string
    {
        return self::PREFIX . ":*";
    }
}
