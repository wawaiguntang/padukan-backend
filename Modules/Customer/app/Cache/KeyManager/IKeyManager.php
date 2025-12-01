<?php

namespace Modules\Customer\Cache\KeyManager;

interface IKeyManager
{
    // ==========================================
    // PROFILE DATA CACHE KEYS (Repository Layer)
    // ==========================================

    /**
     * Generate cache key for profile lookup by user ID
     * Used in: ProfileRepository::findByUserId()
     * TTL: 15 minutes (profile data)
     */
    public static function profileByUserId(string $userId): string;

    /**
     * Generate cache key for profile lookup by ID
     * Used in: ProfileRepository::findById()
     * TTL: 15 minutes (profile data)
     */
    public static function profileById(string $id): string;

    // ==========================================
    // DOCUMENT DATA CACHE KEYS (Repository Layer)
    // ==========================================

    /**
     * Generate cache key for documents by profile ID
     * Used in: DocumentRepository::findByProfileId()
     * TTL: 10 minutes (document data)
     */
    public static function documentsByProfileId(string $profileId): string;

    /**
     * Generate cache key for document by ID
     * Used in: DocumentRepository::findById()
     * TTL: 10 minutes (document data)
     */
    public static function documentById(string $id): string;

    // ==========================================
    // ADDRESS DATA CACHE KEYS (Repository Layer)
    // ==========================================

    /**
     * Generate cache key for addresses by profile ID
     * Used in: AddressRepository::findByProfileId()
     * TTL: 15 minutes (address data)
     */
    public static function addressesByProfileId(string $profileId): string;

    /**
     * Generate cache key for address by ID
     * Used in: AddressRepository::findById()
     * TTL: 15 minutes (address data)
     */
    public static function addressById(string $id): string;

    // ==========================================
    // CACHE INVALIDATION PATTERNS
    // ==========================================

    /**
     * Generate pattern for profile-related cache keys (Invalidation)
     * Pattern: customer:profile:{userId}:*
     */
    public static function profilePattern(string $userId): string;

    /**
     * Generate pattern for document-related cache keys (Invalidation)
     * Pattern: customer:document:{profileId}:*
     */
    public static function documentPattern(string $profileId): string;

    /**
     * Generate pattern for address-related cache keys (Invalidation)
     * Pattern: customer:address:{profileId}:*
     */
    public static function addressPattern(string $profileId): string;

    /**
     * Generate pattern for all customer cache keys (Invalidation)
     * Pattern: customer:*
     */
    public static function allCustomerPattern(): string;
}
