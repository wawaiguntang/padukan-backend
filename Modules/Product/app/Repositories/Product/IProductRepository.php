<?php

namespace Modules\Product\Repositories\Product;

use Illuminate\Database\Eloquent\Collection;
use Modules\Product\Enums\ProductTypeEnum;
use Modules\Product\Models\Product;

/**
 * Interface for Product Repository
 *
 * This interface defines the contract for product data operations
 * in the product module.
 */
interface IProductRepository
{
    /**
     * Find a product by its ID
     *
     * @param string $id The product's UUID
     * @return Product|null The product model if found, null otherwise
     */
    public function findById(string $id): ?Product;


    /**
     * Get products by merchant ID
     *
     * @param string $merchantId The merchant ID
     * @param array $filters Optional filters (category, status, search, etc.)
     * @param int $perPage Items per page (0 for no pagination)
     * @return Collection|\Illuminate\Contracts\Pagination\LengthAwarePaginator The collection or paginated products
     */
    public function getByMerchantId(string $merchantId, array $filters = [], int $perPage = 0);

    /**
     * Get products by category ID
     *
     * @param string $categoryId The category ID
     * @param bool $includeExpired Include expired products (default: false)
     * @return Collection The collection of products
     */
    public function getByCategoryId(string $categoryId, bool $includeExpired = false): Collection;


    /**
     * Create a new product
     *
     * @param array $data Product data containing:
     * - merchant_id: string - Merchant ID
     * - category_id?: string - Category ID
     * - name: string - Product name
     * - slug?: string - Product slug (auto-generated if not provided)
     * - description?: string - Product description
     * - type: ProductTypeEnum - Product type
     * - barcode?: string - Product barcode
     * - sku?: string - Product SKU
     * - base_unit?: string - Base unit
     * - price?: float - Product price
     * - has_variant?: bool - Has variants flag
     * - metadata?: array - Additional metadata
     * @return Product The created product model
     */
    public function create(array $data): Product;

    /**
     * Update an existing product
     *
     * @param string $id The product's UUID
     * @param array $data Product data to update
     * @return bool True if update was successful, false otherwise
     */
    public function update(string $id, array $data): bool;

    /**
     * Delete a product (soft delete)
     *
     * @param string $id The product's UUID
     * @return bool True if deletion was successful, false otherwise
     */
    public function delete(string $id): bool;


    /**
     * Check if a product exists by slug
     *
     * @param string $slug The product slug
     * @param string|null $excludeId Exclude this ID from check (for updates)
     * @return bool True if product exists, false otherwise
     */
    public function existsBySlug(string $slug, ?string $excludeId = null): bool;

    /**
     * Check if SKU exists for merchant
     *
     * @param string $merchantId The merchant ID
     * @param string $sku The SKU
     * @param string|null $excludeId Exclude this product ID from check
     * @return bool True if SKU exists, false otherwise
     */
    public function existsSkuForMerchant(string $merchantId, string $sku, ?string $excludeId = null): bool;

    /**
     * Check if barcode exists for merchant
     *
     * @param string $merchantId The merchant ID
     * @param string $barcode The barcode
     * @param string|null $excludeId Exclude this product ID from check
     * @return bool True if barcode exists, false otherwise
     */
    public function existsBarcodeForMerchant(string $merchantId, string $barcode, ?string $excludeId = null): bool;

    /**
     * Get products that have variants
     *
     * @param string $merchantId The merchant ID
     * @return Collection The collection of products with variants
     */
    public function getProductsWithVariants(string $merchantId): Collection;

    /**
     * Get products without variants
     *
     * @param string $merchantId The merchant ID
     * @return Collection The collection of products without variants
     */
    public function getProductsWithoutVariants(string $merchantId): Collection;

    /**
     * Get expired products
     *
     * @param string $merchantId The merchant ID
     * @return Collection The collection of expired products
     */
    public function getExpiredProducts(string $merchantId): Collection;

    /**
     * Update product expiration status
     *
     * @param string $id The product's UUID
     * @param bool $expired The expiration status
     * @return bool True if update was successful, false otherwise
     */
    public function updateExpirationStatus(string $id, bool $expired): bool;

    // ==========================================
    // MERCHANT-SCOPED METHODS
    // ==========================================

    /**
     * Find a product by ID and merchant
     *
     * @param string $id The product's UUID
     * @param string $merchantId The merchant's UUID
     * @return Product|null The product model if found and owned by merchant, null otherwise
     */
    public function findByIdAndMerchant(string $id, string $merchantId): ?Product;

    /**
     * Create a new product for a specific merchant
     *
     * @param array $data Product data
     * @param string $merchantId The merchant's UUID
     * @return Product The created product model
     */
    public function createForMerchant(array $data, string $merchantId): Product;

    /**
     * Update an existing product for a specific merchant
     *
     * @param string $id The product's UUID
     * @param array $data Product data to update
     * @param string $merchantId The merchant's UUID for ownership validation
     * @return bool True if update was successful, false otherwise
     */
    public function updateForMerchant(string $id, array $data, string $merchantId): bool;

    /**
     * Delete a product for a specific merchant
     *
     * @param string $id The product's UUID
     * @param string $merchantId The merchant's UUID for ownership validation
     * @return bool True if deletion was successful, false otherwise
     */
    public function deleteForMerchant(string $id, string $merchantId): bool;

}
