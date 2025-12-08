<?php

namespace Modules\Product\Cache\ProductExtra;

/**
 * Product Extra TTL Manager
 *
 * Centralized management of cache TTL values for merchant product extra caching.
 * Provides consistent TTL values across the application.
 */
class ProductExtraTtlManager
{
    /**
     * Entity-level cache TTL (individual records)
     * Used for: extra by ID
     */
    public const ENTITY = 600; // 10 minutes

    /**
     * List-level cache TTL (collections)
     * Used for: product extras, merchant product extras
     */
    public const LIST = 300; // 5 minutes

    /**
     * Get TTL for extra entity cache
     */
    public static function extraEntity(): int
    {
        return self::ENTITY;
    }

    /**
     * Get TTL for extra list cache
     */
    public static function extraList(): int
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
            'list' => self::LIST,
        ];
    }

    /**
     * Get human-readable TTL descriptions
     */
    public static function descriptions(): array
    {
        return [
            'entity' => 'Entity cache (10 minutes) - Individual extra records',
            'list' => 'List cache (5 minutes) - Product extra collections',
        ];
    }
}
