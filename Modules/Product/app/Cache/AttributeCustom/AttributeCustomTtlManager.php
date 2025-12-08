<?php

namespace Modules\Product\Cache\AttributeCustom;

/**
 * Attribute Custom TTL Manager
 *
 * Centralized management of cache TTL values for merchant custom attribute caching.
 * Provides consistent TTL values across the application.
 */
class AttributeCustomTtlManager
{
    /**
     * Entity-level cache TTL (individual records)
     * Used for: attribute by ID
     */
    public const ENTITY = 900; // 15 minutes

    /**
     * List-level cache TTL (collections)
     * Used for: merchant attributes, attribute lists
     */
    public const LIST = 600; // 10 minutes

    /**
     * Search/Filtered results cache TTL
     * Used for: filtered attribute results
     */
    public const SEARCH = 300; // 5 minutes

    /**
     * Get TTL for custom attribute entity cache
     */
    public static function attributeEntity(): int
    {
        return self::ENTITY;
    }

    /**
     * Get TTL for custom attribute list cache
     */
    public static function attributeList(): int
    {
        return self::LIST;
    }

    /**
     * Get TTL for custom attribute search cache
     */
    public static function attributeSearch(): int
    {
        return self::SEARCH;
    }

    /**
     * Get TTL by cache type
     */
    public static function get(string $type): int
    {
        return match ($type) {
            'entity' => self::ENTITY,
            'list' => self::LIST,
            'search' => self::SEARCH,
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
            'list' => self::LIST,
            'search' => self::SEARCH,
        ];
    }

    /**
     * Get human-readable TTL descriptions
     */
    public static function descriptions(): array
    {
        return [
            'entity' => 'Entity cache (15 minutes) - Individual custom attribute records',
            'list' => 'List cache (10 minutes) - Merchant custom attribute collections',
            'search' => 'Search cache (5 minutes) - Filtered custom attribute results',
        ];
    }
}
