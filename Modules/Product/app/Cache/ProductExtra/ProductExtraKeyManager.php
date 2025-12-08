<?php

namespace Modules\Product\Cache\ProductExtra;

/**
 * Product Extra Key Manager
 *
 * Generates cache keys for merchant product extra operations.
 * Only handles key generation, not invalidation.
 */
class ProductExtraKeyManager
{
    /**
     * Cache key prefix for product extras
     */
    private const PREFIX = 'product:extra';

    /**
     * Generate cache key for extra by ID
     */
    public static function extraById(string $id): string
    {
        return self::PREFIX . ":id:{$id}";
    }

    /**
     * Generate cache key for product extras
     */
    public static function productExtras(string $productId): string
    {
        return self::PREFIX . ":product:{$productId}";
    }

    /**
     * Generate cache key for merchant product extras
     */
    public static function merchantProductExtras(string $merchantId, string $productId): string
    {
        return self::PREFIX . ":merchant:{$merchantId}:product:{$productId}";
    }

    /**
     * Generate pattern for extra-related cache keys
     */
    public static function extraPattern(): string
    {
        return self::PREFIX . ":*";
    }

    /**
     * Generate pattern for merchant extra-related cache keys
     */
    public static function merchantExtraPattern(string $merchantId): string
    {
        return self::PREFIX . ":merchant:{$merchantId}:*";
    }

    /**
     * Get cache key prefix
     */
    public static function getPrefix(): string
    {
        return self::PREFIX;
    }
}
