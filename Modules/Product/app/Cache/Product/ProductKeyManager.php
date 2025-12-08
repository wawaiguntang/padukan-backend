<?php

namespace Modules\Product\Cache\Product;

/**
 * Product Key Manager
 *
 * Generates cache keys for product-related operations.
 * Only handles key generation, not invalidation.
 */
class ProductKeyManager
{
    /**
     * Cache key prefix for products
     */
    private const PREFIX = 'product:product';

    /**
     * Generate cache key for product by ID
     */
    public static function productById(string $id): string
    {
        return self::PREFIX . ":id:{$id}";
    }


    /**
     * Generate cache key for merchant products
     */
    public static function merchantProducts(string $merchantId): string
    {
        return self::PREFIX . ":merchant:{$merchantId}";
    }

    /**
     * Generate cache key for category products
     */
    public static function categoryProducts(string $categoryId): string
    {
        return self::PREFIX . ":category:{$categoryId}";
    }


    /**
     * Generate pattern for product-related cache keys
     */
    public static function productPattern(): string
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
