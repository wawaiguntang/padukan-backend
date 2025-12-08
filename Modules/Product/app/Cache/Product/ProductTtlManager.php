<?php

namespace Modules\Product\Cache\Product;

/**
 * Product TTL Manager
 *
 * Centralized management of cache TTL values for product-related caching.
 * Provides consistent TTL values across the application.
 */
class ProductTtlManager
{
    /**
     * Entity-level cache TTL (individual records)
     * Used for: product by ID, product by slug
     */
    public const ENTITY = 600; // 10 minutes

    /**
     * List-level cache TTL (collections)
     * Used for: merchant products, category products, all products
     */
    public const LIST = 300; // 5 minutes

    /**
     * Search/Filtered results cache TTL
     * Used for: filtered product lists, search results
     */
    public const SEARCH = 180; // 3 minutes

    /**
     * Statistics/Aggregated data cache TTL
     * Used for: dashboard stats, analytics data
     */
    public const STATS = 900; // 15 minutes

    /**
     * Get TTL for product entity cache
     */
    public static function productEntity(): int
    {
        return self::ENTITY;
    }

    /**
     * Get TTL for product list cache
     */
    public static function productList(): int
    {
        return self::LIST;
    }

    /**
     * Get TTL for product search cache
     */
    public static function productSearch(): int
    {
        return self::SEARCH;
    }

    /**
     * Get TTL for product stats cache
     */
    public static function productStats(): int
    {
        return self::STATS;
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
            'stats', 'statistics' => self::STATS,
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
            'stats' => self::STATS,
        ];
    }

    /**
     * Get human-readable TTL descriptions
     */
    public static function descriptions(): array
    {
        return [
            'entity' => 'Entity cache (10 minutes) - Individual product records',
            'list' => 'List cache (5 minutes) - Product collections and lists',
            'search' => 'Search cache (3 minutes) - Filtered product results',
            'stats' => 'Statistics cache (15 minutes) - Product analytics data',
        ];
    }
}
