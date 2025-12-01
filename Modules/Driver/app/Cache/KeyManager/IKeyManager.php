<?php

namespace Modules\Driver\Cache\KeyManager;

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
    // VEHICLE DATA CACHE KEYS (Repository Layer)
    // ==========================================

    /**
     * Generate cache key for vehicles by profile ID
     * Used in: VehicleRepository::findByProfileId()
     * TTL: 15 minutes (vehicle data)
     */
    public static function vehiclesByProfileId(string $profileId): string;

    /**
     * Generate cache key for vehicle by ID
     * Used in: VehicleRepository::findById()
     * TTL: 15 minutes (vehicle data)
     */
    public static function vehicleById(string $id): string;

    // ==========================================
    // DRIVER STATUS DATA CACHE KEYS (Repository Layer)
    // ==========================================

    /**
     * Generate cache key for driver status by profile ID
     * Used in: DriverStatusRepository::findByProfileId()
     * TTL: 5 minutes (status data - frequently updated)
     */
    public static function driverStatusByProfileId(string $profileId): string;

    /**
     * Generate cache key for driver status by ID
     * Used in: DriverStatusRepository::findById()
     * TTL: 5 minutes (status data - frequently updated)
     */
    public static function driverStatusById(string $id): string;

    // ==========================================
    // CACHE INVALIDATION PATTERNS
    // ==========================================

    /**
     * Generate pattern for profile-related cache keys (Invalidation)
     * Pattern: driver:profile:{userId}:*
     */
    public static function profilePattern(string $userId): string;

    /**
     * Generate pattern for document-related cache keys (Invalidation)
     * Pattern: driver:document:{profileId}:*
     */
    public static function documentPattern(string $profileId): string;

    /**
     * Generate pattern for vehicle-related cache keys (Invalidation)
     * Pattern: driver:vehicle:{profileId}:*
     */
    public static function vehiclePattern(string $profileId): string;

    /**
     * Generate pattern for all driver cache keys (Invalidation)
     * Pattern: driver:*
     */
    public static function allDriverPattern(): string;
}
