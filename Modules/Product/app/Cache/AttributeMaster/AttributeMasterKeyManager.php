<?php

namespace Modules\Product\Cache\AttributeMaster;

/**
 * Attribute Master Key Manager
 *
 * Generates cache keys for global attribute master operations.
 * Only handles key generation, not invalidation.
 */
class AttributeMasterKeyManager
{
    /**
     * Cache key prefix for attribute masters
     */
    private const PREFIX = 'product:attribute_master';

    /**
     * Generate cache key for attribute master by ID
     */
    public static function attributeMasterById(string $id): string
    {
        return self::PREFIX . ":id:{$id}";
    }

    /**
     * Generate cache key for attribute master by key
     */
    public static function attributeMasterByKey(string $key): string
    {
        return self::PREFIX . ":key:{$key}";
    }

    /**
     * Generate cache key for all attribute masters
     */
    public static function allAttributeMasters(): string
    {
        return self::PREFIX . ":all";
    }

    /**
     * Generate pattern for attribute master-related cache keys
     */
    public static function attributeMasterPattern(): string
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
