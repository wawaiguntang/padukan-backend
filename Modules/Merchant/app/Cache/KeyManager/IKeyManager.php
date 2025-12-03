<?php

namespace Modules\Merchant\Cache\KeyManager;

/**
 * Interface for Cache Key Manager
 *
 * This interface defines the contract for generating cache keys
 * used throughout the merchant module.
 */
interface IKeyManager
{
    // ==========================================
    // PROFILE DATA CACHE KEYS (Repository Layer)
    // ==========================================

    /**
     * Generate cache key for profile by user ID
     * Category: Profile Data Cache
     * Layer: Repository
     * TTL: 15 minutes (profile lookup data)
     */
    public static function profileByUserId(string $userId): string;

    /**
     * Generate cache key for profile by ID
     * Category: Profile Data Cache
     * Layer: Repository
     * TTL: 15 minutes (profile lookup data)
     */
    public static function profileById(string $id): string;

    // ==========================================
    // DOCUMENT DATA CACHE KEYS (Repository Layer)
    // ==========================================

    /**
     * Generate cache key for documents by profile ID
     * Category: Document Data Cache
     * Layer: Repository
     * TTL: 10 minutes (document data)
     */
    public static function documentsByProfileId(string $profileId): string;

    /**
     * Generate cache key for document by ID
     * Category: Document Data Cache
     * Layer: Repository
     * TTL: 10 minutes (document data)
     */
    public static function documentById(string $id): string;

    // ==========================================
    // ADDRESS DATA CACHE KEYS (Repository Layer)
    // ==========================================

    /**
     * Generate cache key for addresses by profile ID
     * Category: Address Data Cache
     * Layer: Repository
     * TTL: 15 minutes (address data)
     */
    public static function addressesByProfileId(string $profileId): string;

    /**
     * Generate cache key for address by ID
     * Category: Address Data Cache
     * Layer: Repository
     * TTL: 15 minutes (address data)
     */
    public static function addressById(string $id): string;

    // ==========================================
    // CACHE INVALIDATION PATTERNS
    // ==========================================

    /**
     * Generate cache invalidation pattern for profile by user ID
     * Category: Cache Invalidation Pattern
     * Usage: Invalidate all profile-related caches by user ID
     */
    public static function profilePattern(string $userId): string;

    /**
     * Generate cache invalidation pattern for documents by profile ID
     * Category: Cache Invalidation Pattern
     * Usage: Invalidate all document-related caches by profile ID
     */
    public static function documentPattern(string $profileId): string;

    /**
     * Generate cache invalidation pattern for addresses by profile ID
     * Category: Cache Invalidation Pattern
     * Usage: Invalidate all address-related caches by profile ID
     */
    public static function addressPattern(string $profileId): string;

    /**
     * Generate cache invalidation pattern for ALL merchant caches
     * Category: Cache Invalidation Pattern
     * Usage: Invalidate ALL merchant caches (dangerous!)
     */
    public static function allMerchantPattern(): string;
}
