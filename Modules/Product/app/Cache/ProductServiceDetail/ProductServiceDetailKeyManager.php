<?php

namespace Modules\Product\Cache\ProductServiceDetail;

/**
 * Product Service Detail Key Manager
 *
 * Generates cache keys for merchant product service detail operations.
 * Only handles key generation, not invalidation.
 */
class ProductServiceDetailKeyManager
{
    /**
     * Cache key prefix for service details
     */
    private const PREFIX = 'product:service_detail';

    /**
     * Generate cache key for service detail by ID
     */
    public static function serviceDetailById(string $serviceId): string
    {
        return self::PREFIX . ":id:{$serviceId}";
    }

    /**
     * Generate cache key for service detail by product ID
     */
    public static function serviceDetailByProductId(string $productId): string
    {
        return self::PREFIX . ":product:{$productId}";
    }

    /**
     * Generate cache key for merchant service detail by product ID
     */
    public static function merchantServiceDetailByProductId(string $merchantId, string $productId): string
    {
        return self::PREFIX . ":merchant:{$merchantId}:product:{$productId}";
    }

    /**
     * Generate pattern for service detail-related cache keys
     */
    public static function serviceDetailPattern(): string
    {
        return self::PREFIX . ":*";
    }

    /**
     * Generate pattern for merchant service detail-related cache keys
     */
    public static function merchantServiceDetailPattern(string $merchantId): string
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
