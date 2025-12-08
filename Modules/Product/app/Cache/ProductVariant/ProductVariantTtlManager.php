<?php

namespace Modules\Product\Cache\ProductVariant;

/**
 * Product Variant TTL Manager
 *
 * Centralized management of cache TTL values for merchant product variant caching.
 * Provides consistent TTL values across the application.
 */
class ProductVariantTtlManager
{
    /**
     * Entity-level cache TTL (individual records)
     * Used for: variant by ID
     */
    public const ENTITY = 600; // 10 minutes

    /**
     * List-level cache TTL (collections)
     * Used for: product variants, merchant product variants
     */
    public const LIST = 300; // 5 minutes

    /**
     * Lookup cache TTL (SKU, barcode lookups)
     * Used for: variant by SKU, variant by barcode
     */
    public const LOOKUP = 900; // 15 minutes

    /**
     * Get TTL for variant entity cache
     */
    public static function variantEntity(): int
    {
        return self::ENTITY;
    }

    /**
     * Get TTL for variant list cache
     */
    public static function variantList(): int
    {
        return self::LIST;
    }

    /**
     * Get TTL for variant lookup cache
     */
    public static function variantLookup(): int
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
            'list' => self::LIST,
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
            'list' => self::LIST,
            'lookup' => self::LOOKUP,
        ];
    }

    /**
     * Get human-readable TTL descriptions
     */
    public static function descriptions(): array
    {
        return [
            'entity' => 'Entity cache (10 minutes) - Individual variant records',
            'list' => 'List cache (5 minutes) - Product variant collections',
            'lookup' => 'Lookup cache (15 minutes) - SKU/barcode lookups',
        ];
    }
}
