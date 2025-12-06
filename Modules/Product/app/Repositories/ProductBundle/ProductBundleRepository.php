<?php

namespace Modules\Product\Repositories\ProductBundle;

use Illuminate\Contracts\Cache\Repository as Cache;
use Modules\Product\Cache\KeyManager\IKeyManager;
use Modules\Product\Models\ProductBundle;

class ProductBundleRepository implements IProductBundleRepository
{
    protected ProductBundle $model;
    protected Cache $cache;
    protected IKeyManager $cacheKeyManager;
    protected int $cacheTtl = 900; // 15 minutes

    public function __construct(ProductBundle $model, Cache $cache, IKeyManager $cacheKeyManager)
    {
        $this->model = $model;
        $this->cache = $cache;
        $this->cacheKeyManager = $cacheKeyManager;
    }

    public function findById(string $id): ?ProductBundle
    {
        $cacheKey = $this->cacheKeyManager::bundleById($id);
        return $this->cache->remember($cacheKey, $this->cacheTtl, fn() => $this->model->find($id));
    }

    public function create(array $data): ProductBundle
    {
        return $this->model->create($data);
    }

    public function update(string $id, array $data): bool
    {
        $bundle = $this->model->find($id);
        return $bundle ? $bundle->update($data) : false;
    }

    public function delete(string $id): bool
    {
        $bundle = $this->model->find($id);
        return $bundle ? $bundle->delete() : false;
    }

    public function existsByName(string $name, ?string $excludeId = null): bool
    {
        $query = $this->model->where('name', $name);
        if ($excludeId) $query->where('id', '!=', $excludeId);
        return $query->exists();
    }
}
