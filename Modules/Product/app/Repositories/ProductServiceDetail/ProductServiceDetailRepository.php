<?php

namespace Modules\Product\Repositories\ProductServiceDetail;

use Illuminate\Support\Facades\Cache;
use Modules\Product\Cache\ProductServiceDetail\ProductServiceDetailKeyManager;
use Modules\Product\Cache\ProductServiceDetail\ProductServiceDetailCacheManager;
use Modules\Product\Cache\ProductServiceDetail\ProductServiceDetailTtlManager;
use Modules\Product\Models\ProductServiceDetail;

class ProductServiceDetailRepository implements IProductServiceDetailRepository
{
    protected ProductServiceDetail $model;

    public function __construct(ProductServiceDetail $model)
    {
        $this->model = $model;
    }

    public function findById(string $serviceId): ?ProductServiceDetail
    {
        $cacheKey = ProductServiceDetailKeyManager::serviceDetailById($serviceId);
        $ttl = ProductServiceDetailTtlManager::serviceDetailEntity();

        return Cache::remember($cacheKey, $ttl, function () use ($serviceId) {
            return $this->model->find($serviceId);
        });
    }

    public function findByProductId(string $productId): ?ProductServiceDetail
    {
        $cacheKey = ProductServiceDetailKeyManager::serviceDetailByProductId($productId);
        $ttl = ProductServiceDetailTtlManager::serviceDetailLookup();

        return Cache::remember($cacheKey, $ttl, function () use ($productId) {
            return $this->model->where('product_id', $productId)->first();
        });
    }

    public function create(array $data): ProductServiceDetail
    {
        $serviceDetail = $this->model->create($data);

        ProductServiceDetailCacheManager::invalidateForOperation('create', [
            'product_id' => $serviceDetail->product_id,
        ]);

        return $serviceDetail;
    }

    public function update(string $serviceId, array $data): bool
    {
        $serviceDetail = $this->model->find($serviceId);
        if (!$serviceDetail) return false;

        $oldData = $serviceDetail->toArray();
        $result = $serviceDetail->update($data);

        if ($result) {
            ProductServiceDetailCacheManager::invalidateForOperation('update', [
                'service_id' => $serviceId,
                'data' => $data,
                'old_data' => $oldData,
            ]);
        }

        return $result;
    }

    public function delete(string $serviceId): bool
    {
        $serviceDetail = $this->model->find($serviceId);
        if (!$serviceDetail) return false;

        $result = $serviceDetail->delete();
        if ($result) {
            ProductServiceDetailCacheManager::invalidateForOperation('delete', [
                'service_id' => $serviceId,
                'service_detail' => $serviceDetail->toArray(),
            ]);
        }

        return $result;
    }
}
