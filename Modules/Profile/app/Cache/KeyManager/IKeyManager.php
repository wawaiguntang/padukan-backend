<?php

namespace Modules\Profile\Cache\KeyManager;

/**
 * Interface for Profile Cache Key Manager
 *
 * This interface defines the contract for generating cache keys
 * used throughout the profile module.
 */
interface IKeyManager
{
    /**
     * Generate cache key for profile by user ID
     *
     * @param string $userId The user ID
     * @return string The cache key
     */
    public static function profileByUserId(string $userId): string;

    /**
     * Generate cache key for profile by ID
     *
     * @param string $profileId The profile ID
     * @return string The cache key
     */
    public static function profileById(string $profileId): string;

    /**
     * Generate cache key for addresses by profile ID
     *
     * @param string $profileId The profile ID
     * @return string The cache key
     */
    public static function addressesByProfileId(string $profileId): string;

    /**
     * Generate cache key for bank by ID
     *
     * @param string $bankId The bank ID
     * @return string The cache key
     */
    public static function bankById(string $bankId): string;

    /**
     * Generate cache key for driver profile by profile ID
     *
     * @param string $profileId The profile ID
     * @return string The cache key
     */
    public static function driverProfileByProfileId(string $profileId): string;

    /**
     * Generate cache key for merchant profile by profile ID
     *
     * @param string $profileId The profile ID
     * @return string The cache key
     */
    public static function merchantProfileByProfileId(string $profileId): string;

    /**
     * Generate cache key for customer profile by profile ID
     *
     * @param string $profileId The profile ID
     * @return string The cache key
     */
    public static function customerProfileByProfileId(string $profileId): string;

    /**
     * Generate cache key pattern for profile invalidation by user ID
     *
     * @param string $userId The user ID
     * @return string The cache key pattern
     */
    public static function profilePatternByUserId(string $userId): string;

    /**
     * Generate cache key pattern for all profile caches
     *
     * @return string The cache key pattern
     */
    public static function allProfilePattern(): string;
}