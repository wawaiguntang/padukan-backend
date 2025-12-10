<?php

namespace Modules\Region\Cache;

/**
 * Region Key Manager
 *
 * Generates cache keys for region-related operations.
 * Only handles key generation, not invalidation.
 */
class RegionKeyManager
{
    /**
     * Cache key prefix for regions
     */
    private const PREFIX = 'region';

    /**
     * Generate cache key for region by ID
     */
    public static function regionById(string $id): string
    {
        return self::PREFIX . ":id:{$id}";
    }

    /**
     * Generate cache key for all regions
     */
    public static function allRegions(): string
    {
        return self::PREFIX . ":all";
    }

    /**
     * Generate pattern for region-related cache keys
     */
    public static function regionPattern(): string
    {
        return self::PREFIX . ":*";
    }

    /**
     * Get cache key prefix
     */
    public static function getPrefix(): string
    {
        return self::PREFIX;
    }
}
