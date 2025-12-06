<?php

namespace Modules\Product\Cache\KeyManager;

class KeyManager implements IKeyManager
{
    /**
     * Cache key prefix for product module
     */
    private const PREFIX = 'product';

    // ==========================================
    // CATEGORY CACHE KEYS
    // ==========================================

    /**
     * {@inheritDoc}
     * Category: Category Data Cache
     * Layer: Repository
     * TTL: 15 minutes
     */
    public static function categoryById(string $id): string
    {
        return self::PREFIX . ":category:id:{$id}";
    }

    /**
     * {@inheritDoc}
     * Category: Category Data Cache
     * Layer: Repository
     * TTL: 15 minutes
     */
    public static function categoryBySlug(string $slug): string
    {
        return self::PREFIX . ":category:slug:{$slug}";
    }

    /**
     * {@inheritDoc}
     * Category: Category Hierarchy Cache
     * Layer: Repository
     * TTL: 30 minutes
     */
    public static function rootCategories(): string
    {
        return self::PREFIX . ":category:root";
    }

    // ==========================================
    // ATTRIBUTE MASTER CACHE KEYS
    // ==========================================

    /**
     * {@inheritDoc}
     * Category: Attribute Master Data Cache
     * Layer: Repository
     * TTL: 30 minutes
     */
    public static function attributeMasterById(string $id): string
    {
        return self::PREFIX . ":attribute_master:id:{$id}";
    }

    /**
     * {@inheritDoc}
     * Category: Attribute Master Data Cache
     * Layer: Repository
     * TTL: 30 minutes
     */
    public static function attributeMasterByKey(string $key): string
    {
        return self::PREFIX . ":attribute_master:key:{$key}";
    }

    /**
     * {@inheritDoc}
     * Category: Attribute Master Collection Cache
     * Layer: Repository
     * TTL: 30 minutes
     */
    public static function allAttributeMasters(): string
    {
        return self::PREFIX . ":attribute_master:all";
    }

    // ==========================================
    // ATTRIBUTE CUSTOM CACHE KEYS
    // ==========================================

    /**
     * {@inheritDoc}
     * Category: Merchant Attribute Cache
     * Layer: Repository
     * TTL: 15 minutes
     */
    public static function merchantAttributes(string $merchantId): string
    {
        return self::PREFIX . ":merchant:{$merchantId}:attributes";
    }

    // ==========================================
    // PRODUCT CACHE KEYS
    // ==========================================

    /**
     * {@inheritDoc}
     * Category: Product Data Cache
     * Layer: Repository
     * TTL: 10 minutes
     */
    public static function productById(string $id): string
    {
        return self::PREFIX . ":product:id:{$id}";
    }

    /**
     * {@inheritDoc}
     * Category: Product Data Cache
     * Layer: Repository
     * TTL: 10 minutes
     */
    public static function productBySlug(string $slug): string
    {
        return self::PREFIX . ":product:slug:{$slug}";
    }

    /**
     * {@inheritDoc}
     * Category: Merchant Product Cache
     * Layer: Repository
     * TTL: 5 minutes
     */
    public static function merchantProducts(string $merchantId): string
    {
        return self::PREFIX . ":merchant:{$merchantId}:products";
    }

    /**
     * {@inheritDoc}
     * Category: Category Product Cache
     * Layer: Repository
     * TTL: 5 minutes
     */
    public static function categoryProducts(string $categoryId): string
    {
        return self::PREFIX . ":category:{$categoryId}:products";
    }

    // ==========================================
    // PRODUCT VARIANT CACHE KEYS
    // ==========================================

    /**
     * {@inheritDoc}
     * Category: Product Variant Cache
     * Layer: Repository
     * TTL: 5 minutes
     */
    public static function productVariants(string $productId): string
    {
        return self::PREFIX . ":product:{$productId}:variants";
    }

    /**
     * {@inheritDoc}
     * Category: Variant Lookup Cache
     * Layer: Repository
     * TTL: 10 minutes
     */
    public static function variantBySku(string $sku): string
    {
        return self::PREFIX . ":variant:sku:{$sku}";
    }

    /**
     * {@inheritDoc}
     * Category: Variant Lookup Cache
     * Layer: Repository
     * TTL: 10 minutes
     */
    public static function variantByBarcode(string $barcode): string
    {
        return self::PREFIX . ":variant:barcode:{$barcode}";
    }

    // ==========================================
    // PRODUCT BUNDLE CACHE KEYS
    // ==========================================

    /**
     * {@inheritDoc}
     * Category: Bundle Data Cache
     * Layer: Repository
     * TTL: 15 minutes
     */
    public static function bundleById(string $id): string
    {
        return self::PREFIX . ":bundle:id:{$id}";
    }

    // ==========================================
    // UNIT CONVERSION CACHE KEYS
    // ==========================================

    /**
     * {@inheritDoc}
     * Category: Unit Conversion Cache
     * Layer: Repository
     * TTL: 60 minutes
     */
    public static function unitConversions(): string
    {
        return self::PREFIX . ":unit_conversions";
    }

    // ==========================================
    // CACHE INVALIDATION PATTERNS
    // ==========================================

    /**
     * {@inheritDoc}
     * Category: Cache Invalidation Pattern
     * Usage: Invalidate all category-related caches
     */
    public static function categoryPattern(): string
    {
        return self::PREFIX . ":category:*";
    }

    /**
     * {@inheritDoc}
     * Category: Cache Invalidation Pattern
     * Usage: Invalidate all product-related caches
     */
    public static function productPattern(): string
    {
        return self::PREFIX . ":product:*";
    }

    /**
     * {@inheritDoc}
     * Category: Cache Invalidation Pattern
     * Usage: Invalidate all merchant-related caches
     */
    public static function merchantPattern(string $merchantId): string
    {
        return self::PREFIX . ":merchant:{$merchantId}:*";
    }

    /**
     * {@inheritDoc}
     * Category: Cache Invalidation Pattern
     * Usage: Invalidate ALL product caches (dangerous!)
     */
    public static function allProductPattern(): string
    {
        return self::PREFIX . ":*";
    }
}
