<?php

namespace Modules\Product\Cache\KeyManager;

interface IKeyManager
{
    // ==========================================
    // CATEGORY CACHE KEYS
    // ==========================================

    /**
     * Generate cache key for category lookup by ID
     * Used in: CategoryRepository::findById()
     * TTL: 15 minutes
     */
    public static function categoryById(string $id): string;

    /**
     * Generate cache key for category lookup by slug
     * Used in: CategoryRepository::findBySlug()
     * TTL: 15 minutes
     */
    public static function categoryBySlug(string $slug): string;

    /**
     * Generate cache key for root categories
     * Used in: CategoryRepository::getRootCategories()
     * TTL: 30 minutes
     */
    public static function rootCategories(): string;

    // ==========================================
    // ATTRIBUTE MASTER CACHE KEYS
    // ==========================================

    /**
     * Generate cache key for attribute master lookup by ID
     * Used in: AttributeMasterRepository::findById()
     * TTL: 30 minutes
     */
    public static function attributeMasterById(string $id): string;

    /**
     * Generate cache key for attribute master lookup by key
     * Used in: AttributeMasterRepository::findByKey()
     * TTL: 30 minutes
     */
    public static function attributeMasterByKey(string $key): string;

    /**
     * Generate cache key for all attribute masters
     * Used in: AttributeMasterRepository::getAll()
     * TTL: 30 minutes
     */
    public static function allAttributeMasters(): string;

    // ==========================================
    // ATTRIBUTE CUSTOM CACHE KEYS
    // ==========================================

    /**
     * Generate cache key for merchant's custom attributes
     * Used in: AttributeCustomRepository::getByMerchantId()
     * TTL: 15 minutes
     */
    public static function merchantAttributes(string $merchantId): string;

    // ==========================================
    // PRODUCT CACHE KEYS
    // ==========================================

    /**
     * Generate cache key for product lookup by ID
     * Used in: ProductRepository::findById()
     * TTL: 10 minutes
     */
    public static function productById(string $id): string;

    /**
     * Generate cache key for product lookup by slug
     * Used in: ProductRepository::findBySlug()
     * TTL: 10 minutes
     */
    public static function productBySlug(string $slug): string;

    /**
     * Generate cache key for merchant's products
     * Used in: ProductRepository::getByMerchantId()
     * TTL: 5 minutes
     */
    public static function merchantProducts(string $merchantId): string;

    /**
     * Generate cache key for category products
     * Used in: ProductRepository::getByCategoryId()
     * TTL: 5 minutes
     */
    public static function categoryProducts(string $categoryId): string;

    // ==========================================
    // PRODUCT VARIANT CACHE KEYS
    // ==========================================

    /**
     * Generate cache key for product variants
     * Used in: ProductVariantRepository::getByProductId()
     * TTL: 5 minutes
     */
    public static function productVariants(string $productId): string;

    /**
     * Generate cache key for variant lookup by SKU
     * Used in: ProductVariantRepository::findBySku()
     * TTL: 10 minutes
     */
    public static function variantBySku(string $sku): string;

    /**
     * Generate cache key for variant lookup by barcode
     * Used in: ProductVariantRepository::findByBarcode()
     * TTL: 10 minutes
     */
    public static function variantByBarcode(string $barcode): string;

    // ==========================================
    // PRODUCT BUNDLE CACHE KEYS
    // ==========================================

    /**
     * Generate cache key for bundle lookup by ID
     * Used in: ProductBundleRepository::findById()
     * TTL: 15 minutes
     */
    public static function bundleById(string $id): string;

    // ==========================================
    // UNIT CONVERSION CACHE KEYS
    // ==========================================

    /**
     * Generate cache key for unit conversions
     * Used in: UnitConversionRepository::getConversions()
     * TTL: 60 minutes
     */
    public static function unitConversions(): string;

    // ==========================================
    // CACHE INVALIDATION PATTERNS
    // ==========================================

    /**
     * Generate pattern for category-related cache keys
     * Pattern: product:category:*
     */
    public static function categoryPattern(): string;

    /**
     * Generate pattern for product-related cache keys
     * Pattern: product:product:*
     */
    public static function productPattern(): string;

    /**
     * Generate pattern for merchant-related cache keys
     * Pattern: product:merchant:{merchantId}:*
     */
    public static function merchantPattern(string $merchantId): string;

    /**
     * Generate pattern for all product cache keys
     * Pattern: product:*
     */
    public static function allProductPattern(): string;
}
