<?php

namespace Modules\Product\Repositories\ProductVariant;

use Illuminate\Database\Eloquent\Collection;
use Modules\Product\Models\ProductVariant;

interface IProductVariantRepository
{
    public function findById(string $id): ?ProductVariant;
    public function getByProductId(string $productId, bool $includeExpired = false): Collection;
    public function findBySku(string $sku): ?ProductVariant;
    public function findByBarcode(string $barcode): ?ProductVariant;
    public function create(array $data): ProductVariant;
    public function update(string $id, array $data): bool;
    public function delete(string $id): bool;
    public function existsSku(string $sku, ?string $excludeId = null): bool;
    public function existsBarcode(string $barcode, ?string $excludeId = null): bool;
    public function getExpiredVariants(string $productId): Collection;
    public function updateExpirationStatus(string $id, bool $expired): bool;

    /**
     * Create a variant for a specific product and merchant
     *
     * @param array $data Variant data
     * @param string $productId Product UUID
     * @param string $merchantId Merchant UUID
     * @return ProductVariant Created variant
     */
    public function createForProduct(array $data, string $productId, string $merchantId): ProductVariant;
}
