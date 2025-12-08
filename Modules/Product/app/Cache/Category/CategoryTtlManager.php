<?php

namespace Modules\Product\Cache\Category;

/**
 * Category TTL Manager
 *
 * Centralized management of cache TTL values for category-related caching.
 * Provides consistent TTL values across the application.
 */
class CategoryTtlManager
{
    /**
     * Entity-level cache TTL (individual records)
     * Used for: category by ID
     */
    public const ENTITY = 900; // 15 minutes

    /**
     * List-level cache TTL (collections)
     * Used for: category roots, children, all categories
     */
    public const LIST = 1800; // 30 minutes

    /**
     * Tree/Complex structure cache TTL
     * Used for: category trees, hierarchies
     */
    public const TREE = 3600; // 1 hour

    /**
     * Statistics/Aggregated data cache TTL
     * Used for: dashboard stats, analytics data
     */
    public const STATS = 7200; // 2 hours

    /**
     * Search results cache TTL
     * Used for: filtered lists, search results
     */
    public const SEARCH = 1200; // 20 minutes

    /**
     * Get TTL for category entity cache
     */
    public static function categoryEntity(): int
    {
        return self::ENTITY;
    }

    /**
     * Get TTL for category list cache
     */
    public static function categoryList(): int
    {
        return self::LIST;
    }

    /**
     * Get TTL for category tree cache
     */
    public static function categoryTree(): int
    {
        return self::TREE;
    }

    /**
     * Get TTL by cache type
     */
    public static function get(string $type): int
    {
        return match ($type) {
            'entity' => self::ENTITY,
            'list' => self::LIST,
            'tree' => self::TREE,
            'stats', 'statistics' => self::STATS,
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
            'tree' => self::TREE,
            'stats' => self::STATS,
            'search' => self::SEARCH,
        ];
    }

    /**
     * Get human-readable TTL descriptions
     */
    public static function descriptions(): array
    {
        return [
            'entity' => 'Entity cache (15 minutes) - Individual category records',
            'list' => 'List cache (30 minutes) - Category collections and lists',
            'tree' => 'Tree cache (1 hour) - Category hierarchical structures',
            'stats' => 'Statistics cache (2 hours) - Aggregated category data',
            'search' => 'Search cache (20 minutes) - Filtered category results',
        ];
    }
}
