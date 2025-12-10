<?php

namespace Modules\Product\Services\Product;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductVariant;

/**
 * Interface for Product Service
 *
 * Comprehensive service that handles Product, Variant, and Pricing operations
 * for merchant-scoped data. All operations require merchant_id for data isolation.
 */
interface IProductService
{
    // ==========================================
    // PRODUCT OPERATIONS
    // ==========================================

    /**
     * Create a new product for a merchant
     *
     * @param array $productData Product data with optional variants and pricing
     * @param string $merchantId Merchant UUID
     * @return Product Created product with relationships loaded
     */
    public function createProduct(array $productData, string $merchantId): Product;

    /**
     * Update an existing product
     *
     * @param string $productId Product UUID
     * @param array $productData Updated product data
     * @param string $merchantId Merchant UUID for ownership validation
     * @return Product Updated product
     */
    public function updateProduct(string $productId, array $productData, string $merchantId): Product;

    /**
     * Delete a product
     *
     * @param string $productId Product UUID
     * @param string $merchantId Merchant UUID for ownership validation
     * @return bool Success status
     */
    public function deleteProduct(string $productId, string $merchantId): bool;

    /**
     * Get product by ID with ownership validation
     *
     * @param string $productId Product UUID
     * @param string $merchantId Merchant UUID
     * @return Product|null Product with relationships or null
     */
    public function getProduct(string $productId, string $merchantId): ?Product;

    /**
     * Get product with variants loaded
     *
     * @param string $productId Product UUID
     * @param string $merchantId Merchant UUID
     * @return Product|null Product with variants or null
     */
    public function getProductWithVariants(string $productId, string $merchantId): ?Product;

    /**
     * Get merchant's products with pagination
     *
     * @param string $merchantId Merchant UUID
     * @param array $filters Optional filters (category, status, search, etc.)
     * @param int $perPage Items per page
     * @return LengthAwarePaginator Paginated products
     */
    public function getMerchantProducts(string $merchantId, array $filters = [], int $perPage = 20): LengthAwarePaginator;

    /**
     * Duplicate a product for the same merchant
     *
     * @param string $productId Source product UUID
     * @param string $merchantId Merchant UUID
     * @param array $overrides Data to override in duplicate
     * @return Product Duplicated product
     */
    public function duplicateProduct(string $productId, string $merchantId, array $overrides = []): Product;

    /**
     * Toggle product active status
     *
     * @param string $productId Product UUID
     * @param bool $active New active status
     * @param string $merchantId Merchant UUID
     * @return bool Success status
     */
    public function toggleProductStatus(string $productId, bool $active, string $merchantId): bool;

    // ==========================================
    // VARIANT OPERATIONS
    // ==========================================

    /**
     * Add variant to a product
     *
     * @param string $productId Product UUID
     * @param array $variantData Variant data
     * @param string $merchantId Merchant UUID
     * @return ProductVariant Created variant
     */
    public function addVariant(string $productId, array $variantData, string $merchantId): ProductVariant;

    /**
     * Update existing variant
     *
     * @param string $variantId Variant UUID
     * @param array $variantData Updated variant data
     * @param string $merchantId Merchant UUID
     * @return ProductVariant Updated variant
     */
    public function updateVariant(string $variantId, array $variantData, string $merchantId): ProductVariant;

    /**
     * Remove variant from product
     *
     * @param string $variantId Variant UUID
     * @param string $merchantId Merchant UUID
     * @return bool Success status
     */
    public function removeVariant(string $variantId, string $merchantId): bool;

    /**
     * Get all variants for a product
     *
     * @param string $productId Product UUID
     * @param string $merchantId Merchant UUID
     * @return Collection Product variants
     */
    public function getProductVariants(string $productId, string $merchantId): Collection;

    /**
     * Update variant price
     *
     * @param string $variantId Variant UUID
     * @param float $price New price
     * @param string $merchantId Merchant UUID
     * @return bool Success status
     */
    public function updateVariantPrice(string $variantId, float $price, string $merchantId): bool;

    /**
     * Generate possible variant combinations for a product
     *
     * @param string $productId Product UUID
     * @param string $merchantId Merchant UUID
     * @return Collection Possible combinations
     */
    public function generateVariantCombinations(string $productId, string $merchantId): Collection;

    // ==========================================
    // PRICING OPERATIONS
    // ==========================================

    /**
     * Publish product (make it available for sale)
     *
     * @param string $productId Product UUID
     * @param string $merchantId Merchant UUID
     * @return bool Success status
     */
    public function publishProduct(string $productId, string $merchantId): bool;

    /**
     * Unpublish product (remove from sale)
     *
     * @param string $productId Product UUID
     * @param string $merchantId Merchant UUID
     * @return bool Success status
     */
    public function unpublishProduct(string $productId, string $merchantId): bool;

    // ==========================================
    // BULK OPERATIONS
    // ==========================================

    /**
     * Bulk update products
     *
     * @param array $productIds Array of product UUIDs
     * @param array $updateData Data to update
     * @param string $merchantId Merchant UUID
     * @return array Update results
     */
    public function bulkUpdateProducts(array $productIds, array $updateData, string $merchantId): array;

    // ==========================================
    // VALIDATION & BUSINESS RULES
    // ==========================================

    /**
     * Validate product data
     *
     * @param array $productData Product data to validate
     * @param string $merchantId Merchant UUID
     * @return array Validation results
     */
    public function validateProductData(array $productData, string $merchantId): array;

    /**
     * Validate variant data
     *
     * @param array $variantData Variant data to validate
     * @param string $productId Product UUID
     * @param string $merchantId Merchant UUID
     * @return array Validation results
     */
    public function validateVariantData(array $variantData, string $productId, string $merchantId): array;

    /**
     * Check merchant product limits
     *
     * @param string $merchantId Merchant UUID
     * @return array Limit information
     */
    public function checkProductLimits(string $merchantId): array;

    /**
     * Generate unique slug for product
     *
     * @param string $name Product name
     * @param string|null $excludeId Product ID to exclude
     * @return string Unique slug
     */
    public function generateSlug(string $name, ?string $excludeId = null): string;

    /**
     * Generate SKU for variant
     *
     * @param string $productSku Base product SKU
     * @param array $attributes Variant attributes
     * @return string Generated SKU
     */
    public function generateVariantSku(string $productSku, array $attributes): string;
}
