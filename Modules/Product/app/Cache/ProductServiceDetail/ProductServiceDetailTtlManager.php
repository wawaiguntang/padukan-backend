<?php

namespace Modules\Product\Cache\ProductServiceDetail;

/**
 * Product Service Detail TTL Manager
 *
 * Centralized management of cache TTL values for merchant product service detail caching.
 * Provides consistent TTL values across the application.
 */
class ProductServiceDetailTtlManager
{
    /**
     * Entity-level cache TTL (individual records)
     * Used for: service detail by ID
     */
    public const ENTITY = 600; // 10 minutes

    /**
     * Lookup cache TTL (by product ID)
     * Used for: service detail by product ID
     */
    public const LOOKUP = 300; // 5 minutes

    /**
     * Get TTL for service detail entity cache
     */
    public static function serviceDetailEntity(): int
    {
        return self::ENTITY;
    }

    /**
     * Get TTL for service detail lookup cache
     */
    public static function serviceDetailLookup(): int
    {
        return self::LOOKUP;
    }

    /**
     * Get TTL by cache type
     */
    public static function get(string $type): int
    {
        return match ($type) {
            'entity' => self::ENTITY,
            'lookup' => self::LOOKUP,
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
        ];
    }

    /**
     * Get human-readable TTL descriptions
     */
    public static function descriptions(): array
    {
        return [
            'entity' => 'Entity cache (10 minutes) - Individual service detail records',
            'lookup' => 'Lookup cache (5 minutes) - Service detail by product ID',
        ];
    }
}
