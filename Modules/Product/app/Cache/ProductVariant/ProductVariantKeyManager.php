<?php

namespace Modules\Product\Cache\ProductVariant;

/**
 * Product Variant Key Manager
 *
 * Generates cache keys for merchant product variant operations.
 * Only handles key generation, not invalidation.
 */
class ProductVariantKeyManager
{
    /**
     * Cache key prefix for product variants
     */
    private const PREFIX = 'product:variant';

    /**
     * Generate cache key for variant by ID
     */
    public static function variantById(string $id): string
    {
        return self::PREFIX . ":id:{$id}";
    }

    /**
     * Generate cache key for product variants
     */
    public static function productVariants(string $productId): string
    {
        return self::PREFIX . ":product:{$productId}";
    }

    /**
     * Generate cache key for variant by SKU
     */
    public static function variantBySku(string $sku): string
    {
        return self::PREFIX . ":sku:{$sku}";
    }

    /**
     * Generate cache key for variant by barcode
     */
    public static function variantByBarcode(string $barcode): string
    {
        return self::PREFIX . ":barcode:{$barcode}";
    }

    /**
     * Generate cache key for merchant product variants
     */
    public static function merchantProductVariants(string $merchantId, string $productId): string
    {
        return self::PREFIX . ":merchant:{$merchantId}:product:{$productId}";
    }

    /**
     * Generate cache key for merchant variant by SKU
     */
    public static function merchantVariantBySku(string $merchantId, string $sku): string
    {
        return self::PREFIX . ":merchant:{$merchantId}:sku:{$sku}";
    }

    /**
     * Generate cache key for merchant variant by barcode
     */
    public static function merchantVariantByBarcode(string $merchantId, string $barcode): string
    {
        return self::PREFIX . ":merchant:{$merchantId}:barcode:{$barcode}";
    }

    /**
     * Generate pattern for variant-related cache keys
     */
    public static function variantPattern(): string
    {
        return self::PREFIX . ":*";
    }

    /**
     * Generate pattern for merchant variant-related cache keys
     */
    public static function merchantVariantPattern(string $merchantId): string
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
