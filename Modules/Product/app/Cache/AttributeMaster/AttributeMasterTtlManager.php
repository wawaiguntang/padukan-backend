<?php

namespace Modules\Product\Cache\AttributeMaster;

/**
 * Attribute Master TTL Manager
 *
 * Centralized management of cache TTL values for global attribute master caching.
 * Provides consistent TTL values across the application.
 */
class AttributeMasterTtlManager
{
    /**
     * Entity-level cache TTL (individual records)
     * Used for: attribute master by ID
     */
    public const ENTITY = 1800; // 30 minutes

    /**
     * Lookup cache TTL (by key)
     * Used for: attribute master by key
     */
    public const LOOKUP = 1800; // 30 minutes

    /**
     * List-level cache TTL (collections)
     * Used for: all attribute masters
     */
    public const LIST = 1800; // 30 minutes

    /**
     * Get TTL for attribute master entity cache
     */
    public static function attributeMasterEntity(): int
    {
        return self::ENTITY;
    }

    /**
     * Get TTL for attribute master lookup cache
     */
    public static function attributeMasterLookup(): int
    {
        return self::LOOKUP;
    }

    /**
     * Get TTL for attribute master list cache
     */
    public static function attributeMasterList(): int
    {
        return self::LIST;
    }

    /**
     * Get TTL by cache type
     */
    public static function get(string $type): int
    {
        return match ($type) {
            'entity' => self::ENTITY,
            'lookup' => self::LOOKUP,
            'list' => self::LIST,
            default => self::ENTITY
        };
    }

    /**
     * Get all TTL values as array
     */
    public static function all(): array
    {
        return [
            'entity' => self::ENTITY,
            'lookup' => self::LOOKUP,
            'list' => self::LIST,
        ];
    }

    /**
     * Get human-readable TTL descriptions
     */
    public static function descriptions(): array
    {
        return [
            'entity' => 'Entity cache (30 minutes) - Individual attribute master records',
            'lookup' => 'Lookup cache (30 minutes) - Attribute master by key',
            'list' => 'List cache (30 minutes) - All attribute masters collection',
        ];
    }
}
