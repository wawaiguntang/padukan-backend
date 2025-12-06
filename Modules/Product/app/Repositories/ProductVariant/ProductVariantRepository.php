<?php

namespace Modules\Product\Repositories\ProductVariant;

use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Database\Eloquent\Collection;
use Modules\Product\Cache\KeyManager\IKeyManager;
use Modules\Product\Models\ProductVariant;

class ProductVariantRepository implements IProductVariantRepository
{
    protected ProductVariant $model;
    protected Cache $cache;
    protected IKeyManager $cacheKeyManager;
    protected int $cacheTtl = 300; // 5 minutes

    public function __construct(ProductVariant $model, Cache $cache, IKeyManager $cacheKeyManager)
    {
        $this->model = $model;
        $this->cache = $cache;
        $this->cacheKeyManager = $cacheKeyManager;
    }

    public function findById(string $id): ?ProductVariant
    {
        return $this->model->find($id);
    }

    public function getByProductId(string $productId, bool $includeExpired = false): Collection
    {
        $cacheKey = $this->cacheKeyManager::productVariants($productId);
        return $this->cache->remember($cacheKey, $this->cacheTtl, function () use ($productId, $includeExpired) {
            $query = $this->model->where('product_id', $productId);
            if (!$includeExpired) $query->where('has_expired', false);
            return $query->orderBy('name')->get();
        });
    }

    public function findBySku(string $sku): ?ProductVariant
    {
        $cacheKey = $this->cacheKeyManager::variantBySku($sku);
        return $this->cache->remember(
            $cacheKey,
            $this->cacheTtl,
            fn() =>
            $this->model->where('sku', $sku)->first()
        );
    }

    public function findByBarcode(string $barcode): ?ProductVariant
    {
        $cacheKey = $this->cacheKeyManager::variantByBarcode($barcode);
        return $this->cache->remember(
            $cacheKey,
            $this->cacheTtl,
            fn() =>
            $this->model->where('barcode', $barcode)->first()
        );
    }

    public function create(array $data): ProductVariant
    {
        $variant = $this->model->create($data);
        $this->invalidateProductCache($variant->product_id);
        return $variant;
    }

    public function update(string $id, array $data): bool
    {
        $variant = $this->model->find($id);
        if (!$variant) return false;

        $oldSku = $variant->sku;
        $oldBarcode = $variant->barcode;
        $oldProductId = $variant->product_id;

        $result = $variant->update($data);

        if ($result) {
            $variant->refresh();

            // Invalidate old caches
            if (isset($data['sku']) && $data['sku'] !== $oldSku && $oldSku) {
                $this->cache->forget($this->cacheKeyManager::variantBySku($oldSku));
            }
            if (isset($data['barcode']) && $data['barcode'] !== $oldBarcode && $oldBarcode) {
                $this->cache->forget($this->cacheKeyManager::variantByBarcode($oldBarcode));
            }

            $this->invalidateProductCache($oldProductId);
            if (isset($data['product_id']) && $data['product_id'] !== $oldProductId) {
                $this->invalidateProductCache($data['product_id']);
            }
        }

        return $result;
    }

    public function delete(string $id): bool
    {
        $variant = $this->model->find($id);
        if (!$variant) return false;

        $result = $variant->delete();
        if ($result) $this->invalidateProductCache($variant->product_id);

        return $result;
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

    protected function invalidateProductCache(string $productId): void
    {
        $this->cache->forget($this->cacheKeyManager::productVariants($productId));
    }
}
