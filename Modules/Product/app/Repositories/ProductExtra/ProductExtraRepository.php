<?php

namespace Modules\Product\Repositories\ProductExtra;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Modules\Product\Cache\ProductExtra\ProductExtraKeyManager;
use Modules\Product\Cache\ProductExtra\ProductExtraCacheManager;
use Modules\Product\Cache\ProductExtra\ProductExtraTtlManager;
use Modules\Product\Models\ProductExtra;

class ProductExtraRepository implements IProductExtraRepository
{
    protected ProductExtra $model;

    public function __construct(ProductExtra $model)
    {
        $this->model = $model;
    }

    public function findById(string $id): ?ProductExtra
    {
        $cacheKey = ProductExtraKeyManager::extraById($id);
        $ttl = ProductExtraTtlManager::extraEntity();

        return Cache::remember($cacheKey, $ttl, function () use ($id) {
            return $this->model->find($id);
        });
    }

    public function getByProductId(string $productId): Collection
    {
        $cacheKey = ProductExtraKeyManager::productExtras($productId);
        $ttl = ProductExtraTtlManager::extraList();

        return Cache::remember($cacheKey, $ttl, function () use ($productId) {
            return $this->model->where('product_id', $productId)->orderBy('name')->get();
        });
    }

    public function create(array $data): ProductExtra
    {
        $extra = $this->model->create($data);

        ProductExtraCacheManager::invalidateForOperation('create', [
            'product_id' => $extra->product_id,
        ]);

        return $extra;
    }

    public function update(string $id, array $data): bool
    {
        $extra = $this->model->find($id);
        if (!$extra) return false;

        $oldData = $extra->toArray();
        $result = $extra->update($data);

        if ($result) {
            ProductExtraCacheManager::invalidateForOperation('update', [
                'id' => $id,
                'data' => $data,
                'old_data' => $oldData,
            ]);
        }

        return $result;
    }

    public function delete(string $id): bool
    {
        $extra = $this->model->find($id);
        if (!$extra) return false;

        $result = $extra->delete();
        if ($result) {
            ProductExtraCacheManager::invalidateForOperation('delete', [
                'id' => $id,
                'extra' => $extra->toArray(),
            ]);
        }

        return $result;
    }
}
