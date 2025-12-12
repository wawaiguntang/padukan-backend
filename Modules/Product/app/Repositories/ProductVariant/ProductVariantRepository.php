<?php

namespace Modules\Product\Repositories\ProductVariant;

use Illuminate\Database\Eloquent\Collection;
use Modules\Product\Models\ProductVariant;

class ProductVariantRepository implements IProductVariantRepository
{
    protected ProductVariant $model;

    public function __construct(ProductVariant $model)
    {
        $this->model = $model;
    }

    public function findById(string $id): ?ProductVariant
    {
        return $this->model->find($id);
    }

    public function getByProductId(string $productId, bool $includeExpired = false): Collection
    {
        $query = $this->model->where('product_id', $productId);
        if (!$includeExpired) $query->where('has_expired', false);
        return $query->orderBy('name')->get();
    }

    public function findBySku(string $sku): ?ProductVariant
    {
        return $this->model->where('sku', $sku)->first();
    }

    public function findByBarcode(string $barcode): ?ProductVariant
    {
        return $this->model->where('barcode', $barcode)->first();
    }

    public function create(array $data): ProductVariant
    {
        return $this->model->create($data);
    }

    public function update(string $id, array $data): bool
    {
        $variant = $this->model->find($id);
        if (!$variant) return false;

        if ($variant->update($data)) {
            $variant->refresh();
            return true;
        }

        return false;
    }

    public function delete(string $id): bool
    {
        $variant = $this->model->find($id);
        if (!$variant) return false;

        return $variant->delete();
    }

    public function existsSku(string $sku, ?string $excludeId = null): bool
    {
        $query = $this->model->where('sku', $sku);
        if ($excludeId) $query->where('id', '!=', $excludeId);
        return $query->exists();
    }

    public function existsBarcode(string $barcode, ?string $excludeId = null): bool
    {
        $query = $this->model->where('barcode', $barcode);
        if ($excludeId) $query->where('id', '!=', $excludeId);
        return $query->exists();
    }

    public function getExpiredVariants(string $productId): Collection
    {
        return $this->model->where('product_id', $productId)
            ->where('has_expired', true)
            ->orderBy('name')
            ->get();
    }

    public function updateExpirationStatus(string $id, bool $expired): bool
    {
        return $this->update($id, ['has_expired' => $expired]);
    }

    public function createForProduct(array $data, string $productId, string $merchantId): ProductVariant
    {
        $data['product_id'] = $productId;
        // Note: merchant_id validation should be done at service level
        return $this->create($data);
    }
}
